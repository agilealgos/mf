<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Condition Field: Select - Other Field ID
 *
 * @class WCCF_Condition_Field_Select_Other_Field_ID
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Condition_Field_Select_Other_Field_ID')) {

class WCCF_Condition_Field_Select_Other_Field_ID extends RightPress_Condition_Field_Select
{

    protected $plugin_prefix = WCCF_PLUGIN_PRIVATE_PREFIX;

    protected $key = 'other_field_id';

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
     * Display field
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return void
     */
    public function display($context, $alias = 'condition')
    {

        // Get field attributes
        $attributes = $this->get_field_attributes($context, $alias);

        // Maybe add option data
        $option_data = array();

        // Iterate over options
        if (!empty($attributes['options'])) {
            foreach ($attributes['options'] as $key => $value) {

                // Get field
                if ($field = WCCF_Field_Controller::cache($key)) {

                    // Add field type to option data
                    $option_data[$key]['wccf-condition-other-field-type'] = $field->get_field_type();
                }
            }
        }

        // Set option data
        if (!empty($option_data)) {
            $attributes['option_data'] = $option_data;
        }

        // Print field
        RightPress_Forms::select($attributes, false, $this->is_grouped);
    }

    /**
     * Get options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        global $post;

        $post_type = null;

        // Get post type from post object
        if (is_a($post, 'WP_Post') && WCCF_Field_Controller::wp_post_is_field($post)) {
            $post_type = get_post_type($post);
        }
        // Post is not defined during field duplication process, therefore we must get it from request data
        else if (!empty($_REQUEST['wccf_duplicate']) && !empty($_REQUEST['post_type'])) {
            $post_type = $_REQUEST['post_type'];
        }

        // Post type is not defined
        if ($post_type === null) {
            return array();
        }

        // Get field context
        $context = str_replace('wccf_', '', $post_type);

        // Get current field id
        $field_id = (is_object($post) && !empty($post->ID)) ? $post->ID : null;

        // Get field list
        return WCCF_Field_Controller::get_all_field_list_by_context($context, array('enabled', 'disabled'), $field_id);
    }

    /**
     * Validate field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return bool
     */
    public function validate($posted, $condition, $method_option_key)
    {

        if (isset($posted[$this->key])) {

            // Get options
            $options = $this->get_options();

            // Check if option exists
            if (isset($options[(int) $posted[$this->key]])) {
                return true;
            }
        }

        return false;
    }





}

WCCF_Condition_Field_Select_Other_Field_ID::get_instance();

}
