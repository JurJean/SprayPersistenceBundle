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
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->buildFilterRegistries($container);
    }
    
    /**
     * Build the filter registries
     * 
     * @param ContainerBuilder $container
     * @return void
     */
    public function buildFilterRegistries(ContainerBuilder $container)
    {
        $this->buildLocalFilterRegistries($container);
        $this->buildGlobalFilterRegistries($container);
    }
    
    /**
     * Build all local filter registries
     * 
     * @param ContainerBuilder $container
     * @return void
     */
    public function buildLocalFilterRegistries(ContainerBuilder $container)
    {
        $filters = $container->findTaggedServiceIds('spray_persistence.entity_filter');
        foreach ($filters as $filterId => $filterOptions) {
            foreach (array_filter($filterOptions, array($this, 'filterLocalFilterOptions')) as $options) {
                $registryId = $this->determineRegistryIdForRepositoryId($options['repository']);
                $this->attachFilterToRegistry(
                    $this->locateFilterRegistry($container, $registryId),
                    $filterId,
                    $options
                );
                $this->attachRegistryToRepository($container, $registryId, $options['repository']);
            }
        }
    }
    
    /**
     * Attach all global filters to all registries discovered by
     * buildLocalFilterRegistries()
     * 
     * @param ContainerBuilder $container
     * @return void
     */
    public function buildGlobalFilterRegistries(ContainerBuilder $container)
    {
        $filters          = $container->findTaggedServiceIds('spray_persistence.entity_filter');
        $registries = $container->findTaggedServiceIds('spray_persistence.filter_registry');
        foreach ($filters as $filterId => $filterOptions) {
            foreach (array_filter($filterOptions, array($this, 'filterGlobalFilterOptions')) as $options) {
                foreach ($registries as $registryId => $registryOptions) {
                    $this->attachFilterToRegistry(
                        $this->locateFilterRegistry($container, $registryId),
                        $filterId,
                        $options
                    );
                }
            }
        }
    }
    
    /**
     * Helps filtering local filters options
     * 
     * @param array $filterOptions
     * @return type
     */
    public function filterLocalFilterOptions($filterOptions)
    {
        return isset($filterOptions['repository']);
    }
    
    /**
     * Helps filtering global filters options
     * 
     * @param array $filterOptions
     * @return type
     */
    public function filterGlobalFilterOptions($filtersOptions)
    {
        return ! isset($filtersOptions['repository']);
    }
    
    /**
     * Attaches filter to $registryDefinition
     * 
     * @param Definition $registryDefinition
     * @param string $filterId
     * @param array $options
     */
    public function attachFilterToRegistry(Definition $registryDefinition, $filterId, array $options)
    {
        if ( ! isset($options['alias'])) {
            $options['alias'] = null;
        }
        $registryDefinition->addMethodCall('add', array(
            new Reference($filterId),
            $options['alias']
        ));
    }
    
    /**
     * Determine registry id for repository id
     * @param string $repositoryId
     * @return string
     */
    public function determineRegistryIdForRepositoryId($repositoryId)
    {
        return $repositoryId . '.filter_registry';
    }
    
    /**
     * Locate filter registry for $repositoryId - if it doesn't exist it gets
     * created.
     * 
     * @param ContainerBuilder $container
     * @param string $repositoryId
     * @return Definition
     */
    public function locateFilterRegistry(ContainerBuilder $container, $registryId)
    {
        if ( ! $container->hasDefinition($registryId)) {
            $definition = new Definition();
            $definition->setClass('Spray\PersistenceBundle\FilterLocator\FilterRegistry');
            $container->setDefinition($registryId, $definition);
        }
        return $container->getDefinition($registryId);
    }
    
    /**
     * Attached filter registry to a repository by its id
     * 
     * @param ContainerBuilder $regostry
     * @param string $registryId
     * @param string $repositoryId
     * @return void
     */
    public function attachRegistryToRepository(ContainerBuilder $container, $registryId, $repositoryId)
    {
        $registry   = $container->getDefinition($registryId);
        $repository = $container->getDefinition($repositoryId);
        if ( ! $registry->hasTag('spray_persistence.filter_registry')) {
            $registry->addTag('spray_persistence.filter_registry');
        }
        if ( ! $repository->hasMethodCall('setFilterLocator')) {
            $repository->addMethodCall('setFilterLocator', array(
                new Reference($registryId)
            ));
        }
    }
}
