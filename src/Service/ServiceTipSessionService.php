<?php
namespace Solcre\Pokerclub\Service;

use \Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\Pokerclubmsuy\Exception\ServiceTipInvalidException;
use Exception;

class ServiceTipSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['serviceTip'])) {
            throw new ServiceTipInvalidException();
        }

        $data['hour'] = new \DateTime($data['hour']);
        $serviceTip   = new  ServiceTipSessionEntity();
        $serviceTip->setHour($data['hour']);
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $serviceTip->setSession($session);
        $serviceTip->setServiceTip($data['serviceTip']);

        $this->entityManager->persist($serviceTip);
        $this->entityManager->flush($serviceTip);

        return $serviceTip;
    }

    public function update($data, $strategies = null)
    {
        $data['hour'] = new \DateTime($data['hour']);
        $serviceTip   = parent::fetch($data['id']);
        $serviceTip->setHour($data['hour']);
        $serviceTip->setServiceTip($data['serviceTip']);

        $this->entityManager->flush($serviceTip);

        return $serviceTip;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $serviceTip    = parent::fetch($id);

            $this->entityManager->remove($serviceTip);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == 404) { //magic number
               throw new ServiceTipNotFoundException(); 
            } 
            throw $e;
        }  
    }  
}
