<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ServiceTipInvalidException;
use Solcre\Pokerclub\Exception\ServiceTipNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Exception;

class ServiceTipSessionService extends BaseService
{
    const STATUS_CODE_404 = 404;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function checkGenericInputData($data) 
    {
        // does not include id

        if (!isset($data['idSession'], $data['hour'], $data['serviceTip'])) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['serviceTip']) || $data['serviceTip'] < 0) {
            throw new ServiceTipInvalidException();
        }
    }
      

    public function add($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        $serviceTip   = new  ServiceTipSessionEntity();
        $serviceTip->setHour(new \DateTime($data['hour']));
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $serviceTip->setSession($session);
        $serviceTip->setServiceTip($data['serviceTip']);

        $this->entityManager->persist($serviceTip);
        $this->entityManager->flush($serviceTip);

        return $serviceTip;
    }

    public function update($data, $strategies = null)
    {
         $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $serviceTip   = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new DealerTipNotFoundException();
            }
            throw $e;
        }
        
        $serviceTip->setHour(new \DateTime($data['hour']));
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
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new ServiceTipNotFoundException();
            }
            throw $e;
        }
    }
}
