<?php

namespace Spray\PersistenceBundle\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use ReflectionMethod;

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
        $this->classMetadata = new ClassMetadata('FooClass');
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->queryBuilder  = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array($this->entityManager));
        $this->query         = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, $this->getClassMethods('Doctrine\ORM\AbstractQuery'));
        $this->filterManager = $this->getMock('Spray\PersistenceBundle\EntityFilter\FilterAggregateInterface');
        $this->filter        = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        
    }
    
    protected function getClassMethods($className)
    {
        $reflectionClass = new ReflectionClass($className);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $result = array();
        foreach ($methods as $method) {
            $result[] = $method->getName();
        }
        return $result;
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
    
    public function testCurrentWithoutLoop()
    {
        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->queryBuilder));
        $this->queryBuilder->expects($this->any())
            ->method('select')
            ->with($this->equalTo('fc'))
            ->will($this->returnValue($this->queryBuilder));
        $this->queryBuilder->expects($this->any())
            ->method('from')
            ->will($this->returnValue($this->queryBuilder));
        $this->filterManager->expects($this->once())
            ->method('filter')
            ->with($this->equalTo($this->queryBuilder));
        $this->queryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->query));
        $this->queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($this->equalTo(1));
        $this->query->expects($this->once())
            ->method('getSingleResult')
            ->will($this->returnValue('Foo'));
        $repository = $this->createRepository();
        $this->assertEquals('Foo', $repository->current());
    }
}