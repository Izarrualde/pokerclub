<?php

namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
//use Solcre\Lms\Service\PermissionService;
use Solcre\Pokerclub\Service\AwardService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Service\UserSessionService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Solcre\Pokerclub\Service\MeService;

class MeServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MeService
    {
        $entityManager      = $container->get(EntityManager::class);
        $userService        = $container->get(UserService::class);
        $userSessionService = $container->get(UserSessionService::class);
        $awardService       = $container->get(AwardService::class);
        //$permissionService = $container->get(PermissionService::class);

        return new MeService($entityManager, $userService, $userSessionService, $awardService);
    }
}
