<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\ComissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;

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
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
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
        try {
            $comission    = parent::fetch($id);

            $this->entityManager->remove($comission);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            throw new ComissionNotFoundException();
        } 
    } 
}
