<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Product Property - Shipping Class
 *
 * @class WCCF_Condition_Product_Property_Shipping_Class
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Product_Property_Shipping_Class')) {

class WCCF_Condition_Product_Property_Shipping_Class extends RightPress_Condition_Product_Property_Shipping_Class
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_field',
        'product_prop',
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

WCCF_Condition_Product_Property_Shipping_Class::get_instance();

}
