<?php
namespace ReyCore\Compatibility\WoocommerceTabManager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{

	public function __construct()
	{
		add_filter( 'wc_tab_manager_get_product_tab', [ $this, 'cleanup_toggle_desc' ] );
	}

	function cleanup_toggle_desc($tab){

		if( is_product() && get_theme_mod('product_content_blocks_desc_toggle', false) ){
			$rep = [
				'<div class="rey-prodDescToggle u-toggle-text-next-btn ">',
				'</div><button class="btn btn-line-active"><span data-read-more="Read more" data-read-less="Less"></span></button>',
			];
			$tab['content'] = str_replace($rep, '', $tab['content']);
		}

		return $tab;
	}

}
