<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Coupon\ColumnsRegistrator;
use CouponsPlus\App\Coupon\Columns\ANDColumn;
use CouponsPlus\App\Coupon\Columns\ORColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\ANDOffersColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\MultiOffersColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\OROffersColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\SimpleOfferColumn;
use CouponsPlus\App\Coupon\Columns\OfferColumns\TieredOffersColumn;
use CouponsPlus\App\Coupon\Columns\SimpleColumn;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class BuiltInColumnsRegistrator extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute(ColumnsRegistrator $columnsRegistrator)
    {
        $columnsRegistrator->register(SimpleColumn::class);
        $columnsRegistrator->register(ANDColumn::class);
        $columnsRegistrator->register(ORColumn::class);


        $columnsRegistrator->register(SimpleOfferColumn::class);
        $columnsRegistrator->register(ANDOffersColumn::class);
        $columnsRegistrator->register(OROffersColumn::class);

        $columnsRegistrator->register(TieredOffersColumn::class);
        $columnsRegistrator->register(MultiOffersColumn::class);
    }
}