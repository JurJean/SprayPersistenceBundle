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
    private $expressionBuilder;
    private $classMetadata;
    
    public function setUp()
    {
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->expressionBuilder = $this->getMockBuilder('Doctrine\DBAL\Query\Expression\ExpressionBuilder')
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
        $this->queryBuilder->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($this->expressionBuilder));
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
    
    public function testFailDiscriminatorMapIsEmpty()
    {
        $this->setExpectedException('UnexpectedValueException');
        $this->classMetadata->discriminatorMap = array();
        $this->createFilter()->filter($this->queryBuilder);
    }
    
    public function testFilterByInterface()
    {
        $this->classMetadata->discriminatorMap    = array(
            'unexpected' => 'Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestUnexpectedSubClass',
            'expected'   => 'Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestExpectedSubClass',
        );
        $this->expressionBuilder->expects($this->once())
            ->method('orX')
            ->with($this->equalTo('s INSTANCE OF Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestExpectedSubClass'))
            ->will($this->returnValue('instanceof'));
        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($this->equalTo('instanceof'));
        $this->createFilter()->filter($this->queryBuilder);
    }
    
    public function testFilterNoInterface()
    {
        $this->classMetadata->discriminatorColumn = array(
            'fieldName' => 'discriminator'
        );
        $this->classMetadata->discriminatorMap    = array(
            'unexpected' => 'Spray\PersistenceBundle\EntityFilter\Common\Inheritance\SubclassImplementsTestUnexpectedSubClass',
        );
        $this->queryBuilder->expects($this->never())
            ->method('andWhere');
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