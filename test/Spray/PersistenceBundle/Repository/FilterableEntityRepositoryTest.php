<?php

namespace Spray\PersistenceBundle\Repository;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit_Framework_TestCase;

/**
 * FilterableEntityRepositoryTest
 */
class FilterableEntityRepositoryTest extends PHPUnit_Framework_TestCase
{
    private $entityManager;
    private $repositoryFilter;
    
    public function setUp()
    {
        $this->classMetadata = new ClassMetadata('FooClass');
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repositoryFilter = $this->getMock('Spray\PersistenceBundle\Repository\RepositoryFilterInterface');
    }
    
    protected function createRepository()
    {
        $repository = new FilterableEntityRepository($this->entityManager, $this->classMetadata);
        $repository->setRepositoryFilter($this->repositoryFilter);
        return $repository;
    }
    
    public function proxyMethodProvider()
    {
        return array(
            array(
                'count', array()
            ),
            array(
                'current', array()
            ),
            array(
                'paginate', array(1, 10)
            ),
        );
        
    }
    
    /**
     * @dataProvider proxyMethodProvider
     */
    public function testProxyMethods($method, $arguments)
    {
        $methodMock = $this->repositoryFilter->expects($this->once())
            ->method($method);
        call_user_func_array(array($methodMock, 'with'), $arguments);
        call_user_func_array(array($this->createRepository(), $method), $arguments);
    }
}
