<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Offers\BuiltIn\BundlePrice;
use CouponsPlus\App\Offers\BuiltIn\Discount;
use CouponsPlus\App\Offers\BuiltIn\ExtraProduct;
use CouponsPlus\App\Offers\BuiltIn\ShippingDiscount;
use CouponsPlus\App\Offers\OffersRegistrator;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class BuiltInOffersRegistratorHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute(OffersRegistrator $offersRegistrator)
    {
        $offersRegistrator->register(Discount::class);
        $offersRegistrator->register(ExtraProduct::class);
        $offersRegistrator->register(BundlePrice::class);
        $offersRegistrator->register(ShippingDiscount::class);
    }
}