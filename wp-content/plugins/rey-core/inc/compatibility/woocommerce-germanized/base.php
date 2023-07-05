<?php
namespace ReyCore\Compatibility\WoocommerceGermanized;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{

	public function __construct()
	{
		add_action('init', [$this, 'init']);

		/**
		 * To disable Germanized adjustments,
		 * add this snippet below into the child theme's functions.php .
		 *
		 *   if( ! defined('WC_GZD_DISABLE_CHECKOUT_ADJUSTMENTS') ){
		 *    define('WC_GZD_DISABLE_CHECKOUT_ADJUSTMENTS', true);
		 *   }
		 *
		 * I already tried different ways but Rey's Custom checkout layout is added through
		 * an Elementor element and therefore will execute code too late.
		 */

	}

	function init(){
		add_filter('woocommerce_get_script_data', [$this, 'checkout_params'], 10, 2);
		add_filter('theme_mod_checkout_add_thumbs', '__return_false', 100);
		add_filter('reycore/woocommerce/checkout/force_custom_layout', '__return_false', 100);
	}

	function checkout_params($params, $handle){

		if( $handle === 'wc-checkout' ){
			$params['exclude_cloning_fields'] = '#shipping_address_type';
		}

		return $params;
	}

}
