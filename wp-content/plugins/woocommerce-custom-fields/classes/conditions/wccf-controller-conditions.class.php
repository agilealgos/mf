<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Conditions controller
 *
 * @class WCCF_Controller_Conditions
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Controller_Conditions')) {

class WCCF_Controller_Conditions extends RightPress_Controller_Conditions
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

        // Assign fields and methods controllers
        $this->condition_fields_controller  = WCCF_Controller_Condition_Fields::get_instance();
        $this->condition_methods_controller = WCCF_Controller_Condition_Methods::get_instance();

        // Construct parent
        parent::__construct();
    }

    /**
     * Generate custom taxonomy conditions
     *
     * @access public
     * @return void
     */
    public function generate_custom_taxonomy_conditions()
    {

        // Get enabled taxonomies
        if ($enabled_taxonomies = WCCF_Settings::get('conditions_custom_taxonomies')) {

            // Generate conditions
            $this->generate_custom_taxonomy_conditions_internal($enabled_taxonomies, 'WCCF_Condition_Custom_Taxonomy_Product', 'WCCF_Condition_Field_Multiselect_Custom_Taxonomy');
        }
    }

    /**
     * Check if amounts in conditions include tax
     *
     * @access public
     * @return bool
     */
    public static function amounts_include_tax()
    {

        return (wc_tax_enabled() && WCCF_Settings::get('condition_amounts_include_tax'));
    }

    /**
     * Get supported other field condition methods by field types
     *
     * @access public
     * @return array
     */
    public static function get_other_field_condition_methods_by_field_types()
    {

        $methods = array();

        // Iterate over field types
        foreach (WCCF_Field_Controller::get_field_type_list() as $field_type => $properties) {
            if (!empty($properties['is_page_element'])) {
                $methods[$field_type] = $properties['other_field_condition_methods'];
            }
        }

        return $methods;
    }

    /**
     * Check frontend conditions from submitted field data
     *
     * @access public
     * @param object $field
     * @param array $fields
     * @param array $values
     * @return bool
     */
    public static function check_frontend_conditions($field, $fields, $values)
    {

        $conditions_to_check    = 0;
        $conditions_checked     = 0;

        // Field contains disabled condition
        if (WCCF_Controller_Conditions::field_contains_disabled_condition($field)) {
            return false;
        }

        // Iterate over conditions
        foreach($field->get_conditions() as $condition) {

            // Other custom field
            if ($condition['type'] === 'other__other_custom_field') {

                $conditions_to_check++;

                // Iterate over fields
                foreach ($fields as $field) {

                    // Check if we need to check condition against current field
                    if ((int) $condition['other_field_id'] === $field->get_id()) {

                        $conditions_checked++;

                        // Get value
                        $value = $field->get_value_from_values_array($values);

                        // Check condition
                        $result = WCCF_Controller_Conditions::condition_is_matched(array(
                            'condition'                 => $condition,
                            'other_custom_field_value'  => ($value !== false ? $value : null),
                        ));

                        // Condition check failed
                        if (!$result) {
                            return false;
                        }
                    }
                }

                // Not all conditions were checked as some fields were not present - return value depends on whether condition method is positive or negative
                if ($conditions_checked < $conditions_to_check) {
                    return in_array($condition['method_option'], array('is_empty', 'does_not_contain', 'does_not_equal', 'is_not_checked'), true);
                }
            }
        }

        return true;
    }

    /**
     * Field contains disabled condition
     *
     * @access public
     * @param object $field
     * @return bool
     */
    public static function field_contains_disabled_condition($field)
    {

        // Get disabled condition flags
        $disabled_flags = WCCF_Controller_Conditions::get_all_disabled_condition_flags();

        // Iterate over conditions
        foreach($field->get_conditions() as $condition) {

            // Condition is disabled
            if (array_intersect(array_keys($condition), $disabled_flags)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Filter out fields that do not match conditions
     * Also determines conditions that need to be passed to Javascript
     *
     * @access public
     * @param array $all_fields
     * @param array $params
     * @param mixed $checkout_position
     * @param bool $first_only
     * @return array
     */
    public static function filter_fields($all_fields, $params = array(), $checkout_position = null, $first_only = false)
    {

        $fields = array();

        // Iterate over passed fields
        foreach ($all_fields as $field_id => $field) {

            // Check if we are on Checkout page
            if ($checkout_position !== null) {

                // Check if this is a correct spot for this Checkout field
                if ($checkout_position !== $field->get_position()) {
                    continue;
                }
            }

            // Track if we need to add this field
            $is_ok = true;

            // Iterate over conditions
            foreach ($field->get_conditions() as $condition_key => $condition) {

                // Skip frontend conditions
                if ($condition['type'] === 'other__other_custom_field') {
                    continue;
                }

                // Check if condition is matched
                if (!WCCF_Controller_Conditions::condition_is_matched(array_merge($params, array('condition' => $condition)))) {
                    $is_ok = false;
                    break;
                }
            }

            // Maybe add this field to a set of fields for return
            if ($is_ok) {

                // Add to fields array
                $fields[$field_id] = $field;

                // Break from loop if only one field is required
                if ($first_only) {
                    break;
                }
            }
        }

        // Allow developers to do custom filtering and return
        return apply_filters('wccf_filter_fields', $fields, $all_fields, $params, $checkout_position, $first_only);
    }

    /**
     * Get items for display statically
     *
     * @access public
     * @param string $context
     * @return array
     */
    public static function get_items_for_display($context = null)
    {

        global $post;

        // Get controller instance
        $instance = WCCF_Controller_Conditions::get_instance();

        // Get conditions for display
        $conditions = $instance->get_items_for_display_internal($context);

        // Check if field can be determined
        if (WCCF_Field_Controller::wp_post_is_field($post) && $post->ID) {

            // Get all field ids of this type
            $fields = WCCF_Field_Controller::get_all_by_context($context, array('enabled', 'disabled'));
            $field_ids = array_keys($fields);

            // Other fields do not exist
            if (empty($field_ids) || (count($field_ids) === 1 && in_array((int) $post->ID, $field_ids, true))) {

                // Check if other custom field condition is set
                if (isset($conditions['other']['options']['other_custom_field'])) {

                    // Unset other custom field condition
                    unset($conditions['other']['options']['other_custom_field']);

                    // Remove group if no other conditions exist
                    if (empty($conditions['other']['options'])) {
                        unset($conditions['other']);
                    }
                }
            }
        }

        // Return conditions for display
        return $conditions;
    }

    /**
     * Get list of all custom product taxonomies
     *
     * @access public
     * @param array $blacklist
     * @return void
     */
    public static function get_all_custom_taxonomies($blacklist = array())
    {

        // Do not include taxonomies that we already have as conditions
        $blacklist = array('product_cat', 'product_tag', 'product_type');

        // Get taxonomies
        return parent::get_all_custom_taxonomies($blacklist);
    }

    /**
     * Flag disabled conditions
     *
     * Disabling other custom field conditions that use non-existent fields
     *
     * @access public
     * @param array $conditions
     * @return void
     */
    public static function flag_disabled_conditions(&$conditions)
    {

        // Default flagging
        parent::flag_disabled_conditions($conditions);

        // Iterate over conditions
        foreach ($conditions as $condition_key => $condition) {

            // Condition uses other custom fields
            if ($condition['type'] === 'other__other_custom_field') {

                $other_custom_field_exists = true;

                // Get other field id
                if ($other_field_id = $condition['other_field_id']) {

                    // Attempt to load other field
                    if ($field = WCCF_Field_Controller::get($other_field_id)) {

                        // Field is archived or is in some undefined (not enabled/disabled) state
                        if (!$field->is_enabled() && !$field->is_disabled()) {

                            $other_custom_field_exists = false;
                        }
                    }
                    else {

                        $other_custom_field_exists = false;
                    }
                }
                else {

                    $other_custom_field_exists = false;
                }

                // Set flag
                if (!$other_custom_field_exists) {

                    $conditions[$condition_key]['_non_existent_other_custom_field'] = true;
                }
            }
        }
    }

    /**
     * Get all disabled condition flags
     *
     * @access public
     * @return array
     */
    public static function get_all_disabled_condition_flags()
    {

        return array_merge(parent::get_all_disabled_condition_flags(), array(
            '_non_existent_other_custom_field'
        ));
    }





}

WCCF_Controller_Conditions::get_instance();

}
