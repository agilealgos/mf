<?php

namespace CouponsPlus\App\Coupon\Columns\Meta;

use CouponsPlus\App\Coupon\Columns\OfferColumns\OROffersColumn;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class ORColumnMeta extends ColumnMeta
{
    public function getName() : string
    {
        return __('Or', 'coupons-plus-international');
    }

    public function getDescription() : string
    {
        return __('At least one filters/conditions group must pass. Context not shared between groups.', 'coupons-plus-international');
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
        return OROffersColumn::TYPE;
    }
}