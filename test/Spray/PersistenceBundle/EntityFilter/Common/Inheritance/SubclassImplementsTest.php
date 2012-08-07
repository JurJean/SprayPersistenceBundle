<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\Inheritance;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * SubclassImplements
 */
class SubclassImplementsTest extends TestCase
{
    private $queryBuilder;
    private $em;
    private $classMetadata;
    
    public function setUp()
    {
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->classMetadata = new ClassMetadata('FooClass');
        
        $this->queryBuilder->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->em));
        $this->queryBuilder->expects($this->any())
            ->method('getRootEntities')
            ->will($this->returnValue(array('Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestBaseClass')));
        $this->queryBuilder->expects($this->any())
            ->method('getRootAlias')
            ->will($this->returnValue('s'));
        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo('Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestBaseClass'))
            ->will($this->returnValue($this->classMetadata));
    }
    
    protected function createFilter()
    {
        return new SubclassImplements('Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestInterface');
    }
    
    public function testIsFilter()
    {
        $this->assertInstanceOf(
            'Spray\PersistenceBundle\EntityFilter\EntityFilterInterface',
            $this->createFilter()
        );
    }
    
    public function testName()
    {
        $this->assertEquals('subclass_implements', $this->createFilter()->getName());
    }
    
    public function testFailIsNotAMappedSuperClass()
    {
        $this->setExpectedException('UnexpectedValueException');
        $this->classMetadata->isMappedSuperclass = false;
        $this->createFilter()->filter($this->queryBuilder);
    }
    
    public function testFilterByInterface()
    {
        $this->classMetadata->isMappedSuperclass  = true;
        $this->classMetadata->discriminatorColumn = 'discriminator';
        $this->classMetadata->discriminatorMap    = array(
            'unexpected' => 'Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestUnexpectedSubClass',
            'expected'   => 'Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestExpectedSubClass',
        );
        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($this->equalTo('s.discriminator IN (\'expected\')'));
        $this->createFilter()->filter($this->queryBuilder);
    }
}

interface SubclassImplementsTestInterface
{
    
}

class SubclassImplementsTestBaseClass
{
    
}

class SubclassImplementsTestUnexpectedSubClass extends SubclassImplementsTestBaseClass
{
    
}

class SubclassImplementsTestExpectedSubClass extends SubclassImplementsTestBaseClass
    implements SubclassImplementsTestInterface
{
    
}