<?php

namespace Spray\PersistenceBundle\EntityFilter\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractExactFilter extends AbstractDateTimeFilter
{
    protected $comparison = "=";
}