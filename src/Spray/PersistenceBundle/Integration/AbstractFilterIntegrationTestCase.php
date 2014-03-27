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
     * Create a new RepositoryFilter for $entityName
     * 
     * @param string $entityName
     * @return RepositoryFilterInterface
     */
    protected function createRepositoryFilter($entityName)
    {
        return new RepositoryFilter(
            $this->createEntityManager()->getRepository($entityName)
        );
    }
}
