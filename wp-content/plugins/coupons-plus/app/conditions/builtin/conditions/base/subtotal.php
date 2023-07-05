<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Base;

use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Abstract Class Subtotal extends Condition
{
    abstract protected function getSubtotal() : float;
    abstract protected function getAmountOptions() : array;

    final protected function test() : bool
    {
        (object) $amountValidator = new AmountValidator($this->getAmountOptions());

        $amountValidator->setAmount($this->getSubtotal());

        return $amountValidator->isValid();
    }
}