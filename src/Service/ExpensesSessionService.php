<?php
namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\ORMException;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\ExpensesExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\SessionEntity;

class ExpensesSessionService extends BaseService
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
        if (!isset($data['idSession'], $data['description'], $data['amount'])) {
            throw BaseException::incompleteDataException();
        }

        if (!is_numeric($data['amount']) || $data['amount'] < 0) {
            throw ExpensesExceptions::expensesInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $expenditure = new ExpensesSessionEntity();

        try {
            $session = $this->entityManager->getReference(SessionEntity::class, $data['idSession']);
        } catch (ORMException $e) {
            throw SessionExceptions::sessionNotFoundException();
        }

        if (! $session instanceof SessionEntity) {
            throw SessionExceptions::sessionNotFoundException();
        }

        $expenditure->setSession($session);
        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);


        $this->entityManager->persist($expenditure);
        $this->entityManager->flush($expenditure);

        return $expenditure;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw BaseException::incompleteDataException();
        }

        try {
            $expenditure = $this->fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw ExpensesExceptions::expenditureNotFoundException();
            }

            throw $e;
        }
        
        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);

        $this->entityManager->flush($expenditure);

        return $expenditure;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $expenditure = $this->fetch($id);

            $this->entityManager->remove($expenditure);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() === self::STATUS_CODE_404) {
                throw ExpensesExceptions::expenditureNotFoundException();
            }

            throw $e;
        }
    }
}
