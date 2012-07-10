<?php

namespace Spray\PersistenceBundle\EntityFilter\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractAfterFilter extends AbstractDateTimeFilter
{
    protected $comparison = ">=";
}