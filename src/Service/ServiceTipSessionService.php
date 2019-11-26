<?php
namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\ORMException;
use Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\ServiceTipExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\SessionEntity;

class ServiceTipSessionService extends BaseService
{

    public const STATUS_CODE_404 = 404;
    public const AVATAR_FILE_KEY = 'avatar_file';

    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        parent::__construct($entityManager);
        $this->config = $config;
    }

    public function checkGenericInputData($data): void
    {
        // does not include id

        if (! isset($data['idSession'], $data['hour'], $data['serviceTip'])) {
            throw BaseException::incompleteDataException();
        }

        if (!is_numeric($data['serviceTip']) || $data['serviceTip'] < 0) {
            throw ServiceTipExceptions::serviceTipInvalidException();
        }
    }
      

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $serviceTip   = new  ServiceTipSessionEntity();

        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        if (! $session instanceOf SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $serviceTip->setSession($session);
        $serviceTip->setServiceTip($data['serviceTip']);
        $serviceTip->setHour(new \DateTime($data['hour']));

        $this->entityManager->persist($serviceTip);
        $this->entityManager->flush($serviceTip);

        return $serviceTip;
    }

    public function update($id, $data)
    {
         $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $serviceTip   = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw ServiceTipExceptions::serviceTipNotFoundException();
            }

            throw $e;
        }

        if (! $serviceTip instanceof ServiceTipSessionEntity) {
            throw ServiceTipExceptions::serviceTipNotFoundException();
        }

        $serviceTip->setHour(new \DateTime($data['hour']));
        $serviceTip->setServiceTip($data['serviceTip']);

        $this->entityManager->flush($serviceTip);

        return $serviceTip;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $serviceTip    = $this->fetch($id);

            $this->entityManager->remove($serviceTip);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw ServiceTipExceptions::serviceTipNotFoundException();
            }

            throw $e;
        }
    }
}
