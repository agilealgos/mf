<?php
namespace ReyCore\Compatibility\CheckoutWc;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase {

	public function __construct() {
		add_action('reycore/assets/enqueue', [ $this, 'remove_styles' ] );
	}

	public function remove_styles($manager){

		if( ! (function_exists('is_checkout') && is_checkout()) ){
			return;
		}

		$manager->remove_styles(['rey-wc-forms']);
	}

}
