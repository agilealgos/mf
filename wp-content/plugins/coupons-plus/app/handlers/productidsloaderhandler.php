<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Data\Products\ProductIdsLoader;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class ProductIdsLoaderHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        if (Env::isTesting()) {
            return;
        }

        ProductIdsLoader::registerEvents();   
    }
}