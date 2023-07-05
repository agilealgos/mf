<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\App\Validators\StringValidator;
use CouponsPlus\App\Validators\UserMetaValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CustomUserMeta extends Condition
{
    const TYPE = 'CustomUserMeta';
    
    static public function getName() : string
    {
        return __('Custom User Meta Data', 'coupons-plus-international');
    }

    static public function getDescription() : string
    {
        return '';
    }
    
    public static function getOptions() : Collection
    {
        return UserMetaValidator::getOptions();
    }
    
    protected function test() : bool
    {
        (object) $fieldValidator = new UserMetaValidator($this->options->asArray());

        return $fieldValidator->isValid();
    }
}