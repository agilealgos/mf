<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Rows;
use CouponsPlus\App\Offers\Abilities\Offers;
use CouponsPlus\App\Offers\OffersScheduler;
use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;
use WC_Coupon;

Class Coupon
{
    protected /*WC_Coupon*/ $classicCoupon;

    /**
     * Since instances of this class are instantiated in different parts of the code
     * *DO NOT* add events (like action hooks) in the constructor.
     */
    public function __construct(WC_Coupon $WCCoupon, CouponOffersTracker $couponOffersTracker)
    {
        $this->classicCoupon = $WCCoupon; 
        $this->couponOffersTracker = $couponOffersTracker;
        $this->cache = new MemoryCache; 
        $this->cart = \WC()->cart;

        $this->JSONOptions = json_decode(
            $this->classicCoupon->get_meta(
                Env::getWithPrefix('rows')
            ) ?? '{}'
        );
    }

    public function getClassic() : WC_Coupon
    {
        return $this->classicCoupon;   
    }
    
    public function isCouponsPlusCoupon() : bool
    {
        if (!$this->hasRows() || empty($this->JSONOptions)) {
            return false;
        }
//var_dump('$this->JSONOptions', $this->JSONOptions);
        return $this->JSONOptions !== '{}';   
    }

    public function applyOffers(OffersScheduler $offersScheduler)
    {
        if ($this->couponIsActive() && !$this->couponOffersTracker->haveBeenApplied($this)) {
            (object) $offers = $this->getOffers();

            $offers->apply($offersScheduler);

            $this->couponOffersTracker->track($this, $offers);

            /*
                This code is unnecessary because the auto applied coupons
                will be removed by WooCommerce in the woocommerce_coupon_is_valid event
                which CouponsManager::checkCouponIsValid() is registered to.
                if ($this->canBeAutoApplied() && $this->isInvalid($offers)) {
                    $this->removeCoupon();
                }
            */
        }
    }

    public function isInvalid(Offers $offers = null) : bool
    {
        if ($this->cart->is_empty()) {
            // silently remove it, otherwise WooCommerce would generate
            // an obnoxious error on the cart page.
            $this->cart->remove_coupon($this->classicCoupon->get_code($context = 'edit'));
            return false;
        }
        (object) $offers = $offers ?? $this->getOffers();

        return !$offers->canBeApplied();   
    }
    
    public function canBeAutoApplied()
    {
        return $this->classicCoupon->get_meta(Env::getWithPrefix('coupon_auto_apply_is_enabled')) === 'yes';
    }

    public function hasRows() : bool
    {
        return $this->getRows()->hasAny();   
    }
    
    public function removeCoupon()
    {
        $this->cart->remove_coupon($this->classicCoupon->get_code($context = 'edit'));   
        $this->classicCoupon->add_coupon_message(WC_Coupon::E_WC_COUPON_INVALID_REMOVED);
    }
    
    protected function getOffers() : Offers
    {
        return $this->getRows()->findOffers(ItemsSet::createFromAllCartItems());
    }

    protected function getRows() : Rows
    {
        return Rows::createFromOptions(
            $this->JSONOptions,
            $this->classicCoupon
        );
    }

    protected function couponIsActive() : bool
    {
        return in_array(
            $this->classicCoupon->get_code($context = 'edit'), 
            $this->cart->get_applied_coupons()
        );
    }
}