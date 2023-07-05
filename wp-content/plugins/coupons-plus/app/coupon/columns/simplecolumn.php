<?php

namespace CouponsPlus\App\Coupon\Columns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\Columns\Meta\SimpleColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;

Class SimpleColumn extends Column
{
    const TYPE = 'Simple';

    public static function getColumnMeta() : ColumnMeta
    {
        return new SimpleColumnMeta;
    }

    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        if ($this->contexts->first()) {
            return $this->contexts->first()->findOffers($itemsSet);
        }

        return $itemsSet;
    }
}