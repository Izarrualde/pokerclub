<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\CommissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\CommissionInvalidException;
use Solcre\Pokerclub\Exception\CommissionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

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
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['commission']) || $data['commission'] < 0) {
            throw new CommissionInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $commission  = new CommissionSessionEntity();
        $commission->setHour(new \DateTime($data['hour']));
        $commission->setCommission($data['commission']);
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $commission->setSession($session);

        $this->entityManager->persist($commission);
        $this->entityManager->flush($commission);

        return $commission;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $commission = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new CommissionNotFoundException();
            }
            throw $e;
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
                throw new CommissionNotFoundException();
            }
            throw $e;
        }
    }
}
