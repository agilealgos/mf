<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Product Variations
 *
 * @class WCCF_Condition_Field_Multiselect_Product_Variations
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Multiselect_Product_Variations')) {

class WCCF_Condition_Field_Multiselect_Product_Variations extends RightPress_Condition_Field_Multiselect_Product_Variations
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

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

WCCF_Condition_Field_Multiselect_Product_Variations::get_instance();

}
