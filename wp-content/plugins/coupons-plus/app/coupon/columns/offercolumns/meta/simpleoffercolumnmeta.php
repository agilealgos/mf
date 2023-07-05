<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns\Meta;

use CouponsPlus\App\Coupon\Columns\SimpleColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class SimpleOfferColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('Simple', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('All filters/conditions must pass in order for the offer to apply. One context shared among all conditions/filters. Items filtered by the filters will be passed to the offers.', 'coupons-plus-international');
    }

    public function isOffersColumn() : bool
    {
        return true;
    }

    public function useOneOffersSetForAllContexts() : bool
    {
        return true;
    }

    public function getPreferredColumnConversion() : string
    {
        return SimpleColumn::TYPE;
    }
}