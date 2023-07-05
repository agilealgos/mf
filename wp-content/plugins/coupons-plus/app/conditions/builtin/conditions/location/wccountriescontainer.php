<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Location;

use CouponsPlus\Original\Collections\Collection;
use WC_Countries;
/**
 * Quick container for a \WC_Countries instance
 * so that we don't load the country and state files and 
 * load them into memory each time we want to use it.
 */
Class WCCountriesContainer
{
    protected static $WC_CountriesInstance;

    public static function getWC_Countries() : WC_Countries
    {
        if (!(static::$WC_CountriesInstance instanceof WC_Countries)) {
            static::$WC_CountriesInstance = new WC_Countries;
        }

        return static::$WC_CountriesInstance;
    }

    public static function getStatesWithCountryLabels() : Collection
    {
        (object) $statesWithLabels = new Collection([]);
        (array) $countries = static::getWC_Countries()->get_countries();

        foreach (static::getWC_Countries()->get_states() as $countryCode => $states) {
            foreach ($states as $stateCode => $stateLabel) {
                $statesWithLabels->add(
                    "{$countryCode}:{$stateCode}", 
                    "{$stateLabel} ({$countries[$countryCode]})"
                );
            }
        }

        return $statesWithLabels;
    }
}