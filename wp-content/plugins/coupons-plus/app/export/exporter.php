<?php

namespace CouponsPlus\App\Export;

Abstract Class Exporter
{
    abstract public function export(/*Mixed*/ $dataToExport) : string;
}
