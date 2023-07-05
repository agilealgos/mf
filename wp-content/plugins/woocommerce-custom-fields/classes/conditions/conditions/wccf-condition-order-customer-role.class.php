<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Order Customer - Role
 *
 * @class WCCF_Condition_Order_Customer_Role
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Order_Customer_Role')) {

class WCCF_Condition_Order_Customer_Role extends RightPress_Condition_Order_Customer_Role
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'order_field',
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

WCCF_Condition_Order_Customer_Role::get_instance();

}
