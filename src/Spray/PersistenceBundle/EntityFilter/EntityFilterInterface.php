<?php

namespace Spray\PersistenceBundle\EntityFilter;

use Doctrine\ORM\QueryBuilder;

/**
 * FilterInterface
 *
 * @author MHWK
 */
interface EntityFilterInterface
{
    /**
     * Filter the QueryBuilder: Perform your actions here
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function filter(QueryBuilder $queryBuilder, $options = array());
    
    /**
     * Get the name of the filter
     * 
     * @return string
     */
    public function getName();
}