<?php

namespace CouponsPlus\App\Handlers;

use CouponsPlus\App\Tracking\QueryStringUserHistoryRepository;
use CouponsPlus\Original\Events\Handler\EventHandler;

Class URLParameterHandler extends EventHandler
{
    protected $numberOfArguments = 1;
    protected $priority = 10;

    public function execute()
    {
        if (is_admin()) {
            return;
        }
        
        QueryStringUserHistoryRepository::updateParameters();        
    }
}