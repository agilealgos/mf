<?php

namespace CouponsPlus\App\Offers\Abilities;

use CouponsPlus\App\Offers\OffersScheduler;

Interface Offers
{
    public function apply(OffersScheduler $offersScheduler);
    public function canBeApplied() : bool;
}