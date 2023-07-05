<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns;

use CouponsPlus\App\Coupon\Columns\ORColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\Meta\OROffersColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;

Class OROffersColumn extends ORColumn
{
    const TYPE = 'OROffers';

    public static function getColumnMeta() : ColumnMeta
    {
        return new OROffersColumnMeta;
    }
}