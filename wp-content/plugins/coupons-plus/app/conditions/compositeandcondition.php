<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\TestableComposite;
use CouponsPlus\App\Conditions\Condition;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\App\Export\ExporterFactory;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Utilities\TypeChecker;
use WC_Coupon;

Class CompositeANDCondition implements TestableComposite, ExportableData
{
    use TypeChecker;
    
    protected $conditions;

    public static function createFromData(array $conditionsData, WC_Coupon $coupon) : CompositeANDCondition
    {
        return new static(Collection::create($conditionsData)->map(function($conditionData) use ($coupon) : Condition {
            return Condition::createFromOptions($conditionData, $coupon);
        })->asArray());
    }

    public function __construct(array $conditions)
    {
        $this->conditions = new Collection($this->expectEach($conditions)->toBe(Condition::class));
    }

    /**
     * Returns an empty ItemsSet if a single condition fails
     */
    public function filterSet(ItemsSet $itemsSet) : ItemsSet
    {
        return $this->conditions->reduce(function(ItemsSet $itemsSet, Condition $condition) {
            if (!$itemsSet->isValid()) {
                return $itemsSet;
            }

            return $condition->hasPassed()? $itemsSet : new ItemsSet([]);
        }, $initial = $itemsSet);
    }

    public function getDataToExport()
    {
        return $this->conditions;
    }
}