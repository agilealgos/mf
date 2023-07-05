<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\Abilities\TestableComposite;
use CouponsPlus\App\Conditions\CompositeANDCondition;
use CouponsPlus\App\Conditions\CompositeFilter;
use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Abilities\OffersSetFinder;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\CouponComponent;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\App\Coupon\Meta\ContextMeta;
use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use WC_Coupon;

Class Context extends CouponComponent implements OffersSetFinder
{
    protected $conditionsOrFiltersComposite;
    protected $offersSet;

    public static function getMeta() : CouponComponentMeta
    {
        return new ContextMeta;
    }

    public static function createExtended($options, MappedObject $columnOptions, ColumnMeta $columnMeta, WC_Coupon $coupon)
    {
         (object) $JSONMapper = new JSONMapper(
            static::getMeta()->getOptions()->asArray()
        );

        $options = $JSONMapper->smartMap($options);

        (object) $TestableComposite = $columnOptions->testableType->is('conditions')? CompositeANDCondition::class : CompositeFilter::class;
        (array) $offersData = ($columnMeta->useOneOffersSetForAllContexts()? $columnOptions->defaultOffers : $options->offers)->asArray();

        return new static(
            $TestableComposite::createFromData($options->conditionsOrFilters->asArray(), $coupon), 
            OffersSet::createFromData($offersData, $coupon)
        );
    }
    
    public function __construct(TestableComposite $conditionsOrFiltersComposite, OffersSet $offersSet)
    {
        $this->conditionsOrFiltersComposite = $conditionsOrFiltersComposite;
        $this->offersSet = $offersSet;
    }

    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        (object) $itemsSet = $this->conditionsOrFiltersComposite->filterSet($itemsSet);

        if ($itemsSet->isValid() && $this->offersSet->isValid()) {
            $this->offersSet->getItemsSet()->addItems($itemsSet);

            return $this->offersSet;
        }

        // if $itemsSet's not valid, this will be empty
        // if $itemsSet is valid but offersset is not, this will return the 
        // valid $itemsSet, for when filters return valid items and have no offer associated.
        // all effectively in just 4 lines of executable code. How cool is that? Thanks, Composite!
        return $itemsSet;
    }

    public function getDataToExport()
    {
        return [
            'conditionsOrFilters' => $this->conditionsOrFiltersComposite,
            "offers" => $this->offersSet
        ];
    }

    public static function create(MappedObject $options, \WC_Coupon $coupon)
    {
    }
}