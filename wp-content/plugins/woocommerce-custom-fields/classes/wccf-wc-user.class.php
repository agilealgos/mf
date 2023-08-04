<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to user handling in WooCommerce
 *
 * @class WCCF_WC_User
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_WC_User')) {

class WCCF_WC_User
{

    protected $enabled_address_fields = null;

    // Singleton instance
    protected static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        /**
         * FRONTEND ACCOUNT FIELDS
         */

        // Print user fields in frontend user registration page
        add_action('woocommerce_register_form', array($this, 'print_fields_frontend_register'));

        // Validate user fields submitted from registration page
        add_filter('woocommerce_process_registration_errors', array($this, 'process_registration_errors'), 10, 4);

        // Print user fields in frontend account edit page
        add_action('woocommerce_edit_account_form', array($this, 'print_fields_frontend_account_edit'));

        // Validate user fields submitted from account details page
        add_action('woocommerce_save_account_details_errors', array($this, 'validate_user_field_values_account_details'), 10, 2);

        /**
         * FRONTEND ADDRESS FIELDS
         */

        // Print user fields in frontend address edit pages
        add_action('woocommerce_before_edit_address_form_billing', array($this, 'print_fields_frontend_billing_address_edit'));
        add_action('woocommerce_after_edit_address_form_billing', array($this, 'print_fields_frontend_billing_address_edit'));
        add_action('woocommerce_before_edit_address_form_shipping', array($this, 'print_fields_frontend_shipping_address_edit'));
        add_action('woocommerce_after_edit_address_form_shipping', array($this, 'print_fields_frontend_shipping_address_edit'));

        // Validate user fields submitted from address edit page
        // NOTE: This is a hack since there's no hook for validation, change this bit later
        add_filter('woocommerce_edit_address_slugs', array($this, 'validate_user_field_values_address'));

        // Save address related user field values on address update
        add_action('woocommerce_customer_save_address', array($this, 'save_user_field_values_address_update'), 10, 2);

        // Add custom billing and shipping address field values for display
        add_filter('woocommerce_customer_get_billing', array($this, 'add_custom_billing_address_field_values'), 99, 2);
        add_filter('woocommerce_customer_get_shipping', array($this, 'add_custom_shipping_address_field_values'), 99, 2);

        /**
         * ADDRESS FORMATTING
         */

        // Maybe add billing and shipping address field keys to address formats
        add_filter('woocommerce_localisation_address_formats', array($this, 'maybe_add_field_keys_address_formats'), 99);

        // Maybe add formatted address replacements
        add_filter('woocommerce_formatted_address_replacements', array($this, 'maybe_add_formatted_address_replacements'), 99, 2);

        // Add empty values to package destinations to hide placeholders
        add_filter('woocommerce_shipping_packages', array($this, 'add_empty_values_to_package_destinations'), 99);

    }

    /**
     * Print user fields in frontend WooCommerce account edit page
     *
     * @access public
     * @return void
     */
    public function print_fields_frontend_account_edit()
    {
        // Get user fields to print
        $fields = WCCF_User_Field_Controller::get_filtered();

        // Print only fields set to display on user profile
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'user_profile');

        // Print user fields
        WCCF_Field_Controller::print_fields($fields, get_current_user_id());
    }

    /**
     * Validate user fields submitted from account details page
     *
     * @access public
     * @return void
     */
    public function validate_user_field_values_account_details()
    {
        // Validate only those fields that were displayed
        $fields = WCCF_User_Field_Controller::get_filtered();
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'user_profile');

        // Validate user fields
        WCCF_Field_Controller::validate_posted_field_values('user_field', array(
            'fields'    => $fields,
            'item_id'   => get_current_user_id(),
        ));
    }

    /**
     * Validate user fields submitted from registration page
     *
     * @access public
     * @param object $validation_error
     * @param string $username
     * @param string $password
     * @param string $email
     * @return object
     */
    public function process_registration_errors($validation_error, $username, $password, $email)
    {
        // Validate only those fields that were displayed
        $fields = WCCF_User_Field_Controller::get_filtered();
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'user_profile');

        // Validate user fields
        WCCF_Field_Controller::validate_posted_field_values('user_field', array(
            'fields'    => $fields,
            'wp_errors' => $validation_error,
        ));

        return $validation_error;
    }

    /**
     * Print user fields in frontend billing address edit page
     *
     * @access public
     * @return void
     */
    public function print_fields_frontend_billing_address_edit()
    {
        // Get corresponding checkout hook
        $checkout_hook = strpos(current_filter(), 'before') !== false ? 'woocommerce_before_checkout_billing_form' : 'woocommerce_after_checkout_billing_form';

        // Get fields to print
        $fields = WCCF_User_Field_Controller::get_filtered(null, array(), $checkout_hook);
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'billing_address');

        // Print user fields
        WCCF_Field_Controller::print_fields($fields, get_current_user_id());
    }

    /**
     * Print user fields in frontend shipping address edit page
     *
     * @access public
     * @return void
     */
    public function print_fields_frontend_shipping_address_edit()
    {
        // Get corresponding checkout hook
        $checkout_hook = strpos(current_filter(), 'before') !== false ? 'woocommerce_before_checkout_shipping_form' : 'woocommerce_after_checkout_shipping_form';

        // Get fields to print
        $fields = WCCF_User_Field_Controller::get_filtered(null, array(), $checkout_hook);
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'shipping_address');

        // Print user fields
        WCCF_Field_Controller::print_fields($fields, get_current_user_id());
    }

    /**
     * Validate user fields submitted from address edit page
     * NOTE: This is a hack since there's no hook for validation, change this bit later
     *
     * @access public
     * @param array $slugs
     * @return void
     */
    public function validate_user_field_values_address($slugs = array())
    {
        // Check if validation is needed
        if (doing_filter('template_redirect')) {

            global $wp;

            // Prevent infinite loop
            remove_filter('woocommerce_edit_address_slugs', array($this, 'validate_user_field_values_address'));

            // Check which address is being saved
            $address_type = isset($wp->query_vars['edit-address']) ? wc_edit_address_i18n(sanitize_title($wp->query_vars['edit-address']), true) : 'billing';

            // Validate only those fields that were displayed
            $fields = WCCF_User_Field_Controller::get_filtered();
            $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', $address_type . '_address');

            // Validate user fields
            WCCF_Field_Controller::validate_posted_field_values('user_field', array(
                'fields'    => $fields,
                'item_id'   => get_current_user_id(),
            ));
        }

        // Return filter value
        return $slugs;
    }

    /**
     * Save address related user field values on address update
     *
     * @access public
     * @param int $user_id
     * @param string $address_type
     * @return void
     */
    public function save_user_field_values_address_update($user_id, $address_type)
    {
        // Load customer
        $item = RightPress_Help::wc_get_customer($user_id);

        // Store posted field values
        WCCF_Field_Controller::store_field_values($item, 'user_field', true, false, ($address_type . '_address'));

        // Save customer if needed
        if (is_object($item)) {
            $item->save();
        }
    }

    /**
     * Print user fields in frontend WooCommerce customer registration page
     *
     * @access public
     * @return void
     */
    public function print_fields_frontend_register()
    {
        // Workaround for issue #333
        if (did_action('register_form')) {
            return;
        }

        // Get fields to display
        $fields = WCCF_User_Field_Controller::get_filtered();
        $fields = WCCF_Field_Controller::filter_by_property($fields, 'display_as', 'user_profile');

        // Check if we have any fields to display
        if ($fields) {

            // Display list of fields
            WCCF_Field_Controller::print_fields($fields);
        }
    }

    /**
     * Add custom billing address field values for display
     *
     * @access public
     * @param array $address
     * @param string $customer
     * @return array
     */
    public function add_custom_billing_address_field_values($address, $customer)
    {
        return $this->add_custom_address_field_values($address, $customer, 'billing_address');
    }

    /**
     * Add custom shipping address field values for display
     *
     * @access public
     * @param array $address
     * @param string $customer
     * @return array
     */
    public function add_custom_shipping_address_field_values($address, $customer)
    {
        return $this->add_custom_address_field_values($address, $customer, 'shipping_address');
    }

    /**
     * Add custom billing or shipping address field values for display
     *
     * Used for both customer profile and order display
     *
     * @access public
     * @param array $address
     * @param string $object
     * @param string $context
     * @return array
     */
    public function add_custom_address_field_values($address, $object, $context)
    {

        // Get enabled address fields
        $fields = $this->get_enabled_public_address_fields();

        // Iterate over fields
        foreach ($fields as $field) {

            // Get field value
            $value = $field->get_stored_value($object);

            // Format display value
            $display_value = $field->format_display_value(array(
                'value' => $value,
                'data'  => array(),
            ));

            // Add field value or null to hide placeholder
            $address[('wccf_' . $field->get_key())] = ($value !== false && $field->get_display_as() === $context) ? $display_value : null;
        }

        // Return address
        return $address;
    }

    /**
     * Get enabled address fields
     *
     * @access public
     * @return array
     */
    public function get_enabled_public_address_fields()
    {

        // Fields not defined yet
        if ($this->enabled_address_fields === null) {

            // Get all active user fields
            $this->enabled_address_fields = WCCF_User_Field_Controller::get_all(array('enabled'));

            // Remove non-address and non-public fields
            foreach ($this->enabled_address_fields as $field_key => $field) {
                if (!in_array($field->get_display_as(), array('billing_address', 'shipping_address'), true) || !$field->is_public()) {
                    unset($this->enabled_address_fields[$field_key]);
                }
            }
        }

        // Return enabled address fields
        return $this->enabled_address_fields;
    }

    /**
     * Maybe add billing and shipping address field keys to address formats
     *
     * @access public
     * @param array $formats
     * @return array
     */
    public function maybe_add_field_keys_address_formats($formats)
    {

        // Format strings to prepend and append
        $prepend    = "";
        $append     = "";

        // Get enabled address fields
        $fields = $this->get_enabled_public_address_fields();

        // Iterate over user fields
        foreach ($fields as $field) {

            // Prepend
            if (in_array($field->get_position(), array('woocommerce_before_checkout_billing_form', 'woocommerce_before_checkout_shipping_form'), true)) {
                $prepend .= "{wccf_" . $field->get_key() . "}\n";
            }
            // Append
            else {
                $append .= "\n{wccf_" . $field->get_key() . "}";
            }
        }

        // Check if we have any field keys to add
        if ($prepend || $append) {

            // Iterate over formats
            foreach ($formats as $key => $value) {

                // Add custom field keys
                $formats[$key] = $prepend . $value . $append;
            }
        }

        // Return address formats
        return $formats;
    }

    /**
     * Maybe add formatted address replacements
     *
     * @access public
     * @param array $replace
     * @param array $args
     * @return array
     */
    public function maybe_add_formatted_address_replacements($replace, $args)
    {

        // Iterate over address fields
        foreach ($args as $key => $value) {

            // Check if current field is our custom field
            if (RightPress_Help::string_begins_with_substring($key, 'wccf_')) {

                // Add replacement
                $replace['{' . $key . '}'] = $value;
            }
        }

        // Return address replacements
        return $replace;
    }

    /**
     * Add empty values to package destinations to hide placeholders
     *
     * @access public
     * @param array $packages
     * @return array
     */
    public function add_empty_values_to_package_destinations($packages)
    {

        // Get enabled address fields
        $fields = $this->get_enabled_public_address_fields();

        // Add empty values to package destinations
        foreach ($packages as $package_key => $package) {
            foreach ($fields as $field) {
                $packages[$package_key]['destination'][('wccf_' . $field->get_key())] = null;
            }
        }

        // Return packages
        return $packages;
    }





}

WCCF_WC_User::get_instance();

}
