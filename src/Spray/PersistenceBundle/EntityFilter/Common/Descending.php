<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use Spray\PersistenceBundle\EntityFilter\Exception\InvalidArgumentException;

/**
 * Filter entities ascending by specified $propertyName
 */
class Descending implements EntityFilterInterface, ConflictingFilterInterface
{
    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        if ( ! is_string($options)) {
            throw new InvalidArgumentException('$options is expected to be a property name');
        }
        $queryBuilder->orderBy(
            sprintf('%s.%s', $queryBuilder->getRootAlias(), $options),
            'DESC'
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'descending';
    }
    
    /**
     * @inheritdoc
     */
    public function getConflictingFilters()
    {
        return array('ascending');
    }
}