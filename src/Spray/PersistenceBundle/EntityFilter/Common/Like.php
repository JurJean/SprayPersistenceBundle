<?php
namespace Spray\PersistenceBundle\EntityFilter\Common;

use Doctrine\ORM\QueryBuilder;
use Spray\PersistenceBundle\EntityFilter\EntityFilterInterface;

class Like extends AbstractLikeFilter implements EntityFilterInterface
{
    /**
     * @inheritdoc
     */
    public function filter(QueryBuilder $queryBuilder, $options = array())
    {
        $rootAliases = $queryBuilder->getRootAliases();
        foreach($options as $property => $value){
            $queryBuilder->andWhere(sprintf('%s.%s LIKE :incoming_%2$s', $rootAliases[0], $property));
            $queryBuilder->setParameter('incoming_' . $property, '%' . $value . '%');
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
