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
class RepositoryFilterTest extends TestCase
{
    private $classMetadata;
    private $repository;
    private $entityManager;
    private $queryBuilder;
    private $query;
    private $filterManager;
    private $filter;
    
    public function setUp()
    {
        $this->classMetadata = new ClassMetadata('FooClass');
        $this->repository    = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->queryBuilder  = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array($this->entityManager));
        $this->query         = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, $this->getClassMethods('Doctrine\ORM\AbstractQuery'));
        $this->filterManager = $this->getMockBuilder('Spray\PersistenceBundle\EntityFilter\FilterManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filter        = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        
        $this->repository->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($this->queryBuilder));
        $this->queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($this->query));
        
        $this->filter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
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
    
    public function createRepositoryFilter()
    {
        $repositoryFilter = new RepositoryFilter($this->repository);
        $repositoryFilter->setFilterManager($this->filterManager);
        return $repositoryFilter;
    }
    
    public function testAddFilter()
    {
        $this->filterManager->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo($this->filter));
        $repositoryFilter = $this->createRepositoryFilter();
        $repositoryFilter->filter($this->filter);
    }
    
    public function testFilterQueryBuilder()
    {
        $this->filterManager->expects($this->once())
            ->method('filter')
            ->with($this->equalTo($this->queryBuilder));
        $repositoryFilter = $this->createRepositoryFilter();
        $repositoryFilter->filterQueryBuilder($this->queryBuilder);
    }
    
    public function testCloneAlsoClonesTheFilterManager()
    {
        $repositoryFilter1 = $this->createRepositoryFilter();
        $repositoryFilter2 = clone $repositoryFilter1;
        
        $this->assertNotSame(
            $repositoryFilter1->getFilterManager(),
            $repositoryFilter2->getFilterManager()
        );
    }
    
    public function testCurrentWithoutLoop()
    {
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
        $repositoryFilter = $this->createRepositoryFilter();
        $this->assertEquals('Foo', $repositoryFilter->current());
    }
    
    public function testDisableHydration()
    {
        $this->queryBuilder->expects($this->any())
            ->method('select')
            ->with($this->equalTo('fc'))
            ->will($this->returnValue($this->queryBuilder));
        $this->queryBuilder->expects($this->any())
            ->method('from')
            ->will($this->returnValue($this->queryBuilder));
        $this->query->expects($this->once())
            ->method('getScalarResult');
        
        $repositoryFilter = $this->createRepositoryFilter();
        $repositoryFilter->disableHydration();
        $repositoryFilter->valid();
    }
    
    public function testPaginateReturnsPaginator()
    {
        $repositoryFilter = $this->createRepositoryFilter();
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Pagination\Paginator',
            $repositoryFilter->paginate()
        );
    }
    
    public function testPaginateHasQueryBuilderInjected()
    {
        $repositoryFilter = $this->createRepositoryFilter();
        $paginator  = $repositoryFilter->paginate();
        $this->assertSame($this->query, $paginator->getQuery());
    }
    
    public function maxResultsProvider()
    {
        return array(
            array(
                20,
            ),
            array(
                30,
            ),
            array(
                40,
            ),
            array(
                50,
            ),
        );
    }
    
    /**
     * @dataProvider maxResultsProvider
     */
    public function testPaginateSetMaxResults($maxResults)
    {
        $this->queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($this->equalTo($maxResults));
        $repositoryFilter = $this->createRepositoryFilter();
        $repositoryFilter->paginate(1, $maxResults);
    }
    
    public function pageProvider()
    {
        return array(
            array(
                2, 10
            ),
            array(
                3, 20
            ),
            array(
                4, 30
            ),
            array(
                5, 40
            ),
        );
    }
    
    /**
     * @dataProvider pageProvider
     */
    public function testPaginateSetPage($page, $firstResult)
    {
        $this->queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($this->equalTo($firstResult));
        $repositoryFilter = $this->createRepositoryFilter();
        $repositoryFilter->paginate($page, 10);
    }
    
    public function testCountThroughPaginator()
    {
        $paginator = $this->getMockBuilder('Doctrine\ORM\Tools\Pagination\Paginator')
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryFilter = $this->getMockBuilder('Spray\PersistenceBundle\Repository\RepositoryFilter')
            ->setMethods(array('paginate'))
            ->setConstructorArgs(array($this->repository))
            ->getMock();
        
        $repositoryFilter->expects($this->once())
            ->method('paginate')
            ->will($this->returnValue($paginator));
        $paginator->expects($this->once())
            ->method('count')
            ->will($this->returnValue(55));
        
        $this->assertEquals(55, $repositoryFilter->count());
    }
}