<?php

namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\EntityManager;
use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\Pokerclub\Entity\ScheduledNotificationEntity;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\ApiProblem\ApiProblem;
use Exception;
use DateTime;

class ScheduledNotificationService extends BaseService
{
    private $deviceService;
    private $notificationService;

    public const PLATFORM_ID = [
        'android' => 1,
        'ios'     => 2
    ];

    public const DEVICES_LIMIT_MAX = [
        'android' => 1000,
        'ios'     => 200
    ];

    public function __construct(EntityManager $entityManager, DeviceService $deviceService, NotificationService $notificationService)
    {
        parent::__construct($entityManager);
        $this->deviceService       = $deviceService;
        $this->notificationService = $notificationService;
    }

    public function add(array $data, bool $flush = true)
    {
        $device = $this->deviceService->fetch($data['device_id']);
        $notification = $this->notificationService->fetch($data['notification_id']);
        $scheduledNotification = new ScheduledNotificationEntity();
        $scheduledNotification->setDevice($device);
        $scheduledNotification->setNotification($notification);
        $scheduledNotification->setCreatedDate(new DateTime());
        $scheduledNotification->setSentDate(null);
        $scheduledNotification->setSending(0);
        $this->entityManager->persist($scheduledNotification);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $scheduledNotification;
    }

    public function send()
    {
        try {
            // get notifications that were not sent for any device
            $notifications = $this->getNotificationsWithoutSending();

            if (! empty($notifications) && is_array($notifications)) {
                foreach ($notifications as $notification) {
                    if ($this->lockScheduledNotificationsTable()) {
                        $this->entityManager->beginTransaction();
                        // get devices that not sent them this notification 
                        $androidDevices            = $this->getDevicesWithoutSending($notification['id'],
                                                                                     self::PLATFORM_ID['android'],
                                                                                     self::DEVICES_LIMIT_MAX['android']);
                        $iosDevices                = $this->getDevicesWithoutSending($notification['id'],
                                                                                     self::PLATFORM_ID['ios'],
                                                                                     self::DEVICES_LIMIT_MAX['ios']);
                        $devices                   = array_merge($androidDevices, $iosDevices);
                        $scheduledNotificationsIds = $this->getScheduledNotificationsIds($devices);
                        $this->setAsSending($scheduledNotificationsIds, true);
                        $this->entityManager->flush();
                        $this->entityManager->commit();
                        $this->UnlockScheduledNotificationsTable();

                        if (! empty($androidDevices) && is_array($androidDevices)) {
                            $androidDevicesSent = $this->notificationService->sendAndroidNotification($notification['id'], $androidDevices);

                            if (! empty($androidDevicesSent) && is_array($androidDevicesSent)) {
                                $scheduledNotificationsIdsSent = $this->getScheduledNotificationsIds($androidDevicesSent);
                                $this->setAsSent($scheduledNotificationsIdsSent);
                            }
                        }

                        if (! empty($iosDevices) && is_array($iosDevices)) {
                            $iosDevicesSent = $this->notificationService->sendIosNotification($notification['id'], $iosDevices);

                            if (! empty($iosDevicesSent) && is_array($iosDevicesSent)) {
                                $scheduledNotificationsIdsSent = $this->getScheduledNotificationsIds($iosDevicesSent);
                                $this->setAsSent($scheduledNotificationsIdsSent);
                            }
                        }

                        $this->setAsSending($scheduledNotificationsIds, false);
                    }
                }
            }

            return true;
        } catch (Exception $e)
        {
            $this->UnlockScheduledNotificationsTable();
            if (! empty($scheduledNotificationsIds) && is_array($scheduledNotificationsIds)) {
                $this->setAsSending($scheduledNotificationsIds, false);
            }

            return new ApiProblemResponse(new ApiProblem($e->getCode(), $e->getMessage()));
        }
    }

    private function lockScheduledNotificationsTable(): bool
    {
        $tableName   = $this->entityManager->getClassMetadata(ScheduledNotificationEntity::class)->getTableName();
        $lockedTable = $this->entityManager->getConnection()->exec('LOCK TABLES ' . $tableName . ' WRITE;');

        return $lockedTable === 0;
    }

    private function UnlockScheduledNotificationsTable(): bool
    {
        $UnlockedTables = $this->entityManager->getConnection()->exec('UNLOCK TABLES ');
        return $UnlockedTables === 0;
    }

    private function getNotificationsWithoutSending()
    {
        return $this->repository->getNotificationsWithoutSending();
    }

    private function getDevicesWithoutSending(int $notificationId, int $platformId, int $limit)
    {
        return $this->repository->getDevicesWithoutSending($notificationId, $platformId, $limit);
    }

    private function getScheduledNotificationsIds(array $devices): array
    {
        $scheduledNotificationsIds = [];

        foreach ($devices as $device) {
            $scheduledNotificationsIds[] = $device['scheduledNotificationId'];
        }

        return $scheduledNotificationsIds;
    }

    private function setAsSent(array $scheduledNotificationsIds)
    {
        return $this->repository->setAsSent($scheduledNotificationsIds);
    }

    private function setAsSending(array $scheduledNotificationsIds, bool $sending)
    {
        return $this->repository->setAsSending($scheduledNotificationsIds, $sending);
    }
}
