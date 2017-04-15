<?php

namespace Spray\PersistenceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spray\PersistenceBundle\EntityFilter\FilterAggregateInterface;
use Spray\PersistenceBundle\EntityFilter\FilterManager;
use Spray\PersistenceBundle\FilterLocator\FilterLocatorInterface;
use Spray\PersistenceBundle\FilterLocator\FilterRegistry;

/**
 * RepositoryFilter
 */
class RepositoryFilter implements RepositoryFilterInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;
    
    /**
     * @var boolean
     */
    private $hydrate = true;
    
    /**
     * @var null|array
     */
    private $collection;
    
    /**
     * @var integer
     */
    private $index = 0;
    
    /**
     * @var FilterManager
     */
    private $filterManager;
    
    /**
     * @var FilterLocatorInterface
     */
    private $filterLocator;
    
    /**
     * Construct a RepositoryFilter
     * 
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
        $this->configure();
    }
    
    /**
     * 
     * @return type
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * Set the filter locator
     * 
     * @param FilterLocatorInterface $filterLocator
     * @return void
     */
    public function setFilterLocator(FilterLocatorInterface $filterLocator)
    {
        $this->filterLocator = $filterLocator;
    }
    
    /**
     * Get the filter locator
     * 
     * @return FilterLocatorInterface
     */
    public function getFilterLocator()
    {
        if (null === $this->filterLocator) {
            $this->setFilterLocator(new FilterRegistry());
        }
        return $this->filterLocator;
    }
    
    /**
     * On clone:
     * - clear collection
     * - clone filterManager
     * - clone filterLocator
     * 
     * @return void
     */
    public function __clone()
    {
        $this->collection = null;
        if (null !== $this->filterManager) {
            $this->filterManager = clone $this->filterManager;
        }
        if (null !== $this->filterLocator) {
            $this->filterLocator = clone $this->filterLocator;
        }
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
     * Enable hydration (default)
     * 
     * @return void
     */
    public function enableHydration()
    {
        $this->hydrate = true;
    }
    
    /**
     * Disable hydration
     * 
     * @return void
     */
    public function disableHydration()
    {
        $this->hydrate = false;
    }
    
    /**
     * Test wether hydration is enabled
     * 
     * @return boolean
     */
    public function isHydrationDisabled()
    {
        return false === $this->hydrate;
    }


    /**
     * Load data if not already and return it. Important to call by reference
     * to avoid array copies
     * 
     * @return array
     */
    public function &getCollection()
    {
        if (null === $this->collection) {
            $qb = $this->createAndFilterQueryBuilder($this->getEntityAlias());
            if ($this->isHydrationDisabled()) {
                $this->collection = $qb->getQuery()->getScalarResult();
            } else {
                $this->collection = $qb->getQuery()->getResult();
            }
        }
        return $this->collection;
    }
    
    /**
     * Set the filter chain
     * 
     * @param FilterManager $filterManager
     */
    public function setFilterManager(FilterAggregateInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }
    
    /**
     * Get the filter chain
     * 
     * @return FilterManager
     */
    public function getFilterManager()
    {
        if (null === $this->filterManager) {
            $this->setFilterManager(new FilterManager());
        }
        return $this->filterManager;
    }
    
    /**
     * @inheritdoc
     */
    public function count()
    {
        if (null === $this->collection) {
            return $this->paginate()->count();
        }
        return count($this->collection);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        if (null === $this->collection) {
            $qb = $this->createAndFilterQueryBuilder($this->getEntityAlias());
            $qb->setMaxResults(1);
            return $qb->getQuery()->getSingleResult();
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
     * @inheritdoc
     */
    public function filter($filter, $options = array())
    {
        $this->collection = null;
        $this->getFilterManager()->addFilter(
            $this->getFilterLocator()->locateFilter($filter),
            $options
        );
    }
    
    /**
     * Filter passed $qb
     * 
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function filterQueryBuilder(QueryBuilder $qb)
    {
        $this->getFilterManager()->filter($qb);
        return $qb;
    }
    
    /**
     * Create a new QueryBuilder and filter it using attached filters
     * 
     * @param string $alias
     * @return QueryBuilder
     */
    public function createAndFilterQueryBuilder($alias)
    {
        return $this->filterQueryBuilder($this->getRepository()->createQueryBuilder($alias));
    }
    
    /**
     * Paginate results in current filter scope
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return Paginator
     */
    public function paginate($page = 1, $itemsPerPage = 20)
    {
        $query = $this->createAndFilterQueryBuilder($this->getEntityAlias());
        $query->setFirstResult(($page - 1) * $itemsPerPage);
        $query->setMaxResults($itemsPerPage);
        return new Paginator($query);
    }
    
    /**
     * Returns all uppercase letters of the entity name without the
     * namespace lowercased
     * 
     * @return string
     */
    protected function getEntityAlias()
    {
        $entityName = $this->getRepository()->getClassName();
        if (false !== strpos($entityName, '\\')) {
            $entityName = substr(
                $entityName,
                strrpos($entityName, '\\') + 1
            );
        }

        $matches = array();
        preg_match_all(
            '/[A-Z]/',
            $entityName,
            $matches
        );
        return strtolower(implode('', $matches[0]));
    }
}
