<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * AscendingTest
 */
class AscendingTest extends TestCase
{
    public function setUp()
    {
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testFilter()
    {
        $this->queryBuilder->expects($this->once())
            ->method('getRootAlias')
            ->will($this->returnValue('a'));
        $this->queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with(
                $this->equalTo('a.foo'),
                $this->equalTo('ASC'));
        $filter = new Ascending('foo');
        $filter->filter($this->queryBuilder);
    }
}