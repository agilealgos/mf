<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\Abilities\TestableComposite;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Utilities\TypeChecker;
use WC_Coupon;

Class CompositeFilter implements ItemsSetFilter, ExportableData
{
    use TypeChecker;

    protected $filters;

    public static function createFromData(array $filtersData, WC_Coupon $coupon) : CompositeFilter
    {
        return new static(Collection::create($filtersData)->map(function($filterData) use ($coupon) : Filter {
            return Filter::createFromOptions($filterData, $coupon);
        })->asArray());
    }

    public function __construct(array $filters)
    {
        $this->filters = new Collection($this->expectEach($filters)->toBe(Filter::class));
    }
    
    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        (object) $intersectionFilter = new IntersectionFilter($this->getIntersectionFilters());

        //(object) $subsetSumFilter = new SubsetSumFilter($this->getSubsetSumFilters());
        // first run all the intersection filters
        // then run a subsetsum operation on all subsetsum filters
        // then run another intersection on all subsetsum filters, but still returing 
        // an array of all possible subsetsum ietmssets
        //finally, run the subsetfilter on all the possible subsetsum itemset sets
        // 
        (object) $subsetFilter = new SubsetFilter($this->getSubsetFilters());


        return $subsetFilter->filterSet($intersectionFilter->filterSet($itemsSet));
    }

    protected function getIntersectionFilters() : Collection
    {
        return $this->filters->filter(function(Filter $filter) {
            return $filter->isIntersected();
        });
    }

    protected function getSubsetSumFilters() : Collection
    {
        return $this->filters->filter(function(Filter $filter) {
            return $filter->isSubsetSum();
        });
    }

    protected function getSubsetFilters() : Collection
    {
        return $this->filters->filter(function(Filter $filter) {
            return $filter->isSubset();
        });
    }

    public function getDataToExport()
    {
        return $this->filters;
    }
}