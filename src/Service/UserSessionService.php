<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\TableIsFullException;
use Exception;

class UserSessionService extends BaseService
{
    // ult
    const STATUS_CODE_404 = 404;

    protected $userService;

    public function __construct(EntityManager $em, $userService = null)
    {
        parent::__construct($em);
        $this->userService = $userService;
    }

    public function checkGenericInputData($data)
    {
        // does not include id
        if (!isset($data['idSession'], $data['idUser'], $data['isApproved'], $data['points'])) {
            throw new IncompleteDataException();
        }
    }

    public function add($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $user    = $this->entityManager->getReference('Solcre\Pokerclub\Entity\UserEntity', $data['idUser']);

        if (in_array($data['idUser'], $session->getActivePlayers())) {
            throw new UserSessionAlreadyAddedException();
        }
        /*
        if (!$session->hasSeatAvailable()) {
            throw new TableIsFullException();
        }
        */
        
        $userSession   = new UserSessionEntity();

        $userSession->setSession($session);
        $userSession->setIdUser($data['idUser']);
        $userSession->setIsApproved($data['isApproved']);
        $userSession->setAccumulatedPoints((int)$data['points']);
        $userSession->setUser($user);

        $this->entityManager->persist($userSession);
        $this->entityManager->flush($userSession);

        return $userSession;
    }

    public function update($data, $strategies = null)
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
        
        $userSession->setAccumulatedPoints($data['points']);
        $userSession->setMinimumHours($data['minimumHours']);
        $userSession->setCashout($data['cashout']);
        $userSession->setStart(new \DateTime($data['start']));
        $userSession->setIsApproved($data['isApproved']);
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $userSession->setSession($session);
        $userSession->setIdUser($data['idUser']);

        $this->entityManager->flush($userSession);

        return $userSession;
    }

    public function delete($id, $entityObj = null)
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
