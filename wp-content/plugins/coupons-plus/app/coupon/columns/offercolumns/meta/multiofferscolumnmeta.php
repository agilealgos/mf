<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns\Meta;

use CouponsPlus\App\Coupon\Columns\ORColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class MultiOffersColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('Multi', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('Offers will be applied to ANY of the passing filters/conditions groups. Context not shared between groups.', 'coupons-plus-international');
    }

    public function isOffersColumn() : bool
    {
        return true;
    }

    public function useOneOffersSetForAllContexts() : bool
    {
        return false;
    }

    public function getPreferredColumnConversion() : string
    {
        return ORColumn::TYPE;
    }
}