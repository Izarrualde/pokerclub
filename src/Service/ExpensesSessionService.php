<?php
namespace Solcre\Pokerclub\Service;

use \Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use \Solcre\Pokerclub\Exception\ExpensesInvalidException;
use \Solcre\Pokerclub\Exception\ExpenditureNotFoundException;

class ExpensesSessionService extends BaseService
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function add($data, $strategies = null)
    {
        if (!is_numeric($data['amount'])) {
            throw new ExpensesInvalidException();
        }

        $expenditure = new ExpensesSessionEntity();
        $session     = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $expenditure->setSession($session);

        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);

        $this->entityManager->persist($expenditure);
        $this->entityManager->flush($expenditure);

        return $expenditure;
    }

    public function update($data, $strategies = null)
    {

        $expenditure = parent::fetch($data['id']);
        $expenditure->setDescription($data['description']);
        $expenditure->setAmount($data['amount']);

        $this->entityManager->flush($expenditure);

        return $expenditure;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $expenditure    = parent::fetch($id);

            $this->entityManager->remove($expenditure);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            throw new ExpenditureNotFoundException();
        } 
    } 


}
