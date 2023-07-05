<?php

namespace CouponsPlus\App\Offers;

use WC_Coupon;

Class CouponCartDiscountAmount extends CouponDiscountAmount
{
    protected $originalAmountToApply;

    public function addAmount(float $amount, int $numberOfItemsAppliedTo = 0)
    {
        $this->originalAmountToApply = $amount;

        parent::addAmount($amount, $numberOfItemsAppliedTo);
    }
    
    public function getDiscountedAmount(float $sumOfAllDiscounts, float $discountingAmount, /*array|null*/ $cartItemArray, bool $single, \WC_Coupon $coupon) /* : float*/
    {
        (boolean) $itsTheCouponWeWant = $coupon->get_code($context = 'edit') == $this->coupon->get_code($context = 'edit');

        if ($itsTheCouponWeWant) {
            if ($cartItemArray['line_total'] < $this->amount) {
                (float) $amountToAdd = $cartItemArray['line_total'];
            } else {
                (float) $amountToAdd = $this->amount;
            }

            $sumOfAllDiscounts += $amountToAdd;

            $this->amount = $this->amount - $amountToAdd;
        }

        return $sumOfAllDiscounts;
    }
}