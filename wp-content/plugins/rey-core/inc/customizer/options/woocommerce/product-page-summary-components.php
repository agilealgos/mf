<?php
namespace ReyCore\Customizer\Options\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class ProductPageSummaryComponents extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'woo-product-page-summary-components';
	}

	public function get_title(){
		return esc_html__('Components in Summary', 'rey-core');
	}

	public function get_priority(){
		return 80;
	}

	public function get_icon(){
		return 'woo-pdp-components-in-summary';
	}

	public function help_link(){
		return reycore__support_url('kb/customizer-woocommerce/#product-page-components');
	}

	public function atc_controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Before Add to cart (Content)', 'rey-core' ),
		]);

			$this->add_title( '', [
				'description' => _x('Publish custom content or global sections before the Add To Cart block.', 'Customizer control text', 'rey-core'),
				'separator' => 'none'
			]);

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'gs_before_atc',
				'label'       => esc_html__( 'Select Global Section', 'rey-core' ),
				'default'     => 'no',
				'choices'     => [
					'no'  => esc_attr__( 'Disabled', 'rey-core' )
				],
				'ajax_choices' => 'get_global_sections',
				'edit_preview' => true,
			] );


			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'enable_text_before_add_to_cart',
				'label'       => esc_html__( 'Text before button block', 'rey-core' ),
				'default'     => false,
				'separator' => 'before',
			] );

			$this->add_control( [
				'type'        => 'editor',
				'settings'    => 'text_before_add_to_cart',
				'label'       => '',
				'default'     => '',
				'active_callback' => [
					[
						'setting'  => 'enable_text_before_add_to_cart',
						'operator' => '==',
						'value'    => true,
					],
				],
			] );

		$this->end_controls_accordion();


		$this->start_controls_accordion([
			'label'  => esc_html__( 'Add to cart (block)', 'rey-core' ),
		]);

			// $this->add_title( '', [
			// 	'description' => esc_html__('Adding to cart functionalities.', 'rey-core'),
			// 	'separator' => 'none',
			// ]);

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'single_atc_qty_controls_styles',
				'label'       => esc_html__( 'Quantity Style', 'rey-core' ),
				'default'     => 'default',
				'choices'     => [
					'default' => esc_html__( 'Default', 'rey-core' ),
					'basic' => esc_html__( 'Basic', 'rey-core' ),
					'select' => esc_html__( 'Select Box', 'rey-core' ),
					'disabled' => esc_html__( 'Disabled', 'rey-core' ),
				],
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'single_atc_qty_controls',
				'label'       => esc_html__( 'Enable Quantity "+ -" controls', 'rey-core' ),
				'default'     => false,
				'active_callback' => [
					[
						'setting'  => 'single_atc_qty_controls_styles',
						'operator' => '!=',
						'value'    => 'select',
					],
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'single_atc__color_bg',
				'label'       => esc_html__( 'Button Background Color', 'rey-core' ),
				'default'     => '',
				'transport'   		=> 'auto',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce .rey-cartBtnQty',
						'property' 		=> '--accent-color',
					]
				],
				'separator' => 'before',
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'single_atc__color_text',
				'label'       => esc_html__( 'Button Text Color', 'rey-core' ),
				'default'     => '',
				'transport'   		=> 'auto',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce .rey-cartBtnQty',
						'property' 		=> '--accent-text-color',
					]
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'single_atc__color_text_hover',
				'label'       => esc_html__( 'Button Hover Text Color', 'rey-core' ),
				'default'     => '',
				'transport'   		=> 'auto',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce .rey-cartBtnQty .button.single_add_to_cart_button:hover',
						'property' 		=> 'color',
					]
				],
			] );

			$this->add_control( [
				'type'        => 'rey-color',
				'settings'    => 'single_atc__color_bg_hover',
				'label'       => esc_html__( 'Button Hover Background Color', 'rey-core' ),
				'default'     => '',
				'transport'   		=> 'auto',
				'choices'     => [
					'alpha' => true,
				],
				'output'      		=> [
					[
						'element'  		=> '.woocommerce .rey-cartBtnQty .button.single_add_to_cart_button:hover',
						'property' 		=> 'background-color',
					]
				],
			] );

			$this->add_control( [
				'type'        => 'text',
				'settings'    => 'single_atc__text',
				'label'       => esc_html__( 'Button Text', 'rey-core' ),
				'help' => [
					esc_html__( 'Change button text. Use 0 to disable the text entirely. If you want to completly hide the Add to Cart button, access Customizer > WooCommerce > Product catalog - Misc. and enable Catalog Mode.', 'rey-core' )
				],
				'default'     => '',
				'input_attrs'     => [
					'placeholder' => esc_html__('eg: Add to cart', 'rey-core'),
				],
			] );

			$this->add_control( [
				'type'     => 'select',
				'settings' => 'single_atc__icon',
				'label'    => esc_html_x( 'Button Icon', 'Customizer control label', 'rey-core' ),
				'default'  => '',
				'choices'  => [
					''        => esc_html__( 'No Icon', 'rey-core' ),
					'bag'     => esc_html__( 'Shopping Bag', 'rey-core' ),
					'bag2'    => esc_html__( 'Shopping Bag 2', 'rey-core' ),
					'bag3'    => esc_html__( 'Shopping Bag 3', 'rey-core' ),
					'basket'  => esc_html__( 'Shopping Basket', 'rey-core' ),
					'basket2' => esc_html__( 'Shopping Basket 2', 'rey-core' ),
					'cart'    => esc_html__( 'Shopping Cart', 'rey-core' ),
					'cart2'   => esc_html__( 'Shopping Cart 2', 'rey-core' ),
					'cart3'   => esc_html__( 'Shopping Cart 3', 'rey-core' ),
				],
			] );

			// $this->add_control( [
			// 	'type'        => 'text',
			// 	'settings'    => 'single_atc__text_backorders',
			// 	'label'       => esc_html__( 'Backorders - Button Text', 'rey-core' ),
			// 	'default'     => '',
			// 	'input_attrs'     => [
			// 		'placeholder' => esc_html__('eg: Pre-order', 'rey-core'),
			// 	],
			// ] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'single_atc__disable',
				'label'       => esc_html__( 'Disable Button', 'rey-core' ),
				'help' => [
					esc_html__( 'This option will hide the Add To Cart button. Please know this is not recommended and instead access Customizer > WooCommerce > Product catalog - Misc. and enable Catalog Mode which will make products non-purchasable and therefore disable this button and form.', 'rey-core' )
				],
				'default'     => false,
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'single_atc__stretch',
				'label'       => esc_html__( 'Full-Stretch Button', 'rey-core' ),
				'default'     => false,
			] );


			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'product_page_ajax_add_to_cart',
				'label'       => esc_html__( 'Ajax Add to Cart', 'rey-core' ),
				'help' => [
					__('WooCommerce doesn\'t have this option built-in for product pages, only for the catalog. If enabled, the page will not be reloaded when the product is added to the cart.', 'rey-core')
				],
				'default'     => 'yes',
				'choices'     => array(
					'yes' => esc_attr__('Yes', 'rey-core'),
					'no' => esc_attr__('No', 'rey-core')
				),
				'separator' => 'before',
			] );

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'product_page_after_add_to_cart_behviour',
				'label'       => esc_html__( 'After "Added To Cart" Behaviour', 'rey-core' ),
				'default'     => 'cart',
				'choices'     => [
					'' => esc_html__( 'Do nothing', 'rey-core' ),
					'cart' => esc_html__( 'Open Cart Panel', 'rey-core' ),
					'checkout' => esc_html__( 'Redirect to Checkout', 'rey-core' ),
				],
				'separator' => 'before',
				'active_callback' => [
					[
						'setting'  => 'product_page_ajax_add_to_cart',
						'operator' => '==',
						'value'    => 'yes',
					],
				],
			] );

		$this->end_controls_accordion();

		$this->start_controls_accordion([
			'label'  => esc_html__( 'After Add to cart (Content)', 'rey-core' ),
		]);

			$this->add_title( '', [
				'description' => _x('Publish custom content or global sections after the Add To Cart block.', 'Customizer control text', 'rey-core'),
				'separator' => 'none'
			]);

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'gs_after_atc',
				'label'       => esc_html__( 'Select Global Section', 'rey-core' ),
				'default'     => 'no',
				'choices'     => [
					'no'  => esc_attr__( 'Disabled', 'rey-core' )
				],
				'ajax_choices' => 'get_global_sections',
				'edit_preview' => true,
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'enable_text_after_add_to_cart',
				'label'       => esc_html__( 'Text after button block', 'rey-core' ),
				'default'     => false,
				'separator' => 'before',
			] );

			$this->add_control( [
				'type'        => 'editor',
				'settings'    => 'text_after_add_to_cart',
				'default'     => '',
				'active_callback' => [
					[
						'setting'  => 'enable_text_after_add_to_cart',
						'operator' => '==',
						'value'    => true,
					],
				],
			] );

		$this->end_controls_accordion();


		$this->add_section_marker('atc');
	}

	public function social_sharing_controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Social Sharing', 'rey-core' ),
		]);

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'product_share',
			'label'       => esc_html__('Share', 'rey-core'),
			'description' => __('Select the visibility of share icons.', 'rey-core'),
			'default'     => '1',
			'choices'     => [
				'1' => esc_attr__('Show', 'rey-core'),
				'2' => esc_attr__('Hide', 'rey-core')
			],
		]);

		$this->add_control( [
			'type'        => 'repeater',
			'settings'    => 'product_share_icons',
			'label'       => esc_html__('Social Sharing Icons', 'rey-core'),
			'description' => __('Customize the social icons.', 'rey-core'),
			'row_label' => [
				'type' => 'text',
				'value' => esc_html__('Social Icon', 'rey-core'),
				'field' => 'social_icon',
			],
			'button_label' => esc_html__('New Social Icon', 'rey-core'),
			'default'      => [
				[
					'social_icon' => 'twitter'
				],
				[
					'social_icon' => 'facebook-f'
				],
				[
					'social_icon' => 'linkedin'
				],
				[
					'social_icon' => 'pinterest-p'
				],
				[
					'social_icon' => 'mail'
				],
				[
					'social_icon' => 'copy'
				],
			],
			'fields' => [
				'social_icon' => [
					'type'        => 'select',
					'label'       => esc_html__('Social Icon', 'rey-core'),
					'choices'     => reycore__social_icons_list_select2('share'),
				],
			],
			'active_callback' => [
				[
					'setting'  => 'product_share',
					'operator' => '==',
					'value'    => '1',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'product_share_icons_colored',
			'label'       => esc_html__( 'Colored icons', 'rey-core' ),
			'help' => [
				esc_html__( 'Enable coloring the icons', 'rey-core' )
			],
			'default'     => false,
			'active_callback' => [
				[
					'setting'  => 'product_share',
					'operator' => '==',
					'value'    => '1',
				],
			],
		] );

		$this->end_controls_accordion();

	}

	public function general_controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'General Components', 'rey-core' ),
			'open' => true
		]);

		$this->add_title( '', [
			'description' => esc_html__('Control what components to show or hide in the product page.', 'rey-core'),
			'separator' => 'none',
		]);

		$this->add_control( array(
			'type'        => 'select',
			'settings'    => 'single_breadcrumbs',
			'label'       => esc_html__('Breadcrumbs', 'rey-core'),
			'help' => [
				__('Enable or disable the breadcrumbs and customize wether it should display the Home button.', 'rey-core')
			],
			'default'     => 'yes_hide_home',
			'choices'     => array(
				'yes' => esc_attr__('Yes & Show Home', 'rey-core'),
				'yes_hide_home' => esc_attr__('Yes & Hide Home', 'rey-core'),
				'no' => esc_attr__('Hide', 'rey-core')
			),
		));

		$this->add_control( array(
			'type'        => 'select',
			'settings'    => 'product_navigation',
			'label'       => esc_html__('Navigation', 'rey-core'),
			'help' => [
				__('Select the visibility of the navigation.', 'rey-core')
			],
			'default'     => '1',
			'choices'     => [
				'2' => esc_html__('Disabled', 'rey-core'),
				'1' => esc_html__('Compact', 'rey-core'),
				'extended' => esc_html__('Extended', 'rey-core'),
				'full' => esc_html__('Full', 'rey-core'),
			],
			'separator' => 'before',
		));

		$this->start_controls_group( [
			// 'label'    => esc_html__( 'Navigation options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'product_navigation',
					'operator' => '!=',
					'value'    => '2',
				],
			],
		]);

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'product_navigation_same_term',
			'label'       => esc_html__( 'Navigate only the same category', 'rey-core' ),
			'default'     => true,
		] );

		$this->end_controls_group();

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'product_short_desc_enabled',
			'label'       => esc_html__('Short Description', 'rey-core'),
			'help' => [
				__('Select the visibility of the short description (excerpt).', 'rey-core')
			],
			'default'     => true,
			'separator' => 'before',
		] );

		$this->start_controls_group( [
			// 'label'    => esc_html__( 'Options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'product_short_desc_enabled',
					'operator' => '==',
					'value'    => true,
				],
			],
		]);

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'product_short_desc_after_atc',
				'label'       => esc_html__('Move after Add To Cart', 'rey-core'),
				'help' => [
					__('Enable if you want to reposition the short description after the Add to cart block.', 'rey-core')
				],
				'default'     => false,
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'product_short_desc_toggle_v2',
				'label'       => esc_html__('Toggle text', 'rey-core'),
				'help' => [
					__('Select if you want to add a "Read more/less" button into the short description.', 'rey-core')
				],
				'default'     => false,
				'active_callback' => [
					[
						'setting'  => 'product_short_desc_enabled',
						'operator' => '==',
						'value'    => true,
					],
				],
			] );

			$this->add_control( [
				'type'        => 'toggle',
				'settings'    => 'product_short_desc_toggle_strip_tags',
				'label'       => esc_html__('Toggle text - Strip tags?', 'rey-core'),
				'default'     => true,
				'active_callback' => [
					[
						'setting'  => 'product_short_desc_enabled',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'product_short_desc_toggle_v2',
						'operator' => '==',
						'value'    => true,
					],
				],
			] );

		$this->end_controls_group();


		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'product_page__hide_stock',
			'label'       => esc_html__( 'Hide Stock Information', 'rey-core' ),
			'help' => [
				sprintf(__('Hide occourances of stock information. To control more stock settings, header over to <a href="%s" target="_blank">WooCommerce Inventory settings</a>.', 'rey-core'), admin_url('admin.php?page=wc-settings&tab=products&section=inventory')),
				'clickable' => true
			],
			'default'     => false,
			'separator' => 'before',
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single_product_reviews_after_meta',
			'label'       => esc_html__('Move Ratings Link after meta', 'rey-core'),
			'help' => [
				__('If enabled, this option will move the reviews button after the product meta.', 'rey-core')
			],
			'default'     => true,
			'separator' => 'before',
		] );

		$this->end_controls_accordion();

	}

	public function price_controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Product Price', 'rey-core' ),
		]);

		$this->add_control( array(
			'type'        => 'toggle',
			'settings'    => 'single_product_price',
			'label'       => esc_html__('Show Price', 'rey-core'),
			'help' => [
				__('Select the visibility of the price.', 'rey-core')
			],
			'default'     => true,
		));

		$this->start_controls_group( [
			'group_id' => 'price_block_group',
			'label'    => esc_html__( 'Price block options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
			],
		]);

		$this->add_control( [
			'type'        => 'typography',
			'settings'    => 'single_product_price_typo',
			'label'       => esc_attr__('Price Typo.', 'rey-core'),
			'default'     => [
				'font-family'      => '',
				'font-size'      => '',
				'line-height'    => '',
				'letter-spacing' => '',
				'font-weight' => '',
				'text-transform' => '',
				'variant' => '',
			],
			'output' => [
				[
					'element' => '.woocommerce div.product p.price',
				],
			],
			'load_choices' => true,
			'transport' => 'auto',
			'responsive' => true,
		]);

		$this->add_control( [
			'type'        => 'rey-color',
			'settings'    => 'single_product_price_color',
			'label'       => esc_html__( 'Price Color', 'rey-core' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
			],
			'output'          => [
				[
					'element'  		   => '.woocommerce div.product p.price',
					'property' 		   => 'color',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'single_product_price_text_type',
			'label'       => esc_html__( 'Text near price', 'rey-core' ),
			'default'     => 'no',
			'choices' => [
				'no' => esc_html__('Disabled', 'rey-core'),
				'custom_text' => esc_html__('Custom Text', 'rey-core'),
				'free_shipping' => esc_html__('Free Shipping (if available)', 'rey-core'),
			],
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
			],
			'separator' => 'before',
		] );

		$this->add_control( [
			'type'     => 'text',
			'settings' => 'single_product_price_text_custom',
			'label'    => esc_html__('Text to show', 'rey-core'),
			'default'  => esc_html__('Free Shipping!', 'rey-core'),
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'single_product_price_text_type',
					'operator' => '!=',
					'value'    => 'no',
				],
			],
			'input_attrs' => [
				'placeholder' => esc_html__('eg: Free Shipping', 'rey-core')
			],
		] );

		$this->add_control( [
			'type'        => 'rey-number',
			'settings'    => 'single_product_price_text_shipping_cost',
			'label'       => esc_html__('Minimum Order Amount', 'rey-core'),
			'help' => [
				__('Add a Minimum Order Amount to calculate when to show this text. The calculation is [product-price + cart-total > min-order-amount]', 'rey-core')
			],
			'default'     => 0,
			'choices'     => [
				'min'  => 0,
				'max'  => 1000,
				'step' => 1,
			],
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'single_product_price_text_type',
					'operator' => '==',
					'value'    => 'free_shipping',
				],
			]
		] );

		$this->add_control( [
			'type'        => 'rey-color',
			'settings'    => 'single_product_price_text_color',
			'label'       => esc_html__( 'Text Color', 'rey-core' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'single_product_price_text_type',
					'operator' => '!=',
					'value'    => 'no',
				],
			],
			'output'          => [
				[
					'element'  		   => '.woocommerce div.product p.price .rey-priceText',
					'property' 		   => 'color',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single_product_price_text_inline',
			'label'       => esc_html__( 'Show under price?', 'rey-core' ),
			'default'     => false,
			'active_callback' => [
				[
					'setting'  => 'single_product_price',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'single_product_price_text_type',
					'operator' => '!=',
					'value'    => 'no',
				],
			],
		] );

		$this->end_controls_group();

		$this->end_controls_accordion();

	}

	public function meta_controls(){

		$this->add_section_marker('before_meta');

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Product Meta', 'rey-core' ),
		]);

		$this->add_title( '', [
			'description' => esc_html__('Meta content after Add to cart button.', 'rey-core'),
			'separator' => 'none',
		]);

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single_product_meta_v2',
			'label'       => esc_html__('Product Meta', 'rey-core'),
			'help' => [
				__('Select the visibility of product meta (<strong>SKU, categories, tags</strong>).', 'rey-core')
			],
			'default'     => true,
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'product_sku_v2',
			'label'       => esc_html__('Product SKU', 'rey-core'),
			'help' => [
				__('Select the visibility of the product SKU.', 'rey-core')
			],
			'default'     => true,
			'active_callback' => [
				[
					'setting'  => 'single_product_meta_v2',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single__product_tags',
			'label'       => esc_html__('Product Tags', 'rey-core'),
			'help' => [
				__('Select the visibility of the product tags.', 'rey-core')
			],
			'default'     => true,
			'active_callback' => [
				[
					'setting'  => 'single_product_meta_v2',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single__product_categories',
			'label'       => esc_html__('Product Categories', 'rey-core'),
			'help' => [
				__('Select the visibility of the product tags.', 'rey-core')
			],
			'default'     => true,
			'active_callback' => [
				[
					'setting'  => 'single_product_meta_v2',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->end_controls_accordion();
	}

	public function variations_controls(){

		$this->start_controls_accordion([
			'label'  => esc_html__( 'Product Variations', 'rey-core' ),
		]);

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single_product_hide_out_of_stock_variation',
			'label'       => esc_html__( 'Disable "Out of Stock" Variations', 'rey-core' ),
			'default'     => true,
		] );

		$this->end_controls_accordion();

	}

	public function controls(){
		$this->general_controls();
		$this->atc_controls();
		$this->variations_controls();
		$this->price_controls();
		$this->meta_controls();
		$this->social_sharing_controls();
	}
}
