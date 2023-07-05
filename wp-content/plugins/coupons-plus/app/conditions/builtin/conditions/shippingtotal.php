<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;

Class ShippingTotal extends Condition
{
    const TYPE = 'ShippingTotal';
    
    static public function getName() : string
    {
        return __('', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return AmountValidator::getOptions();
    }

    protected function test() : bool
    {
        (object) $amountValidator = new AmountValidator($this->options->asArray());

        $amountValidator->setAmount($this->getShippingTotal());

        return $amountValidator->isValid();
    }

    public function getShippingTotal() : float
    {
        (float) $shippingWithOutTax =  \WC()->cart->get_shipping_total();
        (float) $shippingTotal = $shippingWithOutTax;

        if ($this->withTax()) {
            $shippingTotal += \WC()->cart->get_shipping_tax();
        }

        return $shippingTotal;
    }

    public function withTax() : bool
    {
        return get_option('woocommerce_tax_display_cart') !== 'excl';
    }
}