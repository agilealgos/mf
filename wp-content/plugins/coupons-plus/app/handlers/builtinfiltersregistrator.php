<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Conditions\BuiltIn\Filters\CombinedCostOfItems;
use CouponsPlus\App\Conditions\BuiltIn\Filters\FeaturedProducts;
use CouponsPlus\App\Conditions\BuiltIn\Filters\InCategories;
use CouponsPlus\App\Conditions\BuiltIn\Filters\InTags;
use CouponsPlus\App\Conditions\BuiltIn\Filters\ItemPrice;
use CouponsPlus\App\Conditions\BuiltIn\Filters\MinimumCombinedCostOfItems;
use CouponsPlus\App\Conditions\BuiltIn\Filters\NumberOfItems;
use CouponsPlus\App\Conditions\BuiltIn\Filters\Products;
use CouponsPlus\App\Conditions\FiltersRegistrator;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class BuiltInFiltersRegistrator extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute(FiltersRegistrator $filtersRegistrator)
    {
        /*------------------------------------------------------------
            Simple InterSection
        *------------------------------------------------------------*/
        /*|*/ $filtersRegistrator->register(FeaturedProducts::class);
        /*|*/ $filtersRegistrator->register(InCategories::class);
        /*|*/ $filtersRegistrator->register(InTags::class);
        /*|*/ $filtersRegistrator->register(Products::class);
        /*|*/ $filtersRegistrator->register(ItemPrice::class);
        /*------------------------------------------------------------
            SubsetSum
        *------------------------------------------------------------*/
        /*|*/ $filtersRegistrator->register(CombinedCostOfItems::class); 
        /*|*/ $filtersRegistrator->register(NumberOfItems::class);
        /*|*/// $filtersRegistrator->register(MinimumCombinedCostOfItems::class);
    }
}