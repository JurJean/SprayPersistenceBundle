<?php

namespace Spray\PersistenceBundle\EntityFilter;

/**
 * FilterManagerInterface
 *
 * @author MHWK
 */
interface FilterAggregateInterface extends EntityFilterInterface
{
    /**
     * Add a filter
     * 
     * @param \Spray\PersistenceBundle\EntityFilter\EntityFilterInterface $filter
     */
    public function addFilter(EntityFilterInterface $filter);
    
    /**
     * Test if a filter is set by either the filter name or a filter instance
     * 
     * @param mixed $filter
     * @return boolean
     */
    public function hasFilter($filter);
    
    /**
     * Remove a filter by either the filter name or a filter instance
     * 
     * @param mixed $filter
     */
    public function removeFilter($filter);
}