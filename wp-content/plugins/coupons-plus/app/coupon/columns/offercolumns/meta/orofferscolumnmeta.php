<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns\Meta;

use CouponsPlus\App\Coupon\Columns\ORColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class OROffersColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('Or (Offers)', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('One single set of offers for all contexts. At least one filters/conditions group must pass in order for the offer to apply. Context not shared between groups.', 'coupons-plus-international');
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
        return ORColumn::TYPE;
    }
}