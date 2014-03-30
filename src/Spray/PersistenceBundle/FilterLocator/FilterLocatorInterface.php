<?php

namespace Spray\PersistenceBundle\FilterLocator;

/**
 * FilterLocatorInterface
 */
interface FilterLocatorInterface
{
    public function has($alias);
    
    public function get($alias);
    
    public function locateFilter($filter);
}
