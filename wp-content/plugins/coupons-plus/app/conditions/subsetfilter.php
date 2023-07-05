<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Utilities\TypeChecker;

Class SubsetFilter implements ItemsSetFilter
{
    use TypeChecker;

    public function __construct(Collection $filters)
    {
        $this->filters = $this->expectEach($filters)->toBe(Filter::class);
    }
    
    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        foreach ($this->filters->asArray() as $filter) {
            $itemsSet = $filter->filterSet($itemsSet);
        }

        return $itemsSet;
    }
}