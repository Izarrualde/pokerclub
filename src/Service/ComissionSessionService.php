<?php
namespace Solcre\lmsuy\Service;

use Solcre\lmsuy\Entity\ComissionSessionEntity;

use Solcre\lmsuy\Entity\SessionEntity;

use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\ComissionInvalidException;

class ComissionSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['comission'])) {
            throw new ComissionInvalidException();
        }

        $comission    = new ComissionSessionEntity();
        $comission->setHour(new \DateTime($data['hour']));
        $comission->setComission($data['comission']);
        $session = $this->entityManager->getReference('Solcre\lmsuy\Entity\SessionEntity', $data['idSession']);
        $comission->setSession($session);

        $this->entityManager->persist($comission);
        $this->entityManager->flush($comission);

        return $comission;
    }


/*
        public function fetchParent($id)
        // only for purposes of unit testing
        {
            $comission = parent::fetch($id);
            return $comission;
        }
*/


    public function update($data, $strategies = null)
    {
        if (!is_numeric($data['comission'])) {
            throw new ComissionInvalidException();
        }
                
        $data['hour'] = new \DateTime($data['hour']);

        $comission    = parent::fetch($data['id']);
        $comission->setHour($data['hour'])
        ;
        $comission->setComission($data['comission']);

        $this->entityManager->flush($comission);

        return $comission;
    }

    public function delete($id, $entityObj = null)
    {
        $comission = $this->entityManager->getReference('Solcre\lmsuy\Entity\ComissionSessionEntity', $id);
        
        $this->entityManager->remove($comission);
        $this->entityManager->flush();

        return true;
    }
}
