<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use Spray\PersistenceBundle\EntityFilter\Exception\InvalidArgumentException;

/**
 * AbstractDateTimeFilter
 *
 * @author MHWK
 */
abstract class AbstractDateTimeFilter implements EntityFilterInterface
{
    /**
     * Generic DateTime filter
     * 
     * @param QueryBuilder $queryBuilder
     * @param DateTime $options
     * @throws InvalidArgumentException if $options is not an instance of DateTime
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        if ( ! $options instanceof DateTime) {
            throw new InvalidArgumentException(
                '$options is expected to be an instance of DateTime'
            );
        }
        $aliases = $queryBuilder->getRootAliases();
        $queryBuilder->andWhere(sprintf(
            '%s.%s %s :%s',
            $aliases[0],
            $this->getPropertyName(),
            $this->getComparison(),
            $this->getName()
        ));
        $queryBuilder->setParameter(
            $this->getName(),
            $options->format('Y-m-d H:i:s')
        );
    }
    
    /**
     * Implement to return the property name to filter
     * 
     * @return string
     */
    abstract public function getPropertyName();
    
    /**
     * Implement to return the comparison to do
     * 
     * @return string
     */
    abstract public function getComparison();
}