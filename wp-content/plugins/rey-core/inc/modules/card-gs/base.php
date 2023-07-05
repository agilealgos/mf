<?php
namespace ReyCore\Modules\CardGs;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-card-gs';

	const GSTYPE = 'card';

	public function __construct()
	{
		add_action( 'init', [$this, 'init']);

		add_filter( 'reycore/global_sections/types', [$this, 'add_support'], 20);
		add_action( 'elementor/element/reycore-carousel/section_content_style/before_section_end', [$this, 'add_card_gs_control_options']);
		add_action( 'elementor/element/reycore-grid/section_content_style/before_section_end', [$this, 'add_card_gs_control_options']);
		add_action( 'reycore/elementor/document_settings/gs/before', [$this, 'gs_settings']);
		add_action( 'reycore/cards/not_existing', [$this, 'render_gs'], 10, 2);

	}

	public function init() {

		if( ! $this->is_enabled() ){
			return;
		}

	}

	public function add_support( $gs ){
		$gs[self::GSTYPE]  = __( 'Card', 'rey-core' );
		return $gs;
	}

	public function add_card_gs_control_options( $stack ){

		if( ! ($card_module = reycore__get_module('cards')) ){
			return;
		}

		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		$unique_name = $stack->get_unique_name();

		// add to Layout list
		$card_control = $controls_manager->get_control_from_stack( $unique_name, $card_module::CARD_KEY );
		$card_control['options'][self::GSTYPE] = esc_html__('Card Template (Global Section)', 'rey-core');
		$stack->update_control( $card_module::CARD_KEY, $card_control );

		// Choose template
		$stack->add_control(
			'card_gs_template',
			[
				'label_block' => true,
				'label'       => __( 'Choose Card Template', 'rey-core' ),
				'type'        => 'rey-query',
				'default'     => '',
				'placeholder' => esc_html__('- Select -', 'rey-core'),
				'query_args'  => [
					'type'      => 'posts',
					'post_type' => \ReyCore\Elementor\GlobalSections::POST_TYPE,
					'meta'      => [
						'meta_key'   => 'gs_type',
						'meta_value' => self::GSTYPE,
					]
				],
				'condition' => [
					$card_module::CARD_KEY => self::GSTYPE,
				],
			]
		);

	}

	public function render_gs($card_id, $element){

		if( self::GSTYPE !== $card_id ){
			return;
		}

		$_settings = $element->_settings;

		if( ! ( isset($_settings['card_gs_template']) && ($card_gs_id = $_settings['card_gs_template']) ) ){
			return;
		}

		$item_settings = $element->_items[$element->item_key];

		if( ! ( isset($item_settings['post_id']) && ($post_id = $item_settings['post_id']) ) ){
			return;
		}

		$GLOBALS['post'] = get_post( $post_id ); // WPCS: override ok.
		setup_postdata( $GLOBALS['post'] );

			echo \ReyCore\Elementor\GlobalSections::do_section($card_gs_id, false, true);

		wp_reset_postdata();

	}


	/**
	 * Add page settings into Elementor
	 *
	 * @since 2.4.4
	 */
	public function gs_settings( $doc )
	{

		$params = $doc->get_params();
		$params['preview_width'][] = self::GSTYPE;
		$doc->set_params($params);

	}

	public function is_enabled() {
		return false;
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Card Global Section module', 'Module name', 'rey-core'),
			'description' => esc_html_x('Build global sections which are used as templates for various elements (Carousel, Grid)', 'Module description', 'rey-core'),
			// 'icon'        => '',
			// 'categories'  => ['woocommerce'],
			// 'keywords'    => ['Elementor', 'Product Page', 'Product catalog'],
			// 'help'        => reycore__support_url('kb/custom-templates/'),
			'show_in_manager'  => false,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
