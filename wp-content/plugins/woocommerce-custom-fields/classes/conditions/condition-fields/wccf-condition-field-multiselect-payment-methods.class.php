<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Multiselect - Payment Methods
 *
 * @class WCCF_Condition_Field_Multiselect_Payment_Methods
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Multiselect_Payment_Methods')) {

class WCCF_Condition_Field_Multiselect_Payment_Methods extends RightPress_Condition_Field_Multiselect_Payment_Methods
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

WCCF_Condition_Field_Multiselect_Payment_Methods::get_instance();

}
