<?php

namespace CouponsPlus\App\Coupon\Columns\Meta;

use CouponsPlus\App\Coupon\Columns\OfferColumns\ANDOffersColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class ANDColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('And', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('All filter/condition groups must pass. Context not shared between groups.', 'coupons-plus-international');
    }

    public function isOffersColumn() : bool
    {
        return false;
    }

    public function useOneOffersSetForAllContexts() : bool
    {
        return true;
    }

    public function getPreferredColumnConversion() : string
    {
        return ANDOffersColumn::TYPE;
    }
}