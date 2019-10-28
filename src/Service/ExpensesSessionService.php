<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ExpensesInvalidException;
use Solcre\Pokerclub\Exception\ExpenditureNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class ExpensesSessionService extends BaseService
{

    const STATUS_CODE_404 = 404;
    const AVATAR_FILE_KEY = 'avatar_file';

    private $config;

    public function __construct(EntityManager $entityManager, array $config)
    {
        parent::__construct($entityManager);
        $this->config = $config;
    }

    public function checkGenericInputData($data)
    {
        // does not include id

        if (!isset($data['idSession'], $data['description'], $data['amount'])) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['amount']) || $data['amount'] < 0) {
            throw new ExpensesInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $expenditure = new ExpensesSessionEntity();
        $session     = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
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
            throw new IncompleteDataException();
        }

        try {
            $expenditure = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new ExpenditureNotFoundException();
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
            $expenditure    = parent::fetch($id);

            $this->entityManager->remove($expenditure);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) { //magic number
                throw new ExpenditureNotFoundException();
            }
            throw $e;
        }
    }
}
