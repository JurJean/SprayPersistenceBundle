<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\Inheritance;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;
use UnexpectedValueException;

/**
 * Allows filtering of subclasses of a mapped superclass that implement specific
 * interfaces
 */
class SubclassImplements implements EntityFilterInterface
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
     * @param ClassMetadata $classMetadata
     * @return string
     * @throws UnexpectedValueException
     */
    public function findImplementingSubClasses(QueryBuilder $qb)
    {
        $em = $qb->getEntityManager();
        $rootEntities = $qb->getRootEntities();
        $classMetadata = $em->getClassMetadata($rootEntities[0]);
        if (empty($classMetadata->discriminatorMap)) {
            throw new UnexpectedValueException(sprintf(
                'No discriminator map found for %s',
                $classMetadata->name
            ));
        }
        $result = array();
        foreach ($classMetadata->discriminatorMap as $key => $className) {
            $reflection = new ReflectionClass($className);
            if ($reflection->implementsInterface($this->interface)) {
                $result[] = sprintf(
                   '%s INSTANCE OF %s',
                    $qb->getRootAlias(),
                    $className
                );
            }
        }
        return $result;
    }
    
    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        $implementingSubClasses = $this->findImplementingSubClasses($queryBuilder);
        if (empty($implementingSubClasses)) {
//            $qb->andWhere(sprintf(
//                '%s.%s IN (null)',
//                $qb->getRootAlias(),
//                $classMetadata->discriminatorColumn['fieldName']
//            ));
            return;
        }
        
        $queryBuilder->andWhere(call_user_func_array(
            array($queryBuilder->expr(), 'orX'),
            $implementingSubClasses
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