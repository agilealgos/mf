<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\BuiltIn\Filters\Helpers\CombinedAmountItemsFilter;
use CouponsPlus\App\Conditions\BuiltIn\Filters\Helpers\DeprecatedCombinedAmountItemsFilter;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Conditions\SubsetFilter;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class CombinedCostOfItems extends Filter
{
    const TYPE = 'CombinedCostOfItems';
    const SET = SubsetFilter::class;

    public static function getName() : string
    {
        return __('Combined Cost of Items', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return __('', 'coupons-plus-international');
    }

    public static function getOptions() : Collection
    {
        return CombinedAmountItemsFilter::getOptionsMap();
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $combinedAmountItemsFilter = new DeprecatedCombinedAmountItemsFilter(
            $itemsSet,
            $this->options,
            $key = 'line_subtotal'
        );

        return $combinedAmountItemsFilter->getFilteredItems();
    }
}