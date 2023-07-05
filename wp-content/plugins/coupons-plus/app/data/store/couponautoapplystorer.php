<?php

namespace CouponsPlus\App\Data\Store;

use CouponsPlus\App\Coupon\Rows;
use CouponsPlus\App\Export\DataExporter;
use CouponsPlus\Original\Environment\Env;

Class CouponAutoApplyStorer extends PostStorer
{
    protected function getPostFieldName() : string
    {
        return Env::getWithPrefix('coupon_auto_apply_is_enabled');
    }
    
    protected function storeData()
    {
        (string) $isenabled = $this->getData();
        
        if (!in_array($isenabled, ['yes', 'no'])) {
            return;
        }

        $this->updateMeta(
            $key = Env::getWithPrefix('coupon_auto_apply_is_enabled'),
            $value = $isenabled
        );
    }
}