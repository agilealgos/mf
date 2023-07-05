<?php

namespace CouponsPlus\App\Offers;

use WC_Coupon;

Class CouponItemDiscountAmount extends CouponDiscountAmount
{
    protected /*string*/ $cartItemKey;

    public function addCartItemKey(string $cartItemKey)
    {
        $this->cartItemKey = $cartItemKey;
    }
    
    public function getDiscountedAmount(float $sumOfAllDiscounts, float $discountingAmount, /*array|null*/ $cartItemArray, bool $single, \WC_Coupon $coupon) /* : float*/
    {
        (boolean) $itsTheItemWeWant = $this->cartItemKey === $cartItemArray['key'];
        (boolean) $itsTheCouponWeWant = $coupon->get_code($context = 'edit') == $this->coupon->get_code($context = 'edit');

        if ($itsTheItemWeWant and $itsTheCouponWeWant) {
            //$this->unregisterEvent();
            $sumOfAllDiscounts += $this->amount;
        }

        return $sumOfAllDiscounts;
    }
}