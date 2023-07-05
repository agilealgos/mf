<?php

namespace CouponsPlus\App\Coupon\Meta;

use CouponsPlus\App\Coupon\ColumnsRegistrator;
use CouponsPlus\App\Coupon\Columns\SimpleColumn;
use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Abstract Class ColumnMeta extends CouponComponentMeta
{
    abstract public function getName() : string;
    abstract public function getDescription() : string;
    abstract public function useOneOffersSetForAllContexts() : bool;
    abstract public function isOffersColumn() : bool;
    abstract public function getPreferredColumnConversion() : string;

    public static function getDefaultOptions() : Collection
    {
        return new Collection([
            'testableType' => Types::STRING()->withDefault('conditions')
                                             ->allowed(['conditions', 'filters']),
            'type' => Types::STRING()->withDefault(SimpleColumn::TYPE)
                                     ->allowed(ColumnsRegistrator::get()->all()->map(function(string $Column) : string {
                                        return $Column::TYPE;
                                     })->asArray()),
            'defaultOffers' => Types::COLLECTION,
            'contexts' => Types::COLLECTION
        ]);
    }

    public function getOptions() : Collection
    {
        return static::getDefaultOptions();
    }
}