<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractExactFilter extends AbstractDateTimeFilter
{
    protected $comparison = "=";
}