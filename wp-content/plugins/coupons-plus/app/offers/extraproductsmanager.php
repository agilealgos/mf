<?php

namespace CouponsPlus\App\Offers;

use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\BuiltIn\ExtraProduct;
use CouponsPlus\Original\Collections\Collection;
use Mattiasgeniar\Percentage\Percentage;
use WC;

Class ExtraProductsManager
{
    protected static $validExtraCartItemKeys = [];

    public static function registerKeyOfValidItem(string $validItemKey)
    {
        static::$validExtraCartItemKeys[] = $validItemKey;   
    }

    public static function reset()
    {
        static::$validExtraCartItemKeys = [];
    }    

    public function __construct()
    {
        add_action(
            'woocommerce_before_calculate_totals', 
            [$this, 'cleanCartFromInvalidItems'], 
            $priority = PHP_INT_MAX // at the very end, thanks
        );

        add_action(
            'woocommerce_coupon_get_discount_amount', 
            [$this, 'handleDiscountPerCartItem'],
            $priority = 1, // as early as possible since we might need to remove the item
            $numberOfArguments = 5
        );   

        // we'll reset the items registered at runtime just in case
        // do not remove!
        add_action(
            'woocommerce_removed_coupon',
            [$this, 'hardReset']
        );

        add_action(
            'woocommerce_cart_item_removed',
            [$this, 'hardReset']
        );

        add_action(
            'woocommerce_cart_reset', 
            [$this, 'hardReset']
        );
        
        add_action(
            'woocommerce_after_calculate_totals', 
            [$this, 'hardReset']
        );

        add_filter(
            'woocommerce_cart_item_price', 
            [$this, 'handleShowPricePerCartItem'], 
            10, 
            2
        );

        add_filter(
            'woocommerce_cart_item_subtotal', 
            [$this, 'handleShowSubtotalPrice'], 
            10, 
            2
        );

        $this->cart = WC()->cart;
    }
    
    public function handleDiscountPerCartItem(float $sumOfAllDiscounts, float $discountingAmount, /*array|null*/ $cartItemArray, bool $single, \WC_Coupon $coupon)
    {
        if ($coupon->get_code($context = 'edit') !== ($cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_COUPON] ?? false)) {
            return $sumOfAllDiscounts;
        }

        (integer) $quantityOfItemsToDiscount = (integer) $cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_QUANTITY];
        (float) $percentageAmountToDiscount = (float) $cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_AMOUNT];
        (object) $product = $cartItemArray['data'];
        (float) $itemSubtotal = $product->get_price() * $quantityOfItemsToDiscount;

        (float) $amountToDiscount = Percentage::of(
            $percentageAmountToDiscount, 
            $itemSubtotal
        );
        (float) $itemsTotalWithDiscount = max(0, $itemSubtotal - $amountToDiscount);
        (float) $individualItemPriceAfterDiscount = max(
            0, 
            $product->get_price() - ($amountToDiscount / $quantityOfItemsToDiscount)
        );

        WC()->cart->cart_contents[$cartItemArray['key']][ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_TOTAL_DISCOUNT] = $amountToDiscount;
        WC()->cart->cart_contents[$cartItemArray['key']][ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_INDIVIDUAL_ITEM_PRICE_AFTER_DISCOUNT] = $individualItemPriceAfterDiscount;
        WC()->cart->cart_contents[$cartItemArray['key']][ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_ITEM_TOTALS_AFTER_DISCOUNT] = $itemsTotalWithDiscount;
                              // don't go lower then $0
        $sumOfAllDiscounts += max(0, $amountToDiscount);

        return $sumOfAllDiscounts;
    }

    public function cleanCartFromInvalidItems()
    {
        if (doing_action('woocommerce_cart_item_removed') && current_filter() !== 'woocommerce_cart_item_removed') {
            return;
        }

        (object) $invalidItems = $this->getAllInvalidItems();

        foreach ($invalidItems->asArray() as $invalidItem) {
            // you know what, for whatever reason (there are several scenarios)
            // the item was not registered at runtime, so f it
            // ditch it asap because we sure as h do not want it.

            $this->cart->remove_cart_item($invalidItem->getState('key'));
        }
    }

    /**
     * Invalid items are basically items that were added by us
     * but weren't registered at runtime, maybe from a previous
     * session that's no longer valid.
     */
    protected function getAllInvalidItems() : Collection
    {
        (object) $allCartItems = ItemsSet::createFromAllCartItems()->getUnfilteredItems();

        (object) $itemsAddedByUs = function(CartItem $cartItem) : bool {
            return $cartItem->getState(ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_TYPE)
                     &&
                   // important since a product may be added with a 0 amount, '0'
                   ($cartItem->getState(ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_AMOUNT) > -1)
                     &&
                   $cartItem->getState(ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_QUANTITY);
        };

        (object) $itemsWithInvalidCoupon = function(CartItem $cartItem) : bool {
            return !in_array(
                $cartItem->getState(ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_COUPON), 
                $this->cart->get_applied_coupons()
            );
        };

        (object) $itemsNotRegisteredAtRuntime = function(CartItem $cartItem) : bool {
            return !in_array($cartItem->getState('key'), static::$validExtraCartItemKeys);
        };

        return $allCartItems->filter($itemsAddedByUs)
                                 ->filter($itemsWithInvalidCoupon)
                                 ->append(
                                    $allCartItems->filter($itemsAddedByUs)
                                                 ->filter($itemsNotRegisteredAtRuntime)
                                 );
    }

    public function handleShowPricePerCartItem(string $priceToShowWithMarkup, /*array|null*/ $cartItemArray)
    {
        (boolean) $couponFromItemIsActive = in_array(
            $cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_COUPON] ?? false,
            array_keys(\WC()->cart->get_coupons())
        );

        if (!$couponFromItemIsActive) {
            return $priceToShowWithMarkup;
        }

        (float) $individualItemPriceAfterDiscount = wc_price($cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_INDIVIDUAL_ITEM_PRICE_AFTER_DISCOUNT]);

        return "
            <s>{$priceToShowWithMarkup}</s>
            <span class=\"cp-item-price\">{$individualItemPriceAfterDiscount}</span>
        ";
    }

    public function handleShowSubtotalPrice(string $subtotalToShowWithMarkup, /*array|null*/ $cartItemArray)
    {
        (boolean) $couponFromItemIsActive = in_array(
            $cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_COUPON] ?? false,
            array_keys(\WC()->cart->get_coupons())
        );

        if (!$couponFromItemIsActive) {
            return $subtotalToShowWithMarkup;
        }

        (float) $itemsTotalWithDiscount = wc_price($cartItemArray[ExtraProduct::PRODUCT_ADDED_WITH_DISCOUNT_ITEM_TOTALS_AFTER_DISCOUNT]);

        return "
            <s>{$subtotalToShowWithMarkup}</s>
            <span class=\"cp-item-subtotal\">{$itemsTotalWithDiscount}</span>
        ";
    }

    public function hardReset()
    {
        $this->reset();
        $this->cleanCartFromInvalidItems();
    }
}