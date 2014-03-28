<?php

namespace Spray\PersistenceBundle\DependencyInjection\CompilerPass;

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
        $this->buildFilterRegistries($container);
    }
    
    public function buildFilterRegistries(ContainerBuilder $container)
    {
        $filters      = $container->findTaggedServiceIds('spray_persistence.entity_filter');
        $repositories = array();
        foreach ($filters as $filterId => $filterOptions) {
            foreach ($filterOptions as $options) {
                if ( ! isset($options['repository'])) {
                    continue;
                }
                $this->attachFilterToRepository(
                    $container,
                    $options['repository'],
                    $filterId,
                    $options
                );
                $repositories[] = $options['repository'];
            }
        }
        foreach ($filters as $filterId => $filterOptions) {
            foreach ($filterOptions as $options) {
                if (isset($options['repository'])) {
                    continue;
                }
                foreach ($repositories as $repositoryId) {
                    $this->attachFilterToRepository(
                        $container,
                        $repositoryId,
                        $filterId,
                        $options
                    );
                }
            }
        }
        
    }
    
    public function attachFilterToRepository(ContainerBuilder $container, $repositoryId, $filterId, array $options)
    {
        $definition = $this->locateFilterRegistryForRepository($container, $repositoryId);
        
        if (isset($options['alias'])) {
            $definition->addMethodCall('add', array(
                new Reference($filterId),
                $options['alias']
            ));
        } else {
            $definition->addMethodCall('add', array(
                new Reference($filterId)
            ));
        }
    }
    
    public function locateFilterRegistryForRepository(ContainerBuilder $container, $repositoryId)
    {
        $registryId = $repositoryId . '.filter_registry';
        if ($container->hasDefinition($registryId)) {
            return $container->getDefinition($registryId);
        }
        $definition = new Definition();
        $definition->setClass('Spray\PersistenceBundle\FilterLocator\FilterRegistry');
        $container->setDefinition($registryId, $definition);
        $repository = $container->getDefinition($repositoryId);
        $repository->addMethodCall('setFilterLocator', array(
            new Reference($registryId)
        ));
        return $definition;
    }
}
