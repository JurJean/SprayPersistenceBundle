<?php

namespace Spray\PersistenceBundle\Integration;

use DateInterval;
use DateTime;

/**
 * ArticleFiltersIntegrationTest
 */
class ArticleFiltersIntegrationTest extends AbstractFilterIntegrationTestCase
{
    public function testFilterArticlesCurrentlyPublished()
    {
        $articles = $this->createArticlesFilter();
        $articles->filter('currentlyPublished');
        $this->assertCount(1, $articles);
    }
    
    public function testFilterArticlesPublishedSince()
    {
        $date = new DateTime();
        $date->sub(DateInterval::createFromDateString('11 days'));
        $articles = $this->createArticlesFilter();
        $articles->filter('publishedSince', $date);
        $this->assertCount(2, $articles);
    }
    
    public function testFilterArticlesAscendingByPublicationDate()
    {
        $articles = $this->createArticlesFilter();
        $articles->filter('ascending', 'publishedAt');
        $this->assertEquals(3, $articles->current()->getId());
    }
    
    public function testFilterArticlesDescendingById()
    {
        $articles = $this->createArticlesFilter();
        $articles->filter('descending', 'id');
        $this->assertEquals(3, $articles->current()->getId());
    }
}
