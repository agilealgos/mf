<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Shipping - Zone
 *
 * @class WCCF_Condition_Shipping_Zone
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Shipping_Zone')) {

class WCCF_Condition_Shipping_Zone extends RightPress_Condition_Shipping_Zone
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'checkout_field',
    );

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }





}

WCCF_Condition_Shipping_Zone::get_instance();

}
