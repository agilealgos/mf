<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Location\Place;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class Location extends Condition
{
    const TYPE = 'Location';
    
    static public function getName() : string
    {
        return __('Location', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return new Collection([
            'inclusionType' => Types::STRING()->withDefault('allowed')
                                              ->allowed([
                                                    __('Allowed', 'coupons-plus-international') => 'allowed', 
                                                    __('Forbidden', 'coupons-plus-international') => 'forbidden',
                                                ]),
            'locationDepth' => Types::STRING()->withDefault('country')
                                              ->allowed([
                                                    __('Countries', 'coupons-plus-international') => 'country', 
                                                    __('States/Regions', 'coupons-plus-international') => 'state',
                                                ]),
            // a raw string of comma-separte country codes and states 
            // in the format: US:CA, US:AZ, MX:DF, HK:HONG KONG (<-- notice the space!), BG:BG-02
            // SHOULD NOT be accessed directly, use self::getSpecifiedPlaces()
            'locations' => Types::STRING(),
            'checkOn' => [
                'billingAddress' => Types::BOOLEAN()->withDefault(true),
                'shippingAddress' => Types::BOOLEAN()->withDefault(true),
                // only countries are currently allowed
                'IP' => Types::BOOLEAN()->withDefault(true)
            ]
        ]);
    }
    
    protected function test() : bool
    {
        (boolean) $billingMatchesLocation = $this->addressMatchesIfEnabled('billing');
        (boolean) $shippingMatchesLocation = $this->addressMatchesIfEnabled('shipping');
        (boolean) $IPMatchesLocation = $this->IPMatchesIfEnabled();
        (boolean) $addressHasBeenMatched = $billingMatchesLocation && $shippingMatchesLocation && $IPMatchesLocation;
        if ($this->options->inclusionType->is('allowed')) {
            return $addressHasBeenMatched;     
        }

        if ($billingMatchesLocation || $shippingMatchesLocation || $IPMatchesLocation) {
            return false;
        }

        return !$addressHasBeenMatched;
    }

    protected function addressMatchesIfEnabled(string $source) : bool
    {
        /**
         * If it's disabled, we'll return true if allowed or false if forbidden
         * so as to make this be ignored
         */
        if (!$this->options->checkOn->{"{$source}Address"}) {
            return $this->options->inclusionType->is('allowed');
        }

        return $this->placeExistsInOptions(
            \WC()->customer->{"get_{$source}_country"}($context = 'edit'),
            \WC()->customer->{"get_{$source}_state"}($context = 'edit')
        );
    }

    protected function IPMatchesIfEnabled() : bool
    {
        if (!$this->options->checkOn->IP) {
            return $this->options->inclusionType->is('allowed');
        }

        (object) $geolocation = new \WC_Geolocation;

        (array) $location = $geolocation->geolocate_ip();

        return $this->placeExistsInOptions($location['country'], $location['state'], $depth = 'country');
    }

    protected function placeExistsInOptions(string $countryCode, string $stateCode, string $locationDepth = '') : bool
    {
        return (boolean) $this->getSpecifiedPlaces()->find(function(Place $place) use ($countryCode, $stateCode, $locationDepth) {
            (boolean) $countryExists = $place->countryIs($countryCode);
            (boolean) $locationExists = $countryExists;

            if ($countryExists && $this->options->locationDepth->is('state') && ($locationDepth !== 'country')) {
                $locationExists = $countryExists && $place->stateIs($stateCode);
            }

            return $locationExists;
        });
    }

    public function getSpecifiedPlaces() : Collection
    {
        return $this->options->locations->explode(',')->map(function(StringManager $code) {
            return new Place($code->trim());
        });
    }
}