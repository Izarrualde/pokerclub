<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\ComissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;

class ComissionSessionService extends BaseService
{
    const STATUS_CODE_404 = 404;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function checkGenericInputData($data)
    {
        // don't include id

        if (!isset($data['idSession'], $data['hour'], $data['comission'])) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['comission']) || $data['comission'] < 0) {
            throw new ComissionInvalidException();
        }
    }

    public function add($data)
    {
        $this->checkGenericInputData($data);

        $comission  = new ComissionSessionEntity();
        $comission->setHour(new \DateTime($data['hour']));
        $comission->setComission($data['comission']);
        $session = $this->entityManager->getReference('Solcre\Pokerclub\Entity\SessionEntity', $data['idSession']);
        $comission->setSession($session);

        $this->entityManager->persist($comission);
        $this->entityManager->flush($comission);

        return $comission;
    }

    public function update($id, $data)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $comission = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new ComissionNotFoundException();
            }
            throw $e;
        }
        
        $comission->setHour(new \DateTime($data['hour']));
        $comission->setComission($data['comission']);

        $this->entityManager->flush($comission);

        return $comission;
    }

    public function delete($id, $entityObj = null): bool
    {
        try {
            $comission  = parent::fetch($id);

            $this->entityManager->remove($comission);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new ComissionNotFoundException();
            }
            throw $e;
        }
    }
}
