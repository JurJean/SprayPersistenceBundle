<?php

namespace Spray\PersistenceBundle\EntityFilter;

use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase as TestCase;
use Spray\PersistenceBundle\EntityFilter\FilterManager;

/**
 * FilterableEntityRepositoryTest
 *
 * @author MHWK
 */
class FilterManagerTest extends TestCase
{
    private $entityManager;
    private $queryBuilder;
    private $filter1;
    private $filter2;
    private $filter3;
    private $filter4;
    private $prioritizedFilter1;
    private $prioritizedFilter2;
    
    public function setUp()
    {
        $this->entityManager = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->queryBuilder = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array($this->entityManager));
        $this->filter1 = new FilterManagerTestFilterStub('filter1', 1);
        $this->filter2 = new FilterManagerTestFilterStub('filter2', 2);
        $this->filter3 = new FilterManagerTestFilterStub('filter3', 3);
        $this->filter4 = new FilterManagerTestFilterStub('filter4', 4);
        $this->prioritizedFilter1 = new FilterManagerTestPrioritizedFilterStub('prioritizedFilter1', 1, 10);
        $this->prioritizedFilter2 = new FilterManagerTestPrioritizedFilterStub('prioritizedFilter2', 2, 20);
        $this->conflictingFilter1 = $this->getMock('Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface');
        
        $this->conflictingFilter1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('conflictingFilter1'));
    }
    
    public function testHighIsBeforeLow()
    {
        $this->queryBuilder->expects($this->at(0))
            ->method('where')
            ->with($this->equalTo(2));
        $this->queryBuilder->expects($this->at(1))
            ->method('where')
            ->with($this->equalTo(1));
        
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->prioritizedFilter1);
        $filterManager->addFilter($this->prioritizedFilter2);
        $filterManager->filter($this->queryBuilder);
    }
    
    public function testNormalOrderIsFifo()
    {
        $this->queryBuilder->expects($this->at(0))
            ->method('where')
            ->with($this->equalTo(1));
        $this->queryBuilder->expects($this->at(1))
            ->method('where')
            ->with($this->equalTo(2));
        $this->queryBuilder->expects($this->at(2))
            ->method('where')
            ->with($this->equalTo(3));
        $this->queryBuilder->expects($this->at(3))
            ->method('where')
            ->with($this->equalTo(4));
        
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->addFilter($this->filter2);
        $filterManager->addFilter($this->filter3);
        $filterManager->addFilter($this->filter4);
        $filterManager->filter($this->queryBuilder);
    }
    
    public function testAddFilterAfterFilterCall()
    {
        $this->queryBuilder->expects($this->at(0))
            ->method('where')
            ->with($this->equalTo(1));
        $this->queryBuilder->expects($this->at(1))
            ->method('where')
            ->with($this->equalTo(1));
        $this->queryBuilder->expects($this->at(2))
            ->method('where')
            ->with($this->equalTo(2));
        
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->filter($this->queryBuilder);
        $filterManager->addFilter($this->filter2);
        $filterManager->filter($this->queryBuilder);
    }
    
    public function testHasFilter()
    {
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $this->assertTrue($filterManager->hasFilter('filter1'));
    }
    
    public function testRemoveFilter()
    {
        $this->queryBuilder->expects($this->never())
            ->method('where');
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->removeFilter('filter1');
        $filterManager->filter($this->queryBuilder);
    }
    
    public function testRemoveConflictingFilters()
    {
        $this->conflictingFilter1->expects($this->any())
            ->method('getConflictingFilters')
            ->will($this->returnValue(array('filter1')));
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->addFilter($this->conflictingFilter1);
        $this->assertFalse($filterManager->hasFilter('filter1'));
    }
    
    public function testFilterUsingOptions()
    {
        $filterManager = new FilterManager();
        $filterManager->addFilter(
            $this->conflictingFilter1,
            array('foo' => 'bar')
        );
        $this->conflictingFilter1
            ->expects($this->once())
            ->method('filter')
            ->with(
                $this->equalTo($this->queryBuilder),
                $this->equalTo(array('foo' => 'bar')));
        $filterManager->filter($this->queryBuilder);
    }
}

class FilterManagerTestFilterStub implements EntityFilterInterface
{
    public $name;
    public $index;
    
    public function __construct($name, $index)
    {
        $this->name = $name;
        $this->index = $index;
    }
    
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        $queryBuilder->where($this->index);
    }

    public function getName()
    {
        return $this->name;
    }
}

class FilterManagerTestPrioritizedFilterStub extends FilterManagerTestFilterStub
    implements PrioritizedFilterInterface
{
    public $priority;
    
    public function __construct($name, $index, $priority)
    {
        parent::__construct($name, $index);
        $this->priority = $priority;
    }
    
    public function getPriority()
    {
        return $this->priority;
    }
}