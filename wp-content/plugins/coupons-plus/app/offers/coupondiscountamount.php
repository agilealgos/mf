<?php

namespace CouponsPlus\App\Offers;

USE WC_Coupon;

Abstract Class CouponDiscountAmount
{
    protected /*float*/ $amount = 0.0;
    protected /*WC_Coupon*/ $coupon;
    protected /*integer*/ $numberOfItemsAppliedTo;
    protected static /*boolean*/ $discountHasBeenSent = [];

    abstract public function getDiscountedAmount(float $sumOfAllDiscounts, float $discountingAmount, /*array|null*/ $cartItemArray, bool $single, \WC_Coupon $coupon) /* : float*/;

    public function __construct(WC_Coupon $coupon)
    {
        $this->coupon = $coupon;
        add_action(
            'woocommerce_coupon_get_discount_amount', 
            [$this, 'getDiscountedAmount'], 
            $priority = 10, 
            $numberOfArguments = 5
        );

        add_action('woocommerce_cart_reset', [$this, 'unregisterEvent']);
        add_action('woocommerce_after_calculate_totals', [$this, 'unregisterEvent']);
    }

    /**
     * NOTE THE *ADD*, this *adds* to the exisiting amount,
     * do not confuse this with *set* (which overrides the previous amount)
     */
    public function addAmount(float $amount, int $numberOfItemsAppliedTo = 0)
    {
        $this->amount += $amount;
        $this->numberOfItemsAppliedTo = $numberOfItemsAppliedTo;
    }

    /**
     * Very important, since a new CouponDiscountAmount object
     * is created every time in woocommerce_before_calculate_totals 
     * so if $cart->calculate_totals() is run thrice:
     *
     * $cart->calculate_totals();
     * $cart->calculate_totals();
     * $cart->calculate_totals();
     *
     * we'll protentially have three times the same woocommerce_coupon_get_discount_amount
     * handler. So we remove it after successfully sending the discount amount.
     * So if another call to $cart->calculate_totals() happens, we're not running this twice.
     *
    */ 
    public function unregisterEvent()
    {
        remove_filter(
            'woocommerce_coupon_get_discount_amount', 
            [$this, 'getDiscountedAmount'], 
            $priority = 10 
        );
    }
}