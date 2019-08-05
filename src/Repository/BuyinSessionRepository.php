<?php
namespace Solcre\pokerclub\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Solcre\pokerclub\Entity\BuyinSessionEntity;
use Solcre\pokerclub\Entity\UserSessionEntity;
use Solcre\pokerclub\Repository\BaseRepository;

/**
 * @codeCoverageIgnore
 */
class BuyinSessionRepository extends BaseRepository
{

    public function fetchAll($sessionId)
    {

          $qb = $this->_em->createQueryBuilder();
          $qb->select('b');
          $qb->from($this->_entityName, 'b');

          $qb->join(UserSessionEntity::class, 'u', Join::WITH, 'b.userSession = u.id');


          $qb->where('u.session=:param');

          $qb->setParameter('param', $sessionId);
       
          //$qb->addOrderBy(‘c.order’, ‘ASC’);
          //$qb->addOrderBy(‘p.id’, ‘ASC’);
       
          return $qb->getQuery()->getResult();
    }
}
