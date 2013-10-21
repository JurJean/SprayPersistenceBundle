<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * Filter entities ascending by specified $propertyName
 */
class Ascending implements EntityFilterInterface, ConflictingFilterInterface
{
    /**
     * @var string
     */
    private $propertyName;
    
    /**
     * Construct a new Ascending entity filter
     * 
     * @param string $propertyName
     */
    public function __construct($propertyName)
    {
        $this->propertyName = $propertyName;
    }
    
    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        $queryBuilder->orderBy(
            sprintf('%s.%s', $queryBuilder->getRootAlias(), $this->propertyName),
            'ASC'
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ascending';
    }
    
    /**
     * @inheritdoc
     */
    public function getConflictingFilters()
    {
        return array('descending');
    }
}