<?php

namespace CouponsPlus\App\Offers\BuiltIn;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Helpers\Currency;
use CouponsPlus\App\Offers\CouponCartDiscountAmount;
use CouponsPlus\App\Offers\CouponDiscountAmount;
use CouponsPlus\App\Offers\CouponItemDiscountAmount;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;
use Mattiasgeniar\Percentage\Percentage;

Class Discount extends Offer
{
    const TYPE = 'Discount';
    
    protected $quantitiesApplied = 0;

    public static function getName() : string
    {
        return __('Discount', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return __('Sets a discount on the filtered items or the whole cart.', 'coupons-plus-international');
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'type' => Types::STRING()->withDefault('percentage')
                                    ->meta([
                                        'name' => __('Type', 'coupons-plus-international')
                                    ])
                                    // PLEASE NOTE: amount CURRENTLY ONLY AVAILABLE TO cartsubtotal (AS CART TOTAL)
                                     ->allowed([
                                        '%' => 'percentage', 
                                        (Currency::$currencies[get_woocommerce_currency()] ?? Currency::$currencies['USD'])['symbol'] => 'amount',
                                    ]),
            'amount' => Types::FLOAT,
            'scope' => Types::STRING()->withDefault('filtereditems')
                                             ->allowed([
                                                __('Filtered items', 'coupons-plus-international')=> 'filtereditems', 
                                                __('Cart subtotal', 'coupons-plus-international')=> 'cartsubtotal',
                                              ]),
            'limit' => [
                'isEnabled' => Types::BOOLEAN()->withDefault(false)->meta([
                    'disabled' => __('With no limit', 'coupons-plus-international'),
                    'enabled' => __('Limit to:', 'coupons-plus-international'),
                ]),
                'amount' => Types::INTEGER()->withDefault(0), // MAXIMUM WILL BE performed,
                'orderBy' => Types::STRING()->withDefault('lowestprice')
                                            ->allowed(['lowestprice', 'highestprice'])
            ]
        ]);
    }

    public function apply(ItemsSet $itemsSet)
    {
        (object) $itemsSet = $this->getItemsSet($itemsSet);

        switch ($this->options->type) {
            case 'percentage':
                $this->applyPercentage($itemsSet);
                break;
            case 'amount':
                $this->applyFixedAmount($itemsSet);
                break;
        }
    }

    protected function applyPercentage(ItemsSet $itemsSet)
    {
        $this->applyDiscountToEachProduct($itemsSet, function(CartItem $cartItem, float $itemsTotalPrice, int $quantity) : float {
            (float) $percentageAmount = $this->options->amount;

            return Percentage::of($percentageAmount, $itemsTotalPrice);
        });  
    }
    
    protected function applyFixedAmount(ItemsSet $itemsSet)
    {
        switch ($this->options->scope) {
            case 'filtereditems':
                return $this->applyFixedAmountToItems($itemsSet);
                break;
            case 'cartsubtotal':
                (object) $cartDiscountAmount = new CouponCartDiscountAmount($this->coupon);

                $cartDiscountAmount->addAmount($this->options->amount);
                break;
        }
    }

    protected function applyFixedAmountToItems(ItemsSet $itemsSet)
    {
        $this->applyDiscountToEachProduct($itemsSet, function(CartItem $cartItem, float $itemsTotalPrice, int $quantity, $individualPrice) : float {
            // this comment stays for when debugging.
            //var_dump('product_id', $cartItem->getState('product_id'), '$itemsTotalPrice', $itemsTotalPrice, '$quantity', $quantity, '$this->options->amount', $this->options->amount, 'then:', $quantity * $this->options->amount, 'RESULT', ($quantity * $this->options->amount));
            //var_dump('________________');
            //

            return ($quantity * min($this->options->amount, $individualPrice));
        });  
    }

    protected function applyDiscountToEachProduct(ItemsSet $itemsSet, callable $getDiscount)
    {
        foreach ($itemsSet->getOrderedByCheapestProduct()->asArray() as $cartItem) {
            (integer) $quantity = $this->getQuantitiesOfItemToApply($cartItem);
            (float) $individualPrice = $cartItem->getProduct()->get_price($context = 'edit');
            (float) $itemsTotalPrice = $individualPrice * $quantity;

            if ($quantity) {
                (object) $couponDiscount = new CouponItemDiscountAmount($this->coupon);
                $couponDiscount->addCartItemKey($cartItem->getState('key'));

                $couponDiscount->addAmount($getDiscount($cartItem, $itemsTotalPrice, $quantity, $individualPrice), $quantity);
            }
        }  
    }

    protected function getQuantitiesOfItemToApply(CartItem $cartItem) : int
    {
        (integer) $quantityToApply = 0;

        # limiting is not supported on cart subtotal
        if (!$this->options->scope->is('cartsubtotal') && $this->options->limit->isEnabled) {
            if ($this->quantitiesApplied < $this->options->limit->amount) {
                $quantityToApply = min($cartItem->getState('quantity'), $this->options->limit->amount - $this->quantitiesApplied);
            }
        } else {
            $quantityToApply = $cartItem->getState('quantity');
        }

        $this->quantitiesApplied += $quantityToApply;

        return $quantityToApply;
    }
    

    protected function getItemsSet(ItemsSet $itemsSet) : ItemsSet
    {
        switch ($this->options->scope) {
            case 'filtereditems':
                return $itemsSet;
                break;
            case 'cartsubtotal':
                return ItemsSet::createFromAllCartItems();
                break;
        }
    }

    public static function getIconUrl() : string
    {
        return Env::directoryURI().'/storage/icons/offers/Discount.svg';   
    }
}