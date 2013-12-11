<?php

namespace Spray\PersistenceBundle\Repository;

use PHPUnit_Framework_TestCase;
/**
 * RepositoryFilterFactoryTest
 */
class RepositoryFilterFactoryTest extends PHPUnit_Framework_TestCase
{
    private $entityManager;
    private $repository;
    
    public function setUp()
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testBuildReturnsARepositoryFilter()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('barbaz'))
            ->will($this->returnValue($this->repository));
        
        $factory = new RepositoryFilterFactory($this->entityManager);
        $this->assertInstanceOf(
            'Spray\PersistenceBundle\Repository\RepositoryFilter',
            $factory->build('barbaz')
        );
    }
    
    public function testBuildInjectsCorrectRepositoryIntoFilter()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('foobar'))
            ->will($this->returnValue($this->repository));
        
        $factory = new RepositoryFilterFactory($this->entityManager);
        $filter = $factory->build('foobar');
        $this->assertSame($this->repository, $filter->getRepository());
    }
}
