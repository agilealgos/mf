<?php

namespace CouponsPlus\App\Validators;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class InclusionValueValidator extends Validator
{
    protected $valueToValidate;

    public static function getOptions() : Collection
    {
        // we have to use func_get_args since PHP won't allow this 
        // class to change the interface from Validator
        (array) $expectedValuesData = func_get_args()[0] ?? [];
        (array) $inclusionTypeData = func_get_args()[1] ?? [];

        return new Collection([
            'expectedValues' => Types::COLLECTION()->meta(
                array_merge(
                    [
                        'name' => __('Values', 'coupons-plus-international')
                    ],
                    $expectedValuesData['meta'] ?? []
                )
            ),
            'inclusionType' => Types::STRING()->allowed([
                                                    __('Allowed', 'coupons-plus-international') => 'allowed', 
                                                    __('Forbidden', 'coupons-plus-international') => 'forbidden',
                                                ])
                                              ->withDefault('allowed')
                                              ->meta(
                                                    array_merge(
                                                        [
                                                            'name' => ''
                                                        ],
                                                        ($expectedValuesData['meta'] ?? [])
                                                    )
                                                )
        ]); 
    }

    public function setValueToValidate(/*mixed*/ $valueToValidate)
    {
        if (is_array($valueToValidate)) {
            $valueToValidate = new Collection($valueToValidate);
        } elseif (!($valueToValidate instanceof Collection)) {
            $valueToValidate = new Collection([$valueToValidate]);
        }

        $this->valueToValidate = $valueToValidate;
    }

    public function isValid() : bool
    {
        (boolean) $valueExists = (boolean) count(array_intersect(
            $this->options->expectedValues->asArray(), 
            $this->valueToValidate->asArray()
        ));

        return $this->options->inclusionType->is('allowed')? $valueExists : !$valueExists;
    }
}