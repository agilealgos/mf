<?php
namespace ReyCore\Customizer\Options\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class CatalogProduct extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'woo-catalog-product-item';
	}

	public function get_title(){
		return esc_html__('Product Components & Layout', 'rey-core');
	}

	public function get_priority(){
		return 40;
	}

	public function get_icon(){
		return 'woo-catalog-product-components';
	}

	public function help_link(){
		return reycore__support_url('kb/customizer-woocommerce/#product-catalog-components');
	}

	public function controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Product Layout', 'rey-core' ),
			'open' => true
		]);

			$this->add_title( '', [
				'description' => esc_html__('Customize the product item layout.', 'rey-core'),
				'separator'   => 'none',
			]);

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'loop_animate_in',
				'label'       => esc_html__( 'Animate In (on scroll)', 'rey-core' ),
				'default'     => true,
			] );

			$this->add_control( [
				'type'         => 'select',
				'settings'     => 'loop_skin',
				'rey_preset'   => 'catalog',
				'label'        => esc_html__('Product Skin', 'rey-core'),
				'description'  => __('Select a skin for the product item.', 'rey-core'),
				'default'      => 'basic',
				'choices'      => [],
				'ajax_choices' => 'get_loop_skins_list',
			]);

			/* ------------------------------------ Basic Skin options ------------------------------------ */

			$this->start_controls_group( [
				'label'    => esc_html__( 'Basic Skin Options', 'rey-core' ),
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'basic',
					],
				],
			]);

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'loop_hover_animation',
				'rey_preset' => 'catalog',
				'label'       => esc_html__('Hover animation', 'rey-core'),
				'description' => __('Select if products should have an animation effect on hover.', 'rey-core'),
				'default'     => true,
			]);

			$this->add_control( [
				'type'        => 'slider',
				'settings'    => 'loop_basic_inner_padding',
				'rey_preset' => 'catalog',
				'label'       => esc_html__( 'Content Inner padding', 'rey-core' ),
				'default'     => 0,
				'transport'       => 'auto',
				'choices'     => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'basic',
					],
				],
				'output'          => [
					[
						'element'  		   => ':root',
						'property' 		   => '--woocommerce-loop-basic-padding',
						'units' 		   => 'px',
					],
				],
			] );


			$this->add_control( [
				'type'            => 'rey-color',
				'settings'        => 'loop_basic__border_color',
				'label'           => __( 'Border Color', 'rey-core' ),
				'default'         => '',
				'choices'         => [
					'alpha'          => true,
				],
				'transport'       => 'auto',
				'active_callback' => [
					[
						'setting'  => 'loop_gap_size_v2',
						'operator' => '==',
						'value'    => 0,
					],
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'basic',
					],
				],
				'output'          => [
					[
						'element'  		   => '.woocommerce ul.products.--skin-basic',
						'property' 		   => '--woocommerce-loop-basic-bordercolor',
					],
				]
			] );

			$this->add_control( [
				'type'            => 'rey-color',
				'settings'        => 'loop_basic_bg_color',
				'rey_preset' => 'catalog',
				'label'           => __( 'Background Color', 'rey-core' ),
				'default'         => '',
				'choices'         => [
					'alpha'          => true,
				],
				'transport'       => 'auto',
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'basic',
					],
				],
				'output'          => [
					[
						'element'  		   => ':root',
						'property' 		   => '--woocommerce-loop-basic-bgcolor',
					],
				],
			] );

			$this->end_controls_group();

			/* ------------------------------------ COMMON OPTIONS ------------------------------------ */


			$this->add_control( [
				'type'        => 'rey-number',
				'settings'    => 'loop_item_inner_padding',
				'label'       => esc_html__( 'Content Inner padding', 'rey-core' ),
				'default'     => '',
				'transport'   => 'auto',
				'choices'     => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'responsive' => true,
				'output' => [
					[
						'element'  => '.woocommerce ul.products',
						'property' => '--woocommerce-loop-inner-padding',
						'units'    => 'px',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-number',
				'settings'    => 'loop_components_spacing',
				'label'       => esc_html__( 'Components spacing', 'rey-core' ) . ' (px)',
				'default'     => '',
				'transport'   => 'auto',
				'choices'     => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'responsive' => true,
				'output' => [
					[
						'element'  => '.woocommerce ul.products li.product',
						'property' => '--components-spacing',
						'units'    => 'px',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => ['default', 'basic', 'wrapped', 'proto', 'rigo', 'iconized', 'cards'], // move to modules
					],
				],
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'loop_expand_thumbnails',
				'label'       => esc_html__('Expand thumbnails?', 'rey-core'),
				'help' => [
					__('Force thumbnails to expand til edges regardless of surrounding padding.', 'rey-core')
				],
				'default'     => false,
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-number',
				'settings'    => 'loop_border_size',
				'label'       => esc_html__( 'Border size', 'rey-core' ),
				'default'     => '',
				'choices'     => [
					'min'  => 0,
					'max'  => 10,
					'step' => 1,
				],
				'output'          => [
					[
						'element'  => '.woocommerce ul.products',
						'property' => '--woocommerce-loop-border-size',
						'units' 		 => 'px',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			$this->add_control( [
				'type'            => 'rey-color',
				'settings'        => 'loop_border_color',
				'label'           => __( 'Border Color', 'rey-core' ),
				'default'         => '',
				'choices'         => [
					'alpha'          => true,
				],
				'transport'       => 'auto',
				'output'          => [
					[
						'element'  		   => '.woocommerce ul.products',
						'property' 		   => '--woocommerce-loop-border-color',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			$this->add_control( [
				'type'            => 'rey-color',
				'settings'        => 'loop_bg_color',
				'label'           => __( 'Background Color', 'rey-core' ),
				'default'         => '',
				'choices'         => [
					'alpha'          => true,
				],
				'transport'       => 'auto',
				'output'          => [
					[
						'element'  		   => '.woocommerce ul.products',
						'property' 		   => '--woocommerce-loop-bg-color',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-number',
				'settings'    => 'loop_radius',
				'label'       => esc_html__( 'Border radius', 'rey-core' ),
				'default'     => '',
				'choices'     => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
				'output'          => [
					[
						'element'  		   => '.woocommerce ul.products',
						'property' 		   => '--woocommerce-loop-radius',
						'units' 		   => 'px',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => 'in',
						'value'    => [],
					],
				],
			] );

			/* ------------------------------------ Wrapped Skin options ------------------------------------ */

			$this->start_controls_group( [
				'label'    => esc_html__( 'Wrapped Skin Options', 'rey-core' ),
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
			]);

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'wrapped_loop_hover_animation',
				'label'       => esc_html__('Hover animation', 'rey-core'),
				'description' => __('Select if products should have an animation effect on hover.', 'rey-core'),
				'default'     => true,
			]);

			$this->add_control( [
				'type'        => 'slider',
				'settings'    => 'wrapped_loop_basic_inner_padding',
				'label'       => esc_html__( 'Content Inner padding', 'rey-core' ),
				'default'     => 40,
				'transport'       => 'auto',
				'choices'     => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
				'output'          => [
					[
						'element'  		   => '.woocommerce ul.products li.product.rey-wc-skin--wrapped',
						'property' 		   => '--woocommerce-loop-wrapped-padding',
						'units' 		   => 'px',
					],
				],
				'responsive' => true
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'wrapped_loop_overlay_color',
				'label'       => esc_html__( 'Overlay Color', 'rey-core' ),
				'default'     => 'rgba(0, 0, 0, 0.3)',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce ul.products li.product.rey-wc-skin--wrapped',
						'property' 		=> '--woocommerce-loop-wrapped-ov-color',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'wrapped_loop_overlay_color_hover',
				'label'       => esc_html__( 'Overlay Hover Color', 'rey-core' ),
				'default'     => 'rgba(0, 0, 0, 0.45)',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce ul.products li.product.rey-wc-skin--wrapped',
						'property' 		=> '--woocommerce-loop-wrapped-ov-color-hover',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
			] );


			$this->add_control( [
				'type'        => 'text',
				'settings'    => 'wrapped_loop_item_height',
				'label'       => esc_html__( 'Item Height', 'rey-core' ),
				'help' => [
					__('Select a custom height for the product images. Don\'t forget unit.', 'rey-core')
				],
				'input_attrs' => [
					'placeholder'  => esc_html__( 'eg: 300px', 'rey-core' ),
				],
				'default'     => '',
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
				'output'      		=> [
					[
						'element'  		=> ':root',
						'property' 		=> '--wrapped-loop-height',
					],
				],
				'responsive' => true,
			] );

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'wrapped_loop_item_fit',
				'label'       => esc_html__( 'Image Fit', 'rey-core' ),
				'default'     => 'cover',
				'choices'     => [
					'cover' => esc_html__( 'Cover', 'rey-core' ),
					'contain' => esc_html__( 'Contain', 'rey-core' ),
					'none' => esc_html__( 'None', 'rey-core' ),
				],
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '==',
						'value'    => 'wrapped',
					],
				],
				'output'      		=> [
					[
						'element'  		=> ':root',
						'property' 		=> '--wrapped-loop-image-fit',
					],
				],
			] );

			$this->end_controls_group();

			/* ------------------------------------ Misc options ------------------------------------ */

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'loop_alignment',
				'label'       => __('Text Alignment', 'rey-core'),
				'help' => [
					__('Select an alignment for the content in product items.', 'rey-core')
				],
				'default'     => '',
				'choices'     => [
					'' => esc_html__('- Inherit (from skin) -', 'rey-core'),
					'left' => esc_html__('Left', 'rey-core'),
					'center' => esc_html__('Center', 'rey-core'),
					'right' => esc_html__('Right', 'rey-core')
				],
			]);

		$this->end_controls_accordion();


	/**
	 * TYPOGRAPHY
	 */
		$this->start_controls_accordion([
			'label'  => esc_html__( 'Title', 'rey-core' ),
		]);

			$this->add_control( [
				'type'        => 'typography',
				'settings'    => 'typography_catalog_product_title',
				'label'       => esc_attr__('Typography', 'rey-core'),
				'default'     => [
					'font-family'    => '',
					'font-size'      => '',
					'line-height'    => '',
					'letter-spacing' => '',
					'font-weight'    => '',
					'variant'        => '',
					'color'          => '',
				],
				'output' => [
					[
						'element' => 'body.woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product[class*="rey-wc-skin"] .woocommerce-loop-product__title',
					]
				],
				'load_choices' => true,
				'transport' => 'auto',
				'responsive' => true,
			]);


			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'product_items_eq',
				'label'       => __('Equalize titles height', 'rey-core'),
				'help' => [
					__('If enabled, the product titles height will be equalized on all sibilings on each row.', 'rey-core')
				],
				'default'     => false,
				'active_callback' => [
					[
						'setting'  => 'loop_skin',
						'operator' => '!=',
						'value'    => 'wrapped',
					],
				],
			] );

		$this->end_controls_accordion();

	/**
	 * ADD TO CART BUTTON
	 */

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Add to Cart Button', 'rey-core' ),
		]);

			$this->add_control( [
				'type' => 'toggle',
				'settings' => 'loop_add_to_cart',
				'label' => esc_html__('Enable Add to Cart button', 'rey-core'),
				'default' => true,
			]);

			$this->start_controls_group( [
				'group_id' => 'catalog_atc_button_options',
				'label'    => esc_html__( 'Button options', 'rey-core' ),
				'active_callback' => [
					[
						'setting'  => 'loop_add_to_cart',
						'operator' => '==',
						'value'    => true,
					],
				],
			]);

			$this->add_section_marker('loop_atc_options');

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_add_to_cart_style',
				'label' => esc_html__('Button style', 'rey-core'),
				'default' => '',
				'choices' => [
					'' => esc_attr__('- Inherit (from skin) -', 'rey-core'),
					'under' => esc_html__('Default (underlined)', 'rey-core'),
					'hover' => esc_html__('Hover Underlined', 'rey-core'),
					'primary' => esc_html__('Primary', 'rey-core'),
					'primary-out' => esc_html__('Primary Outlined', 'rey-core'),
					'clean' => esc_html__('Clean', 'rey-core'),
				],
				'separator' => 'before',
			]);

			$this->add_control( [
				'type' => 'rey-color',
				'settings' => 'loop_add_to_cart_accent_color',
				'label' => esc_html__('Accent Color', 'rey-core'),
				'default' => '',
				'choices' => [
					'alpha' => true,
				],
				'transport' => 'auto',
				'output' => [
					[
						'element' => '.woocommerce ul.products li.product .rey-productInner .button, .tinvwl-loop-button-wrapper, .rey-loopQty',
						'property' => '--accent-color',
					],
				],
			]);

			$this->add_control( [
				'type' => 'rey-color',
				'settings' => 'loop_add_to_cart_accent_hover_color',
				'label' => esc_html__('Accent Hover Color', 'rey-core'),
				'default' => '',
				'choices' => [
					'alpha' => true,
				],
				'transport' => 'auto',
				'output' => [
					[
						'element' => '.woocommerce ul.products li.product .rey-productInner .button, .tinvwl-loop-button-wrapper, .rey-loopQty',
						'property' => '--accent-hover-color',
					],
				],
			]);

			$this->add_control( [
				'type' => 'rey-color',
				'settings' => 'loop_add_to_cart_accent_text_color',
				'label' => esc_html__('Accent Text Color', 'rey-core'),
				'default' => '',
				'choices' => [
					'alpha' => true,
				],
				'transport' => 'auto',
				'output' => [
					[
						'element' => '.woocommerce ul.products li.product .rey-productInner .button, .tinvwl-loop-button-wrapper, .rey-loopQty',
						'property' => '--accent-text-color',
					],
				],
			]);

			$this->add_control( [
				'type' => 'text',
				'settings' => 'loop_atc__text',
				'label'       => esc_html__('Button Text', 'rey-core'),
				'help' => [
					esc_html__('Change button text. Use 0 to disable it.', 'rey-core')
				],
				'default' => '',
				'input_attrs' => [
					'placeholder' => esc_html__('eg: Add to cart', 'rey-core'),
				],
			]);

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_atc__icon',
				'label' => esc_html_x('Button Icon', 'Customizer control label', 'rey-core'),
				'default' => '',
				'choices' => [
					'' => esc_html__('No Icon', 'rey-core'),
					'bag' => esc_html__('Shopping Bag', 'rey-core'),
					'bag2' => esc_html__('Shopping Bag 2', 'rey-core'),
					'bag3' => esc_html__('Shopping Bag 3', 'rey-core'),
					'basket' => esc_html__('Shopping Basket', 'rey-core'),
					'basket2' => esc_html__('Shopping Basket 2', 'rey-core'),
					'cart' => esc_html__('Shopping Cart', 'rey-core'),
					'cart2' => esc_html__('Shopping Cart 2', 'rey-core'),
					'cart3' => esc_html__('Shopping Cart 3', 'rey-core'),
				],
			]);

			$this->add_control( [
				'type' => 'toggle',
				'settings' => 'loop_add_to_cart_mobile',
				'label' => esc_html__('Show button on mobiles', 'rey-core'),
				'default' => false,
			]);

			$this->add_control( [
				'type' => 'toggle',
				'settings' => 'loop_supports_qty',
				'label' => esc_html__('Show quantity controls', 'rey-core'),
				'default' => false,
				'separator' => 'before',
				'help' => [
					esc_html__('For simple products, a "+ -" quantity box will be displayed.', 'rey-core')
				]
			]);

			$this->end_controls_group();

		$this->end_controls_accordion();

		$this->add_section_marker('atc');

		/* ------------------------------------ PRICE & LABELS ------------------------------------ */

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Price & Labels', 'rey-core' ),
		]);

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_show_prices',
				'label' => esc_html__('Price', 'rey-core'),
				'description' => __('Choose if you want to hide prices.', 'rey-core'),
				'default' => '1',
				'choices' => [
					'1' => esc_attr__('Show', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);

			$this->start_controls_group( [
				'group_id' => 'loop_price_options',
				'label'    => esc_html__( 'Price options', 'rey-core' ),
				'active_callback' => [
					[
						'setting' => 'loop_show_prices',
						'operator' => '==',
						'value' => '1',
					],
				],
			]);

			$this->add_control( [
				'type' => 'typography',
				'settings' => 'loop_price_typo',
				'label' => esc_attr__('Price typography', 'rey-core'),
				'default' => [
					'font-family' => '',
					'font-size' => '',
					'line-height' => '',
					'letter-spacing' => '',
					'font-weight' => '',
					'text-transform' => '',
					'variant' => '',
				],
				'output' => [
					[
						'element' => '.woocommerce ul.products li.product .price',
					],
				],
				'load_choices' => true,
				'transport' => 'auto',
				'responsive' => true,
			]);

			$this->add_control( [
				'type' => 'rey-color',
				'settings' => 'loop_price_color',
				'label' => esc_html_x('Price Color', 'Customizer control label', 'rey-core'),
				'default' => '',
				'choices' => [
					'alpha' => true,
				],
				'output' => [
					[
						'element' => '.woocommerce ul.products li.product .price',
						'property' => 'color',
					],
				],
			]);

			$this->end_controls_group();

		$this->end_controls_accordion();

		/**
		 * GENERAL COMPONENTS
		 */

		$this->start_controls_accordion([
			'label'  => esc_html__( 'General Components', 'rey-core' ),
		]);

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_show_categories',
				'label' => esc_html__('Category', 'rey-core'),
				'description' => __('Choose if you want to display product categories.', 'rey-core'),
				'default' => '2',
				'choices' => [
					'1' => esc_attr__('Show', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);

			$this->start_controls_group( [
				'label'    => esc_html__( 'Options', 'rey-core' ),
				'active_callback' => [
					[
						'setting' => 'loop_show_categories',
						'operator' => '==',
						'value' => '1',
					],
				],
			]);

			$this->add_control( [
				'type' => 'toggle',
				'settings' => 'loop_categories__exclude_parents',
				'label' => esc_html__('Exclude parent categories', 'rey-core'),
				'default' => false,
			]);

			$this->end_controls_group();

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_ratings',
				'label' => esc_html__('Ratings', 'rey-core'),
				'description' => __('Choose if you want ratings to be displayed.', 'rey-core'),
				'default' => '2',
				'choices' => [
					'1' => esc_attr__('Show - Before title', 'rey-core'),
					'after' => esc_attr__('Show - After title', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);


			$this->start_controls_group( [
				'label'    => esc_html__( 'Rating options', 'rey-core' ),
				'active_callback' => [
					[
						'setting' => 'loop_ratings',
						'operator' => '!=',
						'value' => '2',
					],
				],
			]);

				$this->add_control( [
					'type'     => 'toggle',
					'settings' => 'loop_ratings_extend',
					'label'    => esc_html__('Extend with ratings count text', 'rey-core'),
					'default'  => false,
				]);

			$this->end_controls_group();

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_short_desc',
				'label' => esc_html__('Short description', 'rey-core'),
				'description' => __('Choose if you want to show the product excerpt.', 'rey-core'),
				'default' => '2',
				'choices' => [
					'1' => esc_attr__('Show', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);

			$this->start_controls_group( [
				'label'    => esc_html__( 'Excerpt options', 'rey-core' ),
				'active_callback' => [
					[
						'setting' => 'loop_short_desc',
						'operator' => '==',
						'value' => '1',
					],
				],
			]);

				$this->add_control( [
					'type' => 'text',
					'settings' => 'loop_short_desc_limit',
					'label'       => esc_html__('Limit words', 'rey-core'),
					'help' => [
						esc_html__('Limit the number of words. 0 means full, not truncated.', 'rey-core')
					],
					'default' => 8,
					'input_attrs' => [
						'placeholder' => 'eg: 8',
					],
				]);

				$this->add_control( [
					'type' => 'toggle',
					'settings' => 'loop_short_desc_mobile',
					'label' => esc_html__('Show on mobiles', 'rey-core'),
					'default' => false,
				]);

			$this->end_controls_group();

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_new_badge',
				'label' => esc_html__('New Badge', 'rey-core'),
				'description' => __('Choose if you want to show a "New" badge on products newer than 30 days.', 'rey-core'),
				'default' => '1',
				'choices' => [
					'1' => esc_attr__('Show', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_sold_out_badge',
				'label' => esc_html__('Sold Out Badge', 'rey-core'),
				'description' => __('Choose if you want to show a "SOLD OUT" badge on products out of stock.', 'rey-core'),
				'default' => '1',
				'choices' => [
					'1' => esc_attr__('Show', 'rey-core'),
					'in-stock' => esc_attr__('Show - In Stock', 'rey-core'),
					'2' => esc_attr__('Hide', 'rey-core'),
				],
			]);

			$this->add_control( [
				'type' => 'select',
				'settings' => 'loop_featured_badge',
				'label' => esc_html__('Featured Badge', 'rey-core'),
				'default' => 'hide',
				'choices' => [
					'show' => esc_html__('Show', 'rey-core'),
					'hide' => esc_html__('Hide', 'rey-core'),
				],
			]);

			$this->add_control( [
				'type' => 'toggle',
				'settings' => 'loop_edit_link',
				'label' => esc_html__('Edit link', 'rey-core'),
				'default' => true,
				'help' => [
					esc_html__('Shows an "edit product" button, when hovering the top left corner.', 'rey-core')
				],
			]);

			$this->start_controls_group( [
				'label'    => esc_html__( 'Badge settings', 'rey-core' ),
				'active_callback' => [
					[
						'setting' => 'loop_featured_badge',
						'operator' => '==',
						'value' => 'show',
						],
				],
			]);

			$this->add_control( [
				'type' => 'text',
				'settings' => 'loop_featured_badge__text',
				'label' => esc_html__('Text', 'rey-core'),
				'default' => esc_html__('FEATURED', 'rey-core'),
				'input_attrs' => [
					'placeholder' => esc_html__('eg: FEATURED', 'rey-core'),
				],
			]);

			$this->end_controls_group();

		$this->end_controls_accordion();


		$this->start_controls_accordion([
			'label'  => esc_html__( 'Product Variations', 'rey-core' ),
		]);

			$this->add_control( [
				'type'     => 'select',
				'settings' => 'woocommerce_loop_variation',
				'label'       => esc_html__('Select Attribute', 'rey-core'),
				'help' => [
					__('Display product variation swatches into product items, by selecting which attributes should be displayed.', 'rey-core')
				],
				'default'  => 'disabled',
				'choices'  => [
					'disabled' => __('- Disabled -', 'rey-core'),
				],
				'ajax_choices' => 'get_loop_variation_list',
			]);

			$this->add_control( [
				'type'       => 'select',
				'settings'   => 'woocommerce_loop_variation_position',
				'label'       => esc_html__('Position', 'rey-core'),
				'help' => [
					__('Choose the position of the swatches.', 'rey-core')
				],
				'default'    => 'after',
				'choices'    => [
					'first'  => __('Before title', 'rey-core'),
					'before' => __('After title', 'rey-core'),
					'after'  => __('After content', 'rey-core'),
				],
				'active_callback' => [
					[
						'setting' => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value' => 'disabled',
					],
				],
			]);

			$this->add_control( [
				'type'       => 'toggle',
				'settings'   => 'woocommerce_loop_variation__side',
				'label'       => esc_html__('Move to side', 'rey-core'),
				'help' => [
					__('If enabled, it will float the swatches onto the side.', 'rey-core')
				],
				'default'    => false,
				'active_callback' => [
					[
						'setting' => 'woocommerce_loop_variation_position',
						'operator' => '!=',
						'value' => 'after',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'all_attributes',
					],
				],
			]);

			$this->add_control( [
				'type'     => 'dimensions',
				'settings' => 'woocommerce_loop_variation_size',
				'label'       => esc_html__('Attribute swatches sizes', 'rey-core'),
				'help' => [
					__('Customize the sizes of the swatches.', 'rey-core')
				],
				'input_attrs' => [
					'placeholder'     => 'eg: 10px',
				],
				'default'  => [
					'width'   => '',
					'height'  => '',
					'padding' => '',
					'spacing' => '',
					'border' => '',
					'radius' => '',
				],
				'choices'  => [
					'labels'  => [
						'width'   => esc_html__( 'Width', 'rey-core' ),
						'height'  => esc_html__( 'Height', 'rey-core' ),
						'padding' => esc_html__( 'Padding', 'rey-core' ),
						'spacing' => esc_html__( 'Spacing', 'rey-core' ),
						'border' => esc_html__( 'Border Size', 'rey-core' ),
						'radius' => esc_html__( 'Corner Radius', 'rey-core' ),
					],
				],
				'output'    => [
					[
						'element'  => ':root',
						'property' => '--woocommerce-swatches',
						'units'    => 'px',
					],
				],
				'active_callback' => [
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'all_attributes',
					],
					[
						'setting'  => 'woocommerce_loop_variation_force_regular',
						'operator' => '==',
						'value'    => '',
					],
				],
			]);

			$this->add_control( [
				'type'     => 'rey-number',
				'settings' => 'woocommerce_loop_variation_limit',
				'label'       => esc_html__('Attributes display limit', 'rey-core'),
				'help' => [
					__('Limit how many attributes to display. 0 is unlimited.', 'rey-core')
				],
				'default'  => 0,
				'choices'  => [
					'min'  => 0,
					'max'  => 80,
					'step' => 1,
				],
				'active_callback' => [
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'all_attributes',
					],
					[
						'setting'  => 'woocommerce_loop_variation_force_regular',
						'operator' => '==',
						'value'    => '',
					],
				],
			]);

			$this->add_control( [
				'type'       => 'toggle',
				'settings'   => 'woocommerce_loop_variation_single_click',
				'label'       => esc_html__('Click to redirect to product', 'rey-core'),
				'help' => [
					__('If enabled, clicking on items will redirect to the product page.', 'rey-core')
				],
				'default'    => false,
				'active_callback' => [
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'all_attributes',
					],
				],
			]);


			$this->add_control( [
				'type' => 'select',
				'settings' => 'woocommerce_loop_variation_force_regular',
				'label'       => esc_html__('Display as text?', 'rey-core'),
				'help' => [
					esc_html__('This option will force showing the available attribute items in a text list or count.', 'rey-core')
				],
				'default' => '',
				'choices'     => [
					'' => esc_html__( 'No (default)', 'rey-core' ),
					'list' => esc_html__( 'Text list', 'rey-core' ),
					'count' => esc_html__( 'Text count', 'rey-core' ),
				],
				'active_callback' => [
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'disabled',
					],
					[
						'setting'  => 'woocommerce_loop_variation',
						'operator' => '!=',
						'value'    => 'all_attributes',
					],
				],
			]);

			$this->add_control( [
				'type'        => 'text',
				'settings'    => 'woocommerce_loop_variation_text_display',
				'label'       => esc_html__( 'Text', 'rey-core' ),
				'default'     => '',
				'input_attrs'     => [
					'placeholder' => esc_html__('ex: colors', 'rey-core'),
				],
				'active_callback' => [
					[
						'setting'  => 'woocommerce_loop_variation_force_regular',
						'operator' => '!=',
						'value'    => '',
					],
				]
			] );

		$this->end_controls_accordion();


	}
}
