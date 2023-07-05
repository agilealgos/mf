<?php

namespace CouponsPlus\App\Coupon\Meta;

use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class RowsMeta extends CouponComponentMeta
{
    public function getOptions() : Collection
    {
        return new Collection([
            'rows' => Types::COLLECTION
        ]);
    }
}