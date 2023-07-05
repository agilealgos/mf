<?php

namespace CouponsPlus\App\Coupon\Columns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\Columns\Meta\ORColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;

Class ORColumn extends Column
{
    const TYPE = 'OR';
    
    public static function getColumnMeta() : ColumnMeta
    {
        return new ORColumnMeta;
    }

    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        (object) $validItemsSet = new ItemsSet([]);

        foreach ($this->contexts->asArray() as $context) {
            (object) $cartComponentsSet = $context->findOffers($itemsSet);

            if ($cartComponentsSet->isValid()) {
                return $cartComponentsSet;
            }
        }

        return $validItemsSet;
    }
}