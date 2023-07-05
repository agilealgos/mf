<?php
/**
 * Plugin Name: WooCommerce Split Orders
 * Plugin URI: https://woocommerce.com/products/split-orders
 * Description: Split orders into multiple separate orders for processing separately.
 * Version: 1.6.0
 * Author: Vibe Agency
 * Author URI: https://vibeagency.uk
 * Developer: Vibe Agency
 * Developer URI: https://vibeagency.uk
 * Text Domain: split-orders
 * Domain path: /languages
 *
 * Woo: 6209689:c8394fda7ddbad90c11464006bf2dc9f
 * WC requires at least: 5.9
 * WC tested up to: 6.5
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use Vibe\Split_Orders\Split_Orders;

define( 'VIBE_SPLIT_ORDERS_VERSION', '1.6.0' );

// Autoloader for all classes
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Protect against conflicts and loading multiple copies of plugin
if ( ! function_exists( 'vibe_split_orders' ) ) {

	/**
	 * Returns the singleton instance of the main plugin class
	 *
	 * @return Split_Orders The singleton
	 */
	function vibe_split_orders() {
		return Split_Orders::instance();
	}

	// Initialise the plugin
	vibe_split_orders();

}
