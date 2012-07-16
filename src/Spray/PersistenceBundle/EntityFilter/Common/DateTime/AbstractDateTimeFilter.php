<?php

namespace Spray\PersistenceBundle\EntityFilter\DateTime;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

/**
 * AbstractDateTimeFilter
 *
 * @author MHWK
 */
abstract class AbstractDateTimeFilter implements EntityFilterInterface
{
    private $reference;
    protected $propertyName;
    protected $comparison;
    
    public function __construct(DateTime $reference)
    {
        $this->reference = $reference;
    }
    
    public function filter(QueryBuilder $qb)
    {
        $qb->andWhere(sprintf(
            '%s.%s %s :%s',
            $qb->getRootAlias(),
            $this->propertyName,
            $this->comparison,
            $this->getName()
        ));
        $qb->setParameter(
            $this->getName(),
            $this->reference->format('Y-m-d H:i:s')
        );
    }
}