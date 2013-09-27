<?php
namespace Spray\PersistenceBundle\EntityFilter\Common;

use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

class Like extends AbstractLikeFilter implements EntityFilterInterface
{
    private $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $qb)
    {
        $rootAliases = $qb->getRootAliases();
        foreach($this->arguments as $property => $value){
            $qb->andWhere(sprintf('%s.%s LIKE :incoming_%2$s', $rootAliases[0], $property));
            $qb->setParameter('incoming_' . $property, '%' . $value . '%');
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'like';
    }
}
