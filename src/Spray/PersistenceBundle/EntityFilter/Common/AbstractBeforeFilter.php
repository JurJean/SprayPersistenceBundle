<?php

namespace Spray\PersistenceBundle\EntityFilter\Common\DateTime;

/**
 * Before
 *
 * @author MHWK
 */
abstract class AbstractBeforeFilter extends AbstractDateTimeFilter
{
    protected $comparison = "<=";
}