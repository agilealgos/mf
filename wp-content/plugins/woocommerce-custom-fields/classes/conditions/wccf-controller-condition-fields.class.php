<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition fields controller
 *
 * @class WCCF_Controller_Condition_Fields
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Controller_Condition_Fields')) {

class WCCF_Controller_Condition_Fields extends RightPress_Controller_Condition_Fields
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    // Conditions are first level items in this plugin
    protected $is_second_level_item = false;

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

WCCF_Controller_Condition_Fields::get_instance();

}
