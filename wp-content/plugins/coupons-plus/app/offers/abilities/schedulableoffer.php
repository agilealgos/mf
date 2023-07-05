<?php

namespace CouponsPlus\App\Offers\Abilities;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Offers\Abilities\Offers;

Interface SchedulableOffer
{
    public function getScheduleId(ItemsSet $itemsSet) : string;
    public function canBeScheduled(ItemsSet $itemsSet) : bool;
}