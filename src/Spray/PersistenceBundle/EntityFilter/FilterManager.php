<?php

namespace Spray\PersistenceBundle\EntityFilter;

use ArrayObject;
use Doctrine\ORM\QueryBuilder;
use IteratorAggregate;
use SplPriorityQueue;

/**
 * FilterManager
 *
 * @author MHWK
 */
class FilterManager implements FilterManagerInterface, IteratorAggregate
{
    private $nameIndex = array();
    private $priorityIndex;
    private $priorityIndexOrder = 0;
    
    public function __construct()
    {
        $this->priorityIndex = new SplPriorityQueue();
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
    
    public function getIterator()
    {
        return clone $this->priorityIndex;
    }
    
    public function addFilter(EntityFilterInterface $filter)
    {
        if ($filter instanceof PrioritizedFilterInterface) {
            $priority = $filter->getPriority();
        } else {
            $priority = 0;
        }
        $this->nameIndex[$filter->getName()] = array(
            'priority' => $priority,
            'filter'   => $filter,
        );
        $this->priorityIndex->insert($filter, array(
            $priority, $this->priorityIndexOrder++
        ));
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
            throw new \InvalidArgumentException(
                '$filter must be either an instance of EntityFilterInterface or the name of the filter as a string'
            );
        }
        return isset($this->nameIndex[$filter]);
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
            throw new \InvalidArgumentException(
                '$filter must be either an instance of EntityFilterInterface or the name of the filter as a string'
            );
        }
        if ( ! $this->hasFilter($filter)) {
            throw new UnexpectedValueException(
                'Cannot remove filter that was never added'
            );
        }
        unset($this->nameIndex[$filter]);
        $this->priorityIndex = new SplPriorityQueue();
        foreach ($this->nameIndex as $key => $data) {
            $this->priorityIndex->insert($data['filter'], array(
                $data['priority'], $this->priorityIndexOrder
            ));
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
        return 'chain';
    }
}