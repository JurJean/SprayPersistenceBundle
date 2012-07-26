<?php

namespace Spray\PersistenceBundle\EntityFilter;

use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use IteratorAggregate;
use SplPriorityQueue;
use UnexpectedValueException;

/**
 * FilterManager
 *
 * @author MHWK
 */
class FilterManager implements FilterAggregateInterface, IteratorAggregate
{
    private $index = array();
    private $queue;
    private $queueOrder = 0;
    
    public function __construct()
    {
        $this->configure();
    }
    
    /**
     * Override to implement configuration
     * 
     * @return void
     */
    protected function configure()
    {
        
    }
    
    /**
     * Build a SplPriorityQueue if not already and returns a clone
     * 
     * @return SplPriorityQueue
     */
    public function getIterator()
    {
        if (null === $this->queue) {
            $this->queue = new SplPriorityQueue();
            $this->queueOrder = 0;
            foreach ($this->index as $data) {
                $this->queue->insert(
                    $data['filter'],
                    array($data['priority'], $this->queueOrder--)
                );
            }
        }
        return clone $this->queue;
    }
    
    /**
     * @inheritdoc
     */
    public function addFilter(EntityFilterInterface $filter)
    {
        if ($filter instanceof ConflictingFilterInterface) {
            if ($this->hasConflictingFilters($filter)) {
                $this->removeConflictingFilters($filter);
            }
        }
        if ($filter instanceof PrioritizedFilterInterface) {
            $priority = $filter->getPriority();
        } else {
            $priority = 0;
        }
        $this->queue = null;
        $this->index[$filter->getName()] = array(
            'priority' => $priority,
            'filter'   => $filter,
        );
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
        $this->queue = null;
    }
    
    /**
     * Test if the repository contains filters that conflict with $filter
     * 
     * @param ConflictingFilterInterface $filter
     * @return boolean
     */
    public function hasConflictingFilters(ConflictingFilterInterface $filter)
    {
        foreach ((array) $filter->getConflictingFilters() as $filterName) {
            if ($this->hasFilter($filterName)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Removes the filters that conflict with $filter
     * 
     * @param ConflictingFilterInterface $filter
     */
    public function removeConflictingFilters(ConflictingFilterInterface $filter)
    {
        foreach ((array) $filter->getConflictingFilters() as $filterName) {
            if ( ! $this->hasFilter($filterName)) {
                continue;
            }
            $this->removeFilter($filterName);
        }
    }

    public function filter(QueryBuilder $qb)
    {
        foreach ($this as $filter) {
            $filter->filter($qb);
        }
    }
    
    public function getName()
    {
        return 'manager';
    }
}