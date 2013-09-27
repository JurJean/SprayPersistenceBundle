<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractAfterFilter extends AbstractDateTimeFilter
{
    protected $comparison = ">=";
}