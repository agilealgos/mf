<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Location;

use CouponsPlus\App\Conditions\BuiltIn\Conditions\Location\WCCountriesContainer;

Class Place
{
    protected $countryCode;
    protected $stateCode;

    public function __construct(string $code)
    {
        $this->parse($code);
    }
    
    public function hasCountry() : bool
    {
        return WCCountriesContainer::getWC_Countries()->country_exists($this->countryCode);
    }

    public function hasState() : bool
    {
        if (!$this->hasCountry()) {
            return false;
        }

        return isset(WCCountriesContainer::getWC_Countries()->get_states($this->countryCode)[$this->stateCode]);
    }

    public function countryIs($countryCode) : bool
    {
        return $this->hasCountry() && $this->countryCode === $countryCode;
    }

    public function stateIs($stateCode) : bool
    {
        return $this->hasState() && $this->stateCode === $stateCode;
    }

    protected function parse(string $code)
    {
        (array) $parts = explode(':', $code);

        $this->countryCode = $parts[0] ?? null;
        $this->stateCode = $parts[1] ?? null;
    }
}