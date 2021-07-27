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
                ->booleanNode('subscribe_to_doctrine')->defaultValue(false)->end()
                ->booleanNode('doctrine_declare_transports')->defaultValue(false)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
