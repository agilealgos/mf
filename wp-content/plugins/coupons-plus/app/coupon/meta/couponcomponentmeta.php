<?php

namespace CouponsPlus\App\Coupon\Meta;

use CouponsPlus\Original\Collections\Collection;

Abstract Class CouponComponentMeta
{
    abstract public function getOptions() : Collection;
}