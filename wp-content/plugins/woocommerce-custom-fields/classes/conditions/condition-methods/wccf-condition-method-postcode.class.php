<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Method: Postcode
 *
 * @class WCCF_Condition_Method_Postcode
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Method_Postcode')) {

class WCCF_Condition_Method_Postcode extends RightPress_Condition_Method_Postcode
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

WCCF_Condition_Method_Postcode::get_instance();

}
