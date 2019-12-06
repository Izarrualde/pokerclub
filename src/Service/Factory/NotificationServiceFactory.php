<?php
namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Solcre\Pokerclub\Service\DeviceService;
use Solcre\Pokerclub\Service\NotificationService;
use Solcre\Pokerclub\Service\UserService;
use Zend\ServiceManager\Factory\FactoryInterface;

class NotificationServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NotificationService
    {
        $doctrineService = $container->get(EntityManager::class);
        $userService     = $container->get(UserService::class);
        $deviceService   = $container->get(DeviceService::class);
        $config          = $container->get('config');

        return new NotificationService($doctrineService, $userService, $deviceService, $config);
    }
}
