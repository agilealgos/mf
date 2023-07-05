<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Abilities\OffersSetFinder;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Column;
use CouponsPlus\App\Coupon\CouponComponent;
use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\App\Coupon\Meta\RowMeta;
use CouponsPlus\App\Offers\Abilities\Offers;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\MappedObject;
use CouponsPlus\Original\Utilities\TypeChecker;
use WC_Coupon;

Class Row extends CouponComponent implements OffersSetFinder
{
    use TypeChecker;

    protected $columns;
    
    public static function getMeta() : CouponComponentMeta
    {
        return new RowMeta;
    }

    public static function create(MappedObject $options, WC_Coupon $coupon) # : Row
    {
        return new static($options->columns->map(function($columnData) use ($coupon) : Column {
            return Column::createFromOptions($columnData, $coupon);
        })->asArray());
    }

    public function __construct(array $columns)
    {
        $this->columns = new Collection($this->expect($columns)->toBe(Column::class));   
    }
    
    /**
     * Should only return an OffersSet or OffersSetCollection (Offers)
     * but since covariant returns are not supported in PHP 7 point o
     * we can't enforce it on runtime. Oh well.
     */
    public function findOffers(ItemsSet $itemsSet) : CartComponentsSet
    {
        foreach ($this->columns->asArray() as $column) {
            (object) $cartComponentsSet = $column->findOffers($itemsSet);

            if (!$cartComponentsSet->isValid()) {
                return new OffersSet([]);
            }

            if ($cartComponentsSet instanceof Offers) {
                return $cartComponentsSet;
            }
            
            $itemsSet = $cartComponentsSet;
        }

        return new OffersSet([]);
    }   

    public function getDataToExport()
    {
        return [
            'columns' => $this->columns
        ];
    }
}
