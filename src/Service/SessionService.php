<?php
namespace Solcre\Pokerclub\Service;

use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\ClassNotExistingException;
use Solcre\Pokerclub\Exception\InvalidRakebackClassException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class SessionService extends BaseService
{
    // ult
    const STATUS_CODE_404 = 404;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function checkGenericInputData($data)
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
            // check with: will->throwException
            throw new IncompleteDataException();
        }
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
            throw new IncompleteDataException();
        }

        try {
            $session = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new SessionNotFoundException();
            }
            throw $e;
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
            $session = parent::fetch($id);

            $this->entityManager->remove($session);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new SessionNotFoundException();
            }
            throw $e;
        }
    }

    public function createRakebackAlgorithm($classname)
    {
        // checkear si existe la clase (class exist)
        if (class_exists($classname)) {
            return new $classname();
        }

        throw new ClassNotExistingException();
    }
    
    public function calculateRakeback($idSession)
    {
        $session = parent::fetch($idSession);

        $rakebackAlgorithm = $this->createRakebackAlgorithm($session->getRakebackClass());
        
        $usersSession = $session->getSessionUsers();
        
        foreach ($usersSession as $userSession) {
            $sessionPointsOld = (int)$userSession->getAccumulatedPoints();

            $sessionPoints = $rakebackAlgorithm->calculate($userSession);
            
            if (!is_numeric($sessionPoints)) {
                throw new InvalidRakebackClassException("Type error: calculate method must return a valid number", 1);
            }

            $userSession->setAccumulatedPoints($sessionPoints);

            $user = $userSession->getUser();

            $user->setPoints($user->getPoints()+$sessionPoints-$sessionPointsOld);
        }

        $this->entityManager->flush();
        return true;
    }

    public function play($idSession)
    {
        try {
            $session = parent::fetch($idSession);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new SessionNotFoundException();
            }
            throw $e;
        }

        $session->setStartTimeReal(new \DateTime());

        $this->entityManager->flush($session);

        return $session;
    }

    public function stop($idSession)
    {
        try {
            $session = parent::fetch($idSession);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new SessionNotFoundException();
            }
            throw $e;
        }

        $session->setEndTime(new \DateTime());

        $this->entityManager->flush($session);

        return $session;
    }
}
