<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Coupon\Coupon;
use CouponsPlus\App\Coupon\CouponOffersTracker;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class AppliedCouponRemoveButtonManager extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        add_filter(
            'woocommerce_cart_totals_coupon_html', 
            [$this, 'removeRemoveButtonIfAutoApplied'], 
            $priority = 10, 
            $numberOfArguments = 3
        );   
    }

    public function removeRemoveButtonIfAutoApplied(string $coupon_html, \WC_Coupon $coupon, string $discount_amount_html)
    {
        (object) $coupon = new Coupon($coupon, new CouponOffersTracker);

        if (!$coupon->canBeAutoApplied()) {
            return $coupon_html;
        }

        #coupon has been autoapplied so let's discard the [Remove] button
        return $discount_amount_html;
    }
}