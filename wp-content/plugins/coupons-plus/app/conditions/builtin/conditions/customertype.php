<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CustomerType extends Condition
{
    const TYPE = 'CustomerType';
    
    static public function getName() : string
    {
        return __('Customer Type', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'type' => Types::STRING()->withDefault('account') // aka a customer with an account
                                     ->allowed([
                                        __('Guest', 'coupons-plus-international') => 'guest', 
                                        __('With An Account', 'coupons-plus-international') => 'account'
                                    ])
        ]);   
    }
 
    protected function test() : bool
    {
        (object) $user = wp_get_current_user();

        return $this->options->type->is('account')? $user->exists() : !$user->exists();
    }
}