<?php

namespace CouponsPlus\Original\Installation;

use CouponsPlus\App\Installators\ConcreteInstallator;
use CouponsPlus\Original\Environment\Env;

Class Installator
{
    public function __construct()
    {
        register_activation_hook(
            Env::absolutePluginFilePath(), 
            [new ConcreteInstallator, 'install']
        );
    }
}