<?php

namespace Spray\PersistenceBundle\TestCase;

require 'app/AppKernel.php';

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit_Framework_TestCase as TestCase;
use UnexpectedValueException;

/**
 * FilterableRepositoryTestCase
 */
abstract class AbstractFilterableRepositoryTestCase extends TestCase
{
    protected static $kernel;
    protected static $container;
    
    /**
     * Currenlty you need to define the paths to your data fixtures
     * 
     * @var array
     */
    protected $dataFixturePaths = array();
    
    /**
     * If $entityName is set, you can call getRepository() without passing a
     * specific entity name
     * 
     * @var string
     */
    protected $entityName;

    /**
     * Set up the app kernel
     * 
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }
    
    /**
     * Reload schema and data fixtures on set up
     * 
     * @return void
     */
    public function setUp()
    {
        $this->reloadSchema();
        $this->reloadDataFixtures();
    }
    
    /**
     * Reload the db schema
     * 
     * @return void
     */
    public function reloadSchema()
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }
    
    /**
     * Reload data fixtures from path specified in $this->dataFixturesPaths
     * 
     * @return void
     */
    protected function reloadDataFixtures()
    {
        $em = $this->getEntityManager();
        $loader = new Loader;
        foreach ($this->dataFixturePaths as $path) {
            $loader->loadFromDirectory($path);
        }
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }
    
    /**
     * Get the entity manager
     * 
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return self::$container->get('doctrine.orm.entity_manager');
    }
    
    /**
     * Create a new repository for $entityName - if $entityName is null,
     * $this->entityName is used
     * 
     * @param null|string $entityName
     * @return EntityRepository
     * @throws UnexpectedValueException if both $entityName and
     *         $this->entityName are null
     */
    protected function createRepository($entityName = null)
    {
        if (null === $entityName) {
            if (null === $this->entityName) {
                throw new UnexpectedValueException(
                    'Please provide entity name in overriding class or as first argument'
                );
            }
            $entityName = $this->entityName;
        }
        return $this->getEntityManager()->getRepository($entityName);
    }
}