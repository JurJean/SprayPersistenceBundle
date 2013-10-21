<?php

namespace Spray\PersistenceBundle\Repository;

use Countable;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Iterator;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * FilterableInterface
 *
 * @author MHWK
 */
interface RepositoryFilterInterface extends Countable, Iterator
{
    /**
     * Attach filter to the internal filter chain
     * 
     * @param string|EntityFilterInterface
     * @return void
     */
    public function filter($filter, $options = array());
    
    /**
     * Paginate results in current filter scope
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @return Paginator
     */
    public function paginate($page = 1, $itemsPerPage = 20);
}
