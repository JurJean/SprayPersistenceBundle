<?php

namespace Spray\PersistenceBundle\EntityFilter;

use ArrayObject;
use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * FilterChain
 *
 * @author MHWK
 */
class FilterChain extends ArrayObject implements EntityFilterInterface
{
    public function filter(QueryBuilder $qb)
    {
        foreach ($this as $filter) {
            $filter->filter($qb);
        }
    }
    
    public function getName()
    {
        return 'chain';
    }
}