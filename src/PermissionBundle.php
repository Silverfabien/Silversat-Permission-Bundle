<?php

namespace Silversat\PermissionBundle;

use Silversat\PermissionBundle\DependencyInjection\PermissionExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PermissionBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PermissionExtension();
    }
}