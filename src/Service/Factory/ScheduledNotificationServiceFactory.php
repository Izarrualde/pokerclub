<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\ScheduledNotificationService;
use Solcre\Pokerclub\Service\DeviceService;
use Solcre\Pokerclub\Service\NotificationService;

class ScheduledNotificationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ScheduledNotificationService
    {
        $entityManager       = $container->get(EntityManager::class);
        $deviceService       = $container->get(DeviceService::class);
        $notificationService = $container->get(NotificationService::class);

        return new ScheduledNotificationService($entityManager, $deviceService, $notificationService);
    }
}
