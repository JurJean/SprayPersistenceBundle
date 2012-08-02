<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * Filter entities ascending by specified $propertyName
 */
class Descending implements EntityFilterInterface, ConflictingFilterInterface
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
    public function filter(QueryBuilder $qb)
    {
        $qb->orderBy(
            sprintf('%s.%s', $qb->getRootAlias(), $this->propertyName),
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