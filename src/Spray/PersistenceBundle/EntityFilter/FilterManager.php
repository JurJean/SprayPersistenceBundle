<?php

namespace Spray\PersistenceBundle\EntityFilter;

use Doctrine\ORM\QueryBuilder;
use SplPriorityQueue;

/**
 * FilterManager
 *
 * @author MHWK
 */
class FilterManager extends FilterChain
{
    private $queue;
    private $queueOrder = 0;
    private $priorities = array();
    
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
            foreach (parent::getIterator() as $filter) {
                $this->queue->insert(
                    $filter,
                    array($this->priorities[$filter->getName()], $this->queueOrder--)
                );
            }
        }
        return clone $this->queue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addFilter($filter, $options = array())
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
        $this->priorities[$filter->getName()] = $priority;
        return parent::addFilter($filter, $options);
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeFilter($filter)
    {
        if ($filter instanceof EntityFilterInterface) {
            $filter = $filter->getName();
        }
        parent::removeFilter($filter);
        unset($this->priorities[$filter]);
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'manager';
    }
}