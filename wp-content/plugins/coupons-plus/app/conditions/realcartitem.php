<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\Original\Collections\Collection;

Class RealCartItem extends CartItem
{
    protected function getStateCollection() : Collection
    {
        return $this->getOriginalState();
    }
}