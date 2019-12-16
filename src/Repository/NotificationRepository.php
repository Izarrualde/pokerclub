<?php

namespace Solcre\Pokerclub\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Solcre\SolcreFramework2\Common\BaseRepository;

class NotificationRepository extends BaseRepository
{
    public function fetchUserNotifications($userId)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->_entityName, 'n');
//        $query = $this->_em->createNativeQuery('SELECT * FROM notifications n0_
//WHERE (n0_.user_id IS NULL AND n0_.type="GENERAL" AND n0_.id NOT IN(SELECT `notification_id` FROM general_notifications_users where user_id= :user and is_deleted=1 group by notification_id)) OR n0_.user_id=:user ORDER BY id DESC',
//            $rsm);

            $query = $this->_em->createNativeQuery(
                'SELECT n0_.* FROM notifications n0_, users as users
                WHERE (n0_.user_id IS NULL AND n0_.type="GENERAL" AND n0_.id NOT IN(SELECT `notification_id` FROM general_notifications_users where user_id= :user and is_deleted=1 group by notification_id)
                AND (users.id = :user AND users.created_date < n0_.created_date)
                ) OR n0_.user_id= :user ORDER BY id DESC',
                $rsm
            );
        $query->setParameter('user', $userId);
        
        return $query->getResult();
    }

    public function fetchCountUserNotifications($userId)
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->_entityName, 'n');
        $query = $this->_em->createNativeQuery(
            'SELECT n0_.id FROM notifications n0_, users as users
            WHERE (n0_.user_id IS NULL AND n0_.type="GENERAL" AND n0_.id NOT IN(SELECT `notification_id` FROM general_notifications_users WHERE general_notifications_users.user_id = :user ))
            AND (users.id = :user AND users.created_date < n0_.created_date) OR (n0_.user_id= :user  AND n0_.was_read = 0)',
            $rsm
        );
        $query->setParameter('user', $userId);

        return $query->getResult();
    }
}
