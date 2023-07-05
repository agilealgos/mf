<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Customer - Logged In
 *
 * @class WCCF_Condition_Customer_Logged_In
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Customer_Logged_In')) {

class WCCF_Condition_Customer_Logged_In extends RightPress_Condition_Customer_Logged_In
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

WCCF_Condition_Customer_Logged_In::get_instance();

}
