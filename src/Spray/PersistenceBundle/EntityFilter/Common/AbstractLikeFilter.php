<?php

namespace Spray\PersistenceBundle\EntityFilter\Common;

class AbstractLikeFilter 
{
    /**
     * @return string
     */
    public function getComparison()
    {
        return '%';
    }
}