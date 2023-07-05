<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\Original\Characters\StringManager;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class InternationalizationHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        load_plugin_textdomain(
            $textDomain = 'coupons-plus-international', 
            $deprecated = false, 
            $plugin_rel_path = StringManager::create(Env::directory())
                                            ->explode(DIRECTORY_SEPARATOR)
                                            ->last()
                                            ->ensureRight('/international/')
                                            ->get()
        );
        /**
         * Script translations are registered in CouponsPlus\App\Handlers\DashboardScriptsHandler
         */
    }
}