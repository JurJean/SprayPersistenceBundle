<?php

namespace Spray\PersistenceBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Spray\PersistenceBundle\Repository\RepositoryFilterInterface;

/**
 * RepositoryFilterHandler
 */
class RepositoryFilterHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $result = array();
        foreach (array('json', 'xml', 'yml') as $format) {
            $result[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type'      => 'Spray\PersistenceBundle\Repository\RepositoryFilter',
                'format'    => $format,
                'method'    => 'serializeRepositoryFilter',
            );
        }
        return $result;
    }
    
    /**
     * Serialize a RepositoryFilter into an array
     * 
     * @param VisitorInterface $visitor
     * @param RepositoryFilterInterface $repositoryFilter
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeRepositoryFilter(VisitorInterface $visitor, RepositoryFilterInterface $repositoryFilter, array $type, Context $context)
    {
        return $visitor->visitArray(iterator_to_array($repositoryFilter), $type, $context);
    }
}
