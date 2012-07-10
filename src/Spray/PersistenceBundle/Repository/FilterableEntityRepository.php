<?php

namespace Spray\PersistenceBundle\Repository;

use Countable;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Iterator;
use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use Spray\PersistenceBundle\EntityFilter\FilterChain;

/**
 * A filterable entity repository
 * 
 * Instead of just passing data around, this Repository has a state 
 *
 * @author MHWK
 */
class FilterableEntityRepository extends DoctrineEntityRepository
    implements FilterableRepositoryInterface, Countable, Iterator
{
    /**
     * @var null|array
     */
    private $collection;
    
    /**
     * @var integer
     */
    private $index = 0;
    
    /**
     * @var FilterChain
     */
    private $filterChain;
    
    /**
     * Load data if not already and return it. Important to call by reference
     * to avoid array copies
     * 
     * @return array
     */
    private function &getCollection()
    {
        if (null === $this->collection) {
            $qb = $this->createAndFilterQueryBuilder($this->getEntityAlias());
            $this->collection = $qb->getQuery()->getResult();
        }
        return $this->collection;
    }
    
    /**
     * Set the filter chain
     * 
     * @param FilterChain $filterChain
     */
    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
    }
    
    /**
     * Get the filter chain
     * 
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }
    
    /**
     * @inheritdoc
     */
    public function count()
    {
        if (null === $this->collection) {
            // This obviously needs to be fixed
            return count($this->getCollection());
        }
        return count($this->collection);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        if (null === $this->collection) {
            return $this->find($this->createAndFilterQueryBuilder($this->getEntityName()));
        }
        return $this->collection[$this->index];
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Loop detected: load data
     * 
     * @inheritdoc
     */
    public function rewind()
    {
        $this->getCollection();
        $this->index = 0;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $collection = &$this->getCollection();
        return isset($collection[$this->index]);
    }
    
    /**
     * Test if the repository contains filters that conflict with $filter
     * 
     * @param \Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface $filter
     * @return boolean
     */
    public function containsConflictingFilters(ConflictingFilterInterface $filter)
    {
        foreach ((array) $filter->getConflictingFilters() as $filterName) {
            if ($this->getFilterChain()->offsetExists($filterName)) {
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
            if ( ! $this->getFilterChain()->offsetExists($filterName)) {
                continue;
            }
            $this->getFilterChain()->offsetUnset($filterName);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function filter(EntityFilterInterface $filter)
    {
        $this->collection = null;
        if ($filter instanceof ConflictingFilterInterface) {
            $this->removeConflictingFilters($filter);
        }
        $this->getFilterChain()->offsetSet($filter->getName(), $filter);
    }
    
    public function preFilterQueryBuilder(QueryBuilder $qb)
    {
        
    }
    
    public function filterQueryBuilder(QueryBuilder $qb)
    {
        $this->preFilterQueryBuilder($qb);
        $this->getFilterChain()->filter($qb);
        $this->postFilterQueryBuilder($qb);
        return $qb;
    }
    
    public function postFilterQueryBuilder(QueryBuilder $qb)
    {
        
    }
    
    /**
     * Create a new QueryBuilder and filter it using attached filters
     * 
     * @param string $alias
     * @return QueryBuilder
     */
    protected function createAndFilterQueryBuilder($alias)
    {
        return $this->filterQueryBuilder($this->createQueryBuilder($alias));
    }
    
    /**
     * Returns the lowercased first letter of the entity name without the
     * namespace
     * 
     * @return string
     */
    protected function getEntityAlias()
    {
        return strtolower(substr(
            $this->getEntityName(),
            strrpos($this->getEntityName(), '\\') + 1,
            1
        ));
    }
}