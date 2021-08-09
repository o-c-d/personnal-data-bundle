<?php

namespace Ocd\PersonnalDataBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OcdPersonnalDataExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ocd_personnal_data.debug_backtrace', $config['debug_backtrace']);
        $container->setParameter('ocd_personnal_data.subscribe_to_doctrine', $config['subscribe_to_doctrine']);
        $container->setParameter('ocd_personnal_data.doctrine_declare_transports', $config['doctrine_declare_transports']);
        $container->setParameter('ocd_personnal_data.annotation_cache_duration', $config['annotation_cache_duration']);
        $container->setParameter('ocd_personnal_data.dpo_personnality', $config['dpo_personnality']);
    }
}
