<?php
namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\UserGroupService;

class UserGroupServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UserGroupService
    {
        $doctrineService = $container->get(EntityManager::class);

        return new UserGroupService($doctrineService);
    }
}
