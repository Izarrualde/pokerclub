<?php
namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Service\DeviceService;
use Zend\ServiceManager\Factory\FactoryInterface;

class DeviceServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DeviceService
    {
        $doctrineService = $container->get(EntityManager::class);
        $userService     = $container->get(UserService::class);

        return new DeviceService($doctrineService, $userService);
    }
}
