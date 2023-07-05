<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\ComponentsRegistrator;

Class ColumnsRegistrator extends ComponentsRegistrator
{
    protected static $instance;
    
    protected static function create(string $Component, $preOptionsOptions, \WC_Coupon $coupon)
    {
        return new $Component($preOptionsOptions, $coupon);    
    }

    protected static function getComponentId() : string
    {
        return 'column';
    }

    protected static function getComponentType() : string
    {
        return Column::class;
    }
}