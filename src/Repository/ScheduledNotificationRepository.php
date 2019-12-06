<?php
namespace Solcre\Pokerclub\Repository;

use Solcre\SolcreFramework2\Common\BaseRepository;
use DateTime;

class ScheduledNotificationRepository extends BaseRepository
{
    public function getNotificationsWithoutSending()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('n.id')
            ->from($this->_entityName, 'sn')
            ->innerJoin('sn.notification', 'n')
            ->where('sn.sentDate IS NULL')
            ->andWhere('sn.sending = 0')
            ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function getDevicesWithoutSending(int $notificationId, int $platformId, int $limit = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d.deviceToken, sn.id AS scheduledNotificationId')
            ->from($this->_entityName, 'sn')
            ->innerJoin('sn.device', 'd')
            ->innerJoin('sn.notification', 'n')
            ->where('sn.sentDate IS NULL')
            ->andWhere('sn.sending = 0')
            ->andWhere('n.id = :notificationId')
            ->andWhere('d.platform = :platformId')
            ->setParameter(':notificationId', $notificationId)
            ->setParameter(':platformId', $platformId);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function setAsSent(array $scheduledNotificationsIds)
    {
        $currentDate = new DateTime("NOW");
        $qb = $this->_em->createQueryBuilder();
        $qb->update($this->_entityName, 'sn')
            ->set('sn.sentDate', ':currentDate')
            ->where($qb->expr()->in('sn.id', ':scheduledNotificationsIds'))
            ->setParameter(':currentDate', $currentDate->format('Y-m-d H:i:s'))
            ->setParameter(':scheduledNotificationsIds', $scheduledNotificationsIds);

        return $qb->getQuery()->getResult();
    }

    public function setAsSending(array $scheduledNotificationsIds, bool $sending)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->update($this->_entityName, 'sn')
            ->set('sn.sending', ':sending')
            ->where($qb->expr()->in('sn.id', ':scheduledNotificationsIds'))
            ->setParameter(':sending', $sending)
            ->setParameter(':scheduledNotificationsIds', $scheduledNotificationsIds);

        return $qb->getQuery()->getResult();
    }
}
