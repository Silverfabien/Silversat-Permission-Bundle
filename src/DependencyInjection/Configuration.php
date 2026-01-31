<?php

namespace Silversat\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('silversat_permission');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('site')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('hierarchy')
                    ->useAttributeAsKey('role')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function ($v) {
                            foreach ($v as $key => $value) {
                                if (!is_array($value)) {
                                    $v[$key] = [$value];
                                }
                            }
                            return $v;
                        })
                    ->end()
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
                ->arrayNode('access_control')
                    ->defaultValue([])
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('role')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}