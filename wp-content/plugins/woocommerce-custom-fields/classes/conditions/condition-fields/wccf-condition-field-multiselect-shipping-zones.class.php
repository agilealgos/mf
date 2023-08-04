<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Shipping Zones
 *
 * @class WCCF_Condition_Field_Multiselect_Shipping_Zones
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Multiselect_Shipping_Zones')) {

class WCCF_Condition_Field_Multiselect_Shipping_Zones extends RightPress_Condition_Field_Multiselect_Shipping_Zones
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

WCCF_Condition_Field_Multiselect_Shipping_Zones::get_instance();

}
