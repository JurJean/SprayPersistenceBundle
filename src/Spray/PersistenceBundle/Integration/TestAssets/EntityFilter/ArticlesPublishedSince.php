<?php

namespace Spray\PersistenceBundle\Integration\TestAssets\EntityFilter;

use Spray\PersistenceBundle\EntityFilter\Common\AbstractAfterFilter;

/**
 * ArticlePublishedSince
 */
class ArticlesPublishedSince extends AbstractAfterFilter
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'article_is_published_at';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPropertyName()
    {
        return 'publishedAt';
    }
}
