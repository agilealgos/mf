<?php

namespace CouponsPlus\App\Coupon\Meta;

use CouponsPlus\App\Coupon\Meta\CouponComponentMeta;
use CouponsPlus\Original\Collections\Collection;
use CouponsPlus\Original\Collections\Mapper\Types;

Class ContextMeta extends CouponComponentMeta
{
    public function getOptions() : Collection
    {
        return new Collection([
            'conditionsOrFilters' => Types::COLLECTION,
            'offers' => Types::COLLECTION
        ]);
    }
}