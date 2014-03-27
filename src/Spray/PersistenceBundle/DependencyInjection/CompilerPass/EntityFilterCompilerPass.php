<?php

namespace Spray\PersistenceBundle\DependencyInjection\CompilerPass;

use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * EntityFilterCompilerPass
 */
class EntityFilterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $repositoryFilterRegistries = $this->buildFilterRegistries($container);
        foreach ($repositoryFilterRegistries as $repositoryName => $filterRegistry) {
            $repository = $container->getDefinition($repositoryName);
            $repository->addMethodCall('setFilterLocator', array(
                new Reference($filterRegistry)
            ));
        }
    }
    
    public function buildFilterRegistries(ContainerBuilder $container)
    {
        $registries = array();
        $filters    = $container->findTaggedServiceIds('spray_persistence.entity_filter');
        foreach ($filters as $id => $filterOptions) {
            foreach ($filterOptions as $options) {
                if ( ! isset($options['repository'])) {
                    throw new RuntimeException('Please provide repository');
                }
                
                $registryName = $options['repository'] . '.filter_registry';
                if ( ! $container->hasDefinition($registryName)) {
                    $definition = new Definition();
                    $definition->setClass('Spray\PersistenceBundle\FilterLocator\FilterRegistry');
                    $container->setDefinition($registryName, $definition);
                } else {
                    $definition = $container->getDefinition($registryName);
                }
                $registries[$options['repository']] = $registryName;
                
                if (isset($options['alias'])) {
                    $definition->addMethodCall('add', array(
                        new Reference($id),
                        $options['alias']
                    ));
                } else {
                    $definition->addMethodCall('add', array(
                        new Reference($id)
                    ));
                }
            }
        }
        return $registries;
    }
}
