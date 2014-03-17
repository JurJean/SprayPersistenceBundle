<?php

namespace Spray\PersistenceBundle\Serializer;

use Iterator;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use Spray\PersistenceBundle\Repository\RepositoryFilterInterface;
use stdClass;
use PHPUnit_Framework_TestCase;

/**
 * RepositoryFilterHandlerTest
 */
class RepositoryFilterHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var VisitorInterface
     */
    private $visitor;
    
    /**
     * @var Context
     */
    private $context;
    
    /**
     * @var RepositoryFilterInterface
     */
    private $repositoryFilter;
    
    protected function setUp()
    {
        $this->visitor          = $this->getMock('JMS\Serializer\VisitorInterface');
        $this->context          = $this->getMock('JMS\Serializer\Context');
        $this->repositoryFilter = $this->getMock('Spray\PersistenceBundle\Repository\RepositoryFilterInterface');
    }
    
    protected function createHandler()
    {
        return new RepositoryFilterHandler();
    }
    
    protected function buildIterator(Iterator $iterator, array $data, $call = 0)
    {
        $iterator->expects($this->at($call++))
            ->method('rewind');
        
        foreach ($data as $key => $value) {
            $iterator->expects($this->at($call++))
                ->method('valid')
                ->will($this->returnValue(true));
            $iterator->expects($this->at($call++))
                ->method('current')
                ->will($this->returnValue($value));
            $iterator->expects($this->at($call++))
                ->method('key')
                ->will($this->returnValue($key));
            $iterator->expects($this->at($call++))
                ->method('next');
        }
        
        $iterator->expects($this->at($call++))
            ->method('valid')
            ->will($this->returnValue(false));
    }
    
    public function testConvertedRepositoryIsPassedToArrayVisitor()
    {
        $entity = new stdClass();
        $this->buildIterator($this->repositoryFilter, array($entity));
        $this->visitor->expects($this->once())
            ->method('visitArray')
            ->with(
                $this->equalTo(array(0=>$entity)),
                array('json'),
                $this->context
            );
        
        $handler = $this->createHandler();
        $handler->serializeRepositoryFilter($this->visitor, $this->repositoryFilter, array('json'), $this->context);
    }
    
    public function testVisitorResultIsReturned()
    {
        $entity = new stdClass();
        $this->buildIterator($this->repositoryFilter, array($entity));
        $this->visitor->expects($this->once())
            ->method('visitArray')
            ->will($this->returnValue('foo'));
        
        $handler = $this->createHandler();
        $this->assertSame(
            'foo',
            $handler->serializeRepositoryFilter(
                $this->visitor,
                $this->repositoryFilter,
                array('json'),
                $this->context
            )
        );
    }
}
