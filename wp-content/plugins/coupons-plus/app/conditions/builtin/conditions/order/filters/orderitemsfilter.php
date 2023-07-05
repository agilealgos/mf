<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Conditions\Order\Filters;

use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;

Abstract Class OrderItemsFilter
{
    protected $options;
    protected $items;

    abstract public static function getOptions() : Collection;
    abstract public function getFilteredItems() : Collection;

    public function __construct(/*array|string*/ $options)
    {
        (object) $JSONMapper = new JSONMapper($this->getOptions()->asArray());

        $this->options = $JSONMapper->smartMap($options);
    }    

    public function setItems(Collection $items)
    {
        $this->items = $items;
    }
}