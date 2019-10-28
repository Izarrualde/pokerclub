<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\SessionService;

class SessionServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionService
    {
        $entityManager = $container->get(EntityManager::class);
        $config        = $container->get('config');
        return new SessionService($entityManager, $config);
    }
}
