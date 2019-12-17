<?php
namespace Solcre\Pokerclub\Service;

use Lms\Service\RakebackService;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class SessionService extends BaseService
{
    public const STATUS_CODE_404 = 404;
    public const AVATAR_FILE_KEY = 'avatar_file';

    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        parent::__construct($entityManager);
        $this->config = $config;
    }

    public function checkGenericInputData($data): void
    {
        // don't include id
        if (!isset(
            $data['date'],
            $data['title'],
            $data['description'],
            $data['seats'],
            $data['start_at'],
            $data['rakeback_class'],
            $data['minimum_user_session_minutes']
        )
        ) {
            throw BaseException::incompleteDataException();
        }
    }

    public function fetchMySessions($username, $count)
    {
        return $this->repository->fetchMySessions($username, $count);
    }

    public function fetchCommissionsBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchCommissionsBetweenDates($from, $to);
    }

    public function fetchDealerTipsBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchDealerTipsBetweenDates($from, $to);
    }

    public function fetchTotalCashinBySession($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchTotalCashinBySession($from, $to);
    }

    public function fetchServiceTipsBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchServiceTipsBetweenDates($from, $to);
    }

    public function fetchExpensesBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchExpensesBetweenDates($from, $to);
    }

    public function fetchHoursPlayedBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchHoursPlayedBetweenDates($from, $to);
    }

    public function fetchPlayersBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchPlayersBetweenDates($from, $to);
    }

    public function fetchRakeRaceBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchRakeRaceBetweenDates($from, $to);
    }

    public function fetchTipsPerDealerBetweenDates($data)
    {
        $from = new \DateTime($data['from']);
        $to   = new \DateTime($data['to']);

        return $this->repository->fetchTipsPerDealerBetweenDates($from, $to);
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $session = new SessionEntity();
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['start_at']));
        $session->setRakebackClass($data['rakeback_class']);
        $session->setMinimumUserSessionMinutes($data['minimum_user_session_minutes']);

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);

        return $session;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $session = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw SessionExceptions::sessionNotFoundException();
            }

            throw $e;
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['start_at']));

        if (isset($data['real_start_at'])) {
            $session->setStartTimeReal(new \DateTime($data['real_start_at']));
        }

        if (isset($data['end_at'])) {
            $session->setEndTime(new \DateTime($data['end_at']));
        }

        $session->setRakebackClass($data['rakeback_class']);
        $session->setMinimumUserSessionMinutes($data['minimum_user_session_minutes']);

        $this->entityManager->flush($session);

        return $session;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $session = $this->fetch($id);

            $this->entityManager->remove($session);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw SessionExceptions::sessionNotFoundException();
            }

            throw $e;
        }
    }

    public function createRakebackAlgorithm($classname)
    {
        if (class_exists($classname)) {
            return new $classname();
        }

        throw BaseException::classNonExistentException();
    }

    public function calculateRakeback($idSession): bool
    {
        $session = $this->fetch($idSession);

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        try {
            $fqcnRakebackClass = RakebackService::NAME_SPACE . '\\' . $session->getRakebackClass();
            $rakebackAlgorithm = $this->createRakebackAlgorithm($fqcnRakebackClass);
        } catch (BaseException $e) {
            throw BaseException::classNonExistentException();
        }

        $usersSession = $session->getSessionUsers();

        /** @var UserSessionEntity $userSession */
        foreach ($usersSession as $userSession) {
            $sessionPointsOld = (int)$userSession->getAccumulatedPoints();

            $sessionPoints = $rakebackAlgorithm->calculate($userSession);

            if (! is_numeric($sessionPoints)) {
                throw SessionExceptions::invalidPointsException();
            }

            $userSession->setAccumulatedPoints($sessionPoints);

            $user = $userSession->getUser();

            if ($user instanceof UserEntity) {
                $user->setPoints($user->getPoints()+$sessionPoints-$sessionPointsOld);
            }
        }

        $this->entityManager->flush();

        return true;
    }

    public function play($idSession): bool
    {
        try {
            $session = $this->fetch($idSession);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw SessionExceptions::sessionNotFoundException();
            }

            throw $e;
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $session->setStartTimeReal(new \DateTime());

        $this->entityManager->flush($session);

        return true;
    }

    public function stop($idSession): bool
    {
        try {
            $session = $this->fetch($idSession);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw SessionExceptions::sessionNotFoundException();
            }

            throw $e;
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $session->setEndTime(new \DateTime());

        $this->entityManager->flush($session);

        return true;
    }
}
