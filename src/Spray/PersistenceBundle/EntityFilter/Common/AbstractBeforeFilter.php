<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractBeforeFilter extends AbstractDateTimeFilter
{
    protected $comparison = "<=";
}