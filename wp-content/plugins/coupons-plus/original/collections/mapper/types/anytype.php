<?php

namespace CouponsPlus\Original\Collections\Mapper\Types;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Collections\Mapper\Types;

Class AnyType extends Types
{
    protected function setType()
    {
        return static::ANY;
    }

    public function isCorrectType($value)
    {
        return true;
    }

    public function hasDefaultValue()
    {
        return false;
    }

    public function concretePickValue($newValue)
    {
        return $newValue;
    }
}