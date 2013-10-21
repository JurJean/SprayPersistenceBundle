<?php

namespace Spray\PersistenceBundle\EntityFilter;

use ArrayIterator;
use Doctrine\ORM\QueryBuilder;
use UnexpectedValueException;

/**
 * FilterChain
 */
class FilterChain implements FilterChainInterface
{
    /**
     * @var array
     */
    private $filters = array();
    
    /**
     * @var array
     */
    private $options = array();
    
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
        return new ArrayIterator($this->filters);
    }
    
    /**
     * @inheritdoc
     */
    public function addFilter($filter, $options = array())
    {
        $this->filters[$filter->getName()] = $filter;
        $this->options[$filter->getName()] = $options;
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
                '$filter must be an instance of EntityFilterInterface or the filter name'
            );
        }
        return isset($this->filters[$filter]);
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
                '$filter must be an instance of EntityFilterInterface or the filter name'
            );
        }
        if ( ! $this->hasFilter($filter)) {
            throw new UnexpectedValueException(
                'Cannot remove filter that was never added'
            );
        }
        unset($this->filters[$filter]);
        unset($this->options[$filter]);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        foreach ($this as $filterName => $filter) {
            $filter->filter($queryBuilder, $this->options[$filterName]);
        }
    }
}