<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Collections\Collection;

Abstract Class CartComponentsSet implements ExportableData
{
    protected /*Collection*/ $items;

    abstract protected function getInitialItemsCollection(array $items) : Collection;

    public function __construct(array $items)
    {
        $this->items = $this->getInitialItemsCollection($items);
    }
    
    public function isValid() : bool
    {
        return $this->items->haveAny();
    }

    public function getItems()
    {
        return $this->items;   
    }

    public function getDataToExport()
    {
        return $this->getItems();   
    }
}