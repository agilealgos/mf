<?php
namespace ReyCore\Elementor\Custom;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use \ReyCore\Elementor\Helper;

class Button
{
	function __construct(){
		add_action( 'elementor/element/button/section_button/before_section_end', [$this, 'button_settings'], 10);
		add_action( 'elementor/element/button/section_button/after_section_end', [$this, 'after_section_end'], 10);
		add_action( 'elementor/element/button/section_style/before_section_end', [$this, 'add_block_option'], 10);
		add_action( 'elementor/element/reycore-acf-button/section_button/before_section_end', [$this, 'button_settings'], 10);
		add_action( 'elementor/element/reycore-acf-button/section_button/after_section_end', [$this, 'after_section_end'], 10);
		add_action( 'elementor/element/reycore-acf-button/section_style/before_section_end', [$this, 'add_block_option'], 10);
		add_action( 'elementor/frontend/widget/before_render', [$this, 'before_render'], 10);
	}

	/**
	 * Add custom settings into Elementor's Section
	 *
	 * @since 1.0.0
	 */
	function button_settings( $element )
	{
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;

		/* Add button types */

		// Get existing button type control
		$button_type = $controls_manager->get_control_from_stack( $element->get_unique_name(), 'button_type' );
		// Add new styles
		$button_type['options'] = $button_type['options'] + \ReyCore\Elementor\Helper::button_styles();
		// Update the control
		$element->update_control( 'button_type', $button_type );

		/* Add button link control dependencies */

		// Get existing button type control
		$button_link = $controls_manager->get_control_from_stack( $element->get_unique_name(), 'link' );
		// Add new styles
		$button_link['condition'] = [
			'rey_atc_enable' => '',
			'rey_trigger' => '',
		];
		// Update the control
		$element->update_control( 'link', $button_link );

		/* Add controls */

		$element->start_injection( [
			'of' => 'icon_indent',
		] );

		$element->add_responsive_control(
			'rey_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'max' => 1000,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}px;',
				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$element->add_control(
			'rey_icon_style',
			[
				'label' => esc_html__( 'Icon Effect', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'None', 'rey-core' ),
					'aoh'  => esc_html__( 'Animate on hover (horizontal)', 'rey-core' ),
					'aov'  => esc_html__( 'Animate on hover (vertical)', 'rey-core' ),
					'soh'  => esc_html__( 'Show on hover', 'rey-core' ),
					// 'soh'  => esc_html__( 'Show on parent column hover', 'rey-core' ),
				],
				'prefix_class' => '--icon-style-',
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$element->end_injection();
	}

	function after_section_end( $element ){
		$this->modal_settings($element);
		$this->add_to_cart_settings($element);
		$this->trigger_settings($element);
	}

	/**
	 * Add option to enable modal link
	 *
	 * @since 1.0.0
	 */
	function modal_settings( $element )
	{

		$element->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Modal Settings', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

		$element->add_control(
			'rey_enable_modal',
			[
				'label' => __( 'Enable Modal Link', 'rey-core' ),
				'description' => sprintf(__( 'Enable to be able to open modal window. Make sure to add Modal section unique ID in the link field. Learn <a href="%s" target="_blank">how to create modals</a>.', 'rey-core' ), reycore__support_url('kb/create-modal-sections/') ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$element->add_control(
			'rey_modal_replace',
			[
				'label' => __( 'Text Replace in modal', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'rey-core' ),
				'description' => sprintf( __( 'Replace text in modal. Each replacement in a separate line. Separate replacement key from the value using %s character.', 'rey-core' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => [
					'rey_enable_modal!' => '',
				],
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Add option
	 *
	 * @since 1.0.0
	 */
	function add_block_option( $element )
	{

		$element->add_responsive_control(
			'rey_btn_block',
			[
				'label' => esc_html__( 'Stretch Button', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => '--btn-block-%s-',
				'separator' => 'before'
			]
		);

	}

	/**
	 * Add option to enable add to cart link
	 *
	 * @since 1.0.0
	 */
	function add_to_cart_settings( $element )
	{

		$element->start_controls_section(
			'section_atc',
			[
				'label' => __( 'Add To Cart Settings', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

		$element->add_control(
			'rey_atc_enable',
			[
				'label' => __( 'Enable Add To Cart Link', 'rey-core' ),
				'description' => __( 'Enable this option to force this button to link to adding a product to cart.', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$element->add_control(
			'rey_atc_product',
			[
				'label' => esc_html__( 'Select Product', 'rey-core' ),
				'description' => esc_html__( 'Leave empty to automatically detect the product, if this button is placed inside a product page.', 'rey-core' ),
				'default' => '',
				'label_block' => true,
				'type' => 'rey-query',
				'query_args' => [
					'type' => 'posts',
					'post_type' => 'product',
				],
				'condition' => [
					'rey_atc_enable!' => '',
				],
			]
		);

		$element->add_control(
			'rey_atc_checkout',
			[
				'label' => esc_html__( 'Redirect to checkout?', 'rey-core' ),
				'description' => __( 'You can basically transform this button into a "Buy Now" button. <strong>Please make sure the "Link" is empty!</strong>.', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'rey_atc_enable!' => '',
				],
			]
		);

		$element->add_control(
			'rey_atc_text',
			[
				'label' => esc_html__( 'Custom "Added to cart" text', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'rey_atc_enable!' => '',
				],
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Add option to enable trigger link
	 *
	 * @since 1.0.0
	 */
	function trigger_settings( $element )
	{

		$element->start_controls_section(
			'section_trigger',
			[
				'label' => __( 'Trigger Settings', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

		$element->add_control(
			'rey_trigger',
			[
				'label' => esc_html__( 'Action', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( '- Select -', 'rey-core' ),
					'offcanvas'  => esc_html__( 'Open Off-Canvas (Global Section)', 'rey-core' ),
					// dropdpwn
				],
			]
		);

		$element->add_control(
			'rey_trigger_offcanvas',
			[
				'label_block' => true,
				'label' => __( 'Off-Canvas Panel Sections', 'rey-core' ),
				'default' => '',
				'type' => 'rey-ajax-list',
				'query_args' => [
					'request' => ['\ReyCore\Elementor\GlobalSections', 'get_global_sections_options'],
					'type' => 'offcanvas',
					'edit_link' => true,
				],
				'condition' => [
					'rey_trigger' => 'offcanvas',
				],
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Render some attributes before rendering
	 *
	 * @since 1.0.0
	 **/
	function before_render( $element )
	{

		if( ! in_array($element->get_unique_name(), ['button', 'reycore-acf-button'], true) ){
			return;
		}

		$settings = $element->get_data('settings');

		if( isset($settings['rey_enable_modal']) && $settings['rey_enable_modal'] !== '' ){
			$this->do_modal( $element );
		}

		if( isset($settings['rey_atc_enable']) && $settings['rey_atc_enable'] !== '' ){
			$this->do_atc( $element );
		}

		if( isset($settings['rey_trigger']) && $settings['rey_trigger'] !== '' ){
			$this->do_trigger( $element, $settings );
		}

	}

	function do_modal($element){

		$settings = $element->get_settings();

		$modal_params = [];
		$external_url = false;

		// override URL, but only works when link is empty
		if( isset($settings['link'], $settings['link']['url']) && ($url = $settings['link']['url']) ){

			// make sure to specify iframe if URL
			if( ( strpos( $url, 'http://' ) === 0 || strpos( $url, 'https://' ) === 0 ) &&
				filter_var( $url, FILTER_VALIDATE_URL ) !== false ){
				$external_url = true;
			}

			$modal_params['id'] = str_replace(['#', '.', '/', '?', ':', '='], '', $url);

			if( $external_url ){
				$modal_params['type'] = 'iframe';
				$modal_params['src'] = $url;
			}
			else {
				$modal_params['content'] = sprintf('[data-rey-modal-id="%s"]', $url);
			}
		}

		// Replacements
		if ( ! $external_url && isset($settings['rey_modal_replace']) && ! empty( $settings['rey_modal_replace'] ) ) {

			$replacements_ready = [];

			foreach ( explode( "\n", $settings['rey_modal_replace'] ) as $replacement ) {
				if ( ! empty( $replacement ) ) {

					$attr = explode( '|', $replacement, 2 );

					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					$replacements_ready[ esc_attr( $attr[0] ) ] = esc_attr( $attr[1] );
				}
			}

			if( $replacements_ready ){
				$modal_params['replacements'] = $replacements_ready;
			}
		}

		if( ! empty($modal_params) ){
			$element->add_render_attribute( 'button', 'data-rey-section-modal', esc_attr(wp_json_encode($modal_params)) );
		}

	}

	function do_atc($element){

		if( ! class_exists('\WooCommerce') ){
			return;
		}

		$settings = $element->get_settings();

		if( !( $product_id = $settings['rey_atc_product'] ) ){
			if( !(is_product() && ($product = wc_get_product()) && $product_id = $product->get_id()) ){
				return;
			}
		}

		$setting_url = $settings['link'];

		// override URL, but only works when link is empty
		if( empty($setting_url['url']) && isset($settings['rey_atc_checkout']) && ($settings['rey_atc_checkout'] !== '') ){

			$setting_url['url'] = add_query_arg([
				'add-to-cart' => $product_id,
				], wc_get_checkout_url()
			);

			$element->add_link_attributes( 'button', $setting_url, true );
			return;
		}

		$element->add_render_attribute( 'button', 'data-product_id', esc_attr($product_id) );
		$element->add_render_attribute( 'button', 'data-quantity', 1 );
		$element->add_render_attribute( 'button', 'class', 'add_to_cart_button ajax_add_to_cart' );

		if( isset($settings['rey_atc_checkout']) && ($settings['rey_atc_checkout'] !== '') ){
			$element->add_render_attribute( 'button', 'data-checkout', esc_url( get_permalink( wc_get_page_id( 'checkout' ) ) ) );
			$element->add_render_attribute( 'button', 'class', '--prevent-aatc --prevent-open-cart' );
		}

		if( isset($settings['rey_atc_text']) ){
			$element->add_render_attribute( 'button', 'data-atc-text', esc_attr($settings['rey_atc_text']) );
		}

		reyCoreAssets()->add_scripts('reycore-elementor-elem-button-add-to-cart');

	}

	function do_trigger( $element, $settings ){

		if( ! ( isset($settings['rey_trigger_offcanvas']) && $gs_id = absint($settings['rey_trigger_offcanvas']) ) ){
			return;
		}

		if( reycore__is_multilanguage() ){
			$gs_id = apply_filters('reycore/translate_ids', $gs_id, \ReyCore\Elementor\GlobalSections::POST_TYPE);
		}

		add_filter("reycore/module/offcanvas_panels/load_panel={$gs_id}", '__return_true');

		$element->add_render_attribute( 'button', [
			'data-offcanvas-id' => $gs_id,
			'data-trigger' => 'click',
			'class' => 'js-triggerBtn',
			'href' => '#'
		] );

		do_action('reycore/elementor/btn_trigger');

	}

}
