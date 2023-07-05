<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use WC_Coupon;

Abstract Class CouponComponent implements ExportableData
{
    abstract public static function getMeta() : CouponComponentMeta;
    abstract public static function create(MappedObject $options, WC_Coupon $coupon); # : static

    public static function createFromOptions($options, WC_Coupon $coupon) : CouponComponent
    {
        (object) $JSONMapper = new JSONMapper(
            static::getMeta()->getOptions()->asArray()
        );

        return static::create($JSONMapper->smartMap($options), $coupon);
    }
}