<?php

namespace Spray\PersistenceBundle\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Spray\BundleIntegration\ORMIntegrationTestCase;
use Spray\PersistenceBundle\Integration\TestAssets\SprayPersistenceIntegrationTestBundle;
use Spray\PersistenceBundle\SprayPersistenceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

/**
 * SerializerIntegrationTest
 */
class SerializerIntegrationTest extends ORMIntegrationTestCase
{
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineFixturesBundle(),
            new JMSSerializerBundle(),
            new SprayPersistenceBundle(),
            new SprayPersistenceIntegrationTestBundle()
        );
    }
    
    public function testSerializeRepository()
    {
        $serializer = $this->createContainer()->get('serializer');
        $repository = $this->createContainer()->get('spray_persistence.integration.articles');
        $result     = json_decode($serializer->serialize($repository, 'json'), true);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
    }
}
