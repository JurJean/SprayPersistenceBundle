<?php

namespace Spray\PersistenceBundle\Repository;

use Countable;
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
}
