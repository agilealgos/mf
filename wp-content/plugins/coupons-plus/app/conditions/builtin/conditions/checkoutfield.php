<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\App\Validators\CheckoutFieldValidator;
use CouponsPlus\App\Validators\StringValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CheckoutField extends Condition
{
    const TYPE = 'CheckoutField';
    
    static public function getName() : string
    {
        return __('Checkout Field', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return CheckoutFieldValidator::getOptions();
    }
    
    protected function test() : bool
    {
        (object) $checkoutFieldValidator = new CheckoutFieldValidator($this->options->asArray());

        return $checkoutFieldValidator->isValid();
    }
}