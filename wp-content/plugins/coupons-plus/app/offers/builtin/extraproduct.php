<?php

namespace CouponsPlus\App\Offers\BuiltIn;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\Abilities\SchedulableOffer;
use CouponsPlus\App\Offers\ExtraProductsManager;
use CouponsPlus\App\Offers\Offer;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use CouponsPlus\Original\Environment\Env;
use WC;

Class ExtraProduct extends Offer implements SchedulableOffer
{
    const TYPE = 'ExtraProduct';
    
    const PRODUCT_ADDED_WITH_DISCOUNT_TYPE = 'cp-product-with-discount-type';
    const PRODUCT_ADDED_WITH_DISCOUNT_AMOUNT = 'cp-product-with-discount-amount';
    const PRODUCT_ADDED_WITH_DISCOUNT_QUANTITY = 'cp-product-with-discount-quantity';
    const PRODUCT_ADDED_WITH_DISCOUNT_TOTAL_DISCOUNT = 'cp-product-with-discount-total-discount';
    const PRODUCT_ADDED_WITH_DISCOUNT_COUPON = 'cp-product-with-discount-coupon';
    const PRODUCT_ADDED_WITH_DISCOUNT_ITEM_TOTALS_AFTER_DISCOUNT = 'cp-product-with-discount-item-totals-after-discount';
    const PRODUCT_ADDED_WITH_DISCOUNT_INDIVIDUAL_ITEM_PRICE_AFTER_DISCOUNT = 'cp-product-with-discount-individual-item-price-after-discount';

    protected $runtimeProductAlreadyAddedIGNORES_SESSION = false;
    protected $itemsSet;

    public static function getName() : string
    {
        return __('Extra Product', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return __('Automatically adds extra products to the cart.', 'coupons-plus-international');
    }

    public static function getOptions() : Collection
    {
        return new Collection([
            'typeOfProductToAdd' => Types::STRING()->withDefault('specific')
                                                   ->allowed([
                                                        __('Specific Product', 'coupons-plus-international') => 'specific',
                                                        __('Product from Filtered Items', 'coupons-plus-international') => 'filtereditems',
                                                    ])
                                                   ->meta([
                                                        'name' => __('Product', 'coupons-plus-international')
                                                   ]),
            // 'product' is for when typeOfProductToAdd === 'specific'
            'product' => [
                'id' => Types::INTEGER()->withDefault(0), # This id is currently the individual id, 
                                        # no parent -> child, but the exact id,
                                        # if a variation, this is the exact variation id
                                        # and not the parent's as opposed to CouponsPlus\App\Conditions\BuiltIn\Filters\Products
                'quantity' => Types::INTEGER()->withDefault(1)->meta([
                    'name' => __('Quantity', 'coupons-plus-international')
                ])
            ],
            'fromFilteredItems' => [
               'quantity' => Types::INTEGER()->withDefault(1)->meta([
                    'name' => __('Quantity', 'coupons-plus-international')
                ])
            ],
            'price' => [
                'type' => Types::STRING()->withDefault('percentageoff')
                                         ->allowed(['percentageoff', /*coming soon: 'finalproductsubtotal'*/]),
                'amount' => Types::FLOAT
            ]
        ]);
    }

    public function onOptionsLoaded()
    {
        // the exact individual ID, could be a regular product id or a variation id
        do_action(
            Env::getWithPrefix('extra_product_individual_loaded_id'), 
            $this->getProductId()
        );   
    }

    public function canBeScheduled(ItemsSet $itemsSet) : bool
    {
        if ($this->options->typeOfProductToAdd->is('filtereditems')) {
            return $itemsSet->getUnfilteredItems()->haveAny();
        }

        return (boolean) wc_get_product($this->options->product->id);
    }

    public function getScheduleId(ItemsSet $itemsSet) : string
    {
        if ($this->options->typeOfProductToAdd->is('specific') && !$this->getProductId()) {
            return '__null__';
        }

        $this->itemsSet = $itemsSet;

        return $this->getProductCartId();   
    }

    public function apply(ItemsSet $itemsSet)
    {
        $this->itemsSet = $itemsSet;

        if (!$this->getProductId() || !wc_get_product($this->getProductId())) {
            return;
        }

        $this->silentAddToCart();
        // this is added at runtime and when the coupon is active and valid
        // ExtraProductsManager will only apply the discounts
        // when the product ids were registered at runtime.
        // This method static::apply() is ONLY called when the offer should be appied.        
        // if the item is in cart (from a previous session) and this 
        // method is not called (ExtraProductsManager::registerKeyOfValidItem()), 
        // then the discount will not be applied and/or the item will be removed.
        // 
        // for example, if the user removes the coupon
        // or if the conditions for this offer are no longer valid (eg: the cart items changed)
        // or if the user adds another item to this one
        // etc
        (string) $cartId = $this->getProductCartId();

        ExtraProductsManager::registerKeyOfValidItem($cartId);
    }

    protected function silentAddToCart() : string
    {
        if (!$this->couponIsActive()) {
            return '';
        }

        (array) $calculateTotals = [WC()->cart, 'calculate_totals'];
        (integer) $priority = 20;

        /**
         * The problem: Woocommerce is calling WC_Cart::calculate_totals()
         * after adding the product. That method then fires:
         * woocommerce_before_calculate_totals, which causes and endless
         * loop, se we need to temporarily disable it.
         *  
         */
        remove_action('woocommerce_add_to_cart', $calculateTotals, $priority);

        (string) $productCartId = $this->addToCart();

        add_action('woocommerce_add_to_cart', $calculateTotals, $priority);

        return $productCartId ?? '';
        //\WC()->cart->calculate_totals();
    }

    protected function addToCart() : string
    {
        (object) $product = $this->getProductData();

        (string) $productCartId = $this->getProductCartId();

        if (!WC()->cart->find_product_in_cart($productCartId)) {
            WC()->cart->add_to_cart(
                ...$product->getValues()->asArray()
            );
        }

        return $productCartId;
    }

    protected function getProductCartId() : string
    {
        return WC()->cart->generate_cart_id(
            ...$this->getProductData()->except(['quantity'])->getValues()->asArray()
        );   
    }
    
    protected function getProductData() : Collection
    {

        //debug_print_backtrace();
        (object) $product = wc_get_product($this->getProductId());

        return new Collection([
            'id' => $product->get_type() === 'variation' ? $product->get_parent_id($context = 'edit') : $this->getProductId(),
            'quantity' => $this->getQuantity(),
            'variationId' => $product->get_type() === 'variation' ? $this->getProductId() : 0,
            'variation' => $product->get_type() === 'variation'? $product->get_variation_attributes($with_prefix = false) : [],
            'cartItemData' => [
                static::PRODUCT_ADDED_WITH_DISCOUNT_TYPE => $this->options->price->type->get(),
                static::PRODUCT_ADDED_WITH_DISCOUNT_AMOUNT => $this->options->price->amount,
                static::PRODUCT_ADDED_WITH_DISCOUNT_QUANTITY => $this->getQuantity(),
                static::PRODUCT_ADDED_WITH_DISCOUNT_COUPON => $this->coupon->get_code($context = 'edit'),
                static::PRODUCT_ADDED_WITH_DISCOUNT_TOTAL_DISCOUNT => 0,
                static::PRODUCT_ADDED_WITH_DISCOUNT_ITEM_TOTALS_AFTER_DISCOUNT => 0,
                static::PRODUCT_ADDED_WITH_DISCOUNT_INDIVIDUAL_ITEM_PRICE_AFTER_DISCOUNT => 0
            ]
        ]);
    }

    protected function getQuantity() : int
    {
        if ($this->options->typeOfProductToAdd->is('specific')) {
            return $this->options->product->quantity;   
        }

        return $this->options->fromFilteredItems->quantity;
    }
    
    protected function getProductId() : int
    {
        ob_flush();
        if ($this->options->typeOfProductToAdd->is('specific')) {
            return $this->options->product->id;
        }

        if (!($this->itemsSet instanceof ItemsSet) || $this->itemsSet->getUnfilteredItems()->haveNone()) {

            return 0;
        }

        return $this->itemsSet->getOrderedByCheapestProduct()->first()->getProduct()->get_id();
    }
    
    
    public static function getIconUrl() : string
    {
        return Env::directoryURI().'/storage/icons/offers/ExtraProduct.svg';   
    }
}