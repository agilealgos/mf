<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition: Custom Taxonomy - Product
 *
 * This is a special condition - it is instantiated with different settings for
 * each custom taxonomy that is enabled
 *
 * @class WCCF_Condition_Custom_Taxonomy_Product
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Custom_Taxonomy_Product')) {

class WCCF_Condition_Custom_Taxonomy_Product extends RightPress_Condition_Custom_Taxonomy_Product
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $contexts = array(
        'product_field',
        'product_prop',
    );

    /**
     * Constructor
     *
     * @access public
     * @param string $key
     * @param array $fields
     * @return void
     */
    public function __construct($key, $fields, $label)
    {

        parent::__construct($key, $fields, $label);
    }





}
}
