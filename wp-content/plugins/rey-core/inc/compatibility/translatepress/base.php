<?php
namespace ReyCore\Compatibility\Translatepress;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{
	public function __construct()
	{
		add_filter('trp_force_search', [$this, 'force_search'], 10);
		add_action('reycore/woocommerce/search/before_get_data', [$this, 'ajax_search_before_get_data']);
		add_filter( 'reycore/is_multilanguage', [$this, 'is_multilanguage'] );

	}

	function ajax_search_before_get_data(){

		// force translated title
		add_filter( 'the_title', function($title){
			$trp = \TRP_Translate_Press::get_trp_instance();
			$translation_render = $trp->get_component( 'translation_render' );
			return $translation_render->translate_page($title);
		}, 20);

	}

	function force_search( $status ){

		if( isset( $_REQUEST[ \ReyCore\Ajax::ACTION_KEY ] ) && $_REQUEST[ \ReyCore\Ajax::ACTION_KEY ] === 'ajax_search' ){
			return true;
		}

		return $status;
	}

	public function is_multilanguage() {
		return true;
	}

}
