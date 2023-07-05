<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Utilities\TypeChecker;

Class IntersectionFilter implements ItemsSetFilter
{
    use TypeChecker;

    public function __construct(Collection $filters)
    {
        $this->filters = $this->expectEach($filters)->toBe(Filter::class);
    }
    
    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        if ($this->filters->haveNone()) {
            return $itemsSet;
        }

        return new ItemsSet(
            $this->filters->map(function(Filter $filter) use ($itemsSet) : Collection {
                return $filter->filterSet($itemsSet)->getItems();
            })->intersect()->asArray()
        );
    }
}