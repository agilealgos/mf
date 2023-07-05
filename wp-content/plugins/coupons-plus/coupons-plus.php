<?php

use CouponsPlus\Original\Installation\Installator;
use CouponsPlus\Original\Events\Registrator\EventsRegistrator;

/*
Plugin Name: Coupons+
Plugin URI:   https://couponspluspro.com
Description:  Better WooCommerce coupons by Neblabs
Version:      1.4.1
Author:       Neblabs
Author URI:   https://neblabs.com
Text Domain:  coupons-plus-international
Domain Path:  /international
Requires at least: 4.4
Requires PHP: 7.0
*/

/***************************************************
****************************************************
**               
**                            +
**                       + Coupons + 
**                           +
**
**      This is a read-only directory.
**      DO NOT try to edit the contents of this code base as 
**      custom edits will be removed with future updates. If
**      you need to extend the default functionality provided 
**      with the current version of this plugin, please contact
**      support.
**
**      The interfaces in this codebase (classes, event hooks, etc) are meant to be private and 
**      as such are prone to change at any time and without previous notice.
** 
**      It is recommended to browse this code base using a 
**      code editor or an IDE with namespace -> filename support.
**      
**      CouponsPlus logic is located under the app/ directory.
**
**      All classes are namespaced. Namespaces are mapped 1:1 to file 
**      names and directories, with the exception of the ID. 
**      For example, the namespace:
**      -- CouponsPlus\App\Handlers\CouponsManagerHandler
**      is mapped to a class with the filename: 
**      -- app/handlers/couponsmanagerhandler.php
**      
**      All CouponsPlus event handlers are registered at: app/events/actions.php
**
**      Third Party packages are located under the vendor/ directory and are autoloaded
**      using Composer's autoloader.  
**
**      This plugin heavily relies on the Composite pattern for applying offers based on
**      discounts and filters. This is an object-oriented program.
**      ALL CONDITIONS AND FILTERS FROM THIS PLUGIN
**      ARE EXECUTED AFTER ANY OF THE DEFAULT WOOCOMMERCE RESTRICTIONS AND LIMITS,
**      like 'Usage limit per coupon', 'Usage limit per user', 'Individual use only', 'Allowed emails', ETC
** 
**      Requires WooCommerce: 3.3.2 + up
**
*****************************************************
****************************************************/

require_once 'bootstrap.php';

(object) $installator = new Installator;

(object) $eventsRegistrator = new EventsRegistrator;

$eventsRegistrator->registerEvents();