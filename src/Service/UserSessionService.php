<?php
namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\ORMException;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\UserExceptions;
use Solcre\Pokerclub\Exception\UserSessionExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\SessionEntity;

class UserSessionService extends BaseService
{
    public const STATUS_CODE_404 = 404;
    public const AVATAR_FILE_KEY = 'avatar_file';

    protected $userService;
    private $config;

    public function __construct(EntityManager $em, UserService $userService, array $config)
    {
        parent::__construct($em);
        $this->userService = $userService;
        $this->config      = $config;
    }

    public function checkGenericInputData($data): void
    {
        // does not include id
        if (!isset($data['idSession'], $data['isApproved'], $data['points'])) {
            throw BaseException::incompleteDataException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        if (! isset($data['users_id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $seatedPlayers = $session->getSeatedPlayers();

        if ($session->getSeats() === count($seatedPlayers)) {
            throw SessionExceptions::tableIsFullException();
        }

        if (($session->getSeats() - count($seatedPlayers)) < (count($data['users_id']))) {
            throw SessionExceptions::insufficientAvailableSeatsException();
        }

        $usersAlreadyAdded = array_intersect($data['users_id'], $seatedPlayers);

        if (count($usersAlreadyAdded) > 0) {
                throw UserSessionExceptions::userSessionAlreadyAddedException($usersAlreadyAdded);
        }

        $usersSessionsAdded = [];

        foreach ($data['users_id'] as $user_id) {
            $user = $this->entityManager->getReference(UserEntity::class, $user_id);

            if (! $user instanceof UserEntity) {
                throw UserExceptions::userNotFoundException();
            }

            $userSession = new UserSessionEntity(null, $session);

            $userSession->setIdUser($user_id);
            $userSession->setIsApproved($data['isApproved']);
            $userSession->setAccumulatedPoints((int)$data['points']);
            $userSession->setUser($user);

            $usersSessionsAdded[] = $userSession;

            $this->entityManager->persist($userSession);
        }

        $this->entityManager->flush();

        return $usersSessionsAdded;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (! isset($id)) {
            throw BaseException::incompleteDataException();
        }

        try {
            $userSession = $this->fetch($id);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserSessionExceptions::userSessionNotFoundException();
            }

            throw $e;
        }

        if (! $userSession instanceof UserSessionEntity) {
            throw UserSessionExceptions::userSessionNotFoundException();
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

        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $userSession->setSession($session);

        $this->entityManager->flush($userSession);

        return $userSession;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $userSession = $this->fetch($id);

            $this->entityManager->remove($userSession);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserSessionExceptions::userSessionNotFoundException();
            }

            throw $e;
        }
    }

    public function close($data, $strategies = null): void
    {
        if (! isset($data['id'], $data['idUser'], $data['cashout'], $data['end'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $userSession = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw UserSessionExceptions::userSessionNotFoundException();
            }

            throw $e;
        }

        if (! $userSession instanceof UserSessionEntity) {
            throw UserSessionExceptions::userSessionNotFoundException();
        }

        $startSession      = $userSession->getStart();
        $attemptEndSession = new \DateTime();
        $requiredTime      =  $userSession->getMinimumMinutes();

        if ($userSession->inMinutes($startSession, $attemptEndSession) < $requiredTime) {
            throw UserSessionExceptions::insufficientUserSessionTimeException();
        }

        $userSession->setEnd(new \DateTime($data['end']));
        $userSession->setCashout($data['cashout']);

        $user = $this->userService->fetch($data['idUser']);

        /** @var UserEntity $user */
        $user->setHours($user->getHours()+$userSession->getDuration());

        $this->entityManager->persist($user);
        $this->entityManager->persist($userSession);
        $this->entityManager->flush();
    }

    public function getHistoricalSessions(UserEntity $user, int $count): array
    {
        return $this->repository->getHistoricalSessions($user->getId(), $count);
    }
}
