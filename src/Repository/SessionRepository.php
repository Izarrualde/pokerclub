<?php

namespace Solcre\Pokerclub\Repository;

use Solcre\SolcreFramework2\Common\BaseRepository;

class SessionRepository extends BaseRepository
{
    public function fetchMySessions($username, int $count): array
    {
        // var_dump( $this->_entityName);
        var_dump('en sessionRepository'); die();
        /*var_dump($this->_entityName);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('*');

        // select all usersessions where el id de usuario esta vinculado a ese username

        $qb->from('UserSessionEntity'/* de session_users *//*, 'us');*/ // $this->_entityName ver que arroja sino
/*
        $qb->where('us.user_id = :userId'); /* quizas no sea user_id sino idUser como en entity */
       /* $qb->andWhere('su.isApproved = 1');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->orderBy('us.startTime', 'DESC');
        $qb->setMaxResults($count);
        $qb->setParameter('userId', $userId);
        return $qb->getQuery()->getResult();*/
    }
}
