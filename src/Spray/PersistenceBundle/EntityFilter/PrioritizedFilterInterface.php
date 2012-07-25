<?php

namespace Spray\PersistenceBundle\EntityFilter;

/**
 * PrioritizedFilterInterface
 *
 * @author MHWK
 */
interface PrioritizedFilterInterface extends EntityFilterInterface
{
    public function getPriority();
}