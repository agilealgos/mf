<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Other - Other Custom Field
 *
 * @class WCCF_Condition_Other_Other_Custom_Field
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Other_Other_Custom_Field')) {

class WCCF_Condition_Other_Other_Custom_Field extends RightPress_Condition_Other
{

    protected $key          = 'other_custom_field';
    protected $method       = 'field';
    protected $fields       = array(
        'before'    => array('other_field_id'),
        'after'     => array('text'),
    );
    protected $main_field   = 'text';
    protected $position     = 10;

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_field',
        'product_prop',
        'checkout_field',
        'order_field',
        'user_field',
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

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return __('Other field', 'rp_wccf');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        return isset($params['other_custom_field_value']) ? $params['other_custom_field_value'] : null;
    }





}

WCCF_Condition_Other_Other_Custom_Field::get_instance();

}
