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
    }
    
    public function testLowIsBeforeHigh()
    {
        $this->prioritizedFilter1->expects($this->once())
            ->method('getPriority')
            ->will($this->returnValue(20));
        $this->prioritizedFilter2->expects($this->once())
            ->method('getPriority')
            ->will($this->returnValue(1));
        
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->prioritizedFilter1);
        $filterManager->addFilter($this->prioritizedFilter2);
        
        $expectedOrder = array(
            1 => $this->prioritizedFilter2,
            2 => $this->prioritizedFilter1,
        );
        
        foreach ($filterManager as $key => $filter) {
            $this->assertSame($expectedOrder[$key], $filter);
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
            1 => $this->filter1,
            2 => $this->filter2,
            3 => $this->filter3,
            4 => $this->filter4,
        );
        
        foreach ($filterManager as $key => $filter) {
            $this->assertSame($expectedOrder[$key], $filter);
        }
    }
    
    public function testHasFilter()
    {
        $this->filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $this->assertTrue($filterManager->hasFilter('foo'));
    }
    
    public function testRemoveFilter()
    {
        $this->filter1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $this->filter1->expects($this->never())
            ->method('filter');
        $filterManager = new FilterManager();
        $filterManager->addFilter($this->filter1);
        $filterManager->removeFilter('foo');
        $filterManager->filter($this->queryBuilder);
    }
}