<?php

namespace CouponsPlus\App\Conditions\Abilities;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Conditions\ItemsSetCollection;

Interface ItemsSetSubsetSumFilter 
{
    /**
     * If the returning collection is empty, the test fails.
     */
    public function filterSetSubsetSum(ItemsSet $itemsSet) : ItemsSetCollection;
}