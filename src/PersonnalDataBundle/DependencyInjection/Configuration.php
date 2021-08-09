<?php

namespace Ocd\PersonnalDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ocd_personnal_data');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('subscribe_to_doctrine')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('doctrine_declare_transports')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('debug_backtrace')
                    ->defaultValue(false)
                ->end()
                ->scalarNode('annotation_cache_duration')
                    ->defaultValue('1 day')
                ->end()
                ->enumNode('dpo_personnality')
                    ->values(['lazy', 'vigilent', 'agressive'])
                    ->defaultValue('lazy')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
