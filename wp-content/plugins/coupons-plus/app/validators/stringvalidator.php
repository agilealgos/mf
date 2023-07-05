<?php // do NOT define strict standards as self::setStringToValidate(string $stringToValidate) 
     // might get anything, null for example.

namespace CouponsPlus\App\Validators;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class StringValidator extends Validator
{
    protected $stringToValidate;

    public static function getOptions() : Collection
    {
        return new Collection([
            'expectedValue' => Types::STRING()->meta([
                'name' => __('Value', 'coupons-plus-international')
            ]),
            'comparisonType' => Types::STRING()->withDefault('is')
                                                ->allowed([
                                                    __('Is', 'coupons-plus-international') => 'is',
                                                    __('Not', 'coupons-plus-international') => 'not_is',
                                                    // NOTE: Starts with WILL NOT match equal strings.
                                                    // For example: 'high' WILL NOT match 'high' 
                                                    // 'high' WILL match 'highwaypro'
                                                    __('Starts with', 'coupons-plus-international') => 'startswith',
                                                    __("Doesn't start with", 'coupons-plus-international') => 'not_startswith',
                                                    // same as with startswith
                                                    __('Ends with', 'coupons-plus-international') => 'endswith',
                                                    __("Doesn't end with", 'coupons-plus-international') => 'not_endswith',
                                                    // HERE H-O-W-E-V-E-R,
                                                    // it could be equals, at the start, end or anywhere 
                                                    // within the string.
                                                    // Read it as: I don't care where or how
                                                    // as long that word is present in any form, it matches
                                                    __('Contains', 'coupons-plus-international') => 'contains',
                                                    __("Doesn't contain", 'coupons-plus-international') => 'not_contains',
                                                ])
        ]);
    }

    public function setStringToValidate(string $stringToValidate)
    {
        $this->stringToValidate = new StringManager($stringToValidate);
    }
    
    public function isValid() : bool
    {
        if ($this->options->expectedValue->isEmpty() || $this->options->expectedValue->isEmpty()) {
            // if either is empty, we'll return false if regular comparison or true if !not comparison
            return $this->options->comparisonType->startswith('not_');
        }

        return $this->stringsMatch();
    }

    protected function stringsMatch() : bool
    {
        (boolean) $matchesValue = false;

        switch ($this->options->comparisonType->removeLeft('not_')) {
            case 'is':
                $matchesValue = $this->stringIs();
                break;
            case 'startswith':
                $matchesValue = $this->getStringToValidate()->startsWith($this->getExpectedValue(), $caseSensitive = false) && !$this->stringIs();
                break;
            case 'endswith':
                $matchesValue = $this->getStringToValidate()->endsWith($this->getExpectedValue(), $caseSensitive = false) && !$this->stringIs();
                break;
            case 'contains':
                $matchesValue = $this->getStringToValidate()->contains($this->getExpectedValue(), $caseSensitive = false);
                break;
        }

        return $this->options->comparisonType->startswith('not_')? !$matchesValue : $matchesValue;
    }

    protected function getExpectedValue() : StringManager
    {
        return $this->options->expectedValue->trim();
    }

    protected function getStringToValidate() : StringManager
    {
        return $this->stringToValidate->trim();
    }

    protected function stringIs() : bool
    {
        return $this->getStringToValidate()->is($this->getExpectedValue(), $caseInsensitive = true);
    }
}