<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Service\UserSessionService;

class UserSessionServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UserSessionService
    {
        $entityManager = $container->get(EntityManager::class);
        $userService = $container->get(UserService::class);
        $config        = $container->get('config');

        return new UserSessionService($entityManager, $userService, $config);
    }
}
