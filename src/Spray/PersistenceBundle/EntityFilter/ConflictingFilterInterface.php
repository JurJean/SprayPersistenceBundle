<?php

namespace Spray\PersistenceBundle\EntityFilter;

/**
 * ConflictingFilterInterface should be implemented if a filter can conflict
 * with another
 *
 * @author MHWK
 */
interface ConflictingFilterInterface
{
    /**
     * Return which other filters this filter conflicts with. Return either the
     * filter name or an array of filter names
     * 
     * @return array
     */
    public function getConflictingFilters();
}