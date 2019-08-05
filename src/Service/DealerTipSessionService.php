<?php
namespace Solcre\pokerclub\Service;

use \Solcre\pokerclub\Entity\DealerTipSessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\pokerclub\Exception\DealerTipInvalidException;

class DealerTipSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['dealerTip'])) {
            throw new DealerTipInvalidException();
        }

        $data['hour'] = new \DateTime($data['hour']);
        $dealerTip    = new DealerTipSessionEntity();
        $dealerTip->setHour($data['hour']);
        $session = $this->entityManager->getReference('Solcre\pokerclub\Entity\SessionEntity', $data['idSession']);
        $dealerTip->setSession($session);
        $dealerTip->setDealerTip($data['dealerTip']);

        $this->entityManager->persist($dealerTip);
        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function update($data, $strategies = null)
    {
       
        $dealerTip    = parent::fetch($data['id']);
        $dealerTip->setHour(new \DateTime($data['hour']));
        $dealerTip->setDealerTip($data['dealerTip']);

        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function delete($id, $entityObj = null)
    {
        $dealerTip = $this->entityManager->getReference('Solcre\pokerclub\Entity\DealerTipSessionEntity', $id);

        $this->entityManager->remove($dealerTip);
        $this->entityManager->flush();

        return true;
    }
}
