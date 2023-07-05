<?php

namespace CouponsPlus\App\Coupon\Abilities;

use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\MappedObject;

Interface DashboardCard extends ExportableData
{
    public static function getName() : string;
    public static function getDescription() : string;
    public static function getOptions() : Collection;
    public static function exportDefault() : MappedObject;
    public function getLoadedOptions() : MappedObject;
}