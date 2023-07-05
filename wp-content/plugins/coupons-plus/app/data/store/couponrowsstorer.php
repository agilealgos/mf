<?php

namespace CouponsPlus\App\Data\Store;

use CouponsPlus\App\Coupon\Rows;
use CouponsPlus\App\Export\DataExporter;
use CouponsPlus\Original\Environment\Env;

Class CouponRowsStorer extends PostStorer
{
    protected function getPostFieldName() : string
    {
        return Env::getWithPrefix('rows');
    }
    
    protected function storeData()
    {
        (string) $rowsData = ($this->getData());

        (object) $rows = Rows::createFromOptions(
            $rowsData,
            new \WC_Coupon
        );

        (object) $dataExporter = new DataExporter();

        $this->updateMeta(
            $key = Env::getWithPrefix('rows'),
            // sanitized individually
            $value = $dataExporter->export($rows)
        );
    }
}