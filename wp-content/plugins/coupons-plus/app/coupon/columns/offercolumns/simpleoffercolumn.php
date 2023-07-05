<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns;

use CouponsPlus\App\Coupon\Columns\OfferColumns\Meta\SimpleOfferColumnMeta;
use CouponsPlus\App\Coupon\Columns\SimpleColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;

Class SimpleOfferColumn extends SimpleColumn
{
    const TYPE = 'SimpleOffer';

    public static function getColumnMeta() : ColumnMeta
    {
        return new SimpleOfferColumnMeta;
    }
}