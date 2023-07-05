<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns\Meta;

use CouponsPlus\App\Coupon\Columns\ANDColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class ANDOffersColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('And (Offers)', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('One single set of offers for all contexts. All filters/conditions must pass in order for the offer to apply. Context not shared between groups.', 'coupons-plus-international');
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
        return ANDColumn::TYPE;
    }
}