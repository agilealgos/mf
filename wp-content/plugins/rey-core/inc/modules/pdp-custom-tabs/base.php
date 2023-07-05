<?php
namespace ReyCore\Modules\PdpCustomTabs;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-pdp-custom-tabs';

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
	}

	public function init() {

		new Customizer();
		new AcfFields();

		if( ! $this->is_enabled() ){
			return;
		}

		add_filter( 'woocommerce_product_tabs', [$this, 'manage_tabs'], 20);
		add_filter( 'acf/load_value/key=field_5ecae99f56e6d', [$this, 'load_custom_tabs'], 10, 3);

	}

	public function manage_tabs($tabs){

		$custom_tabs = $this->option();

		if( ! (is_array($custom_tabs) && class_exists('\ACF')) ){
			return $tabs;
		}

		$custom_tabs_content = get_field('product_custom_tabs');

		foreach ($custom_tabs as $key => $c_tab) {

			$tab_content = isset($c_tab['content']) ? reycore__parse_text_editor($c_tab['content']) : '';

			if( isset($custom_tabs_content[$key]['tab_content']) && !empty($custom_tabs_content[$key]['tab_content']) ){
				$tab_content =  reycore__parse_text_editor( $custom_tabs_content[$key]['tab_content'] );
			}

			if( empty($tab_content) ){
				continue;
			}

			$title = $c_tab['text'];

			// Override with custom title
			if( isset($custom_tabs_content[$key]['override_title']) && $custom_tabs_content[$key]['override_title'] &&
				isset($custom_tabs_content[$key]['tab_title']) && !empty($custom_tabs_content[$key]['tab_title']) ){
				$title = reycore__parse_text_editor( $custom_tabs_content[$key]['tab_title'] );
			}

			// legacy, force the default title if specified with filter
			if( isset($custom_tabs_content[$key]['tab_title']) && $c_tab['text'] !== $custom_tabs_content[$key]['tab_title'] &&
				apply_filters('reycore/woocommerce/custom_tabs/force_default_title', false ) ){
				$title = $c_tab['text'];
			}

			$tabs['custom_tab_' . $key] = [
				'title' => $title,
				'priority' => absint($c_tab['priority']),
				'callback' => function() use ($tab_content) {
					echo reycore__parse_text_editor($tab_content);
				},
				'type' => 'custom'
			];

		}

		return $tabs;
	}


	public function load_custom_tabs($value, $post_id, $field) {

		$tabs = $this->option();

		// has custom content
		if ($value !== false) {

			// if new tabs were added, load the new tab
			if( is_array($tabs) && !empty($tabs) && count($value) < count($tabs) ){

				for ($i=0; $i < count($tabs) - count($value); $i++) {
					array_shift($tabs);
				}

				foreach ($tabs as $key => $tab) {

					$new_tab = [
						'field_615be5b543708' => '',
						'field_5ecae9c356e6e' => $tab['text'],
						'field_5ecae9ef56e6f' => '',
					];

					$value[] = $new_tab;
				}
			}

			return $value;
		}


		if( is_array($tabs) && !empty($tabs) ){
			$value = [];
			foreach ($tabs as $key => $tab) {
				$value[]['field_5ecae9c356e6e'] = $tab['text'];
			}
		}

		return $value;
	}

	public function option() {
		return get_theme_mod('single__custom_tabs', []);
	}

	public function is_enabled() {
		return ! empty( $this->option() );
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Custom Tabs in Product Page', 'Module name', 'rey-core'),
			'description' => esc_html_x('Adds the ability to create as many custom tabs or blocks, inside product pages.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/create-product-page-custom-tabs-blocks/#add-custom-tabs-blocks'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
