<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Abilities\OffersSetFinder;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\CouponComponent;
use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\App\Coupon\Meta\RowsMeta;
use CouponsPlus\App\Coupon\Row;
use CouponsPlus\App\Offers\Abilities\Offers;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\MappedObject;
use CouponsPlus\Original\Utilities\TypeChecker;
use WC_Coupon;

Class Rows extends CouponComponent implements OffersSetFinder
{
    use TypeChecker;
    
    protected $rows;

    public static function getMeta() : CouponComponentMeta
    {
        return new RowsMeta;
    }

    public static function create(MappedObject $options, WC_Coupon $coupon) : Rows
    {
        return new static($options->rows->map(function($row) use ($coupon) : Row {
            return Row::createFromOptions($row, $coupon);
        })->asArray());
    }

    public function __construct(array $rows)
    {
        $this->rows = new Collection($this->expect($rows)->toBe(Row::class));   
    }
    
    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        foreach ($this->rows->asArray() as $row) {
            (object) $cartComponentsSet = $row->findOffers($itemsSet);

            if ($cartComponentsSet->isValid() && $cartComponentsSet instanceof Offers) {
                return $cartComponentsSet;
            }
        }

        return new OffersSet([]);
    }

    public function hasAny() : bool
    {
        return $this->rows->haveAny();   
    }

    public function getDataToExport()
    {
        return [
            'rows' => $this->rows
        ];
    }
}