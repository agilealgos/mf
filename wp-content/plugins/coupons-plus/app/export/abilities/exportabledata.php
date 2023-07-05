<?php

namespace CouponsPlus\App\Export\Abilities;

Interface ExportableData
{
    public function getDataToExport() /* : Mixed */;
}