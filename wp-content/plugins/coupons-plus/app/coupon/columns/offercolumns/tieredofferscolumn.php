<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\Columns\OfferColumns\Meta\TieredOffersColumnMeta;
use CouponsPlus\App\Coupon\Columns\OfferColumns\OROffersColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;

Class TieredOffersColumn extends OROffersColumn
{
    const TYPE = 'TieredOffers';

    public static function getColumnMeta() : ColumnMeta
    {
        return new TieredOffersColumnMeta;
    }

    /**
     * PLEASE NOTE.
     *
     *       THIS IS **NOT***
     *       The same as OROffersColumn.
     *       It currently uses the same implementation.
     *       However, there are some notorious differences.
     *
     *      OROffersColumn only has ONE SINGLE OffersSet for all the contexts,
     *      which means, whatever passing context will return the same
     *      offerset, in other words: 
     *      all the contexts from OROffersColumn **SHARE** THE SAME OffersContext
     *
     *      This one, on the other hand, needs every single Context to have its
     *      own OfferSet. The OffersSet from every context can be different.
     * 
     */
}