<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Conditions\Abilities\Exportable;
use CouponsPlus\App\Conditions\Abilities\Testable;
use CouponsPlus\App\Conditions\Abilities\TestableComposite;
use CouponsPlus\App\Conditions\CardExportableData;
use CouponsPlus\App\Coupon\Abilities\DashboardCard;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\JSONMapper;
use CouponsPlus\Original\Collections\MappedObject;
use WC_Coupon;

Abstract Class Condition implements DashboardCard
{
    protected $options;

    abstract public static function getOptions() : Collection;
    abstract protected function test() : bool;
    #: Extended by classes that need it, variable number of args and called in the constructor
    #abstract protected function setExtraData() : void;
    #abstract protected function onOptionsLoaded() : void;

    public static function exportDefault() : MappedObject
    {
        (object) $condition = new static([], new \WC_Coupon());

        return $condition->options;
    }
    
    public static function createFromOptions($options, WC_Coupon $coupon) : Condition
    {
        return ConditionsRegistrator::createFromOptions($options, $coupon);
    }

    public function __construct(/*array|string*/ $options, ...$extraData)
    {
        (object) $JSONMapper = new JSONMapper(
            array_merge(
                static::getOptions()->asArray(), 
                $this->getDefaultOptions()->asArray()
            )
        );

        $this->options = $JSONMapper->smartMap($options);

        if (method_exists($this, 'setExtraData')) {
            $this->setExtraData(...$extraData);
        }

        if (method_exists($this, 'onOptionsLoaded')) {
            $this->onOptionsLoaded();
        }
    }

    public function getDefaultOptions() : Collection
    {
        return new Collection([]);   
    }

    public function hasPassed()
    {
        return $this->test();   
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