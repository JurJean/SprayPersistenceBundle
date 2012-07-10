<?php

namespace Spray\PersistenceBundle\EntityFilter\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractBeforeFilter extends AbstractDateTimeFilter
{
    protected $comparison = "<=";
}