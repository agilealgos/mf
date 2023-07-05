<?php

namespace CouponsPlus\App\Offers;

use WC_Coupon;

Class CouponShippingDiscountAmount extends CouponDiscountAmount
{
    protected $originalAmountToApply;

    public function getDiscountedAmount(float $sumOfAllDiscounts, float $discountingAmount, /*array|null*/ $cartItemArray, bool $single, \WC_Coupon $coupon) /* : float*/
    {
        (boolean) $itsTheCouponWeWant = $coupon->get_code($context = 'edit') == $this->coupon->get_code($context = 'edit');

        if ($itsTheCouponWeWant) {
            $sumOfAllDiscounts += $this->amount;
        }

        return $sumOfAllDiscounts;
    }
}