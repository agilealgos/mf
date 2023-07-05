<?php
namespace ReyCore\Customizer\Options\Header;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class Cart extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'header-mini-cart';
	}

	public function get_title(){
		return esc_html__('Shopping Cart (Button & Panel)', 'rey-core');
	}

	public function get_priority(){
		return 60;
	}

	public function can_load(){
		return class_exists('\WooCommerce');
	}

	public function get_icon(){
		return 'shopping-cart';
	}

	public function help_link(){
		return reycore__support_url('kb/customizer-header-settings/#shopping-cart');
	}

	public function controls(){

		$default_header_conditions = [
			'setting'  => 'header_layout_type',
			'operator' => '==',
			'value'    => 'default',
		];

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_enable_cart',
			'label'       => esc_html__( 'Enable Shopping Cart?', 'rey-core' ),
			'default'     => true,
			'active_callback' => [
				$default_header_conditions,
			],
		] );

		$this->add_title( esc_html__('Cart Button', 'rey-core'), [
			'separator' => 'none',
		]);

		$header_type__is_default = get_theme_mod('header_layout_type', 'default') === 'default';

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_cart_layout',
			'label'       => esc_html__( 'Cart Layout', 'rey-core' ),
			'help' => [
				(! $header_type__is_default ? esc_html__('In case this option is not working, please check the "Header - Cart" element from the current Header Global Section , and edit its settings.', 'rey-core') : '')
			],
			'default'     => 'bag',
			'choices'     => [
				'bag' => esc_html__( 'Icon - Shopping Bag', 'rey-core' ),
				'bag2' => esc_html__( 'Icon - Shopping Bag 2', 'rey-core' ),
				'bag3' => esc_html__( 'Icon - Shopping Bag 3', 'rey-core' ),
				'basket' => esc_html__( 'Icon - Shopping Basket', 'rey-core' ),
				'basket2' => esc_html__( 'Icon - Shopping Basket 2', 'rey-core' ),
				'cart' => esc_html__( 'Icon - Shopping Cart', 'rey-core' ),
				'cart2' => esc_html__( 'Icon - Shopping Cart 2', 'rey-core' ),
				'cart3' => esc_html__( 'Icon - Shopping Cart 3', 'rey-core' ),
				'disabled' => esc_html__( 'No Icon', 'rey-core' ),
			],
			// 'active_callback' => [
			// 	[
			// 		'setting'  => 'header_enable_cart',
			// 		'operator' => '==',
			// 		'value'    => true,
			// 	],
			// ],
		] );

		$this->add_control( [
			'type'     => 'text',
			'settings' => 'header_cart_text_v2',
			'label'       => esc_html__( 'Cart Text', 'rey-core' ),
			'help' => [
				esc_html__( 'Use {{total}} string to add the cart totals.', 'rey-core' ) .( ! $header_type__is_default ? '<br>' . esc_html__('In case this option is not working, please check the "Header - Cart" element from the current Header Global Section , and edit its settings.', 'rey-core') : '')
			],
			'default'  => '',
			'input_attrs' => [
				'placeholder' => esc_html__( 'eg: CART', 'rey-core' )
			]
		] );


		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_cart_hide_empty',
			'label'       => esc_html__( 'Hide empty cart?', 'rey-core' ),
			'help' => [
				esc_html__( 'Will hide the cart icon if no products in cart.', 'rey-core' ) .( ! $header_type__is_default ? '<br>' . esc_html__('In case this option is not working, please check the "Header - Cart" element from the current Header Global Section , and edit its settings.', 'rey-core') : '')
			],
			'default'     => 'no',
			'choices'     => [
				'yes' => esc_html__( 'Yes', 'rey-core' ),
				'no' => esc_html__( 'No', 'rey-core' ),
			],
		] );

		/* ------------------------------------ PANEL ------------------------------------ */

		$this->add_title( esc_html__('Cart Panel', 'rey-core'), [ ]);

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__panel_disable',
			'label'       => esc_html__( 'Disable Cart Panel', 'rey-core' ),
			'default'     => false,
		] );

		$this->add_control( [
			'type'        => 'text',
			'settings'    => 'header_cart__title',
			'label'       => esc_html__( 'Panel Title', 'rey-core' ),
			'default'     => '',
			'input_attrs'     => [
				'placeholder' => esc_html__('eg: Shopping Bag', 'rey-core'),
			],
			'separator'     => 'before',
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_cart__panel_width',
			'label'       => esc_html__( 'Panel Width Type', 'rey-core' ),
			'default'     => 'default',
			'choices'     => [
				'default'   => esc_html__( 'Default', 'rey-core' ),
				'px'  => esc_html__( 'Custom in Pixels (px)', 'rey-core' ),
				'vw' => esc_html__( 'Custom in Viewport (vw)', 'rey-core' ),
			],
		] );

		$this->add_control( [
			'type'        		=> 'rey-number',
			'settings'    		=> 'header_cart__panel_width__vw',
			'label'       		=> esc_attr__( 'Panel Width (vw)', 'rey-core' ),
			'default'     		=> 90,
			'choices'     		=> [
				'min'  => 10,
				'max'  => 100,
				'step' => 1,
			],
			'transport'   		=> 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--header-cart-width',
					'units'    		=> 'vw',
				]
			],
			'active_callback' => [
				[
					'setting'  => 'header_cart__panel_width',
					'operator' => '==',
					'value'    => 'vw',
				],
			],
			'responsive' => true
		]);

		$this->add_control( [
			'type'        		=> 'rey-number',
			'settings'    		=> 'header_cart__panel_width__px',
			'label'       		=> esc_attr__( 'Panel Width (px)', 'rey-core' ),
			'default'     		=> 470,
			'choices'     		=> array(
				'min'  => 200,
				'max'  => 2560,
				'step' => 10,
			),
			'transport'   		=> 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--header-cart-width',
					'units'    		=> 'px',
				]
			],
			'active_callback' => [
				[
					'setting'  => 'header_cart__panel_width',
					'operator' => '==',
					'value'    => 'px',
				],
			],
			'responsive' => true
		]);

		$this->add_control( [
			'type'        => 'rey-color',
			'settings'    => 'header_cart__bg_color',
			'label'       => esc_html__( 'Background Color', 'rey-core' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
			'transport'   		=> 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--header-cart-bgcolor',
				]
			],
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_cart__text_theme',
			'label'       => esc_html__( 'Text color theme', 'rey-core' ),
			'default'     => 'def',
			'choices'     => [
				'def' => esc_html__( 'Default', 'rey-core' ),
				'light' => esc_html__( 'Light', 'rey-core' ),
				'dark' => esc_html__( 'Dark', 'rey-core' ),
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__close_extend',
			'label'       => esc_html__( 'Extend closing triggers', 'rey-core' ),
			'help' => [
				esc_html__( 'This extends the close button as well as add a custom Continue shoppings button.', 'rey-core')
			],
			'default'     => false,
			'separator'   => 'before',
		] );

		$this->start_controls_group( [
			'label'    => esc_html__( 'Options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'header_cart__close_extend',
					'operator' => '==',
					'value'    => true,
				],
			],
		]);

			$this->add_control( [
				'type'        => 'text',
				'settings'    => 'header_cart__close_text',
				'label'       => esc_html__( 'Close Button Text', 'rey-core' ),
				'default'     => '',
				'input_attrs'     => [
					'placeholder' => esc_html__('eg: CLOSE', 'rey-core'),
				],
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'header_cart__continue_shop',
				'label'       => esc_html__( 'Add "Continue Shopping" Button', 'rey-core' ),
				'help' => [
					esc_html__('Adds a Continue Shopping button after the products', 'rey-core')
				],
				'default'     => false,
			] );

		$this->end_controls_group();

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__btns_inline',
			'label'       => esc_html__( 'Cart & Checkout Buttons Inline', 'rey-core' ),
			'default'     => true,
			'separator'   => 'before',
			'help' => [
				esc_html__( 'Make the Cart and Checkout buttons stay on a single row. NOTE: If disabled, please know the buttons will stack separately on rows only on a viewport of +768px because of the limited real estate inside the Cart panel.', 'rey-core')
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__btn_cart__enable',
			'label'       => esc_html__( 'Enable Cart Button', 'rey-core' ),
			'default'     => true,
			'separator'   => 'before',
		] );

		$this->start_controls_group( [
			'label'    => esc_html__( 'Options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'header_cart__btn_cart__enable',
					'operator' => '==',
					'value'    => true,
				],
			],
		]);

			$this->add_control( [
				'type'        => 'text',
				'settings'    => 'header_cart__btn_cart__text',
				'label'       => esc_html__( 'Cart Button Text', 'rey-core' ),
				'default'     => '',
				'input_attrs'     => [
					'placeholder' => esc_html_x('eg: View Cart', 'Customizer control placeholder text.', 'rey-core'),
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'header_cart__btn_cart__color',
				'label'       => esc_html__( 'Cart Button Text Color', 'rey-core' ),
				'default'     => '',
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      		=> [
					[
						'element'  		=> '.rey-cartPanel .woocommerce-mini-cart__buttons .button--cart',
						'property' 		=> 'color',
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'header_cart__btn_cart__bg',
				'label'       => esc_html__( 'Cart Button BG. Color', 'rey-core' ),
				'default'     => '',
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      		=> [
					[
						'element'  		=> '.rey-cartPanel .woocommerce-mini-cart__buttons .button--cart',
						'property' 		=> 'background-color',
					],
				],
			] );

		$this->end_controls_group();

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_cart_gs',
			'label'       => esc_html__( 'Empty Cart Content', 'rey-core' ),
			'description' => esc_html__( 'Add custom Elementor content into the Cart Panel if no products are added into it.', 'rey-core' ),
			'default'     => 'none',
			'choices'     => [
				'none' => '- None -'
			],
			'ajax_choices' => 'get_global_sections',
			'active_callback' => [
				[
					'setting'  => 'header_cart_hide_empty',
					'operator' => '==',
					'value'    => 'no',
				],
			],
			'edit_preview' => true,
			'separator'   => 'before',
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart_show_shipping',
			'label'       => esc_html__( 'Show "Shipping" under subtotal', 'rey-core' ),
			'default'     => false,
			'separator'   => 'before',
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart_show_qty',
			'label'       => esc_html__( 'Show Quantity Controls', 'rey-core' ),
			'default'     => true,
			'separator'   => 'before',
			'help' => [
				esc_html__( 'Display "+ -" quantity controls for each product item.', 'rey-core')
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart_show_subtotal',
			'label'       => esc_html__( 'Show items Subtotal', 'rey-core' ),
			'default'     => true,
			'separator'   => 'before',
			'help' => [
				esc_html__( 'Will show the item subtotal mount based on its quantity.', 'rey-core')
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart_coupon',
			'label'       => esc_html__( 'Show "Coupon Code" form', 'rey-core' ),
			'default'     => true,
			'separator'   => 'before',
		] );


		/* ------------------------------------ CROSS-SELLS ------------------------------------ */

		$this->add_title( esc_html__('CROSS-SELLS', 'rey-core'), [
			'description' => esc_html__( 'Cross-sells are manually picked products that are shown when a user adds a product to cart. To pick cross-sells, edit any product and edit their Linked products.', 'rey-core' ),
		]);

		$this->add_control( [
			'type'        => 'text',
			'settings'    => 'header_cart__cross_sells_btn_text',
			'label'       => esc_html__( 'Button text', 'rey-core' ),
			'default'     => '',
			'input_attrs'     => [
				'placeholder' => __( 'eg: Add to order', 'woocommerce' ),
			],
		] );


	}
}
