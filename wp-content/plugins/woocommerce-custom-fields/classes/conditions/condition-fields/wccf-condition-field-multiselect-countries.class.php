<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Countries
 *
 * @class WCCF_Condition_Field_Multiselect_Countries
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Multiselect_Countries')) {

class WCCF_Condition_Field_Multiselect_Countries extends RightPress_Condition_Field_Multiselect_Countries
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

WCCF_Condition_Field_Multiselect_Countries::get_instance();

}
