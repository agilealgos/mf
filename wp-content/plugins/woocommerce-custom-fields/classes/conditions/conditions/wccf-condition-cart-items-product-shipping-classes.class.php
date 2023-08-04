<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Cart Items - Product Shipping Classes
 *
 * @class WCCF_Condition_Cart_Items_Product_Shipping_Classes
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Cart_Items_Product_Shipping_Classes')) {

class WCCF_Condition_Cart_Items_Product_Shipping_Classes extends RightPress_Condition_Cart_Items_Product_Shipping_Classes
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

WCCF_Condition_Cart_Items_Product_Shipping_Classes::get_instance();

}
