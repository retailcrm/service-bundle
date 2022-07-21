<?php

namespace RetailCrm\ServiceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('retail_crm_service');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('request_schema')
                    ->children()
                        ->arrayNode('callback')
                            ->children()
                                ->arrayNode('supports')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('type')->isRequired()->end()
                                            ->arrayNode('params')
                                                ->isRequired()->scalarPrototype()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->scalarNode('serializer')
                                    ->defaultValue('retail_crm_service.symfony_serializer.adapter')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('client')
                            ->children()
                                ->arrayNode('supports')
                                    ->scalarPrototype()->end()
                                ->end()
                                ->scalarNode('serializer')
                                    ->defaultValue('retail_crm_service.symfony_serializer.adapter')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('messenger')
                    ->children()
                        ->scalarNode('message_handler')->isRequired()->defaultValue('simple_console_runner')->end()
                        ->scalarNode('process_timeout')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
