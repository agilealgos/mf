<?php

namespace CouponsPlus\App\Installators;

use CouponsPlus\App\Data\Settings\Settings;
use CouponsPlus\Original\Data\Drivers\WordPressDatabaseDriver;
use CouponsPlus\Original\Environment\Env;
use CouponsPlus\Original\Installation\Installator;

Class ConcreteInstallator
{
    protected $applicationDatabase;

    public function __construct()
    {
        (string) $ApplicationDatabase = Env::settings()->schema->applicationDatabase;

        $this->applicationDatabase = new $ApplicationDatabase(new WordPressDatabaseDriver);   
    }
    
    public function install()
    {
        $this->applicationDatabase->install();
    }

    public function update()
    {
        $this->applicationDatabase->update();   
    }
}