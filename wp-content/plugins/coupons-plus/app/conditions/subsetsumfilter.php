<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\Filter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\Original\Collections\Collection;

Class SubsetSumFilter implements ItemsSetFilter
{
    public function __construct(Collection $filters)
    {
        $this->filters = $this->expectEach($filters)->toBe(Filter::class);
    }

    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        foreach ($this->filters->getValues()->asArray() as $key => $filter) {
            (boolean) $isTheLastFilter = $this->filters->lastKey() === $key;

            if ($isTheLastFilter) {
                /**
                 * Subsetsum can be bery expensive in terms of memory
                 * since it can potentially create hundreds if not thousands of 
                 * possible combinations.
                 * 
                 * If there are no more filters to check against,
                 * there is no reason to create a subsetsum set.
                 * Just get me the first matching set.
                 */
                (object) $itemsSetCollection = new Collection([$filter->filterSet($itemsSet)]);
            } else {
                (object) $itemsSetCollection = $filter->filterSetSubsetSum($itemsSet);
            }

            foreach ($itemsSetCollection->asArray() as $itemsSet) {
                (object) $subsetSubFilter = new static(
                    $this->filters->exceptFirst($key + 1)
                );

                (object) $filteredItemsSet = $subsetSubFilter->filterSet($itemsSet);

                if ($filteredItemsSet->isTheSameAs($itemsSet)) {
                    return $filteredItemsSet;
                }
            }
        }

        # no match
        return new ItemsSet([]);
    }
}