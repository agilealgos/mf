<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Coupon\CouponsManager;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class CouponsManagerHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        (object) $couponsManager = new CouponsManager;   

        $couponsManager->registerEvents();
    }
}