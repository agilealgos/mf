<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WCCF')) {

/**
 * Main plugin class
 *
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
class WCCF
{

    /*
        Field data storage structure since version 2.0:
            - Checkout Fields
                _wccf_cf_{key}          = {field_value}         in order meta
                _wccf_cf_id_{key}       = {field_id}            in order meta
                _wccf_cf_data_{key}     = {extra_data_array}    in order meta
                _wccf_file_{access_key} = {file_data}           in order meta
            - Order Fields
                _wccf_of_{key}          = {field_value}         in order meta
                _wccf_of_id_{key}       = {field_id}            in order meta
                _wccf_of_data_{key}     = {extra_data_array}    in order meta
                _wccf_file_{access_key} = {file_data}           in order meta
            - Product Fields
                _wccf_pf_{key}          = {field_value}         in order item meta
                _wccf_pf_id_{key}       = {field_id}            in order item meta
                _wccf_pf_data_{key}     = {extra_data_array}    in order item meta
                _wccf_file_{access_key} = {file_data}           in order item meta
            - Product Properties
                _wccf_pp_{key}          = {field_value}         in product meta
                _wccf_pp_id_{key}       = {field_id}            in product meta
                _wccf_pp_data_{key}     = {extra_data_array}    in product meta
                _wccf_file_{access_key} = {file_data}           in product meta
            - Customer Fields
                _wccf_uf_{key}          = {field_value}         in user meta
                _wccf_uf_id_{key}       = {field_id}            in user meta
                _wccf_uf_data_{key}     = {extra_data_array}    in user meta
                _wccf_file_{access_key} = {file_data}           in user meta
    */

    // Singleton instance
    private static $instance = false;

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
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Load translation
        load_textdomain('rp_wccf', WP_LANG_DIR . '/' . WCCF_PLUGIN_KEY . '/rp_wccf-' . apply_filters('plugin_locale', get_locale(), 'rp_wccf') . '.mo');
        load_textdomain('rightpress', WP_LANG_DIR . '/' . WCCF_PLUGIN_KEY . '/rightpress-' . apply_filters('plugin_locale', get_locale(), 'rightpress') . '.mo');
        load_plugin_textdomain('rp_wccf', false, WCCF_PLUGIN_KEY . '/languages/');
        load_plugin_textdomain('rightpress', false, WCCF_PLUGIN_KEY . '/languages/');

        // Admin-only hooks
        if (is_admin() && !defined('DOING_AJAX')) {

            // Additional Plugins page links
            add_filter('plugin_action_links_' . (WCCF_PLUGIN_KEY . '/' . WCCF_PLUGIN_KEY . '.php'), array($this, 'plugins_page_links'));

            // Add settings page menu link
            add_action('admin_menu', array($this, 'admin_menu'), 11);
        }

        // Include RightPress library loader class
        require_once WCCF_PLUGIN_PATH . 'rightpress/rightpress-loader.class.php';

        // Continue setup when all plugins are loaded
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 1);
    }

    /**
     * Continue setup when all plugins are loaded
     *
     * @access public
     * @return void
     */
    public static function on_plugins_loaded()
    {

        // Load helper classes
        RightPress_Loader::load();

        // Load product price component
        RightPress_Loader::load_component(array(
            'rightpress-assets-component',
            'rightpress-product-price',
        ));

        // Check environment
        if (!WCCF::check_environment()) {
            return;
        }

        // Load class collections
        RightPress_Loader::load_class_collection(array(
            'item-control',
            'conditions',
        ));

        // Load includes
        require_once WCCF_PLUGIN_PATH . 'includes/wccf-functions.inc.php';

        // Load field controllers
        require_once WCCF_PLUGIN_PATH . 'classes/fields/field-controllers/wccf-checkout-field-controller.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/field-controllers/wccf-order-field-controller.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/field-controllers/wccf-product-field-controller.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/field-controllers/wccf-product-property-controller.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/field-controllers/wccf-user-field-controller.class.php';

        // Load fields
        require_once WCCF_PLUGIN_PATH . 'classes/fields/fields/wccf-checkout-field.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/fields/wccf-order-field.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/fields/wccf-product-field.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/fields/wccf-product-property.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/fields/fields/wccf-user-field.class.php';

        // Load condition controllers
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/wccf-controller-condition-fields.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/wccf-controller-condition-methods.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/wccf-controller-conditions.class.php';

        // Load conditions
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-coupons.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-product-attributes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-product-categories.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-product-shipping-classes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-product-tags.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-product-variations.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-items-products.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-cart-subtotal.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-checkout-payment-method.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-checkout-shipping-method.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-custom-taxonomy-product.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-customer-capability.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-customer-logged-in.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-customer-meta.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-customer-role.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-coupons.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-customer-capability.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-customer-role.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-items-product-attributes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-items-product-categories.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-items-product-tags.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-items-product-variations.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-items-products.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-payment-method.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-shipping-method.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-order-total.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-other-other-custom-field.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-attributes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-category.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-product.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-property-meta.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-property-shipping-class.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-property-type.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-tags.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-product-variation.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-shipping-country.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-shipping-postcode.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-shipping-state.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/conditions/wccf-condition-shipping-zone.class.php';

        // Load condition methods
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-boolean.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-coupons.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-field.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-list-advanced.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-list.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-meta.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-numeric.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-methods/wccf-condition-method-postcode.class.php';

        // Load condition fields
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-decimal-decimal.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-capabilities.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-countries.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-coupons.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-custom-taxonomy.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-payment-methods.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-product-attributes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-product-categories.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-product-tags.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-product-types.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-product-variations.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-products.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-roles.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-shipping-classes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-shipping-methods.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-shipping-zones.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-multiselect-states.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-select-other-field-id.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-text-meta-key.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-text-postcode.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/conditions/condition-fields/wccf-condition-field-text-text.class.php';

        // Load other classes
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-ajax.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-assets.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-fb.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-files.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-migration.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-pricing.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-settings.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-shortcodes.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-cart.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-checkout.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-order-item.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-order.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-product-price-shop.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-product.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-session.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wc-user.class.php';
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-wp-user.class.php';

        // These classes must always be loaded after other classes are loaded
        require_once WCCF_PLUGIN_PATH . 'classes/wccf-data-updater.class.php';

        // Load integrations
        require_once WCCF_PLUGIN_PATH . 'integrations/wccf-integration-rp-wcdpd.class.php';
        require_once WCCF_PLUGIN_PATH . 'integrations/wccf-integration-wc-product-bundles.class.php';
    }

    /**
     * Check if current user is admin or it's equivalent (shop manager etc)
     *
     * @access public
     * @return bool
     */
    public static function is_admin()
    {

        return current_user_can(self::get_admin_capability());
    }

    /**
     * Get admin capability
     *
     * @access public
     * @return string
     */
    public static function get_admin_capability()
    {

        return 'manage_wccf_settings';
    }

    /**
     * Check if user is authorized to do current action - proxy with filter
     *
     * @access public
     * @param string $action
     * @param array $params
     * @return bool
     */
    public static function is_authorized($action, $params = array())
    {

        // System is always authorized
        if (defined('WCCF_IS_SYSTEM') && WCCF_IS_SYSTEM) {
            return true;
        }

        if ($action === 'manage_posts') {
            $action = 'manage_fields';
        }

        return (bool) apply_filters('wccf_is_authorized', WCCF::is_authorized_check($action, $params), $action, $params);
    }

    /**
     * Check if user is authorized to do current action
     *
     * This is used to check if user is shop manager or if user is allowed to
     * do specific action when other means of authorization are not sufficient
     * (like in our own specific ajax requests)
     *
     * @access public
     * @param string $action
     * @param array $params
     * @return bool
     */
    public static function is_authorized_check($action, $params = array())
    {

        // Shop manager is allowed to do everything
        if (WCCF::is_admin()) {
            return true;
        }

        // Actions allowed for other users
        $non_admin_actions = array('upload_file', 'edit_user_submitted_values');

        // Check if action is allowed for other users
        if (!in_array($action, $non_admin_actions, true)) {
            return false;
        }

        // Check by item id
        if (!empty($params['item_id']) && !empty($params['context'])) {

            // Get item id
            $item_id = (int) $params['item_id'];

            // Get correct capability
            $capability = $params['context'] === 'user_field' ? 'edit_users' : 'edit_posts';

            // Fix item id for order items - need to check if user has access to whole order
            if ($params['context'] === 'product_field') {

                // Get order id
                $item_id = RightPress_Help::get_wc_order_id_from_order_item_id($item_id);

                // Faile to determine order id
                if (!$item_id) {
                    return false;
                }
            }

            // Check capability
            return current_user_can($capability, $item_id);
        }

        // Not authorized
        return false;
    }

    /**
     * Check if current request is for a plugin's settings page
     *
     * @access public
     * @return bool
     */
    public static function is_settings_page()
    {

        global $typenow;
        global $post;

        // Settings page
        if (isset($_REQUEST['page']) && $_REQUEST['page'] === 'wccf_settings') {
            return true;
        }

        // Attempt to get post type from global $post variable
        if (!$typenow && $post && is_object($post) && isset($post->post_type) && $post->post_type) {
            $typenow = $post->post_type;
        }

        // Attempt to get post type from query var post_type
        if (!$typenow && isset($_REQUEST['post_type'])) {
            $typenow = $_REQUEST['post_type'];
        }

        // Attempt to get post type from query var post
        if (!$typenow && !empty($_REQUEST['post']) && is_numeric($_REQUEST['post'])) {
            if (function_exists('get_post_type') && ($post_type = get_post_type($_REQUEST['post']))) {
                $typenow = $post_type;
            }
        }

        // Known post types
        if ($typenow && array_key_exists($typenow, WCCF_Post_Object_Controller::get_post_types())) {
            return true;
        }

        return false;
    }

    /**
     * Check if environment meets requirements
     *
     * @access public
     * @return bool
     */
    public static function check_environment()
    {

        $is_ok = true;

        // Check PHP version
        if (!version_compare(PHP_VERSION, WCCF_SUPPORT_PHP, '>=')) {

            // Add notice
            add_action('admin_notices', array('WCCF', 'php_version_notice'));

            // Do not proceed as RightPress Helper requires PHP 5.3 for itself
            return false;
        }

        // Check WordPress version
        if (!RightPress_Help::wp_version_gte(WCCF_SUPPORT_WP)) {
            add_action('admin_notices', array('WCCF', 'wp_version_notice'));
            $is_ok = false;
        }

        // WooCommerce not enabled
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array('WCCF', 'wc_disabled_notice'));
            $is_ok = false;
        }
        // WooCommerce version is not supported
        else if (!RightPress_Help::wc_version_gte(WCCF_SUPPORT_WC)) {
            add_action('admin_notices', array('WCCF', 'wc_version_notice'));
            $is_ok = false;
        }

        return $is_ok;
    }

    /**
     * Display PHP version notice
     *
     * @access public
     * @return void
     */
    public static function php_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(__('<strong>%s</strong> requires PHP %s or later. Please update PHP on your server to use this plugin.', 'rp_wccf'), WCCF_PLUGIN_NAME, WCCF_SUPPORT_PHP) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'rp_wccf'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'rp_wccf') . '</a>') . '</p></div>';
    }

    /**
     * Display WP version notice
     *
     * @access public
     * @return void
     */
    public static function wp_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(__('<strong>%s</strong> requires WordPress version %s or later. Please update WordPress to use this plugin.', 'rp_wccf'), WCCF_PLUGIN_NAME, WCCF_SUPPORT_WP) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'rp_wccf'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'rp_wccf') . '</a>') . '</p></div>';
    }

    /**
     * Display WC disabled notice
     *
     * @access public
     * @return void
     */
    public static function wc_disabled_notice()
    {

        echo '<div class="error"><p>' . sprintf(__('<strong>%s</strong> requires WooCommerce to be active. You can download WooCommerce %s.', 'rp_wccf'), WCCF_PLUGIN_NAME, '<a href="http://url.rightpress.net/woocommerce-download-page">' . __('here', 'rp_wccf') . '</a>') . ' ' . sprintf(__('If you have any questions, please contact %s.', 'rp_wccf'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'rp_wccf') . '</a>') . '</p></div>';
    }

    /**
     * Display WC version notice
     *
     * @access public
     * @return void
     */
    public static function wc_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(__('<strong>%s</strong> requires WooCommerce version %s or later. Please update WooCommerce to use this plugin.', 'rp_wccf'), WCCF_PLUGIN_NAME, WCCF_SUPPORT_WC) . ' ' . sprintf(__('If you have any questions, please contact %s.', 'rp_wccf'), '<a href="http://url.rightpress.net/new-support-ticket">' . __('RightPress Support', 'rp_wccf') . '</a>') . '</p></div>';
    }

    /**
     * Add settings link on plugins page
     *
     * @access public
     * @param array $links
     * @return void
     */
    public function plugins_page_links($links)
    {

        // Add support link
        $settings_link = '<a href="http://url.rightpress.net/woocommerce-custom-fields-help" target="_blank">'.__('Support', 'rp_wccf').'</a>';
        array_unshift($links, $settings_link);

        // Add settings link
        if (self::check_environment()) {
            $settings_link = WCCF::is_custom_checkout_fields() ? 'wccf_checkout_field' : 'wccf_product_field';
            $settings_link = '<a href="edit.php?post_type=' . $settings_link . '">' . __('Settings', 'rp_wccf') . '</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * Add or remove admin menu items
     *
     * @access public
     * @return void
     */
    public function admin_menu()
    {

        global $submenu;

        // Define new menu order
        $reorder = array(
            'edit.php?post_type=wccf_product_field'     => 51,
            'edit.php?post_type=wccf_product_prop'      => 52,
            'edit.php?post_type=wccf_checkout_field'    => 53,
            'edit.php?post_type=wccf_user_field'        => 54,
            'edit.php?post_type=wccf_order_field'       => 55,
        );

        // Check if our menu exists
        if (isset($submenu['edit.php?post_type=wccf_product_field'])) {

            // Iterate over submenu items
            foreach ($submenu['edit.php?post_type=wccf_product_field'] as $item_key => $item) {

                // Remove Add Field menu link
                if (in_array('post-new.php?post_type=wccf_product_field', $item)) {
                    unset($submenu['edit.php?post_type=wccf_product_field'][$item_key]);
                }

                // Rearrange other items
                foreach ($reorder as $order_key => $order) {
                    if (in_array($order_key, $item)) {

                        // Check if menu item should be displayed
                        $display = false;

                        // Current plugin is WooCommerce Custom Fields - leave all menu items
                        if (!WCCF::is_custom_product_addons() && !WCCF::is_custom_checkout_fields()) {

                            $display = true;
                        }
                        // Current plugin is WooCommerce Custom Product Add-Ons - leave Product Fields only
                        else if (WCCF::is_custom_product_addons() && $order_key === 'edit.php?post_type=wccf_product_field') {

                            $display = true;
                        }
                        // Current plugin is WooCommerce Custom Checkout Fields - leave Checkout Fields and User Fields only
                        else if (WCCF::is_custom_checkout_fields() && in_array($order_key, array('edit.php?post_type=wccf_checkout_field', 'edit.php?post_type=wccf_user_field'), true)) {

                            $display = true;
                        }

                        // Maybe add menu item to correct position
                        if ($display) {

                            $submenu['edit.php?post_type=wccf_product_field'][$order] = $item;
                        }

                        // Unset item from current position
                        unset($submenu['edit.php?post_type=wccf_product_field'][$item_key]);
                    }
                }
            }

            // Sort array by key
            ksort($submenu['edit.php?post_type=wccf_product_field']);
        }
    }

    /**
     * Include template
     *
     * @access public
     * @param string $template
     * @param array $args
     * @return string
     */
    public static function include_template($template, $args = array())
    {

        RightPress_Help::include_template($template, WCCF_PLUGIN_PATH, WCCF_PLUGIN_KEY, $args);
    }

    /**
     * Check if currently running plugin is WooCommerce Custom Product Add-Ons
     *
     * @access public
     * @return bool
     */
    public static function is_custom_product_addons()
    {

        return defined('RP_WCCF_CUSTOM_PRODUCT_ADDONS_LOADED') && RP_WCCF_CUSTOM_PRODUCT_ADDONS_LOADED;
    }

    /**
     * Check if currently running plugin is WooCommerce Custom Checkout Fields
     *
     * @access public
     * @return bool
     */
    public static function is_custom_checkout_fields()
    {

        return defined('RP_WCCF_CUSTOM_CHECKOUT_FIELDS_LOADED') && RP_WCCF_CUSTOM_CHECKOUT_FIELDS_LOADED;
    }





}

WCCF::get_instance();

}
