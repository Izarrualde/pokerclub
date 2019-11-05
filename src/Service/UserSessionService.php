<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\TableIsFullException;
use Solcre\Pokerclub\Exception\InsufficientUserSessionTimeException;
use Solcre\Pokerclub\Exception\InsufficientAvailableSeatsException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class UserSessionService extends BaseService
{

    const STATUS_CODE_404 = 404;
    const AVATAR_FILE_KEY = 'avatar_file';

    protected $userService;
    private $config;

    public function __construct(EntityManager $em, $userService, array $config)
    {
        parent::__construct($em);
        $this->userService = $userService;
        $this->config      = $config;
    }

    public function checkGenericInputData($data)
    {
        // does not include id
        if (!isset($data['idSession'], $data['isApproved'], $data['points'])) {
            throw new IncompleteDataException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['users_id'])) {
            throw new IncompleteDataException();
        }

        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);

        $seatedPlayers = $session->getSeatedPlayers();

        if ($session->getSeats() == count($seatedPlayers)) {
            throw new TableIsFullException();
        }

        if ($session->getSeats() - count($seatedPlayers)) < count($data['users_id']) {
            throw new InsuficcientAvailableSeats();
        }

        if in_array($data['users_id'], $seatedPlayers) {
                throw new UserSessionAlreadyAddedException();
        }

        foreach ($data['users_id'] as $user_id) {
            $user = $this->entityManager->getReference('Solcre\Pokerclub\Entity\UserEntity', $user_id);
            $userSession = new UserSessionEntity(null, $session);

            $userSession->setIdUser($data['user_id']);
            $userSession->setIsApproved($data['isApproved']);
            $userSession->setAccumulatedPoints((int)$data['points']);
            $userSession->setUser($user);

            $this->entityManager->persist($userSession);
        }
        
        $this->entityManager->flush();

        return $userSession;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($id)) {
            throw new IncompleteDataException();
        }

        try {
            $userSession = parent::fetch($id);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserSessionNotFoundException();
            }
            throw $e;
        }
        
        $userSession->setAccumulatedPoints($data['points']);

        if (isset($data['minimum_minutes'])) {
            $userSession->setMinimumMinutes($data['minimum_minutes']);
        }

        if (isset($data['cashout'])) {
            $userSession->setCashout($data['cashout']);
        }

        $userSession->setStart(new \DateTime($data['start']));
        $userSession->setIsApproved($data['isApproved']);
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $userSession->setSession($session);

        $this->entityManager->flush($userSession);

        return $userSession;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $userSession    = parent::fetch($id);

            $this->entityManager->remove($userSession);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserSessionNotFoundException();
            }
            throw $e;
        }
    }

    public function close($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $userSession = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserSessionNotFoundException();
            }
            throw $e;
        }

        $startSession      = $userSession->getStart();
        $attemptEndSession = new \DateTime();
        $requiredTime      =  $userSession->getMinimumMinutes();
        if ($userSession->inMinutes($startSession, $attemptEndSession) < $requiredTime) {
            throw new InsufficientUserSessionTimeException();
        }

        $userSession->setEnd(new \DateTime($data['end']));
        $userSession->setCashout($data['cashout']);
        
        if ($this->userService instanceof UserService) {
            $user = $this->userService->fetch($data['idUser']);
            $user->setHours($user->getHours()+$userSession->getDuration());

            $this->entityManager->persist($user);
        }
        
            $this->entityManager->persist($userSession);
            $this->entityManager->flush();
    }
}
