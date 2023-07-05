<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Customer - Meta
 *
 * @class WCCF_Condition_Customer_Meta
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Customer_Meta')) {

class WCCF_Condition_Customer_Meta extends RightPress_Condition_Customer_Meta
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

WCCF_Condition_Customer_Meta::get_instance();

}
