<?php

namespace CouponsPlus\App\Offers\BuiltIn;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\CouponShippingDiscountAmount;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;
use Mattiasgeniar\Percentage\Percentage;

Class ShippingDiscount extends Offer
{
    const TYPE = 'ShippingDiscount';

    // test:
    // with two different offer types at once
    // $couponsWithThisOfferAlreadyApplied might use a 
    // [
    //  'ShippingDiscount' => ['coupon-code']
    // ]
    // in the parent class so as to avoid having a static property on each class
    // since we can have a static property on the main class because of late binding
    protected static $couponsWithThisOfferAlreadyApplied = [];

    public static function getName() : string
    {
        return __('Shipping Discount', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return __('Applies a discount on the cart\'s shipping total.', 'coupons-plus-international');
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            // currently only percentage off is supported
            // type: percentage off, fixed price eg: $20 total, fixed disocunt eg: total - $20
            'amount' => Types::FLOAT
        ]);
    }
    
    public function apply(ItemsSet $itemsSet)
    {
        if (in_array($this->coupon->get_code($context = 'edit'), static::$couponsWithThisOfferAlreadyApplied)) {
            return;
        }
        add_filter('woocommerce_shipping_method_add_rate_args', [$this, 'getShippingWithDiscount']);
        add_action('woocommerce_after_calculate_totals', [$this, 'unregisterEvent']);

        static::$couponsWithThisOfferAlreadyApplied[] = $this->coupon->get_code($context = 'edit');
    }

    public function getShippingWithDiscount(array $rateArguments) : array
    {
        // only apply once
        //var_dump('$this->rateIdsApplied', $this->rateIdsApplied, $rateArguments['id'], spl_object_hash($this));

        (float) $cost = (float) is_array($rateArguments['cost'])? array_sum($rateArguments['cost']) : $rateArguments['cost'];
        (float) $percentageAmount = $this->options->amount;
        (float) $amountToDiscount = Percentage::of($percentageAmount, $cost);
        $rateArguments['cost'] -= $amountToDiscount;

        //$this->registerDiscountedAmount($amountToDiscount);

        return $rateArguments;
    }

    protected function registerDiscountedAmount(float $amountToDiscount)
    {
        (object) $discountAmount = new CouponShippingDiscountAmount($this->coupon);

        $discountAmount->addAmount($this->options->amount);
    }
    
    public function unregisterEvent()
    {
        if (!is_admin()) {
            static::$couponsWithThisOfferAlreadyApplied = [];
        }

        remove_filter(
            'woocommerce_shipping_method_add_rate_args', 
            [$this, 'getShippingWithDiscount']
        );
        /*
        remove_filter(
            'woocommerce_package_rates', 
            [$this, 'getShippingDiscount']
        );*/
    }

    public static function getIconUrl() : string
    {
        return Env::directoryURI().'/storage/icons/offers/ShippingDiscount.svg';   
    }
}