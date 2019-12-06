<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Solcre\Pokerclub\Service\UserGroupPermissionService;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserGroupPermissionServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UserGroupPermissionService
    {
        $doctrineService = $container->get(EntityManager::class);
        return new UserGroupPermissionService($doctrineService);
    }
}
