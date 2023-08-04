<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Text - Meta Key
 *
 * @class WCCF_Condition_Field_Text_Meta_Key
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Text_Meta_Key')) {

class WCCF_Condition_Field_Text_Meta_Key extends RightPress_Condition_Field_Text_Meta_Key
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

WCCF_Condition_Field_Text_Meta_Key::get_instance();

}
