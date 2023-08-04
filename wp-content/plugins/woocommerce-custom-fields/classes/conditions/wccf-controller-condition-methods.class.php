<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition methods controller
 *
 * @class WCCF_Controller_Condition_Methods
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Controller_Condition_Methods')) {

class WCCF_Controller_Condition_Methods extends RightPress_Controller_Condition_Methods
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

WCCF_Controller_Condition_Methods::get_instance();

}
