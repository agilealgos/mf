<?php

namespace CouponsPlus\App\Events;

use CouponsPlus\Original\Cache\MemoryCache;
use CouponsPlus\Original\Events\Handler\GlobalEventsValidator;

Class CustomGlobalEventsValidator extends GlobalEventsValidator
{
    protected static $messageHasBeenRegistered = false;

    public function canBeExecuted() : bool
    {
        (boolean) $WooCommerceIsActive = class_exists(\WooCommerce::class) || function_exists('WC');

        if ($WooCommerceIsActive) {
            return true;
        }

        if (!static::$messageHasBeenRegistered) {
            static::$messageHasBeenRegistered = true;

            add_action( 'admin_notices', function () {
                (string) $message = __('Almost Ready! WooCommerce needs to be installed and activated for Coupons+ to work.', 'coupons-plus-international');
             
                print '<div class="notice notice-error"><p>'.esc_html($message).'</p></div>';
            });

        }
        
        return false;
    }
}