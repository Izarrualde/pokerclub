<?php
namespace Solcre\Pokerclub\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Solcre\Lms\Service\AwardService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AwardServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AwardService
    {
        $entityManager = $container->get(EntityManager::class);
        return new AwardService($entityManager);
    }
}
