<?php

namespace CouponsPlus\App\Coupon\Columns\OfferColumns;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\Columns\OfferColumns\Meta\MultiOffersColumnMeta;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\App\Offers\OffersSetCollection;

Class MultiOffersColumn extends Column
{
    const TYPE = 'MultiOffers';

    public static function getColumnMeta() : ColumnMeta
    {
        return new MultiOffersColumnMeta;
    }

    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        (object) $offersSetCollection = new OffersSetCollection([]);

        foreach ($this->contexts->asArray() as $context) {
            (object) $cartComponentsSet = $context->findOffers($itemsSet);

            if ($cartComponentsSet->isValid() && $cartComponentsSet instanceof OffersSet) {
                $offersSetCollection->addOffersSet($cartComponentsSet);
            }
        }

        return $offersSetCollection;
    }
}