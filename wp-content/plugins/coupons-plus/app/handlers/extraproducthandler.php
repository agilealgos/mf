<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Offers\ExtraProductsManager;
use CouponsPlus\Original\Events\Handler\EventHandler;

/**
 * This class registers the components
 * that'll apply the discount or remove the items
 * from the items that were added by CouponsPlus\App\Offers\BuiltIn\ExtraProduct
 */
Class ExtraProductHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        new ExtraProductsManager;
    }
}