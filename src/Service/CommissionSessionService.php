<?php
namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\ORMException;
use Solcre\Pokerclub\Entity\CommissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\CommissionExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\SessionEntity;

class CommissionSessionService extends BaseService
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
        // don't include id
        if (!isset($data['idSession'], $data['hour'], $data['commission'])) {
            throw BaseException::IncompleteDataException();
        }

        if (!is_numeric($data['commission']) || $data['commission'] < 0) {
            throw CommissionExceptions::commissionInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $commission  = new CommissionSessionEntity();


        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $commission->setSession($session);
        $commission->setHour(new \DateTime($data['hour']));
        $commission->setCommission($data['commission']);

        $this->entityManager->persist($commission);
        $this->entityManager->flush($commission);

        return $commission;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (! isset($id)) {
            throw BaseException::incompleteDataException();
        }

        try {
            $commission = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw CommissionExceptions::commissionNotFoundException();
            }

            throw $e;
        }

        if (! $commission instanceof CommissionSessionEntity) {
            throw CommissionExceptions::commissionNotFoundException();
        }

        $commission->setHour(new \DateTime($data['hour']));
        $commission->setCommission($data['commission']);

        $this->entityManager->flush($commission);

        return $commission;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $commission  = parent::fetch($id);

            $this->entityManager->remove($commission);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw CommissionExceptions::commissionNotFoundException();
            }

            throw $e;
        }
    }
}
