<?php

namespace Spray\PersistenceBundle\Integration;

use DateInterval;
use DateTime;
use Spray\PersistenceBundle\EntityFilter\Common\Ascending;
use Spray\PersistenceBundle\EntityFilter\Common\Descending;

/**
 * ArticleFiltersIntegrationTest
 */
class ArticleFiltersIntegrationTest extends AbstractFilterIntegrationTestCase
{
    const ALIAS = 'spray_persistence.integration.articles';
    
    public function testFilterArticlesCurrentlyPublished()
    {
        $articles = $this->createRepositoryFilter();
        $articles->filter('currentlyPublished');
        $this->assertCount(1, $articles);
    }
    
    public function testFilterArticlesPublishedSince()
    {
        $date = new DateTime();
        $date->sub(DateInterval::createFromDateString('11 days'));
        $articles = $this->createRepositoryFilter();
        $articles->filter('publishedSince', $date);
        $this->assertCount(2, $articles);
    }
    
    public function testFilterArticlesAscendingByPublicationDate()
    {
        $articles = $this->createRepositoryFilter();
        $articles->filter('ascending', 'publishedAt');
        $this->assertEquals(3, $articles->current()->getId());
    }
    
    public function testFilterArticlesDescendingById()
    {
        $articles = $this->createRepositoryFilter();
        $articles->filter('descending', 'id');
        $this->assertEquals(3, $articles->current()->getId());
    }
}
