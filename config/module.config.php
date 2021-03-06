<?php

use Solcre\Pokerclub\Service\AwardService;
use Solcre\Pokerclub\Service\DeviceService;
use Solcre\Pokerclub\Service\Factory\AwardServiceFactory;
use Solcre\Pokerclub\Service\Factory\DeviceServiceFactory;
use Solcre\Pokerclub\Service\Factory\NotificationServiceFactory;
use Solcre\Pokerclub\Service\Factory\PermissionServiceFactory;
use Solcre\Pokerclub\Service\Factory\ScheduledNotificationServiceFactory;
use Solcre\Pokerclub\Service\Factory\UserGroupPermissionServiceFactory;
use Solcre\Pokerclub\Service\Factory\UserPermissionServiceFactory;
use Solcre\Pokerclub\Service\NotificationService;
use Solcre\Pokerclub\Service\PermissionService;
use Solcre\Pokerclub\Service\ScheduledNotificationService;
use Solcre\Pokerclub\Service\UserGroupPermissionService;
use Solcre\Pokerclub\Service\UserPermissionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Service\Factory\UserServiceFactory;
use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\Factory\BuyinSessionServiceFactory;
use Solcre\Pokerclub\Service\CommissionSessionService;
use Solcre\Pokerclub\Service\Factory\CommissionSessionServiceFactory;
use Solcre\Pokerclub\Service\DealerTipSessionService;
use Solcre\Pokerclub\Service\Factory\DealerTipSessionServiceFactory;
use Solcre\Pokerclub\Service\ServiceTipSessionService;
use Solcre\Pokerclub\Service\Factory\ServiceTipSessionServiceFactory;
use Solcre\Pokerclub\Service\ExpensesSessionService;
use Solcre\Pokerclub\Service\Factory\ExpensesSessionServiceFactory;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\Factory\SessionServiceFactory;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\Factory\UserSessionServiceFactory;
use Solcre\Pokerclub\Service\UserGroupService;
use Solcre\Pokerclub\Service\Factory\UserGroupServiceFactory;
use Solcre\Pokerclub\Service\MeService;
use Solcre\Pokerclub\Service\Factory\MeServiceFactory;

return [
    'service_manager' => [
        'factories' => [
            UserService::class                  => UserServiceFactory::class,
            BuyinSessionService::class          => BuyinSessionServiceFactory::class,
            CommissionSessionService::class     => CommissionSessionServiceFactory::class,
            DealerTipSessionService::class      => DealerTipSessionServiceFactory::class,
            ServiceTipSessionService::class     => ServiceTipSessionServiceFactory::class,
            ExpensesSessionService::class       => ExpensesSessionServiceFactory::class,
            SessionService::class               => SessionServiceFactory::class,
            UserSessionService::class           => UserSessionServiceFactory::class,
            AwardService::class                 => AwardServiceFactory::class,
            UserGroupService::class             => UserGroupServiceFactory::class,
            MeService::class                    => MeServiceFactory::class,
            DeviceService::class                => DeviceServiceFactory::class,
            NotificationService::class          => NotificationServiceFactory::class,
            PermissionService::class            => PermissionServiceFactory::class,
            ScheduledNotificationService::class => ScheduledNotificationServiceFactory::class,
            UserGroupPermissionService::class   => UserGroupPermissionServiceFactory::class,
            UserPermissionService::class        => UserPermissionServiceFactory::class,
        ]
    ],
    'doctrine'        => [
        'driver' => [
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
            'my_annotation_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    'vendor/solcre/pocker-club/src/Entity'
                ],
            ],
            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default'          => [
                'drivers' => [
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    'Solcre\\Pokerclub\\Entity' => 'my_annotation_driver',
                ],
            ],
        ],
    ]
];
