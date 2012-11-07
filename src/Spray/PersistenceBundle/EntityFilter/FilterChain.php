<?php

namespace Spray\PersistenceBundle\EntityFilter;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use IteratorAggregate;
use UnexpectedValueException;

/**
 * FilterChain
 */
class FilterChain implements FilterAggregateInterface, IteratorAggregate
{
    /**
     * @var array
     */
    private $index = array();
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'filter_chain';
    }
    
    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->index);
    }
    
    /**
     * @inheritdoc
     */
    public function addFilter(EntityFilterInterface $filter)
    {
        $this->index[$filter->getName()] = $filter;
    }
    
    /**
     * @inheritdoc
     */
    public function hasFilter($filter)
    {
        if ($filter instanceof EntityFilterInterface) {
            $filter = $filter->getName();
        }
        if ( ! is_string($filter)) {
            throw new InvalidArgumentException(
                '$filter must be either an instance of EntityFilterInterface or the name of the filter as a string'
            );
        }
        return isset($this->index[$filter]);
    }
    
    /**
     * @inheritdoc
     */
    public function removeFilter($filter)
    {
        if ($filter instanceof EntityFilterInterface) {
            $filter = $filter->getName();
        }
        if ( ! is_string($filter)) {
            throw new InvalidArgumentException(
                '$filter must be either an instance of EntityFilterInterface or the name of the filter as a string'
            );
        }
        if ( ! $this->hasFilter($filter)) {
            throw new UnexpectedValueException(
                'Cannot remove filter that was never added'
            );
        }
        unset($this->index[$filter]);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(QueryBuilder $qb)
    {
        foreach ($this as $filter) {
            $filter->filter($qb);
        }
    }
}