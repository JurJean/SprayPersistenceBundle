<?php

namespace Spray\PersistenceBundle\EntityFilter;

use PHPUnit_Framework_TestCase;

/**
 * FilterChainTest
 */
class FilterChainTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->filter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
    }
    
    protected function createFilterChain()
    {
        return new FilterChain();
    }
    
    public function testDoesNotHaveFilter()
    {
        $chain = $this->createFilterChain();
        $this->assertFalse($chain->hasFilter($this->filter));
    }
    
    public function testAddFilterHasFilter()
    {
        $chain = $this->createFilterChain();
        $chain->addFilter($this->filter);
        $this->assertTrue($chain->hasFilter($this->filter));
    }
    
    public function testRemoveFilterHasNoFilter()
    {
        $chain = $this->createFilterChain();
        $chain->addFilter($this->filter);
        $chain->removeFilter($this->filter);
        $this->assertFalse($chain->hasFilter($this->filter));
    }
    
    public function testFilterUsingOptions()
    {
        $this->filter->expects($this->once())
            ->method('filter')
            ->with(
                $this->identicalTo($this->queryBuilder),
                $this->equalTo(array('foo' => 'bar')));
        
        $chain = $this->createFilterChain();
        $chain->addFilter($this->filter, array('foo' => 'bar'));
        $chain->filter($this->queryBuilder);
    }
}
