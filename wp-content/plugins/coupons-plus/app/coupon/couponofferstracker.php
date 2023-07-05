<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Offers\Abilities\Offers;

Class CouponOffersTracker
{
    protected $couponsWithAppliedOffers = [];

    public function track(Coupon $coupon, Offers $offers)
    {
        $this->couponsWithAppliedOffers[] = $coupon->getClassic()->get_code($context = 'edit');

        $this->couponsWithAppliedOffers = array_unique($this->couponsWithAppliedOffers);
    }

    public function haveBeenApplied(Coupon $coupon) : bool
    {
        return in_array($coupon->getClassic()->get_code($context = 'edit'), $this->couponsWithAppliedOffers);  
    }

    public function reset()
    {
        $this->couponsWithAppliedOffers = [];
    }
}