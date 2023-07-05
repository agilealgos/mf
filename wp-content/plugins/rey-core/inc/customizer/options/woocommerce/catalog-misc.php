<?php
namespace ReyCore\Customizer\Options\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class CatalogMisc extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'woo-catalog-misc';
	}

	public function get_title(){
		return esc_html__('Misc. options', 'rey-core');
	}

	public function get_priority(){
		return 50;
	}

	public function get_icon(){
		return 'woo-catalog-misc';
	}

	public function help_link(){
		return reycore__support_url('kb/customizer-woocommerce/#product-catalog-miscellaneous');
	}

	public function controls(){

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'shop_catalog',
			'label'       => esc_html__( 'Enable Catalog Mode', 'rey-core' ),
			'default'     => false,
			// 'priority'    => 5,
			'description' => __( 'Enabling catalog mode will disable all cart functionalities.', 'rey-core' ),
		] );

		$this->start_controls_group( [
			'label'    => esc_html__( 'Extra options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'shop_catalog',
					'operator' => '==',
					'value'    => true,
					],
			],
		]);

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'shop_catalog__variable',
			'label'       => esc_html__( 'Variable products', 'rey-core' ),
			'help' => [
				esc_html__( 'Choose how the Add To Cart & Variation choices should be handled in Catalog Mode.', 'rey-core' )
			],
			'default'     => 'hide',
			// 'priority'    => 5,
			'choices'     => [
				'hide' => esc_html__( 'Hide the entire ATC. form', 'rey-core' ),
				'hide_just_atc' => esc_html__( 'Hide only the ATC. button', 'rey-core' ),
				'show' => esc_html__( 'Show form, but prevent purchasing', 'rey-core' ),
			],
		] );

		$this->end_controls_group();

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'shop_catalog_page_exclude',
			'label'       => __('Exclude categories from Shop Page', 'rey-core'),
			'help' => [
				__('Choose to exclude products of specific categories, from the Shop page.', 'rey-core')
			],
			'default'     => '',
			// 'priority'    => 5,
			'multiple'    => 100,
			'query_args' => [
				'type' => 'terms',
				'taxonomy' => 'product_cat',
			],
			'separator' => 'before',
			'css_class' => '--block-label',
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'shop_hide_prices_logged_out',
			'label'       => __('Hide prices when logged out', 'rey-core'),
			'help' => [
				__('If enabled, product prices will be hidden when the visitor is not logged in.', 'rey-core')
			],
			'default'     => false,
			'separator' => 'before',
		] );

		$this->add_control( [
			'type'        => 'text',
			'settings'    => 'shop_hide_prices_logged_out_text',
			'label'       => __('Show custom text', 'rey-core'),
			'help' => [
				__('Add a custom text to display instead of the prices.', 'rey-core')
			],
			'default'     => '',
			// 'priority'    => 5,
			'input_attrs'     => [
				'placeholder' => esc_html__('eg: Login to see prices.', 'rey-core'),
			],
			'active_callback' => [
				[
					'setting'  => 'shop_hide_prices_logged_out',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );


		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'archive__title_back',
			'label'       => __('Enable back arrow', 'rey-core'),
			'help' => [
				__('If enabled, a back arrow will be displayed in the left side of the product archive.', 'rey-core')
			],
			'default'     => false,
			// 'priority'    => 5,
			'separator' => 'before',
		] );

		$this->start_controls_group( [
			'label'    => esc_html__( 'Back button options', 'rey-core' ),
			'active_callback' => [
				[
					'setting'  => 'archive__title_back',
					'operator' => '==',
					'value'    => true,
					],
			],
		]);

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'archive__back_behaviour',
			'label'       => esc_html__( 'Behaviour', 'rey-core' ),
			'default'     => 'parent',
			'choices'     => [
				'parent' => esc_html__( 'Back to parent', 'rey-core' ),
				'shop' => esc_html__( 'Back to shop page', 'rey-core' ),
				'page' => esc_html__( 'Back to previous page', 'rey-core' ),
			],
			// 'priority'    => 5,
		] );

		$this->end_controls_group();

	}
}
