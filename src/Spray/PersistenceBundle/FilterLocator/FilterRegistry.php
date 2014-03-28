<?php

namespace Spray\PersistenceBundle\FilterLocator;

use InvalidArgumentException;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use UnexpectedValueException;

/**
 * DiLocator
 */
class FilterRegistry implements FilterLocatorInterface
{
    /**
     * @var array <string,EntityFilterInterface>
     */
    private $filters = array();
    
    /**
     * Add filter
     * 
     * @param EntityFilterInterface $filter
     * @param null|string $alias
     */
    public function add(EntityFilterInterface $filter, $alias = null)
    {
        if (null === $alias) {
            $alias = $filter->getName();
        }
        $this->filters[$alias] = $filter;
    }
    
    /**
     * Is filter with $alias set?
     * 
     * @param string $alias
     * @return boolean
     */
    public function has($alias)
    {
        if ($alias instanceof EntityFilterInterface) {
            $alias = $alias->getName();
        }
        return isset($this->filters[$alias]);
    }
    
    /**
     * Get filter by $alias
     * 
     * @param string $alias
     * @return EntityFilterInterface
     * @throws UnexpectedValueException if filter was not found
     */
    public function get($alias)
    {
        if ( ! $this->has($alias)) {
            throw new UnexpectedValueException(sprintf(
                'Could not locate filter %s in',
                $alias
            ));
        }
        if ($alias instanceof EntityFilterInterface) {
            $alias = $alias->getName();
        }
        return $this->filters[$alias];
    }
    
    /**
     * Locates $filter:
     * - if it is a string it proxies to get($alias)
     * - if it is an instance it will be added
     * - if it is an instance and it already exists, the existing version will
     *   be returned
     * 
     * @param string|EntityFilterInterface $filter
     * @return EntityFilterInterface
     * @throws InvalidArgumentException if no string or EntityFilterInterface was given
     */
    public function locateFilter($filter)
    {
        if (is_string($filter)) {
            return $this->get($filter);
        }
        
        if ( ! $filter instanceof EntityFilterInterface) {
            throw new InvalidArgumentException('Please provide an EntityFilterInterface');
        }
        
        if ( ! $this->has($filter)) {
            $this->add($filter);
        }
        return $this->get($filter);
    }
}
