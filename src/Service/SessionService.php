<?php
namespace Solcre\Pokerclub\Service;

use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\Pokerclub\Exception\SessionNotFoundException;
use Exception;

class SessionService extends BaseService
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        $session = new SessionEntity();
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['start_at']));
        $session->setStartTimeReal(new \DateTime($data['real_start_at']));
        $session->setEndTime(new \DateTime($data['end_at']));
        $session->setRakebackClass($data['rakebackClass']);

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);

        return $session;
    }

    public function update($data, $strategies = null)
    {
        $session = parent::fetch($data['id']);
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['start_at']));
        $session->setStartTimeReal(new \DateTime($data['real_start_at']));
        $session->setEndTime(new \DateTime($data['end_at']));

        $this->entityManager->flush($session);

        return $session;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $session = parent::fetch($id);

            $this->entityManager->remove($session);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == 404) { //magic number
               throw new SessionNotFoundException(); 
            } 
            throw $e;
        }  
    } 

    protected function createRakebackAlgorithm($classname)
    {
        // checkear si existe la clase
        return new $classname();
    }
    
    public function calculateRakeback($idSession)
    {
        $session = parent::fetch($idSession);

        $rakebackAlgorithm = $this->createRakebackAlgorithm(
            $session->getRakebackClass()
        );

        $usersSession = $session->getSessionUsers();
        
        foreach ($usersSession as $userSession) {
            $sessionPoints = $rakebackAlgorithm->calculate($userSession);
            
            $userSession->setAccumulatedPoints($sessionPoints);

            $user = $userSession->getUser();

            $user->setPoints($user->getPoints()+$sessionPoints);

            // $this->entityManager->persist($userSession);
        }

        $this->entityManager->flush();
    }
}
