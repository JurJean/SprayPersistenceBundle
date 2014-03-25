<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractAfterFilter extends AbstractDateTimeFilter
{
    /**
     * {@inheritdoc}
     */
    public function getComparison()
    {
        return '>=';
    }
}