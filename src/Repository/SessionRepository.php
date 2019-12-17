<?php

namespace Solcre\Pokerclub\Repository;

use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\SolcreFramework2\Common\BaseRepository;

class SessionRepository extends BaseRepository
{
    public function fetchMySessions($username, int $count): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('us.id');
        $qb->addSelect('us.start');
        $qb->addSelect('us.end');
        $qb->addSelect('us.cashout');
        $qb->addSelect('us.accumulatedPoints');
        $qb->addSelect('s.id');
        $qb->from(UserSessionEntity::class, 'us');
        $qb->join('us.user', 'u');
        $qb->join('us.session', 's');
        $qb->where('u.username = :username');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setMaxResults($count);
        $qb->setParameter('username', $username);

        return $qb->getQuery()->getResult();
    }

    public function fetchCommissionsBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('sum(c.commission) AS total');
        $qb->from('Solcre\Pokerclub\Entity\CommissionSessionEntity', 'c');
        $qb->join('c.session', 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchDealerTipsBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('sum(d.dealerTip) AS total');
        $qb->from('Solcre\Pokerclub\Entity\DealerTipSessionEntity', 'd');
        $qb->join('d.session', 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchServiceTipsBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('sum(st.serviceTip) AS total');
        $qb->from('Solcre\Pokerclub\Entity\ServiceTipSessionEntity', 'st');
        $qb->join('st.session', 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchExpensesBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('sum(e.amount) AS total');
        $qb->from('Solcre\Pokerclub\Entity\ExpensesSessionEntity', 'e');
        $qb->join('e.session', 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchTotalCashinBySession(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('sum(b.amountCash)');
        $qb->from('Solcre\Pokerclub\Entity\UserSessionEntity', 'us');
        $qb->join('us.session', 's');
        $qb->join('us.buyins', 'b');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchHoursPlayedBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('s.endTime');
        // $qb->addSelect('DATE_DIFF(s.startTimeReal, s.endTime)');
        $qb->from($this->_entityName, 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function fetchPlayersBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->addSelect('s.startTimeReal');
        $qb->addSelect('count(us.id) AS players');
        $qb->from(UserSessionEntity::class, 'us');
        $qb->join('us.session', 's');
        $qb->groupBy('s.id');
        $qb->where('s.startTimeReal BETWEEN :from AND :to');
        $qb->andWhere('s.startTimeReal IS NOT NULL');
        $qb->andWhere('s.endTime IS NOT NULL');
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }
}
