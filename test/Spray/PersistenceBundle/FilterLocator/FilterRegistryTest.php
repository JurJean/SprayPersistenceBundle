<?php

namespace Spray\PersistenceBundle\FilterLocator;

use PHPUnit_Framework_TestCase;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use stdClass;

/**
 * FilterRegistryTest
 */
class FilterRegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EntityFilterInterface
     */
    private $filter;
    
    protected function setUp()
    {
        $this->filter = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $this->filter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
    }
    
    /**
     * @return FilterRegistry
     */
    protected function createRegistry()
    {
        return new FilterRegistry();
    }
    
    public function testHasNotGotFilter()
    {
        $this->assertFalse(
            $this->createRegistry()->has($this->filter)
        );
    }
    
    public function testHasGotFilterAfterAdd()
    {
        $registry = $this->createRegistry();
        $registry->add($this->filter);
        $this->assertTrue($registry->has($this->filter));
    }
    
    public function testHasGotFilterByNameAfterAdd()
    {
        $registry = $this->createRegistry();
        $registry->add($this->filter);
        $this->assertTrue($registry->has('foo'));
    }
    
    public function testHasNotGotAliasedFilter()
    {
        $this->assertFalse(
            $this->createRegistry()->has('bar')
        );
    }
    
    public function testHasGotAliasedFilterAfterAdd()
    {
        $registry = $this->createRegistry();
        $registry->add($this->filter, 'bar');
        $this->assertTrue($registry->has('bar'));
    }
    
    public function testLocateFilterAddsFilterIfNotAdded()
    {
        $registry = $this->createRegistry();
        $registry->locateFilter($this->filter);
        $this->assertTrue($registry->has($this->filter));
    }
    
    public function testLocateFilterReturnsTheFilter()
    {
        $this->assertSame(
            $this->filter,
            $this->createRegistry()->locateFilter($this->filter)
        );
    }
    
    public function testLocateFilterReturnsAlreadyAddedFilter()
    {
        $filter = $this->getMock('Spray\PersistenceBundle\EntityFilter\EntityFilterInterface');
        $filter->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $registry = $this->createRegistry();
        $registry->add($this->filter);
        $this->assertSame(
            $this->filter,
            $registry->locateFilter($filter)
        );
    }
    
    public function testFailGetFilterThatWasNotAdded()
    {
        $this->setExpectedException('UnexpectedValueException');
        $this->createRegistry()->get('foo');
    }
    
    public function testFailLocateInvalidFilter()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->createRegistry()->locateFilter(new stdClass);
    }
}
