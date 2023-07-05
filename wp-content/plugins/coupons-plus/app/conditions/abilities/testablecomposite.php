<?php

namespace CouponsPlus\App\Conditions\Abilities;

use CouponsPlus\App\Conditions\ItemsSet;

Interface TestableComposite 
{
    /**
     * If the returning $itemsSet is empty, the test fails.
     */
    public function filterSet(ItemsSet $itemsSet) : ItemsSet;
}