<?php

namespace CouponsPlus\App\Validators;

use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\App\Validators\StringValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Abstract Class FieldValidator extends Validator
{
    abstract protected function getFieldValue(); # : mixed

    public static function getOptions() : Collection
    {
        return new Collection([
            'name' => Types::STRING()->meta([
                'name' => __('Name', 'coupons-plus-international')
            ]),
            'type' => Types::STRING()->withDefault('text')
                                     ->allowed(
                                        [
                                            __('Text', 'coupons-plus-international') => 'text', 
                                            __('Number', 'coupons-plus-international') => 'number', 
                                            __('Field Exists', 'coupons-plus-international') => 'exists' /*good for checkboxes or table rows*/]
                                      )
                                     ->meta([
                                        'name' => __('Data', 'coupons-plus-international')
                                     ]),
            'comparisonTypes' => [
                'text' => StringValidator::getOptions()->asArray(),
                'number' => AmountValidator::getOptions()->asArray(),
                'exists' => Types::BOOLEAN()->withDefault(true)
            ]
        ]);
    }

    public function isValid() : bool
    {
        /*Mixed*/$fieldValue = $this->getFieldValue();//\WC()->checkout->get_value($this->options->name->get());

        switch ($this->options->type) {
            case 'text':
                return $this->matchText($fieldValue ?? '');
                break;
            case 'number':
                return $this->matchNumber($fieldValue);
                break;
            case 'exists':
                return $this->matchExists($fieldValue);
                break;
        }
    }

    protected function matchText(string $fieldValue) : bool
    {
        (object) $stringValidator = new StringValidator($this->options->comparisonTypes->text->asArray());

        $stringValidator->setStringToValidate($fieldValue);

        return $stringValidator->isValid();
    }

    protected function matchNumber(/*float*/ $fieldValue) : bool
    {
        if (is_null($fieldValue)) {
            return false;
        }

        (object) $amountValidator = new AmountValidator($this->options->comparisonTypes->number->asArray());

        $amountValidator->setAmount($fieldValue);

        return $amountValidator->isValid();
    }

    protected function matchExists(/*mixed*/ $fieldValue) : bool
    {
        (boolean) $exists = !empty($fieldValue);

        return $this->options->comparisonTypes->exists? $exists : !$exists;
    }
}