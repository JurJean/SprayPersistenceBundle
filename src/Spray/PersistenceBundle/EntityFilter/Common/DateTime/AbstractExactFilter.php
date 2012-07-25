<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractExactFilter extends AbstractDateTimeFilter
{
    protected $comparison = "=";
}