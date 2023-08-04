<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to scripts and stylesheets
 *
 * @class WCCF_Assets
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_Assets')) {

class WCCF_Assets extends RightPress_Assets
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

        // Construct parent
        parent::__construct();








/**
 * TODO: OLD CODE BELOW
 */

        // Enqueue frontend stylesheets
        add_action('wp_enqueue_scripts', array('WCCF_Assets', 'enqueue_frontend_stylesheets'));
        add_action('login_enqueue_scripts', array('WCCF_Assets', 'enqueue_frontend_stylesheets'));

        // Enqueue cart scripts
        add_action('wp_enqueue_scripts', array('WCCF_Assets', 'enqueue_cart_scripts'));

        // Enqueue checkout scripts
        add_action('wp_enqueue_scripts', array('WCCF_Assets', 'enqueue_checkout_scripts'));

        // Enqueue backend assets
        add_action('admin_enqueue_scripts', array('WCCF_Assets', 'enqueue_backend_assets'), 20);

        // Enqueue Select2
        add_action('init', array($this, 'enqueue_select2'), 1);
    }

    /**
     * Get asset constructor arguments
     *
     * Passing custom arguments to included library's assets
     *
     * @access public
     * @param string $key
     * @param array $asset_args
     * @return array
     */
    public function get_asset_constructor_arguments($key, $asset_args = array())
    {

        // Datetimepicker
        if ($key === 'datetimepicker') {

            // Set date and time formats from settings
            $asset_args['date_format']      = WCCF_Settings::get_date_format();
            $asset_args['time_format']      = WCCF_Settings::get_time_format();
            $asset_args['datetime_format']  = WCCF_Settings::get_datetime_format();
            $asset_args['time_step']        = WCCF_Settings::get('time_step');
        }

        // Call parent method
        return parent::get_asset_constructor_arguments($key, $asset_args);
    }






























/**
 * TODO: OLD CODE BELOW
 */

    /**
     * Load frontend stylesheets
     *
     * @access public
     * @return void
     */
    public static function enqueue_frontend_stylesheets()
    {

        global $post;

        $instance = WCCF_Assets::get_instance();

        // Load styles conditionally
        // WC31: Products will no longer be posts
        if (is_admin() || (!doing_action('wp_print_footer_scripts') && !is_checkout() && !is_account_page() && !(isset($post) && is_object($post) && $post->post_type === 'product') && !RightPress_Help::is_wp_frontend_user_registration_page())) {
            return;
        }

        // Make sure this is only executed once
        if (defined('WCCF_FRONTEND_STYLESHEETS_ENQUEUED')) {
            return;
        }
        else {
            define('WCCF_FRONTEND_STYLESHEETS_ENQUEUED', true);
        }

        // General styles (file upload control, date picker, color picker etc)
        self::enqueue_general_stylesheets();
        $instance->load_asset_styles('datetimepicker');
        self::enqueue_color_picker_stylesheets();

        // Frontend styles
        RightPress_Help::enqueue_or_inject_stylesheet('wccf-frontend-styles', WCCF_PLUGIN_URL . '/assets/css/frontend.css', WCCF_VERSION);
    }

    /**
     * Enqueue cart scripts
     */
    public static function enqueue_cart_scripts()
    {

        // Page is not cart or there are no enabled product fields
        if (!is_cart() || !WCCF_Product_Field_Controller::get_all()) {
            return;
        }

        // Enqueue frontend scripts
        WCCF_Assets::enqueue_frontend_scripts();
    }

    /**
     * Enqueue checkout scripts
     */
    public static function enqueue_checkout_scripts()
    {

        // Page is not checkout or there are no enabled checkout fields
        if (!is_checkout() || !WCCF_Checkout_Field_Controller::get_all()) {
            return;
        }

        // Enqueue frontend scripts
        WCCF_Assets::enqueue_frontend_scripts();
    }

    /**
     * Load frontend scripts in footer
     *
     * Also attempts to inject stylesheets to head section if they were not enqueued properly
     * This will only be the case when fields or field values are displayed programmatically by developers
     * Note: Problems with IE8 and earlier are to be expected if stylesheets are loaded this way
     *
     * @access public
     * @return void
     */
    public static function enqueue_frontend_scripts()
    {

        $instance = WCCF_Assets::get_instance();

        // Make sure this is only executed once
        if (defined('WCCF_FRONTEND_SCRIPTS_ENQUEUED') || is_admin()) {
            return;
        }
        else {
            define('WCCF_FRONTEND_SCRIPTS_ENQUEUED', true);
        }

        // Enqueue jQuery plugins
        // TODO: Review where else we could load this and reuse functionality
        RightPress_Loader::load_jquery_plugin('rightpress-helper');

        // General scripts (file upload control, date picker, color picker etc)
        self::enqueue_general_scripts('frontend');
        $instance->load_asset_scripts('datetimepicker');
        self::enqueue_color_picker_scripts();

        // Ensure that stylesheets are present
        add_action('wp_print_footer_scripts', array('WCCF_Assets', 'enqueue_frontend_stylesheets'));
    }

    /**
     * Enqueue Select2
     *
     * @access public
     * @return void
     */
    public static function enqueue_select2()
    {
        // Load assets conditionally
        if (WCCF::is_settings_page()) {

            // Enqueue Select2 related scripts and styles
            wp_enqueue_script('wccf-select2-scripts', WCCF_PLUGIN_URL . '/assets/select2/js/select2.full.min.js', array('jquery'), '4.0.7');
            wp_enqueue_script('wccf-select2-rp', WCCF_PLUGIN_URL . '/assets/js/rp-select2.js', array(), WCCF_VERSION);
            wp_enqueue_style('wccf-select2-styles', WCCF_PLUGIN_URL . '/assets/select2/css/select2.min.css', array(), '4.0.7');

            // Load Grouped Select2
            RightPress_Loader::load_jquery_plugin('rightpress-grouped-select2');

            // Print scripts before WordPress takes care of it automatically (helps load our version of Select2 before any other plugin does it)
            add_action('wp_print_scripts', array('WCCF_Assets', 'print_select2'));
        }
    }

    /**
     * Print Select2 scripts
     *
     * @access public
     * @return void
     */
    public static function print_select2()
    {
        remove_action('wp_print_scripts', array('WCCF_Assets', 'print_select2'));
        wp_print_scripts('wccf-select2-scripts');
        wp_print_scripts('wccf-select2-rp');
    }

    /**
     * Load backend assets conditionally
     *
     * @access public
     * @return void
     */
    public static function enqueue_backend_assets()
    {

        global $typenow;

        $instance = WCCF_Assets::get_instance();

        // Check what page we are on
        $is_settings_page       = WCCF::is_settings_page();
        // WC31: Orders and products will no longer be posts
        $is_order_edit_page     = $typenow === 'shop_order';
        $is_product_edit_page   = $typenow === 'product';
        $is_user_edit_page      = RightPress_Help::is_wp_backend_user_edit_page();
        $is_new_user_page       = RightPress_Help::is_wp_backend_new_user_page();

        // Assets for all pages
        self::enqueue_backend_assets_all();

        // Load general assets on all pages where fields are printed
        if ($is_settings_page || $is_order_edit_page || $is_product_edit_page || $is_user_edit_page || $is_new_user_page) {
            self::enqueue_general_assets('backend');
            $instance->load_asset_styles('datetimepicker');
            $instance->load_asset_scripts('datetimepicker');
            self::enqueue_color_picker_assets();
            RightPress_Help::enqueue_or_inject_stylesheet('wccf-jquery-ui-styles', WCCF_PLUGIN_URL . '/assets/jquery-ui/jquery-ui.min.css', '1.11.4');
        }

        // Assets for settings pages (including custom post type pages)
        if ($is_settings_page) {
            self::enqueue_backend_assets_settings();
        }

        // Assets for order and product edit pages
        if ($is_order_edit_page || $is_product_edit_page) {
            self::enqueue_backend_assets_product_order();
        }

        // Assets for user edit page
        if ($is_user_edit_page || $is_new_user_page) {
            self::enqueue_backend_assets_user();
        }
    }

    /**
     * Load some assets on all admin pages
     *
     * @access public
     * @return void
     */
    public static function enqueue_backend_assets_all()
    {
        // Styles loaded on all pages
        wp_enqueue_style('wccf-backend-all-styles', WCCF_PLUGIN_URL . '/assets/css/backend-all.css', array(), WCCF_VERSION);

        // Font awesome (icons)
        wp_enqueue_style('wccf-font-awesome', WCCF_PLUGIN_URL . '/assets/font-awesome/css/font-awesome.min.css', array(), '4.6.2');
    }

    /**
     * Load some assets on plugin settings page, including custom post type pages
     *
     * @access public
     * @return void
     */
    public static function enqueue_backend_assets_settings()
    {
        // jQuery UI Accordion
        wp_enqueue_script('jquery-ui-accordion');

        // jQuery UI Sortable
        wp_enqueue_script('jquery-ui-sortable');

        // jQuery UI Button
        wp_enqueue_script('jquery-ui-button');

        // jQuery UI Tooltip
        wp_enqueue_script('jquery-ui-tooltip');

        // Condition validation
        wp_enqueue_script('wccf-condition-validation-scripts', WCCF_PLUGIN_URL . '/assets/js/condition-validation.js', array('jquery'), WCCF_VERSION);

        // WooCommerce-style tips
        wp_enqueue_script('jquery-tiptip');
        wp_enqueue_style('wccf-tiptip-styles', WCCF_PLUGIN_URL . '/assets/jquery-tiptip/tiptip.css', array(), WCCF_VERSION);

        // Our own scripts and styles
        wp_enqueue_script('wccf-backend-scripts', WCCF_PLUGIN_URL . '/assets/js/backend.js', array('jquery'), WCCF_VERSION);
        wp_enqueue_style('wccf-settings-styles', WCCF_PLUGIN_URL . '/assets/css/settings.css', array(), WCCF_VERSION);
        wp_enqueue_style('wccf-post-styles', WCCF_PLUGIN_URL . '/assets/css/post.css', array(), WCCF_VERSION);

        // Pass variables to JS
        wp_localize_script('wccf-backend-scripts', 'wccf', array(
            'ajaxurl'                               => WCCF_Ajax::get_url(),
            'interchangeable_fields'                => WCCF_FB::get_interchangeable_fields(),
            'other_field_condition_methods_by_type' => WCCF_Controller_Conditions::get_other_field_condition_methods_by_field_types(),
            'labels' => array(
                'enable_field'                                  => __('Enable Field', 'rp_wccf'),
                'disable_field'                                 => __('Disable Field', 'rp_wccf'),
                'select2_placeholder'                           => __('Select values', 'rp_wccf'),
                'select2_no_results'                            => __('No results found', 'rp_wccf'),
                'select2_placeholder_custom_product_taxonomies' => __('No taxonomies enabled', 'rp_wccf'),
                'separator'                                     => __('Separator', 'rp_wccf'),
            ),
            'placeholders' => array(
                'label' => __('New Field', 'rp_wccf'),
                'key'   => __('new_field', 'rp_wccf'),
            ),
            'error_messages' => array(
                'option_key_must_be_unique'                 => __('Option key must be unique.', 'rp_wccf'),
                'field_key_must_be_unique'                  => __('Field key must be unique across all fields of this type (including archived and trashed).', 'rp_wccf'),
                'field_key_validation_in_progress'          => __('Validating field key, please wait...', 'rp_wccf'),
                'editing_archived_field'                    => __('Changes are not allowed to archived fields.', 'rp_wccf'),
                'generic_error'                             => __('Error: Please fix this element.', 'rp_wccf'),
                'required'                                  => __('Value is required.', 'rp_wccf'),
                'number_natural'                            => __('Value must be positive.', 'rp_wccf'),
                'number_min_0'                              => __('Value must be positive.', 'rp_wccf'),
                'number_min_1'                              => __('Value must be greater than or equal to 1.', 'rp_wccf'),
                'number_whole'                              => __('Value must be a whole number.', 'rp_wccf'),
                'condition_disabled'                        => __('Field must not contain disabled conditions.', 'rp_wccf'),
                'condition_non_existent'                    => __('Field must not contain conditions of non-existent type.', 'rp_wccf'),
                'condition_non_existent_other_custom_field' => __('Field must not contain conditions that include non-existent fields.', 'rp_wccf'),
                'field_type_incompatible'                   => __('This field type cannot be selected as it is incompatible with current field type. Create a new field instead.', 'rp_wccf'),
                'pricing_option_incompatible'               => __('This pricing option is not compatible with other field settings.', 'rp_wccf'),
            ),
            'confirmation' => array(
                'trashing_field'    => __('Trashing fields will make submitted field data no longer accessible. Consider archiving fields instead. Trash field?', 'rp_wccf'),
                'deleting_field'    => __('Deleting fields will make submitted field data no longer accessible. Delete field permanently?', 'rp_wccf'),
                'archiving_field'   => __('Archived fields let you access previously submitted field data but cannot be restored. Archive field?', 'rp_wccf'),
                'duplicating_field' => __('Each field must have a unique field key. Please enter a field key for the duplicate field. Value cannot be changed later.', 'rp_wccf'),
            ),
        ));
    }

    /**
     * Load some assets on order and product edit pages
     *
     * @access public
     * @return void
     */
    public static function enqueue_backend_assets_product_order()
    {
        // Styling
        wp_enqueue_style('wccf-product-order-styles', WCCF_PLUGIN_URL . '/assets/css/product-order.css', array(), WCCF_VERSION);
    }

    /**
     * Load some assets on user edit page
     *
     * @access public
     * @return void
     */
    public static function enqueue_backend_assets_user()
    {
        // Styling
        wp_enqueue_style('wccf-user-styles', WCCF_PLUGIN_URL . '/assets/css/user.css', array(), WCCF_VERSION);
    }

    /**
     * Enqueue scripts and styles used in both backend and frontend
     *
     * @access public
     * @param string $context
     * @return void
     */
    public static function enqueue_general_assets($context = 'frontend')
    {
        // Enqueue scripts
        self::enqueue_general_scripts($context);

        // Enqueue styles
        self::enqueue_general_stylesheets();
    }

    /**
     * Enqueue scripts used in both backend and frontend
     *
     * @access public
     * @param string $context
     * @return void
     */
    public static function enqueue_general_scripts($context = 'frontend')
    {
        // General scripts
        wp_enqueue_script('wccf-general-scripts', WCCF_PLUGIN_URL . '/assets/js/general.js', array('jquery'), WCCF_VERSION);

        // Color picker configuration
        wp_localize_script('wccf-general-scripts', 'wccf_color_picker_config', self::get_color_picker_config($context));

        // Other properties to pass to Javascript
        wp_localize_script('wccf-general-scripts', 'wccf_general_config', array(
            'is_backend'            => is_admin(),
            'ajaxurl'               => WCCF_Ajax::get_url(),
            'display_total_price'   => WCCF_Settings::get('display_total_price'),
            'messages'              => array(
                'file_uploading'                => __('Uploading file', 'rp_wccf'),
                'file_upload_error'             => __('Failed to upload', 'rp_wccf'),
                'file_upload_delete'            => __('[x]', 'rp_wccf'),
                'error_reload_notice'           => __('Something went wrong, please try again.', 'rp_wccf'),
                'error_loading_field_view'      => __('Error occurred while loading fields.', 'rp_wccf'),
                'error_updating_field_values'   => __('Error occurred while updating field values.', 'rp_wccf'),
                'field_values_updated'          => __('Field values updated successfully.', 'rp_wccf'),
                'try_again'                     => __('Please try again.', 'rp_wccf'),
                'page_reload'                   => __('This page will now reload.', 'rp_wccf'),
                'required_field'                => __('This field is required.', 'rp_wccf')
            ),
        ));

        // jQuery file upload
        wp_enqueue_script('wccf-iframe-transport', WCCF_PLUGIN_URL . '/assets/jquery-file-upload/jquery.iframe-transport.js', array('jquery'), '9.12.4');
        wp_enqueue_script('wccf-file-upload', WCCF_PLUGIN_URL . '/assets/jquery-file-upload/jquery.fileupload.js', array('jquery', 'jquery-ui-widget'), '9.12.4');
    }

    /**
     * Enqueue styles used in both backend and frontend
     *
     * @access public
     * @return void
     */
    public static function enqueue_general_stylesheets()
    {
        // Field styling
        RightPress_Help::enqueue_or_inject_stylesheet('wccf-field-styles', WCCF_PLUGIN_URL . '/assets/css/fields.css', WCCF_VERSION);
    }

    /**
     * Enqueue Iris Color Picker scripts and styles
     *
     * @access public
     * @return void
     */
    public static function enqueue_color_picker_assets()
    {

        // Enqueue scripts
        self::enqueue_color_picker_scripts();

        // Enqueue styles
        self::enqueue_color_picker_stylesheets();
    }

    /**
     * Enqueue Iris Color Picker scripts
     *
     * @access public
     * @return void
     */
    public static function enqueue_color_picker_scripts()
    {

        // Load Iris Color Picker in frontend
        if (RightPress_Help::is_request('frontend')) {

            // Register Iris Color Picker
            wp_register_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        }

        // Enqueue Iris Color Picker
        wp_enqueue_script('iris');
    }

    /**
     * Enqueue Iris Color Picker styles
     *
     * @access public
     * @return void
     */
    public static function enqueue_color_picker_stylesheets()
    {

    }

    /**
     * Get WP Color Picker config
     *
     * @access public
     * @param string $context
     * @return array
     */
    public static function get_color_picker_config($context)
    {

        return apply_filters('wccf_color_picker_config', array(
            'palettes'  => true,
            'width'     => 250,
        ), $context);
    }




}

WCCF_Assets::get_instance();

}
