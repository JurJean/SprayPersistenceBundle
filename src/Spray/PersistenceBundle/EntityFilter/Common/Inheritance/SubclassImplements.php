<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\Inheritance;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use UnexpectedValueException;

/**
 * Allows filtering of subclasses of a mapped superclass that implement specific
 * interfaces
 */
class SubclassImplements
{
    /**
     * @var string
     */
    private $interface;
    
    /**
     * Construct a new SubclassImplements filter for $interface
     * 
     * @param string $interface
     */
    public function __construct($interface)
    {
        $this->interface = $interface;
    }
    
    /**
     * Find all classes that implement $this->interface from $classMetadata
     * 
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return string
     * @throws UnexpectedValueException
     */
    public function findImplementingSubClasses(ClassMetadata $classMetadata)
    {
        if ( ! $classMetadata->isMappedSuperclass) {
            throw new UnexpectedValueException(
                'This filter is to be used by an entity that is a mapped superclass'
            );
        }
        $result = array();
        foreach ($classMetadata->discriminatorMap as $key => $className) {
            $reflection = new ReflectionClass($className);
            if ($reflection->implementsInterface($this->interface)) {
                $result[] = "'" . $key . "'";
            }
        }
        return $result;
    }
    
    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $qb)
    {
        $em = $qb->getEntityManager();
        $classMetadata = $em->getClassMetadata($qb->getRootEntities());
        
        $qb->andWhere(sprintf(
            '%s.%s IN (%s)',
            $qb->getRootAlias(),
            $classMetadata->discriminatorColumn,
            implode(',', $this->findImplementingSubClasses($classMetadata))
        ));
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'subclass_implements';
    }
}