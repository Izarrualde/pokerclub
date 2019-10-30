<?php

namespace Solcre\Pokerclub\Repository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use PDO;
use Doctrine\DBAL\Connection;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\SolcreFramework2\Common\BaseRepository;

class UserRepository extends BaseRepository{

    public function userExists($data, $id): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
                ->from($this->_entityName, 'u')
                ->where('(u.cellphone =:cellphone OR u.email =:email) AND u.id !=:id')
                ->setParameter('cellphone', $data['cellphone'])
                ->setParameter('email', $data['email'])
                ->setParameter('id', $id);
        $user = $qb->getQuery()->getOneOrNullResult();
        return $user instanceof UserEntity;
    }
}
