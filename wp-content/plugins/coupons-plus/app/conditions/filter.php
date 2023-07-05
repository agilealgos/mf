<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\ItemsSetFilter;
use CouponsPlus\App\Conditions\CardExportableData;
use CouponsPlus\App\Conditions\FiltersRegistrator;
use CouponsPlus\App\Conditions\SubsetSumFilter;
use CouponsPlus\App\Coupon\Abilities\DashboardCard;
use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use WC_Coupon;

Abstract Class Filter implements ItemsSetFilter, DashboardCard
{
    const TYPE = 'MUSTBEOVERRIDDEN';

    protected $localSet;
    protected $options;
    
    abstract public static function getOptions() : Collection;
    #abstract protected function onOptionsLoaded() : void;

    public static function createFromOptions($options, WC_Coupon $coupon) : Filter
    {
        return FiltersRegistrator::createFromOptions($options, $coupon);
    }

    public static function exportDefault() : MappedObject
    {
        (object) $filter = new static([]);

        return $filter->options;
    }

    public function __construct(/*array|string*/ $options)
    {
        $this->cache = new MemoryCache;
        /*(object) $categoriesFilter = new InCategories($options);

        (object) $newset = $categoriesFilter->filterSet($itemsSet);

        (object) $set = new CompositeSet($categoriesFilter, $productsFilter);

        (object) $newSet = $set->filterSet($itemsSet);
*/

        (object) $JSONMapper = new JSONMapper(
            array_merge(
                static::getOptions()->asArray(), 
                $this->getDefaultOptions()->asArray()
            )
        );

        $this->options = $JSONMapper->smartMap($options);

        if (method_exists($this, 'onOptionsLoaded')) {
            $this->onOptionsLoaded();
        }
    }

    public function isIntersected() : bool
    {
        return static::SET === IntersectionFilter::class;
    }

    public function isSubsetSum() : bool
    {
        return static::SET === SubsetSumFilter::class;
    }

    public function isSubset() : bool
    {
        return static::SET === SubsetFilter::class;
    }

    public function getDefaultOptions() : Collection
    {
        return new Collection([]);
    }

    public function getLoadedOptions() : MappedObject
    {
        return $this->options;   
    }
    
    public function getDataToExport()
    {
        return new CardExportableData($this);
    }
}