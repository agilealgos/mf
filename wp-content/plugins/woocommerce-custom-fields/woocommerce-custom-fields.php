<?php

/**
 * Plugin Name: WooCommerce Custom Fields
 * Plugin URI: http://www.rightpress.net/woocommerce-custom-fields
 * Description: Create custom fields for WooCommerce product, checkout, order and customer pages
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 *
 * Text Domain: rp_wccf
 * Domain Path: /languages
 *
 * Version: 2.3.4
 *
 * Requires at least: 4.0
 * Tested up to: 5.4
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.3
 *
 * @package WooCommerce Custom Fields
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Only load plugin if its constants are not yet defined
// Note: We only need to do this for WooCommerce Custom Fields since it has related plugins that use the same code base
if (!defined('WCCF_VERSION')) {

    // Define Constants
    define('WCCF_PLUGIN_KEY', 'woocommerce-custom-fields');
    define('WCCF_PLUGIN_NAME', 'WooCommerce Custom Fields');
    define('WCCF_PLUGIN_PUBLIC_PREFIX', 'wccf_');
    define('WCCF_PLUGIN_PRIVATE_PREFIX', 'wccf_');
    define('WCCF_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('WCCF_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
    define('WCCF_SUPPORT_PHP', '5.3');
    define('WCCF_SUPPORT_WP', '4.0');
    define('WCCF_SUPPORT_WC', '3.0');
    define('WCCF_VERSION', '2.3.4');

    // Load main plugin class
    require_once 'wccf.class.php';

    // Initialize automatic updates
    require_once(WCCF_PLUGIN_PATH . 'rightpress-updates/rightpress-updates.class.php');
    RightPress_Updates_11332742::init(__FILE__, WCCF_VERSION);
}
