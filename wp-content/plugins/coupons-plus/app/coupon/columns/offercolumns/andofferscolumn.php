<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns;

use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Columns\ANDColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\Meta\ANDOffersColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\App\Offers\OffersSet;

Class ANDOffersColumn extends ANDColumn
{
    const TYPE = 'ANDOffers';

    public static function getColumnMeta() : ColumnMeta
    {
        return new ANDOffersColumnMeta;
    }

    protected function getNewValidCartComponentsSet() : CartComponentsSet
    {
        return new OffersSet([]);
    }

    protected function whenContextIsValid(CartComponentsSet $offersSet)
    {
        $this->validCartComponentsSet->getItemsSet()->addItems($offersSet->getItemsSet());

        $offersSet->getItemsSet()->setItems(
            $this->validCartComponentsSet->getItemsSet()
        );

        $this->validCartComponentsSet = $offersSet;
    }
}