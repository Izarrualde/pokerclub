<?php

namespace Solcre\Pokerclub\Repository;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use PDO;
use Doctrine\DBAL\Connection;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\SolcreFramework2\Common\BaseRepository;

class UserRepository extends BaseRepository
{

    public function userExists($data): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
           ->from($this->_entityName, 'u')
           ->where('(u.username =:username OR u.email =:email)')
           ->setParameter('username', $data['username'])
           ->setParameter('email', $data['email']);
        $user = $qb->getQuery()->getOneOrNullResult();

        return $user instanceof UserEntity;
    }
}
