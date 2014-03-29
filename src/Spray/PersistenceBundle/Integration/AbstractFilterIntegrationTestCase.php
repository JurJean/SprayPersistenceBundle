<?php

namespace Spray\PersistenceBundle\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Spray\BundleIntegration\ORMIntegrationTestCase;
use Spray\PersistenceBundle\Integration\TestAssets\SprayPersistenceIntegrationTestBundle;
use Spray\PersistenceBundle\Repository\RepositoryFilter;
use Spray\PersistenceBundle\Repository\RepositoryFilterInterface;
use Spray\PersistenceBundle\SprayPersistenceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

/**
 * FilterIntegrationTestCase
 */
abstract class AbstractFilterIntegrationTestCase extends ORMIntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineFixturesBundle(),
            new SprayPersistenceBundle(),
            new SprayPersistenceIntegrationTestBundle(),
        );
    }
    
    /**
     * Get RepositoryFilter for articles
     * 
     * @return RepositoryFilterInterface
     */
    protected function createArticlesFilter()
    {
        return $this->createContainer()->get('spray_persistence.integration.articles');
    }
}
