<?php

namespace CouponsPlus\App\Coupon\Columns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\Columns\Meta\ANDColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\Original\Collections\Collection;

Class ANDColumn extends Column
{
    const TYPE = 'AND';

    protected $validCartComponentsSet;

    public static function getColumnMeta() : ColumnMeta
    {
        return new ANDColumnMeta;
    }

    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        $this->validCartComponentsSet = $this->getNewValidCartComponentsSet();

        foreach ($this->contexts->asArray() as $context) {
            (object) $cartComponentsSet = $context->findOffers($itemsSet);

            if (!$cartComponentsSet->isValid()) {
                return $cartComponentsSet;
            } else {
                $this->whenContextIsValid($cartComponentsSet);
            }
        }

        return $this->validCartComponentsSet;
    }

    protected function getNewValidCartComponentsSet() : CartComponentsSet
    {
        return new ItemsSet([]);
    }
    
    protected function whenContextIsValid(CartComponentsSet $cartComponentsSet)
    {
        $this->validCartComponentsSet->addItems($cartComponentsSet->getItems()->asArray());   
    }
}