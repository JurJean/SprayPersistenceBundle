<?php

namespace Spray\PersistenceBundle\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

/**
 * A filterable entity repository
 * 
 * Instead of just passing data around, this Repository has a state that you can
 * alter with the use of EntityFilters.
 *
 * @author MHWK
 */
class FilterableEntityRepository extends DoctrineEntityRepository
    implements RepositoryFilterInterface
{
    /**
     * @var RepositoryFilterInterface
     */
    private $repositoryFilter;
    
    /**
     * On clone:
     * - also clone the repositoryFilter
     * 
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        if (null !== $this->repositoryFilter) {
            $this->repositoryFilter = clone $this->repositoryFilter;
        }
    }
    
    /**
     * Set repository filter
     * 
     * @param RepositoryFilterInterface $repositoryFilter
     * @return void
     */
    public function setRepositoryFilter(RepositoryFilterInterface $repositoryFilter)
    {
        $this->repositoryFilter = $repositoryFilter;
    }
    
    /**
     * Get repository filter
     * 
     * @return RepositoryFilterInterface
     */
    public function getRepositoryFilter()
    {
        if (null === $this->repositoryFilter) {
            $this->setRepositoryFilter(new RepositoryFilter($this));
        }
        return $this->repositoryFilter;
    }
    
    public function count()
    {
        return $this->getRepositoryFilter()->count();
    }

    public function current()
    {
        return $this->getRepositoryFilter()->current();
    }

    public function key()
    {
        return $this->getRepositoryFilter()->key();
    }

    public function next()
    {
        return $this->getRepositoryFilter()->next();
    }

    public function rewind()
    {
        return $this->getRepositoryFilter()->rewind();
    }

    public function valid()
    {
        return $this->getRepositoryFilter()->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function filter($filter, $options = array())
    {
        return $this->getRepositoryFilter()->filter($filter, $options);
    }
}