<?php

namespace Silversat\PermissionBundle\DependencyInjection;

use Silversat\PermissionBundle\Security\AccessControlSubscriber;
use Silversat\PermissionBundle\Security\PermissionChecker;
use Silversat\PermissionBundle\Security\RoleHierarchy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class PermissionExtension extends Extension
{
    public function getAlias(): string {
        return 'silversat_permission';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('silversat_permission.site', $config['site']);
        $container->setParameter('silversat_permission.hierarchy', $config['hierarchy']);
        $container->setParameter('silversat_permission.access_control', $config['access_control']);

        $container->register(RoleHierarchy::class, RoleHierarchy::class)
            ->addArgument("%silversat_permission.hierarchy%");

        $container->register(PermissionChecker::class, PermissionChecker::class)
            ->addArgument(new Reference(RoleHierarchy::class))
            ->addArgument("%silversat_permission.site%");

        $container->register(AccessControlSubscriber::class, AccessControlSubscriber::class)
            ->addArgument(new Reference(PermissionChecker::class))
            ->addArgument("%silversat_permission.access_control%")
            ->addArgument("%silversat_permission.site%")
            ->addTag('kernel.event_subscriber');
    }
}