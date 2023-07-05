<?php

namespace CouponsPlus\App\Coupon\Columns\Meta;

use CouponsPlus\App\Coupon\Columns\OfferColumns\SimpleOfferColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class SimpleColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('Simple', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('All filters/conditions must pass. One context shared among all conditions/filters.', 'coupons-plus-international');
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
        return SimpleOfferColumn::TYPE;
    }
}