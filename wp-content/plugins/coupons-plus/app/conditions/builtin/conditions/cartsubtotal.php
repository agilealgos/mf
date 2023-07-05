<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Base\Subtotal;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CartSubtotal extends Subtotal
{
    const TYPE = 'CartSubtotal';

    static public function getName() : string
    {
        return __('Cart Subtotal', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return AmountValidator::getOptions();
    }

    protected function getAmountOptions() : array
    {
        return $this->options->asArray();
    }
    
    protected function getSubtotal() : float
    {
        (object) $cart = WC()->cart;

        if ($cart->display_prices_including_tax()) {
            return $cart->get_subtotal() + $cart->get_subtotal_tax();
        }

        return $cart->get_subtotal();
    }
}