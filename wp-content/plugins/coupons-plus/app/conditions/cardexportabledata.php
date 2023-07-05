<?php

namespace CouponsPlus\App\Conditions;

use CouponsPlus\App\Coupon\Abilities\DashboardCard;
use CouponsPlus\App\Export\Abilities\ExportableData;

Class CardExportableData implements ExportableData
{
    public function __construct(DashboardCard $card)
    {
        $this->card = $card;
    }

    public function getDataToExport()
    {
        return [
            'type' => $this->card::TYPE,
            "options" => $this->card->getLoadedOptions()->asArray()
        ];
    }
}