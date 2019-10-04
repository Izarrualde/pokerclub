<?php
namespace Solcre\Pokerclub\Service;

use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Exception;

class UserService extends BaseService
{
    const STATUS_CODE_404 = 404;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function checkGenericInputData($data)
    {
        // does not include id
        if (!isset(
            $data['password'],
            $data['name'],
            $data['lastname'],
            $data['email'],
            $data['username'],
            $data['multiplier'],
            $data['active'],
            $data['hours'],
            $data['points'],
            $data['sessions'],
            $data['results'],
            $data['cashin']
        )
        ) {
            throw new IncompleteDataException();
        }

        if (!is_numeric($data['multiplier']) ||
            (!is_numeric($data['hours']) || $data['hours'] < 0) ||
            (!is_numeric($data['points']) || $data['points'] < 0) ||
            (!is_numeric($data['sessions']) || $data['sessions'] < 0)||
            !is_numeric($data['results']) ||
            (!is_numeric($data['cashin']) || $data['cashin'] < 0)) {
            throw new UserInvalidException();
        }
    }

    public function add($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        $user = new UserEntity();
        $user->setPassword($data['password']);
        $user->setName($data['name']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setMultiplier($data['multiplier']);
        $user->setIsActive($data['active']);
        $user->setHours($data['hours']);
        $user->setPoints($data['points']);
        $user->setSessions($data['sessions']);
        $user->setResults($data['results']);
        $user->setCashin($data['cashin']);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }

    public function update($data, $strategies = null)
    {
        $this->checkGenericInputData($data);

        if (!isset($data['id'])) {
            throw new IncompleteDataException();
        }

        try {
            $user = parent::fetch($data['id']);
        } catch (Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserNotFoundException();
            }
            throw $e;
        }

        $user->setName($data['name']);
        $user->setLastname($data['lastname']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setMultiplier($data['multiplier']);
        $user->setPassword($data['password']);
        $user->setIsActive($data['active']);
        $user->setSessions($data['sessions']);
        $user->setHours($data['hours']);
        $user->setResults($data['results']);
        $user->setCashin($data['cashin']);

        $this->entityManager->flush($user);

        return $user;
    }

    public function delete($id, $entityObj = null)
    {
        try {
            $user = parent::fetch($id);

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == self::STATUS_CODE_404) {
                throw new UserNotFoundException();
            }
            throw $e;
        }
    }
}
