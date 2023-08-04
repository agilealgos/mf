<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Cart Items - Product Variations
 *
 * @class WCCF_Condition_Cart_Items_Product_Variations
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Cart_Items_Product_Variations')) {

class WCCF_Condition_Cart_Items_Product_Variations extends RightPress_Condition_Cart_Items_Product_Variations
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

WCCF_Condition_Cart_Items_Product_Variations::get_instance();

}
