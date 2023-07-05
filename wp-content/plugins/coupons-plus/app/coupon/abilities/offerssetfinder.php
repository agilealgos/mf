<?php

namespace CouponsPlus\App\Coupon\Abilities;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\CartComponentsSet;

Interface OffersSetFinder
{
    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet;
}