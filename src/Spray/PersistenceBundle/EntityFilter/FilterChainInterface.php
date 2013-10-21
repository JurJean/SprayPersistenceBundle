<?php

namespace Spray\PersistenceBundle\EntityFilter;

use IteratorAggregate;

interface FilterChainInterface
    extends FilterAggregateInterface,
            EntityFilterInterface,
            IteratorAggregate
{
    
}