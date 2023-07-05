<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Customer - Capability
 *
 * @class WCCF_Condition_Customer_Capability
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Customer_Capability')) {

class WCCF_Condition_Customer_Capability extends RightPress_Condition_Customer_Capability
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_field',
        'checkout_field',
        'user_field',
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

WCCF_Condition_Customer_Capability::get_instance();

}
