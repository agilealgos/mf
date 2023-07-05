<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class UserRole extends Condition
{
    const TYPE = 'UserRole';

    static public function getName() : string
    {
        return __('User Role', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    // desc:
    // USER MUST BE LOGGED IN (AKA HAVE AN ACCOUNT) FOR THESE CHECKS TO WORK, 
    // IF THE USER IS NOT LOGGED IN (AKA A SIMPLE GUEST/VISITOR), THIS CONDITION WILL ALWAYS FAIL REGARDLESS OF 
    // THE OPTIONS SET.
    public static function getOptions() : Collection
    {
        return new Collection([
            'roles' => Types::COLLECTION,
            'inclusionType' => Types::STRING()->allowed([
                                                __('Allowed', 'coupons-plus-international') => 'allowed', 
                                                __('Not', 'coupons-plus-international') => 'forbidden'
                                               ])
                                              ->withDefault('allowed'),
            //'allowGuestsOnForbidden' => Types::BOOLEAN()->withDefault(false)
        ]);
    }

    protected function test() : bool
    {
        (object) $user = wp_get_current_user();

        (boolean) $userHasSpecifiedRoles = $this->options->roles->containEither($user->roles);

        if (!$user->exists()) {
            return false;
        }

        return $this->options->inclusionType->is('allowed')? $userHasSpecifiedRoles : !$userHasSpecifiedRoles;
    }
}