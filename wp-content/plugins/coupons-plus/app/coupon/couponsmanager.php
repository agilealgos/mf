<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Coupon\Coupon;
use CouponsPlus\App\Coupon\CouponOffersTracker;
use CouponsPlus\App\Coupon\CouponURLsManager;
use CouponsPlus\App\Offers\OffersScheduler;
use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Environment\Env;
use WC_Coupon;
use WC_Discounts;

Class CouponsManager
{
    const MANAGE_HOOK = 'woocommerce_coupon_get_items_to_validate';
    
    public function __construct()
    {
        $this->cache = new MemoryCache;   
        $this->couponOffersTracker = new CouponOffersTracker;
        $this->offersScheduler = new OffersScheduler;
        //$this->couponURLManager = new CouponURLsManager;
    }
    
    public function registerEvents()
    {
        add_filter(static::MANAGE_HOOK, [$this, 'manage'], 10);
        add_filter('woocommerce_cart_reset', [$this, 'onCartReset'], 10);
        add_action('woocommerce_after_calculate_totals', [$this, 'autoApplyCoupons'], 10);
        add_action('woocommerce_coupon_is_valid', [$this, 'checkCouponIsValid'], 10, 3);
        add_filter('woocommerce_apply_individual_use_coupon', [$this, 'keepAutoAppliedCouponWhenIndividualCouponHasBeenApplied'], 10, 3);

        $this->offersScheduler->registerEvents();
     //   $this->couponURLManager->manage();
    }
    
    public function keepAutoAppliedCouponWhenIndividualCouponHasBeenApplied(array $couponsToKeep, $appliedCoupon, array $allAppliedCouponCodes) : array
    {
        return Collection::create($allAppliedCouponCodes)->filter(function(string $couponCode) : bool {
            return $this->createCouponsPlusCoupon(new WC_Coupon($couponCode))->canBeAutoApplied();
        })->getValues()->merge(array_values($couponsToKeep))->asArray();
    }
    /**
     * Useful to both manual and auto aplied coupons
     */
    public function checkCouponIsValid(bool $currentIsValid, WC_Coupon $WCCoupon, WC_Discounts $discounts) : bool
    {
        (object) $coupon = $this->createCouponsPlusCoupon($WCCoupon);

        if (!$coupon->isCouponsPlusCoupon()) {
            return $currentIsValid;
        }

        if ($coupon->canBeAutoApplied() && !$coupon->hasRows()) {

            // we'll just let wordpress decide on coupons that were autoapplied but
            // have no coupons+ rows, so that people can choose to autoapply
            // without using coupons+ offers.
            return $currentIsValid;
        }

        return !$coupon->isInvalid();   
    }

    public function onCartReset()
    {
        $this->couponOffersTracker->reset();   
    }

    public function manage($items)
    {
        $this->dispatchToCoupons();
        return $items;
    }
    
    public function dispatchToCoupons()
    {
        foreach ($this->getAppliedCouponsPlusCoupons()->asArray() as $coupon) {
            $coupon->applyOffers($this->offersScheduler);
        }
    }

    public function autoApplyCoupons()
    {
        (boolean) $withinIncompatibleActions = doing_action('woocommerce_removed_coupon') || doing_action('woocommerce_applied_coupon');

        if ($withinIncompatibleActions) {
            return;
        }

        foreach ($this->getCouponsToAutoApply()->asArray() as $couponToAutoApply) {
            /**
             * We need to manually check if it's valid or
             * woocommerce will yell very annoyingly
             * that this coupon is not valid.
             *
             * We don't wann' be showin' no error messages when autoapplying, thanks. 
             */

            if (!$couponToAutoApply->isInvalid() || ($couponToAutoApply->canBeAutoApplied() && !$couponToAutoApply->hasRows())) {
                \WC()->cart->apply_coupon($couponToAutoApply->getClassic()->get_code($context = 'edit'));
            }
        }
    }

    public function removeInvalidCoupons()
    {
        (object) $invalidCoupons = function(Coupon $coupon) {
            return $coupon->isInvalid();  
        };

        foreach ($this->getAppliedCouponsPlusCoupons()->filter($invalidCoupons)->asArray() as $invalidCoupon) {
            $invalidCoupon->removeCoupon();
        }
    }
    
    protected function getAppliedCouponsPlusCoupons() : Collection
    {
        return (new Collection(\WC()->cart->get_coupons()))->map([$this, 'createCouponsPlusCoupon'])->filter(function(Coupon $coupon) : bool {
            return $coupon->isCouponsPlusCoupon();
        });
    }

    public function createCouponsPlusCoupon(WC_Coupon $WCCoupon) : Coupon 
    {
        return new Coupon(
            $WCCoupon, 
            $this->couponOffersTracker
        );
    }

    protected function getCouponsToAutoApply() : Collection
    {
        (object) $couponsToAutoApply = new \WP_Query([
            'post_type' => 'shop_coupon',
            'meta_key' => Env::getWithPrefix('coupon_auto_apply_is_enabled'),
            'meta_value'   => 'yes',
            'meta_compare' => '=',
            'posts_per_page' => -1
        ]);

        return (new Collection($couponsToAutoApply->posts))->map(function(\WP_Post $coupon) : Coupon {
            return $this->createCouponsPlusCoupon(new WC_Coupon($coupon->ID));
        })->filter(function(Coupon $WCCoupon) : bool {
            return !in_array(
                        $WCCoupon->getClassic()->get_code($context = 'edit'), 
                        array_keys(WC()->cart->get_coupons())
                    );
        });
    }
}