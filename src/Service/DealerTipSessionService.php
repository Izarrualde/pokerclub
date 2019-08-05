<?php
namespace Solcre\Pokerclub\Service;

use \Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\Pokerclub\Exception\DealerTipInvalidException;

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
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
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
        try {
            $dealerTip = parent::fetch($id);

            $this->entityManager->remove($dealerTip);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            throw new DealerTipNotFoundException();
        } 
    } 
}
