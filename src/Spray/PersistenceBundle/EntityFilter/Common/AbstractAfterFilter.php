<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractAfterFilter extends AbstractDateTimeFilter
{
    protected $comparison = ">=";
}