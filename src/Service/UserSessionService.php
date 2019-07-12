<?php
namespace Solcre\lmsuy\Service;

use \Solcre\lmsuy\Entity\UserSessionEntity;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\UserSessionAlreadyAddedException;
use Solcre\lmsuy\Exception\TableIsFullException;

class UserSessionService extends BaseService
{
    protected $userService;

    public function __construct(EntityManager $em, $userService = null)
    {
        parent::__construct($em);
        $this->userService = $userService;
    }


    public function add($data, $strategies = null)
    {
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
        $user    = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserEntity', $data['idUser']);

        if (in_array($data['idUser'], $session->getActivePlayers())) {
            throw new UserSessionAlreadyAddedException();
        }
/*
        if (!$session->hasSeatAvailable()) {
            throw new TableIsFullException();
        }
*/
        $data['start'] = new \DateTime($data['start']);
        $userSession   = new UserSessionEntity();

        $userSession->setSession($session);
        $userSession->setIdUser($data['idUser']);
        $userSession->setIsApproved($data['isApproved']);
        $userSession->setAccumulatedPoints((int)$data['points']);
        $userSession->setStart($data['start']);
        $userSession->setUser($user);

        $this->entityManager->persist($userSession);
        $this->entityManager->flush($userSession);
    }

    public function update($data, $strategies = null)
    {
        $userSession = parent::fetch($data['id']);
        $userSession->setAccumulatedPoints($data['points']);
        $userSession->setCashout($data['cashout']);
        $userSession->setStart(new \DateTime ($data['start']));
        $userSession->setEnd($data['end']);
        $userSession->setIsApproved($data['isApproved']);
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
        $userSession->setSession($session);
        $userSession->setIdUser($data['idUser']);

        $this->entityManager->persist($userSession);
        $this->entityManager->flush($userSession);
    }

    public function delete($id, $entityObj = null)
    {
        $userSession = $this->entityManager->getReference('Solcre\lmsuy\Entity\UserSessionEntity', $id);

        $this->entityManager->remove($userSession);
        $this->entityManager->flush();
    }


    public function close($data)
    {

        $userSession = parent::fetch($data['id']);

        $data['end'] = new \DateTime($data['end']);
        $userSession->setEnd($data['end']);
        $userSession->setCashout($data['cashout']);

        if ($this->userService instanceof UserService) {
            $user           = $this->userService->fetch($data['idUser']);
            $user->setHours($user->getHours()+$userSession->getDuration());

            $this->entityManager->persist($user);
        }
            
            $this->entityManager->persist($userSession);
            $this->entityManager->flush();
    }
}
