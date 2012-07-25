<?php

namespace Spray\PersistenceBundle\Repository;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * FilterableEntityRepositoryTest
 *
 * @author MHWK
 */
class FilterableEntityRepositoryTest extends TestCase
{
    private $filterManager;
    
    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->classMetadata = $this->getMock('Doctrine\ORM\Mapping\ClassMetadata', array(), array(), '', false);
        $this->queryBuilder = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array($this->entityManager));
        $this->filterManager = $this->getMock('Spray\PersistenceBundle\EntityFilter\FilterAggregateInterface');
        $this->filter = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
    }
    
    public function createRepository()
    {
        $repository = new FilterableEntityRepository($this->entityManager, $this->classMetadata);
        $repository->setFilterManager($this->filterManager);
        return $repository;
    }
    
    public function testAddFilter()
    {
        $this->filterManager->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo($this->filter));
        $repository = $this->createRepository();
        $repository->filter($this->filter);
    }
    
    public function testFilterQueryBuilder()
    {
        $this->filterManager->expects($this->once())
            ->method('filter')
            ->with($this->equalTo($this->queryBuilder));
        $repository = $this->createRepository();
        $repository->filterQueryBuilder($this->queryBuilder);
    }
    
}