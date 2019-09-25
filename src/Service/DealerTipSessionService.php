<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\DealerTipInvalidException;
use Solcre\Pokerclub\Exception\DealerTipNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Exception;

class DealerTipSessionService extends BaseService
{
    const STATUS_CODE_404 = 404;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function checkGenericInputData($data)
    {
        // does not include id

        if (!isset($data['idSession'], $data['hour'], $data['dealerTip'])) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['dealerTip']) || $data['dealerTip'] < 0) {
            throw new DealerTipInvalidException();
        }
    }

    public function add($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        $dealerTip    = new DealerTipSessionEntity();
        $dealerTip->setHour(new \DateTime($data['hour']));
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $dealerTip->setSession($session);
        $dealerTip->setDealerTip($data['dealerTip']);

        $this->entityManager->persist($dealerTip);
        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function update($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $dealerTip    = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new DealerTipNotFoundException();
            }

            throw $e;
        }
        
        $dealerTip->setHour(new \DateTime($data['hour']));
        $dealerTip->setDealerTip($data['dealerTip']);

        $this->entityManager->flush($dealerTip);

        return $dealerTip;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $dealerTip    = parent::fetch($id);

            $this->entityManager->remove($dealerTip);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new DealerTipNotFoundException();
            }
            throw $e;
        }
    }
}
