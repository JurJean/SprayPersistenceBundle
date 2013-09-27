<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * AscendingTest
 */
class LikeTest extends TestCase
{
    public function setUp()
    {
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFilter()
    {
        $count = 1;

        $this->queryBuilder->expects($this->at($count))
             ->method('getRootAliases')
             ->will($this->returnValue(array('a')));
        $this->queryBuilder->expects($this->at($count++))
             ->method('andWhere')
             ->with($this->equalTo('.foo LIKE :incoming_foo'));
        $this->queryBuilder->expects($this->at($count++))
             ->method('setParameter')
             ->with(
                 $this->equalTo('incoming_foo'),
                 $this->equalTo('%bar%')
             );

        $filter = new Like(array(
            'foo' => 'bar',
            'bar' => 'baz',
        ));
        $filter->filter($this->queryBuilder);
    }
}