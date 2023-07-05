<?php

namespace CouponsPlus\App\Offers\BuiltIn;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\CouponItemDiscountAmount;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;

Class BundlePrice extends Offer
{
    const TYPE = 'BundlePrice';
    
    public static function getName() : string
    {
        return __('Bundle Price', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return __('Sets a single price for all the filtered items.', 'coupons-plus-international');
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'amount' => Types::FLOAT
        ]);
    }

    public function apply(ItemsSet $itemsSet)
    {
        $this->amountToDiscount = max(0, $itemsSet->getTotalCost() - $this->options->amount);

        foreach ($itemsSet->getItems()->asArray() as $cartItem) {
            (object) $discount = $this->findMaximumDiscount($cartItem);

            if ($discount->get('totalDiscount')) {
                (object) $itemDiscountAmount = new CouponItemDiscountAmount($this->coupon);

                $itemDiscountAmount->addCartItemKey($cartItem->getState('key'));
                $itemDiscountAmount->addAmount(...$discount->getValues()->asArray());
            }
        }
    }

    protected function findMaximumDiscount(CartItem $cartItem) : Collection
    {
        (float) $individualPrice = $cartItem->getProduct()->get_price($context = 'edit');
        (float) $totalDiscount = 0;
        (integer) $quantityAppliedTo = 0;

        for ($item = 1; $item <= $cartItem->getState('quantity'); $item++) { 
            if (!$this->amountToDiscount) {
                break;
            } elseif ($this->amountToDiscount > $individualPrice) {
                $totalDiscount += $individualPrice;
                $this->amountToDiscount -= $individualPrice;
            } else {
                $totalDiscount += $this->amountToDiscount;
                $this->amountToDiscount -= $this->amountToDiscount;
            }

            $quantityAppliedTo++; 
        }

        return new Collection([
            'totalDiscount' => $totalDiscount,
            'quantityAppliedTo' => $quantityAppliedTo,
        ]);
    }    

    public static function getIconUrl() : string
    {
        return Env::directoryURI().'/storage/icons/offers/BundlePrice.svg';   
    }
}