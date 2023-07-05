<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Decimal - Decimal
 *
 * @class WCCF_Condition_Field_Decimal_Decimal
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Decimal_Decimal')) {

class WCCF_Condition_Field_Decimal_Decimal extends RightPress_Condition_Field_Decimal_Decimal
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

WCCF_Condition_Field_Decimal_Decimal::get_instance();

}
