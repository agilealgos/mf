<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Form Builder Class
 *
 * @class WCCF_FB
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_FB')) {

class WCCF_FB
{
    // Define form field types
    private static $field_types = null;

    /**
     * Define field types and return field types array
     *
     * @access public
     * @return array
     */
    public static function get_field_types_definition()
    {

        // Define field types
        if (empty(self::$field_types)) {

            self::$field_types = array(

                // Text & Number
                'text_input' => array(
                    'label'     => __('Text & Number', 'rp_wccf'),
                    'options'   => array(

                        'text' => array(
                            'label'                 => __('Text', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                        'textarea' => array(
                            'label'                 => __('Text area', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                        'password' => array(
                            'label'                 => __('Password', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                        'email' => array(
                            'label'                 => __('Email', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                        'number' => array(
                            'label'                 => __('Number', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                        'decimal' => array(
                            'label'                 => __('Decimal', 'rp_wccf'),
                            'interchangeable_with'  => array('text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                    ),
                ),

                // Date & Time
                'date_time' => array(
                    'label'     => __('Date & Time', 'rp_wccf'),
                    'options'   => array(

                        // Date
                        'date' => array(
                            'label'                 => __('Date picker', 'rp_wccf'),
                            'interchangeable_with'  => array('date', 'text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),

                        // Time
                        'time' => array(
                            'label'                 => __('Time picker', 'rp_wccf'),
                            'interchangeable_with'  => array('time', 'text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),

                        // Datetime
                        'datetime' => array(
                            'label'                 => __('Date/time picker', 'rp_wccf'),
                            'interchangeable_with'  => array('datetime', 'text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                    ),
                ),

                // Selection
                'selection' => array(
                    'label'     => __('Selection', 'rp_wccf'),
                    'options'   => array(

                        // Select
                        'select' => array(
                            'label'                 => __('Select', 'rp_wccf'),
                            'interchangeable_with'  => array('select', 'multiselect', 'checkbox', 'radio'),
                        ),

                        // Multiselect
                        'multiselect' => array(
                            'label'                 => __('Multiselect', 'rp_wccf'),
                            'interchangeable_with'  => array('select', 'multiselect', 'checkbox', 'radio'),
                        ),

                        // Checkbox
                        'checkbox' => array(
                            'label'                 => __('Checkboxes', 'rp_wccf'),
                            'interchangeable_with'  => array('select', 'multiselect', 'checkbox', 'radio'),
                        ),

                        // Radio
                        'radio' => array(
                            'label'                 => __('Radio buttons', 'rp_wccf'),
                            'interchangeable_with'  => array('select', 'multiselect', 'checkbox', 'radio'),
                        ),
                    ),
                ),

                // Color & Picture
                'color_picture' => array(
                    'label'     => __('Color', 'rp_wccf'),
                    'options'   => array(

                        // Color
                        'color' => array(
                            'label'                 => __('Color picker', 'rp_wccf'),
                            'interchangeable_with'  => array('color', 'text', 'textarea', 'password', 'email', 'number', 'decimal'),
                        ),
                    ),
                ),

                // File
                'file' => array(
                    'label'     => __('File', 'rp_wccf'),
                    'options'   => array(

                        // File
                        'file' => array(
                            'label'                 => __('File upload', 'rp_wccf'),
                            'interchangeable_with'  => array('file'),
                        ),
                    ),
                ),

                // Page Elements
                'page_elements' => array(
                    'label'     => __('Page Elements', 'rp_wccf'),
                    'options'   => array(

                        // Heading
                        'heading' => array(
                            'label'                 => __('Heading', 'rp_wccf'),
                            'interchangeable_with'  => array('heading', 'separator'),
                        ),

                        // Separator
                        'separator' => array(
                            'label'                 => __('Separator', 'rp_wccf'),
                            'interchangeable_with'  => array('separator', 'heading'),
                        ),
                    ),
                ),
            );
        }

        // Return field types
        return self::$field_types;
    }

    /**
     * Get list of field types for display in select fields
     *
     * @access public
     * @return array
     */
    public static function get_types()
    {
        $types = array();

        // Iterate over option groups
        foreach (self::get_field_types_definition() as $option_group_key => $option_group) {

            // Add group
            $types[$option_group_key] = array(
                'label' => $option_group['label'],
            );

            // Iterate over group options
            foreach ($option_group['options'] as $type => $properties) {

                // Add option
                $types[$option_group_key]['options'][$type] = $properties['label'];
            }
        }

        return $types;
    }

    /**
     * Get list of interchangeable fields
     *
     * @access public
     * @return array
     */
    public static function get_interchangeable_fields()
    {
        $types = array();

        foreach (self::get_field_types_definition() as $option_group) {
            foreach ($option_group['options'] as $type => $properties) {
                $types[$type] = $properties['interchangeable_with'];
            }
        }

        return $types;
    }

    /**
     * Render text field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function text($params, $field = null)
    {
        self::input('text', $params, array('value', 'maxlength', 'placeholder'), $field);
    }

    /**
     * Render text area field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function textarea($params, $field = null)
    {
        // Get attributes
        $attributes = self::attributes($params, array('value', 'maxlength', 'placeholder'), 'textarea', $field);

        // Get value
        if (isset($params['value'])) {
            $value = $params['value'];
        }
        // Get default field value
        else if ($default_value = WCCF_FB::get_default_value($field)) {
            $value = $default_value;
        }
        // No value
        else {
            $value = '';
        }

        // Generate field html
        $field_html = '<textarea ' . $attributes . '>' . htmlspecialchars($value) . '</textarea>';

        // Render field
        self::output($params, $field_html, $field, 'textarea');
    }

    /**
     * Render password field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function password($params, $field = null)
    {
        $params['autocomplete'] = 'off';
        self::input('password', $params, array('value', 'maxlength', 'placeholder'), $field);
    }

    /**
     * Render email field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function email($params, $field = null)
    {
        // Display as regular text field in the frontend, will do our own validation
        $input_type = WCCF_FB::is_backend() ? 'email' : 'text';

        // Print field
        self::input($input_type, $params, array('value', 'maxlength', 'placeholder'), $field);
    }

    /**
     * Render number field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function number($params, $field = null)
    {

        // Define supported attributes
        $attributes = array('value', 'maxlength', 'placeholder', 'min', 'max', 'step');

        // Set whole number step
        $params['step'] = isset($params['step']) ? $params['step'] : '1';

        // Allow developers to provide custom step
        $params['step'] = apply_filters('wccf_number_field_step', $params['step'], $params, $field);

        // Print field
        self::input('number', $params, $attributes, $field);
    }

    /**
     * Render decimal field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function decimal($params, $field = null)
    {

        // Define supported attributes
        $attributes = array('value', 'maxlength', 'placeholder', 'min', 'max', 'step');

        // Set decimal step
        $params['step'] = isset($params['step']) ? $params['step'] : '0.01';

        // Allow developers to provide custom step
        $params['step'] = apply_filters('wccf_decimal_field_step', $params['step'], $params, $field);

        // Print field
        self::input('number', $params, $attributes, $field);
    }

    /**
     * Render date field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function date($params, $field = null)
    {
        // Disable autocomplete
        $params['autocomplete'] = 'off';

        // Display as regular text field, will initialize date/time picker based on object's class
        self::input('text', $params, array('value', 'placeholder'), $field);
    }

    /**
     * Render time field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function time($params, $field = null)
    {
        // Disable autocomplete
        $params['autocomplete'] = 'off';

        // Display as regular text field, will initialize date/time picker based on object's class
        self::input('text', $params, array('value', 'placeholder'), $field);
    }

    /**
     * Render datetime field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function datetime($params, $field = null)
    {
        // Disable autocomplete
        $params['autocomplete'] = 'off';

        // Display as regular text field, will initialize date/time picker based on object's class
        self::input('text', $params, array('value', 'placeholder'), $field);
    }

    /**
     * Render color field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function color($params, $field = null)
    {
        // Disable autocomplete
        $params['autocomplete'] = 'off';

        // Display as regular text field, will initialize color picker based on object's class
        self::input('text', $params, array('value', 'placeholder'), $field);
    }

    /**
     * Render select field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @param bool $is_multiple
     * @param bool $is_grouped
     * @param bool $prepend_group_key
     * @return void
     */
    public static function select($params, $field = null, $is_multiple = false, $is_grouped = false, $prepend_group_key = false)
    {
        // Add empty option - check if we need one
        if (!WCCF::is_settings_page() && !$is_multiple && empty($params['value']) && (!isset($field) || !$field->has_default_value())) {

            // Also skip select fields in product level form builder
            if (empty($params['name']) || !preg_match('/^wccf_/i', $params['name'])) {

                // If no options are selected, we need to add a blank option at the very beginning of options
                $params['options'] = array('' => '') + $params['options'];
            }
        }

        // Get attributes
        $attributes = self::attributes($params, array(), 'select', $field);

        // Get options
        $options = self::options($params, $field, $is_grouped, $prepend_group_key);

        // Check if it's multiselect
        $multiple_html = $is_multiple ? 'multiple' : '';

        // Generate field html
        $field_html = '<select ' . $multiple_html . ' ' . $attributes . '>' . $options . '</select>';

        // Render field
        $field_type = $is_multiple ? 'multiselect' : ($is_grouped ? 'grouped_select' : 'select');
        self::output($params, $field_html, $field, $field_type, $is_multiple);
    }

    /**
     * Render grouped select field (for internal use only)
     *
     * @access public
     * @param array $params
     * @param object $field
     * @param bool $prepend_group_key
     * @return void
     */
    public static function grouped_select($params, $field = null, $prepend_group_key = false)
    {
        self::select($params, $field, false, true, $prepend_group_key);
    }

    /**
     * Render multiselect field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function multiselect($params, $field = null)
    {
        self::select($params, $field, true);
    }

    /**
     * Render checkbox field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function checkbox($params, $field = null)
    {
        self::checkbox_or_radio('checkbox', $params, $field);
    }

    /**
     * Render radio field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function radio($params, $field = null)
    {
        self::checkbox_or_radio('radio', $params, $field);
    }

    /**
     * Render checkbox or radio field
     *
     * @access public
     * @param string $type
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function checkbox_or_radio($type, $params, $field = null)
    {
        $field_html = '';

        // Make sure we have at least one option configured
        if (!empty($params['options'])) {

            // Get list of items that are checked by default if value is not present
            if (!isset($params['value']) && $default_value = WCCF_FB::get_default_value($field)) {
                $params['value'] = $default_value;
            }

            // Open list
            $user_field_styles = WCCF_FB::is_backend_user_profile($field) ? 'style="margin: 1px;"' : '';
            $field_html .= '<ul ' . $user_field_styles .  ' >';

            // Iterate over field options and display as individual items
            foreach ($params['options'] as $option_key => $label) {

                // Show pricing information
                $price_html = '';

                // Check if field is set
                if (isset($field)) {

                    // Check if pricing needs to be displayed
                    if (($field->context_is('product_field') && WCCF_Settings::get('prices_product_page')) || ($field->context_is('checkout_field') && WCCF_Settings::get('checkout_field_price_display'))) {

                        // Check if current option has pricing
                        if ($option_pricing = $field->get_option_pricing($option_key)) {

                            // Get pricing string
                            $price_html = WCCF_Pricing::get_pricing_string($option_pricing['pricing_method'], $option_pricing['pricing_value'], true, '', '', WCCF_FB::get_wc_product($params));
                        }
                    }
                }

                // Customize params
                $custom_params = $params;
                $custom_params['id'] = $custom_params['id'] . '_' . $option_key;

                // Get attributes
                $attributes = self::attributes($custom_params, array(), $type, $field);

                // Check if this item needs to be checked
                if (isset($params['value'])) {
                    $values = (array) $params['value'];
                    $checked = in_array($option_key, $values) ? 'checked="checked"' : '';
                }
                // Item is not checked
                else {
                    $checked = '';
                }

                // Generate HTML
                $user_field_styles = WCCF_FB::is_backend_user_profile($field) ? 'style="margin: 0px;"' : '';
                $field_html .= '<li ' . $user_field_styles . ' ><input type="' . $type . '" value="' . $option_key . '" ' . $checked . ' ' . $attributes . '><label for="' . $custom_params['id'] . '">' . (!empty($label) ? ' ' . $label : '') . $price_html . '</label></li>';
            }

            // Close list
            $field_html .= '</ul>';
        }

        // Allow direct no-option calls for internal use
        else if (!isset($field)) {
            $attributes = self::attributes($params, array('value', 'checked'), $type, $field);
            $field_html .= '<input type="' . $type . '" ' . $attributes . '>';
        }

        // Render field
        self::output($params, $field_html, $field, $type, true);
    }

    /**
     * Render file field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function file($params, $field = null)
    {
        // Define custom attributes
        $custom_attributes = array('accept');

        // Modify field name so that visible file upload field value is not checked during regular form submit
        $params['name'] = str_replace('wccf[', 'wccf_ignore[', $params['name']);

        // Optionally allow multiple file uploads
        if ($field && $field->accepts_multiple_values()) {
            $params['multiple'] = 'multiple';
            $custom_attributes[] = 'multiple';
        }

        // Print field
        self::input('file', $params, $custom_attributes, $field, true);
    }

    /**
     * Render hidden field
     * For internal use only
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function hidden($params, $field = null)
    {
        self::input('hidden', $params, array('value'), $field);
    }

    /**
     * Render generic input field
     *
     * @access public
     * @param string $type
     * @param array $params
     * @param array $custom_attributes
     * @param object $field
     * @param bool $print_placeholder_input
     * @return void
     */
    private static function input($type, $params, $custom_attributes = array(), $field = null, $print_placeholder_input = false)
    {
        // Get default field value if not set
        if (!isset($params['value']) && $default_value = WCCF_FB::get_default_value($field)) {
            $params['value'] = $default_value;
        }

        // Get attributes
        $attributes = self::attributes($params, $custom_attributes, $type, $field);

        // Generate field html
        $field_html = '<input type="' . $type . '" ' . $attributes . '>';

        // Render field
        self::output($params, $field_html, $field, $type, $print_placeholder_input);
    }

    /**
     * Render heading
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function heading($params, $field = null)
    {

        // Get attributes
        $attributes = self::attributes($params, array(), 'heading', $field, true);

        // Get label
        if (!empty($params['label'])) {
            $label = $params['label'];
        }
        else if ($field !== null) {
            $label = $field->get_label();
        }
        else {
            $label = '';
        }

        // Get heading level
        $h = $field->get_heading_level() ? $field->get_heading_level() : 'h3';

        // Format html
        $field_html = '<' . $h . ' ' . $attributes . '>' . $label . '</' . $h . '>';

        // Render field
        self::output($params, $field_html, $field, 'heading', false, true);
    }

    /**
     * Render separator
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    public static function separator($params, $field = null)
    {

        // Get attributes
        $attributes = self::attributes($params, array(), 'heading', $field, true);

        // Format html
        $field_html = '<hr ' . $attributes . '>';

        // Render field
        self::output($params, $field_html, $field, 'separator', false, true);
    }

    /**
     * Render attributes
     *
     * @access public
     * @param array $params
     * @param array $custom
     * @param string $type
     * @param object $field
     * @param bool $is_page_element
     * @return void
     */
    private static function attributes($params, $custom = array(), $type = 'text', $field = null, $is_page_element = false)
    {
        $html = '';

        // Get list of attributes for all elements
        $attributes = array_merge(array('id', 'class', 'style', 'disabled', 'title'), $custom);

        // Add field input attributes
        if (!$is_page_element) {
            $attributes = array_merge(array('type', 'name', 'autocomplete', 'pattern'), $attributes);
        }

        // Additional attributes for admin ui
        if (!$is_page_element && WCCF_FB::is_backend()) {
            $attributes[] = 'required';
        }

        // Allow developers to add custom attributes (e.g. placeholder)
        $attributes = apply_filters('wccf_field_attributes', $attributes, $type, $field);

        // Allow developers to add custom attribute values (e.g. placeholder string)
        $params = apply_filters('wccf_field_attribute_values', $params, $type, $field);

        // Unset maxlength (we handle character limit by ourselves)
        $maxlength_allowed = in_array('maxlength', $attributes, true);
        $attributes = $maxlength_allowed ? array_diff($attributes, array('maxlength')) : $attributes;

        // Extract attributes and append to html string
        foreach ($attributes as $attribute) {
            if (isset($params[$attribute]) && !RightPress_Help::is_empty($params[$attribute]) && !is_array($params[$attribute])) {
                $html .= $attribute . '="' . htmlspecialchars($params[$attribute]) . '" ';
            }
        }

        // Add data attributes provided in $params['data-wccf']
        if (!empty($params['data-wccf'])) {
            foreach ($params['data-wccf'] as $key => $value) {
                $html .= 'data-wccf-' . $key . '="' . $value . '" ';
            }
        }

        // Add a flag indicating that field uses pricing
        if ($field && $field->has_pricing()) {
            $html .= $field->context_is('checkout_field') ? 'data-wccf-checkout-pricing="1" ' : 'data-wccf-pricing="1" ';
        }

        // Add a flag indicating that field is quantity based
        if ($field && $field->is_quantity_based()) {
            $html .= 'data-wccf-quantity-based="1" ';
        }

        // Add field id data attribute
        if ($field) {
            $html .= 'data-wccf-field-id="' . $field->get_id() . '" ';
        }

        // Add min selected data attribute
        if ($field && $field->get_min_selected()) {
            $html .= 'data-wccf-min-selected="' . $field->get_min_selected() . '" ';
        }

        // Add max selected data attribute
        if ($field && $field->get_max_selected()) {
            $html .= 'data-wccf-max-selected="' . $field->get_max_selected() . '" ';
        }

        // Add character limit
        if ($maxlength_allowed && isset($params['maxlength'])) {
            $html .= 'data-wccf-character-limit="' . $params['maxlength'] . '" ';
        }

        // Add required flag
        if (isset($params['required']) && $params['required']) {
            $html .= 'data-wccf-required-field="1" ';
        }

        return $html;
    }

    /**
     * Get options for select field
     *
     * @access public
     * @param array $params
     * @param object $field
     * @param bool $is_grouped
     * @param bool $prepend_group_key
     * @return string
     */
    private static function options($params, $field = null, $is_grouped = false, $prepend_group_key = false)
    {
        $html = '';
        $selected = array();

        // Get selected option(s)
        if (isset($params['value'])) {
            $selected = (array) $params['value'];
        }
        else if ($default_value = WCCF_FB::get_default_value($field)) {
            $selected = $default_value;
        }

        // Extract options and append to html string
        if (!empty($params['options']) && is_array($params['options'])) {

            // Fix array depth if options are not grouped
            if (!$is_grouped) {
                $params['options'] = array(
                    'wccf_not_grouped' => array(
                        'options' => $params['options'],
                    ),
                );
            }

            // Iterate over option groups
            foreach ($params['options'] as $group_key => $group) {

                // Option group start
                if ($is_grouped) {
                    $html .= '<optgroup label="' . $group['label'] . '">';
                }

                // Iterate over options
                foreach ($group['options'] as $option_key => $option) {

                    // Show pricing information
                    $price_html = '';

                    // Check if field is set
                    if (isset($field)) {

                        // Check if pricing needs to be displayed
                        if (($field->context_is('product_field') && WCCF_Settings::get('prices_product_page')) || ($field->context_is('checkout_field') && WCCF_Settings::get('checkout_field_price_display'))) {

                            // Check if current option has pricing
                            if ($option_pricing = $field->get_option_pricing($option_key)) {

                                // Get pricing string
                                $price_html = WCCF_Pricing::get_pricing_string($option_pricing['pricing_method'], $option_pricing['pricing_value'], false, '', '', WCCF_FB::get_wc_product($params));
                            }
                        }
                    }

                    // Get option key
                    $option_key = (($is_grouped && $prepend_group_key) ? $group_key . '_' . $option_key : $option_key);

                    // Check if option is selected
                    $selected_html = in_array($option_key, $selected) ? 'selected="selected"' : '';

                    // Data attribute
                    $option_data = '';

                    // Format option html
                    $html .= '<option value="' . $option_key . '" ' . $selected_html . ' ' . $option_data . '>' . $option . $price_html . '</option>';
                }

                // Option group end
                if ($is_grouped) {
                    $html .= '</optgroup>';
                }
            }
        }

        return $html;
    }

    /**
     * Render field label
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return string
     */
    private static function label($params, $field = null)
    {
        echo self::label_html($params, $field);
    }

    /**
     * Get field label html
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return string
     */
    private static function label_html($params, $field = null)
    {
        // Check if label needs to be displayed
        if (!empty($params['id']) && !empty($params['label'])) {

            // Field is required
            $required_html = !empty($params['required']) ? ' <abbr class="required" title="' . __('required', 'rp_wccf') . '">*</abbr>' : '';

            // Display pricing information
            $price_html = '';

            // Check if field has pricing but does not have options
            if (isset($field) && !self::uses_options($field->get_field_type()) && $field->has_pricing()) {

                // Check if pricing information needs to be displayed
                if (($field->context_is('product_field') && WCCF_Settings::get('prices_product_page')) || ($field->context_is('checkout_field') && WCCF_Settings::get('checkout_field_price_display'))) {
                    $price_html = WCCF_Pricing::get_pricing_string($field->get_pricing_method(), $field->get_pricing_value(), true, '', '', WCCF_FB::get_wc_product($params));
                }
            }

            // Build label html
            $html = '<label for="' . $params['id'] . '"><span class="wccf_label">' . $params['label'] . '</span>' . $required_html . $price_html . self::min_max($params, $field) . '</label>';

            // User field labels need special treatment
            if (WCCF_FB::is_backend_user_profile($field)) {
                $html = '<th>' . $html . '</th>';
            }

            // Return label html
            return $html;
        }

        return '';
    }

    /**
     * Maybe display character limit information
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    private static function character_limit($params, $field = null)
    {
        if (!empty($params['maxlength']) || (isset($params['maxlength']) && $params['maxlength'] === '0')) {
            if (apply_filters('wccf_display_character_limit', true, $params, $field)) {
                echo '<small class="wccf_character_limit" style="display: none;"><span class="wccf_characters_remaining">' . $params['maxlength'] . '</span> ' . __('characters remaining', 'rp_wccf') . '</small>';
            }
        }
    }

    /**
     * Maybe print min/max selected and min/max value information
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    private static function min_max($params, $field = null)
    {
        if ($field) {

            $parts = array();

            // Get min selected
            if ($field->get_min_selected() && apply_filters('wccf_display_min_selected', true, $params, $field)) {
                $parts[] = __('min', 'rp_wccf') . ' ' . $field->get_min_selected();
            }

            // Get max selected
            if ($field->get_max_selected() && apply_filters('wccf_display_max_selected', true, $params, $field)) {
                $parts[] = __('max', 'rp_wccf') . ' ' . $field->get_max_selected();
            }

            // Get min value
            if ($field->get_min_value() && apply_filters('wccf_display_min_value', true, $params, $field)) {
                $parts[] = __('min', 'rp_wccf') . ' ' . $field->get_min_value();
            }

            // Get max value
            if ($field->get_max_value() && apply_filters('wccf_display_max_value', true, $params, $field)) {
                $parts[] = __('max', 'rp_wccf') . ' ' . $field->get_max_value();
            }

            // Display min/max limits
            if (!empty($parts)) {
                return '<small class="wccf_min_max_limit">' . join(', ', $parts) . '</small>';
            }
        }
    }

    /**
     * Render field description
     *
     * @access public
     * @param array $params
     * @param object $field
     * @param string $where
     * @return void
     */
    private static function description($params, $field, $where)
    {
        // Determine position
        if ($where === 'before' && apply_filters('wccf_description_before_field', false, $params, $field)) {
            $display = true;
        }
        else if ($where === 'after' && !apply_filters('wccf_description_before_field', false, $params, $field)) {
            $display = true;
        }
        else {
            $display = false;
        }

        // Display description
        if ($display) {
            echo self::description_html($params, $field);
        }
    }

    /**
     * Get field description html
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return string
     */
    private static function description_html($params, $field)
    {
        if (isset($field)) {

            // Get description
            $description = $field->get_description();

            // Check if description is set
            if ($description !== null) {

                // User fields have special handling
                if (WCCF_FB::is_backend_user_profile($field)) {
                    return ' <span class="description">' . $description . '</span>';
                }
                else {
                    return ' <small>' . $description . '</small>';
                }
            }
        }

        return '';
    }

    /**
     * Output frontend conditions
     *
     * @access public
     * @param array $params
     * @param object $field
     * @return void
     */
    private static function frontend_conditions($params, $field)
    {
        // Check if field object is set
        if (!$field) {
            return;
        }

        // Get frontend conditions for this field
        $frontend_conditions = $field->get_frontend_conditions();

        // Check if we have any frontend conditions
        if (!empty($frontend_conditions)) {

            // Get field DOM element id
            $id = $params['id'];

            // Fix field DOM element id for checkbox and radio button fields
            if ($field->field_type_is(array('checkbox', 'radio'))) {
                $option_keys = array_keys($params['options']);
                $id .= '_' . array_shift($option_keys);
            }

            // Fix other custom field conditions for quantity based product fields
            if ($field->context_is('product_field')) {

                // Iterate over frontend conditions
                foreach ($frontend_conditions as $frontend_condition_key => $frontend_condition) {

                    // Only fix other custom field conditions
                    if ($frontend_condition['type'] !== 'other__other_custom_field') {
                        continue;
                    }

                    // Load other field
                    $other_field_id = !empty($frontend_condition['other_field_id']) ? (int) $frontend_condition['other_field_id'] : 0;
                    $other_field = WCCF_Field_Controller::cache($other_field_id);

                    // Unable to load field
                    // Note: ideally we should react to this somehow, e.g. not print the field that we are printing now or so
                    if (!$other_field) {
                        continue;
                    }

                    // Both master and slave fields are quantity based and the field that is being printed is not the first one
                    if ($field->is_quantity_based() && $other_field->is_quantity_based() && !empty($params['quantity_index'])) {
                        $frontend_conditions[$frontend_condition_key]['other_field_id'] = $other_field_id . '_' . $params['quantity_index'];
                    }
                }
            }

            // Pass both conditions and context string
            $data = array(
                'context'       => $field->get_context(),
                'conditions'    => $frontend_conditions,
            );

            // Output script element
            echo '<script type="text/javascript" style="display: none;">var wccf_conditions_' . $id . ' = ' . json_encode($data) . ';</script>';
        }
    }

    /**
     * Output field based on context
     *
     * @access public
     * @param array $params
     * @param string $field_html
     * @param object $field
     * @param string $type
     * @param bool $print_placeholder_input
     * @param bool $is_page_element
     * @return void
     */
    private static function output($params, $field_html, $field, $type, $print_placeholder_input = false, $is_page_element = false)
    {
        // Open container
        self::output_begin($params, $field, $type);

        // Print frontend conditions
        self::frontend_conditions($params, $field);

        // Print label
        if (!$is_page_element) {
            self::label($params, $field);
        }

        // User field treatment
        if (WCCF_FB::is_backend_user_profile($field)) {
            echo '<td class="wccf_user_profile_field_td">';
        }

        // Print description before field
        self::description($params, $field, 'before');

        // Treat file upload fields
        if ($type === 'file' && isset($field)) {

            // Print current file download link
            if (!empty($params['value'])) {

                // Maybe display left border
                $left_border = count($params['value']) > 1 ? 'wccf_file_upload_left_border' : '';

                // Open container
                echo '<div class="wccf_file_upload_list ' . $left_border . '">';

                // Iterate over files
                foreach ($params['value'] as $access_key) {

                    // Open single file container
                    echo '<small class="wccf_file_upload_item">';

                    // Print file name with download link
                    WCCF_Files::print_file_download_link_html($access_key);

                    // Print delete icon
                    echo ' <span class="wccf_file_upload_delete">[x]</span>';

                    // Print hidden field with existing file data
                    $hidden_field_name = str_replace('wccf_ignore', 'wccf', $params['name']);
                    $hidden_field_name .= preg_match('/\[\]$/i', $hidden_field_name) ? '' : '[]';
                    echo '<input type="hidden" class="_' . $params['id'] . '" name="' . $hidden_field_name . '" value="' . $access_key . '" data-wccf-file-access-key="' . $access_key . '">';

                    // Close single file container
                    echo '</small>';
                }

                // Close container
                echo '</div>';
            }
        }

        // Maybe print hidden placeholder input so that empty fields of all types are always passed to server in $_POST
        if ($field && $print_placeholder_input) {
            self::print_placeholder_input($params);
        }

        // Print field
        echo $field_html;

        // Print character limit information
        if (!$is_page_element) {
            self::character_limit($params, $field);
        }

        // Print description after field
        self::description($params, $field, 'after');

        // User field treatment
        if (WCCF_FB::is_backend_user_profile($field)) {
            echo '</td>';
        }

        // Close container
        self::output_end($field, $type);
    }

    /**
     * Output container begin
     *
     * @access public
     * @param array $params
     * @param object $field
     * @param string $type
     * @return void
     */
    private static function output_begin($params, $field, $type)
    {

        // Hide fields that depend on other fields (have frontend conditions)
        $display_none_html = ($field && $field->has_frontend_conditions() && !defined('WCCF_CART_ITEM_PRODUCT_FIELD_EDITING_VIEW_REQUEST')) ? ' style="display: none;" ' : '';

        // Get field context
        $context = isset($field) ? $field->get_context() : false;

        // Get container id string
        $container_id_string = !empty($params['id']) ? ' id="' . $params['id'] . '_container"' : '';

        // Product Fields, Checkout Fields, User Fields in frontend
        if (in_array($context, array('product_field', 'checkout_field')) || ($context === 'user_field' && !WCCF_FB::is_backend())) {
            echo '<div' . $container_id_string . ' class="wccf_field_container wccf_field_container_' . $context . ' wccf_field_container_' . $type . ' wccf_field_container_' . $context . '_' . $type . '"' . $display_none_html . '>';
        }
        // Product Properties, Order Fields, User Fields in backend but not on user edit page
        else if (in_array($context, array('product_prop', 'order_field')) || ($context === 'user_field' && !WCCF_FB::is_backend_user_profile($field))) {
            echo '<div' . $container_id_string . ' class="wccf_meta_box_field_container wccf_' . $context . '_field_container"' . $display_none_html . '>';
        }
        // User Fields on user edit page
        else if (WCCF_FB::is_backend_user_profile($field)) {
            echo '<tr' . $container_id_string . ' class="user-' . $params['id'] . '-wrap wccf_user_profile_field_container wccf_' . $context . '_field_container"' . $display_none_html . '">';
        }
    }

    /**
     * Output container end
     *
     * @access public
     * @param object $field
     * @param string $type
     * @return void
     */
    private static function output_end($field, $type)
    {
        if (isset($field)) {

            // User field
            if (WCCF_FB::is_backend_user_profile($field)) {
                echo '</tr>';
            }
            // All other fields
            else {
                echo '</div>';
            }
        }
    }

    /**
     * Print hidden placeholder input so that empty fields of all types are always passed to server in $_POST
     * By default if value for multiselect, checkbox or radio button field is not selected, field name can't be found in $_POST
     *
     * @access protected
     * @param array $params
     * @return void
     */
    public static function print_placeholder_input($params)
    {
        echo '<input type="hidden" name="' . $params['name'] . '" id="_' . $params['id'] . '" value="">';
    }

    /**
     * Check if field type uses options
     *
     * @access public
     * @param string $type
     * @return bool
     */
    public static function uses_options($type)
    {
        return in_array($type, array('select', 'multiselect', 'checkbox', 'radio'), true);
    }

    /**
     * Validate text field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_text($value)
    {
        // Value must be string
        if (gettype($value) !== 'string') {
            throw new Exception(__('must be a valid text string', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate textarea field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_textarea($value)
    {
        // Value must be valid text value
        self::validate_text($value);

        return true;
    }

    /**
     * Validate password field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_password($value)
    {
        // Value must be valid text value
        self::validate_text($value);

        return true;
    }

    /**
     * Validate email field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_email($value)
    {
        // Value must be valid text value
        self::validate_text($value);

        // Value must be valid email
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception(__('must be a valid email address', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate number field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_number($value)
    {
        // Value must be either string, integer or float and must be numeric
        if (!in_array(gettype($value), array('string', 'integer', 'double')) || !RightPress_Help::is_whole_number($value)) {
            throw new Exception(__('must be a valid numeric value', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate decimal field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_decimal($value)
    {
        // Value must be either string, integer or float and must be numeric
        if (!in_array(gettype($value), array('string', 'integer', 'double')) || !is_numeric($value)) {
            throw new Exception(__('must be a valid decimal value', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate date field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_date($value)
    {

        // Value must be date
        if (!RightPress_Help::is_date($value, WCCF_Settings::get_date_format())) {
            throw new Exception(__('must be a valid date', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate time field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_time($value)
    {

        // Get time format
        $format = WCCF_Settings::get_time_format();

        // Value must be time
        if (!RightPress_Help::is_date($value, $format)) {
            throw new Exception(__('must be a valid time', 'rp_wccf'));
        }

        // Validate minutes against minutes step value in settings
        WCCF_FB::validate_minutes_against_minutes_step($value, $format);

        return true;
    }

    /**
     * Validate datetime field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_datetime($value)
    {

        // Get time format
        $format = WCCF_Settings::get_datetime_format();

        // Value must be date
        if (!RightPress_Help::is_date($value, $format)) {
            throw new Exception(__('must be a valid date/time', 'rp_wccf'));
        }

        // Validate minutes against minutes step value in settings
        WCCF_FB::validate_minutes_against_minutes_step($value, $format);

        return true;
    }

    /**
     * Validate color field value (expects hexadecimal color code)
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public static function validate_color($value)
    {

        // Value must be hexadecimal color code
        if (!RightPress_Help::is_hexadecimal_color_code($value)) {
            throw new Exception(__('must be a valid hexadecimal color code', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate select field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @param object $field
     * @return bool
     */
    public static function validate_select($value, $field)
    {
        return self::validate_select_or_radio($value, $field);
    }

    /**
     * Validate radio field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @param object $field
     * @return bool
     */
    public static function validate_radio($value, $field)
    {
        return self::validate_select_or_radio($value, $field);
    }

    /**
     * Validate select or radio field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @param object $field
     * @return bool
     */
    public static function validate_select_or_radio($value, $field)
    {
        // Value must be a string and must validate against predefined options
        if (gettype($value) !== 'string' || !self::validate_value_against_options($value, $field->get_options_list())) {
            throw new Exception(__('is not one of the provided values', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate multiselect field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param mixed $value
     * @param object $field
     * @return bool
     */
    public static function validate_multiselect($value, $field)
    {
        return self::validate_multiselect_or_checkbox($value, $field);
    }

    /**
     * Validate checkbox field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param mixed $value
     * @param object $field
     * @return bool
     */
    public static function validate_checkbox($value, $field)
    {
        return self::validate_multiselect_or_checkbox($value, $field);
    }

    /**
     * Validate multiselect or checkbox field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param mixed $value
     * @param object $field
     * @return bool
     */
    public static function validate_multiselect_or_checkbox($value, $field)
    {
        // Cast value to array
        $values = (array) $value;

        // Track validation of each value
        $validation_passed = true;

        // Iterate over values
        foreach ($values as $value) {

            // Each value must be string
            if (gettype($value) !== 'string') {
                $validation_passed = false;
                break;
            }

            // Validate value against options
            if (!self::validate_value_against_options($value, $field->get_options_list())) {
                $validation_passed = false;
                break;
            }
        }

        if (!$validation_passed) {
            throw new Exception(__('is not one of the provided values', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Validate field value against options
     *
     * @access public
     * @param string $value
     * @param array $options
     * @return bool
     */
    public static function validate_value_against_options($value, $options)
    {
        // Field must have options defined
        if (empty($options) || !is_array($options)) {
            return false;
        }

        // Track if match is found in field options
        $match_found = false;

        // Iterate over field options
        foreach ($options as $option_key => $option_label) {

            // Check if value matches option key
            if ((string) $value === (string) $option_key) {
                $match_found = true;
                break;
            }
        }

        if (!$match_found) {
            return false;
        }

        return true;
    }

    /**
     * Validate file field value
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @param object $field
     * @param int $quantity_index
     * @param mixed $item
     * @return bool
     */
    public static function validate_file($value, $field, $quantity_index = null, $item = null)
    {
        // Get file extension
        $file_extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

        // Get extension lists
        $extension_whitelist = apply_filters('wccf_file_extension_whitelist', WCCF_Settings::get('file_extension_whitelist'));
        $extension_blacklist = apply_filters('wccf_file_extension_blacklist', WCCF_Settings::get('file_extension_blacklist'));

        // Check against whitelist
        if (!empty($extension_whitelist) && !in_array($file_extension, $extension_whitelist, true)) {
            throw new Exception(__('contains a file that is not allowed', 'rp_wccf'));
        }

        // Check against blacklist
        if (in_array($file_extension, $extension_blacklist, true)) {
            throw new Exception(__('contains a file that is not allowed', 'rp_wccf'));
        }

        // Get file size
        $file_size = filesize($value['tmp_name']);

        // Get min file size
        $min_file_size = WCCF_Settings::get('min_file_size') ?: 0;
        $min_file_size = apply_filters('wccf_min_file_size', $min_file_size) * 1000;

        // Get max file size
        $max_file_size = WCCF_Settings::get('max_file_size') ?: null;
        $max_file_size = apply_filters('wccf_max_file_size', $max_file_size);
        $max_file_size = $max_file_size !== null ? ($max_file_size * 1000) : null;

        // Get max combined file size for field
        $max_combined_file_size = WCCF_Settings::get('max_combined_file_size_per_field') ?: null;
        $max_combined_file_size = apply_filters('wccf_max_combined_file_size_per_field', $max_combined_file_size);
        $max_combined_file_size = $max_combined_file_size !== null ? ($max_combined_file_size * 1000) : null;

        // Check file size
        if ($file_size < $min_file_size) {
            throw new Exception(__('contains a file that is too small', 'rp_wccf'));
        }
        else if ($max_file_size !== null && $file_size > $max_file_size) {
            throw new Exception(__('contains a file that is too large', 'rp_wccf'));
        }

        // Check combined file size per field
        if ($max_combined_file_size !== null && $field->accepts_multiple_values()) {

            // Add current file size
            $combined_size = $file_size;

            // Load session object
            $session = WCCF_WC_Session::initialize_session();

            // Parse form data
            parse_str(urldecode($_POST['form_data']), $form_data);

            // Get field properties
            $field_context = $field->get_context();
            $field_id_for_name = $quantity_index ? ($field->get_id() . '_' . $quantity_index) : $field->get_id();

            // Get already stored values
            $stored = $item ? $field->get_stored_value($item) : array();

            // Combine all file access keys to check
            $file_access_keys = array();

            // Get access keys from hidden inputs
            if (!empty($form_data['wccf'][$field_context][$field_id_for_name])) {
                $file_access_keys = array_merge($file_access_keys, $form_data['wccf'][$field_context][$field_id_for_name]);
            }

            // Add sizes of previously uploaded files
            foreach ($file_access_keys as $index => $file_access_key) {

                $file_data = false;

                // Get permanently stored file data from meta
                if (is_array($stored) && in_array($file_access_key, $stored, true)) {
                    $file_data = maybe_unserialize($field->get_data($item, WCCF_Field::get_file_data_access_key($file_access_key), true));
                }

                // Get temporarily stored file data from meta
                if (!$file_data && $item) {
                    $file_data = maybe_unserialize($field->get_data($item, WCCF_Field::get_temp_file_data_access_key($file_access_key), true));
                }

                // Get file data from session
                if (!$file_data && $session) {
                    $file_data = $session->get(WCCF_Field::get_temp_file_data_access_key($file_access_key), false);
                }

                // Check if file data was found
                if ($file_data) {

                    // Get file path
                    if ($file_path = WCCF_Files::locate_file($file_data['subdirectory'], $file_data['storage_key'])) {

                        // Add file size
                        $combined_size += filesize($file_path);
                    }
                }
                // Remove from file access keys array so we can check in explicitly posted file sizes
                else {
                    unset($file_access_keys[$index]);
                }
            }

            // Add explicitly posted file sizes (file uploads of one file selection)
            if (!empty($_POST['wccf_uploaded_file_sizes'])) {
                if ($uploaded_file_sizes = json_decode(stripslashes($_POST['wccf_uploaded_file_sizes']), true)) {
                    foreach ($uploaded_file_sizes as $file_access_key => $current_size) {
                        if (!in_array($file_access_key, $file_access_keys, true)) {
                            $combined_size += $current_size;
                        }
                    }
                }
            }

            // Check combined file size
            if ($combined_size > $max_combined_file_size) {
                throw new Exception(__('combined file size is too large', 'rp_wccf'));
            }
        }

        return true;
    }

    /**
     * Validate minutes in time
     *
     * Throws exception if validation fails
     *
     * @access public
     * @param string $value
     * @param string $format
     * @return bool
     */
    public static function validate_minutes_against_minutes_step($value, $format)
    {

        // Get datetime object
        $datetime = DateTime::createFromFormat($format, $value, RightPress_Help::get_time_zone());

        // Minutes in datetime object must be supported by minutes step value in settings
        if (!in_array($datetime->format('i'), WCCF_Settings::get_step_allowed_minutes(), true)) {
            throw new Exception(__('must have a supported minutes value', 'rp_wccf'));
        }

        return true;
    }

    /**
     * Check if fields are being printed in backend
     *
     * @access public
     * @return bool
     */
    public static function is_backend()
    {
        return (is_admin() && (!is_ajax() || (isset($_POST['action']) && $_POST['action'] === 'wccf_get_backend_editing_field')));
    }

    /**
     * Check if fields are being printed in backend new user or user edit page
     *
     * @access public
     * @param object $field
     * @return bool
     */
    public static function is_backend_user_profile($field = null)
    {
        return (is_object($field) && $field->context_is('user_field') && (RightPress_Help::is_wp_backend_user_edit_page() || RightPress_Help::is_wp_backend_new_user_page()));
    }

    /**
     * Get default field value
     *
     * @access public
     * @param object $field
     * @return mixed
     */
    public static function get_default_value($field)
    {
        if (!($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['wccf_qv_conf'])) && $field && $default_value = $field->get_default_value()) {
            return $default_value;
        }

        return null;
    }

    /**
     * Get WooCommerce product from field params
     *
     * @access public
     * @param array $params
     * @return object|null
     */
    public static function get_wc_product($params)
    {
        if (!empty($params['product']) && is_a($params['product'], 'WC_Product')) {
            return $params['product'];
        }

        return null;
    }


}
}
