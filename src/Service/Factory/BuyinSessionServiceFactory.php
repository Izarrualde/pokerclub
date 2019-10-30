<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\UserSessionService;

class BuyinSessionServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BuyinSessionService
    {
        $entityManager = $container->get(EntityManager::class);
        $userSessionService = $container->get(UserSessionService::class);
        $config        = $container->get('config');
        return new BuyinSessionService($entityManager, $userSessionService, $config);
    }
}
