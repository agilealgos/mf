<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class PaymentMethod extends Condition
{
    const TYPE = 'PaymentMethod';
    
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
        return new Collection([
            'ids' => Types::COLLECTION, //payment method ids/slugs (string)
            'inclusionType' => Types::STRING()->allowed(['allowed', 'forbidden'])
                                              ->withDefault('allowed')
        ]);
    }
    
    protected function test() : bool
    {
        (string) $selectedPaymentMethod = WC()->session->get('chosen_payment_method');

        if ($selectedPaymentMethod === null) {
            // here we should add a message to the $messages object
            // $notices->add('Please select a valid payment method.').
            return false;
        }

        (boolean) $selectedPaymentMethodExistsInOptions = $this->options->ids->contain($selectedPaymentMethod);

        return $this->options->inclusionType->is('allowed')? $selectedPaymentMethodExistsInOptions : !$selectedPaymentMethodExistsInOptions;
    }
}