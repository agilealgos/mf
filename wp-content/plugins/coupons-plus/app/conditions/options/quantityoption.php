<?php

namespace CouponsPlus\App\Conditions\Options;

use CouponsPlus\Original\Collections\Collection;

Class QuantityOption
{
    protected function getOptions() : Collection
    {
        return new Collection([

        ]);
    }
    
    public static function getMap()
    {
        return $this->getOptions();   
    }
}