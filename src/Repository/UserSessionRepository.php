<?php

namespace Solcre\Pokerclub\Repository;

use Solcre\SolcreFramework2\Common\BaseRepository;

class UserSessionRepository extends BaseRepository
{
    public function getHistoricalSessions(int $userId, int $count): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.title');
        $qb->addSelect('s.startTime');
        $qb->addSelect('SUM(su.accumulatedPoints) AS points');
        $qb->from($this->_entityName, 'su');
        $qb->join('su.user', 'u');
        $qb->join('su.session', 's');
        $qb->where('u.id = :userId');
        $qb->andWhere('su.isApproved = 1');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->groupBy('s.id');
        $qb->orderBy('s.startTime', 'DESC');
        $qb->setMaxResults($count);
        $qb->setParameter('userId', $userId);
        return $qb->getQuery()->getResult();
    }
}
