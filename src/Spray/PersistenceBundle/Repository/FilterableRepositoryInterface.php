<?php

namespace Spray\PersistenceBundle\Repository;

use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * FilterableInterface
 *
 * @author MHWK
 */
interface FilterableRepositoryInterface
{
    public function filter(EntityFilterInterface $filter);
}