<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class FrontEndScriptsRegistrationHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        // Might be needed in the future but not this time.
        /*
        wp_enqueue_style(
            Env::getwithPrefix('store-styles'), 
            Env::directoryURI().'/app/styles/store.css',
            null,
            $version = Env::settings()->environment === 'development'? time() : '1.2.1'
        );*/
    }
}