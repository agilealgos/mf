<?php

namespace CouponsPlus\App\Coupon\Columns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;

Class ConditionsORColumn extends Column
{
    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        foreach ($this->contexts->asArray() as $context) {
            (object) $cartComponentsSet = $context->findOffers($itemsSet);
            
            if ($cartComponentsSet->isValid()) {
                /**
                 * Since conditions do not modify the filtered items
                 * we should just return what we were passed
                 */
                return $itemsSet;
            }
        }

        return new ItemsSet([]);
    }
}