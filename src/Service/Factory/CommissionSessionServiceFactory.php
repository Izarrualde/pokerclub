<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\CommissionSessionService;

class CommissionSessionServiceFactory implements FactoryInterface
{
  
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): CommissionSessionService {
        $entityManager = $container->get(EntityManager::class);
        $config        = $container->get('config');
        return new CommissionSessionService($entityManager, $config);
    }
}
