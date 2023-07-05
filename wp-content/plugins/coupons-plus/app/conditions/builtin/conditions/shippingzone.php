<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class ShippingZone extends Condition
{
    const TYPE = 'ShippingZone';
    
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
            'zone_ids' => Types::COLLECTION,
            'inclusionType' => Types::STRING()->withDefault('allowed')
                                              ->allowed(['allowed', 'forbidden'])
        ]);
    }
    
    protected function test() : bool
    {
        (integer) $currentZoneId = $this->getCurrentZoneId();
        (boolean) $currentZoneIdIsInCollection = $this->options->zone_ids->contain($currentZoneId);

        return $this->options->inclusionType->is('allowed')? $currentZoneIdIsInCollection : !$currentZoneIdIsInCollection;
    }

    protected function getCurrentZoneId() : int
    {
        (object) $shippingZone = \WC_Shipping_Zones::get_zone_matching_package(
            array_values(\WC()->cart->get_shipping_packages())[0]
        );

        return $shippingZone->get_id();
    }
}