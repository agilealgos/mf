<?php

namespace CouponsPlus\App\Conditions\BuiltIn\Filters;

use CouponsPlus\App\Conditions\BuiltIn\Filters\Helpers\CombinedAmountItemsFilter;
use CouponsPlus\App\Conditions\CartItem;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\IntersectionFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Conditions\SubsetFilter;
use CouponsPlus\App\Quantities\AmountValidator;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;
use Mistralys\SubsetSum\SubsetSum;

/**
 * The number of items across all products.
 * the sum of the quanities of all products from all line items
 * 
 */
Class NumberOfItems extends Filter
{
    const TYPE = 'NumberOfItems';
    const SET = SubsetFilter::class;
    
    protected $amountValidator;

    public static function getName() : string
    {
        return __('Number of Items', 'coupons-plus-international');
    }

    public static function getDescription() : string
    {
        return '';
    }

    public static function getOptions() : Collection
    {
        return CombinedAmountItemsFilter::getOptionsMap();  
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $combinedAmountItemsFilter = new CombinedAmountItemsFilter(
            $itemsSet,
            $this->options->asArray(),
            'quantity'
        );

        return $combinedAmountItemsFilter->getFilteredItems();
    }
}