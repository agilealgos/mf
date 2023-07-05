<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Coupon\ComponentsRegistrator;

Class FiltersRegistrator extends ComponentsRegistrator
{
    protected static $instance;
    
    protected static function create(string $Component, $preOptionsOptions, \WC_Coupon $coupon)
    {
        return new $Component($preOptionsOptions, $coupon);    
    }

    protected static function getComponentId() : string
    {
        return 'filter';
    }

    protected static function getComponentType() : string
    {
        return Filter::class;
    }
}