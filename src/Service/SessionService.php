<?php
namespace Solcre\lmsuy\Service;

use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;

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
        $session->setStartTime(new \DateTime($data['startTime']));
        $session->setStartTimeReal(new \DateTime($data['startTimeReal']));
        $session->setEndTime(new \DateTime($data['endTime']));
        $session->setRakebackClass($data['rakebackClass']);

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);
    }

    public function update($data, $strategies = null)
    {

        $session = parent::fetch($data['idSession']);
        $session->setDate(new \DateTime($data['date']));
        $session->setTitle($data['title']);
        $session->setDescription($data['description']);
        $session->setSeats($data['seats']);
        $session->setStartTime(new \DateTime($data['startTime']));
        $session->setStartTimeReal(new \DateTime($data['startTimeReal']));
        $session->setEndTime(new \DateTime($data['endTime']));

        $this->entityManager->persist($session);
        $this->entityManager->flush($session);
    }

    public function delete($id, $entityObj = null)
    {
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $id);

        $this->entityManager->remove($session);
        $this->entityManager->flush();
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

            $this->entityManager->persist($userSession);
        }

        $this->entityManager->flush();
    }
}
