<?php
namespace Solcre\Pokerclub\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Repository\BaseRepository;

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
