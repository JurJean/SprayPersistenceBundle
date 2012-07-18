<?php

namespace Spray\PersistenceBundle\Repository;

use PHPUnit_Framework_TestCase as TestCase;
use Spray\PersistenceBundle\EntityFilter\FilterManager;

/**
 * FilterableEntityRepositoryTest
 *
 * @author MHWK
 */
class FilterManagerTest extends TestCase
{
    private $queryBuilder;
    private $filter1;
    private $filter2;
    private $filter3;
    private $filter4;
    private $prioritizedFilter1;
    private $prioritizedFilter2;
    
    public function setUp()
    {
        $this->queryBuilder = $this->getMock('Doctrine\ORM\QueryBuilder', array(), array(), '', false);
        $this->filter1 = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->filter2 = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->filter3 = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->filter4 = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->prioritizedFilter1 = $this->getMock('Spray\PersistenceBundle\EntityFilter\PrioritizedFilterInterface');
        $this->prioritizedFilter2 = $this->getMock('Spray\PersistenceBundle\EntityFilter\PrioritizedFilterInterface');
        $this->conflictingFilter1 = $this->getMock('Spray\PersistenceBundle\EntityFilter\ConflictingFilterInterface');
        
        $this->filter1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('filter1'));
        $this->filter2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('filter2'));
        $this->filter3->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('filter3'));
        $this->filter4->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('filter4'));
        
        $this->prioritizedFilter1->expects($this->any())
            ->method('getPriority')
            ->will($this->returnValue(10));
        $this->prioritizedFilter1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('prioritizedFilter1'));
        $this->prioritizedFilter2->expects($this->any())
            ->method('getPriority')
            ->will($this->returnValue(20));
        $this->prioritizedFilter2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('prioritizedFilter2'));
        
        $this->conflictingFilter1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('conflictingFilter1'));
    }
    
    public function testLowIsBeforeHigh()
    {
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->prioritizedFilter2);
        $filterManager->addFilter($this->prioritizedFilter1);
        
        $expectedOrder = array(
            1 => 'prioritizedFilter1',
            2 => 'prioritizedFilter2',
        );
        
        foreach ($filterManager as $key => $filter) {
            $this->assertSame($expectedOrder[$key], $filter->getName());
        }
    }
    
    public function testNormalOrderIsFifo()
    {
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->addFilter($this->filter2);
        $filterManager->addFilter($this->filter3);
        $filterManager->addFilter($this->filter4);
        
        $expectedOrder = array(
            1 => 'filter1',
            2 => 'filter2',
            3 => 'filter3',
            4 => 'filter4',
        );
        
        foreach ($filterManager as $key => $filter) {
            $this->assertSame($expectedOrder[$key], $filter->getName());
        }
    }
    
    public function testAddFilterAfterFilterCall()
    {
        $this->filter2->expects($this->once())
            ->method('filter')
            ->with($this->equalTo($this->queryBuilder));
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
        $this->filter1->expects($this->never())
            ->method('filter');
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
}