<?php

use CouponsPlus\Original\Autoloading\Autoloader;
use CouponsPlus\Original\Environment\Env;


require 'original/environment/env.php';

Env::set(__FILE__);

require Env::directory().'original/autoloading/autoloader.php';
require Env::directory().'vendor/autoload.php';

Autoloader::register();