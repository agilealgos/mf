<?php

namespace CouponsPlus\App\Coupon;

use CouponsPlus\App\Conditions\ItemsSet;
use CouponsPlus\App\Coupon\Abilities\OffersSetFinder;
use CouponsPlus\App\Coupon\CartComponentsSet;
use CouponsPlus\App\Coupon\Context;
use CouponsPlus\App\Coupon\CouponComponent;
use CouponsPlus\App\Coupon\Meta\ColumnMeta;
use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\App\Offers\OffersSet;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use CouponsPlus\Original\Utilities\TypeChecker;
use WC_Coupon;

Abstract Class Column extends CouponComponent implements OffersSetFinder
{
    use TypeChecker;

    abstract public static function getColumnMeta() : ColumnMeta;

    protected $contexts;
    
    public static function getMeta() : CouponComponentMeta
    {
        return static::getColumnMeta();
    }

    public static function createFromOptions($options, WC_Coupon $coupon) : CouponComponent
    {
        (object) $JSONMapper = new JSONMapper(
            ColumnMeta::getDefaultOptions()->asArray()
        );
        (object) $preOptions = $JSONMapper->smartMap($options);

        return static::createColumn($preOptions, $options, $coupon);
    }

    public static function createColumn(MappedObject $preOptions, $options, WC_Coupon $coupon) #: Column
    {
        (string) $Column = ColumnsRegistrator::get()->all()->find(function(string $Column) use ($preOptions) : string {
            return $preOptions->type->is($Column::TYPE);
        });

        return new $Column($preOptions->contexts->map(function($contextData) use ($preOptions, $coupon, $Column) : Context {
            return Context::createExtended(
                $contextData, 
                $preOptions,
                $Column::getMeta(),
                $coupon
            );
        })->asArray(), $preOptions);
    }
    
    public function __construct(array $contexts, MappedObject $options = null)
    {
        $this->contexts = new Collection($this->expect($contexts)->toBe(Context::class));
        $this->options = $options;
    }

    public function getDataToExport()
    {
        return [
            "type" => $this->options->type,
            "testableType" => $this->options->testableType,
            "defaultOffers" => $this->options->defaultOffers,
            'contexts' => $this->contexts->map(function(Context $context) : array {
                return Collection::create($context->getDataToExport())->map(function($item) {
                    if ($item instanceof OffersSet && static::getMeta()->useOneOffersSetForAllContexts()) {
                        return [];
                    }

                    return $item;
                })->asArray();
            })
        ];
    }
    
    public static function create(MappedObject $options, WC_Coupon $coupon)
    {
        // not used
    }
}