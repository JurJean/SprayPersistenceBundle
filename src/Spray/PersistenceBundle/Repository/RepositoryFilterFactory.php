<?php

namespace Spray\PersistenceBundle\Repository;

use Doctrine\ORM\EntityManager;

/**
 * RepositoryFilterFactory
 */
class RepositoryFilterFactory implements FactoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * Construct a new RepositoryFilterFactory
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function build($entityName)
    {
        return new RepositoryFilter(
            $this->entityManager->getRepository($entityName)
        );
    }
}
