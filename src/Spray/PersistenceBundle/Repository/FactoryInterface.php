<?php

namespace Spray\PersistenceBundle\Repository;

/**
 * FactoryInterface
 */
interface FactoryInterface
{
    /**
     * Build a new RepositoryFilter based on $entityName
     * 
     * @param string $entityName
     * @return RepositoryFilterInterface
     */
    public function build($entityName);
}
