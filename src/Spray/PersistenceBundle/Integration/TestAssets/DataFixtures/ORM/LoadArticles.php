<?php

namespace Spray\PersistenceBundle\Integration\TestAssets\DataFixtures\ORM;

use DateInterval;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Spray\PersistenceBundle\Integration\TestAssets\Entity\Article;

/**
 * LoadArticles
 */
class LoadArticles extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $tomorrow = new DateTime();
        $tomorrow->add(DateInterval::createFromDateString('1 day'));
        $firstArticle = new Article();
        $firstArticle->setTitle('First article');
        $firstArticle->setPublishedAt($tomorrow);
        $this->setReference('article-first', $firstArticle);
        $manager->persist($firstArticle);
        
        $notSoLongAgo = new DateTime();
        $notSoLongAgo->sub(DateInterval::createFromDateString('10 days'));
        $secondArticle = new Article();
        $secondArticle->setTitle('Second article');
        $secondArticle->setPublishedAt($notSoLongAgo);
        $this->setReference('article-second', $secondArticle);
        $manager->persist($secondArticle);
        
        $longAgo = new DateTime();
        $longAgo->sub(DateInterval::createFromDateString('100 days'));
        $thirdArticle = new Article();
        $thirdArticle->setTitle('Third article');
        $thirdArticle->setPublishedAt($longAgo);
        $this->setReference('article-third', $thirdArticle);
        $manager->persist($thirdArticle);
        
        $manager->flush();
    }
}
