<?php

namespace CouponsPlus\App\Export;

use CouponsPlus\App\Export\Abilities\ExportableData;
use CouponsPlus\Original\Collections\Collection;

Class DataExporter extends Exporter
{
    public function export(/*Mixed*/ $dataToExport) : string
    {
        (boolean) $collectionHasBeenExported = false;

        if ($this->isCollection($dataToExport)) {
            $dataToExport = Collection::create($dataToExport)->map([$this, 'exportItem'])->asArray();
            $collectionHasBeenExported = true;
        }

        return json_encode($this->exportItem($dataToExport, $collectionHasBeenExported));
    }

    public function exportItem($item, bool $collectionHasBeenExported = false)
    {
        switch (true) {
            case $item instanceof ExportableData:
                return json_decode($this->export($item->getDataToExport()));
                break;
            case $this->isCollection($item) && !$collectionHasBeenExported:
                return json_decode($this->export($item));
                break;
        }

        return $item;
    }

    protected function isCollection($dataToExport) : bool
    {
        return is_array($dataToExport) || $dataToExport instanceof Collection;   
    }
    
}