<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractExactFilter extends AbstractDateTimeFilter
{
    /**
     * {@inheritdoc}
     */
    public function getComparison()
    {
        return '=';
    }
}