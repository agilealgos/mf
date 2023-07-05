<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Tracking\QueryStringUserHistoryRepository;
use CouponsPlus\App\Validators\StringValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class URLParameter extends Condition
{
    const TYPE = 'URLParameter';
    
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
            'parameter' => [
                'name' => Types::STRING,
                'value' => Types::STRING
            ],
            'stringComparisonType' => StringValidator::getOptions()->get('comparisonType')
        ]);
    }
    
    protected function test() : bool
    {
        (object) $queryStringUserHistoryRepository = new QueryStringUserHistoryRepository(
            QueryStringUserHistoryRepository::QUERY_ID
        );

        (object) $stringValidator = new StringValidator([
            'expectedValue' => $this->options->parameter->value,
            'comparisonType' => $this->options->stringComparisonType
        ]);

        $stringValidator->setStringToValidate(
            $queryStringUserHistoryRepository->getValueForParamater($this->options->parameter->name)
        );

        return $stringValidator->isValid();
    }
}