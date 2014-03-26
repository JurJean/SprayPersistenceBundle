<?php

namespace Spray\PersistenceBundle\Integration\TestAssets\EntityFilter;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\Common\AbstractAfterFilter;

/**
 * ArticlesCurrentlyPublished
 */
class ArticlesCurrentlyPublished extends AbstractAfterFilter
{
    /**
     * Overridden to inject current DateTime
     * 
     * {@inheritdoc}
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        parent::filter($queryBuilder, new DateTime());
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'article_is_published';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPropertyName()
    {
        return 'publishedAt';
    }
}
