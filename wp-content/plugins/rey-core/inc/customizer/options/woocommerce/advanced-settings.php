<?php
namespace ReyCore\Customizer\Options\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class AdvancedSettings extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'woo-advanced-settings';
	}

	public function get_title(){
		return esc_html__('Advanced Options', 'rey-core');
	}

	public function get_priority(){
		return 300;
	}

	public function get_icon(){
		return 'woo-advanced-options';
	}

	public function get_title_before(){
		return esc_html__('OTHER SETTINGS', 'rey-core');
	}

	public function controls(){

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'fix_variable_product_prices',
			'label'       => esc_html__( 'Fix Variables Sale Prices Display', 'rey-core' ),
			'help' => [
				esc_html__( 'If a Variation product sale price is different than the others, this option will properly display it.', 'rey-core' )
			],
			'default'     => false,
		] );

		$this->add_separator();

		$this->add_control( [
			'type'        => 'text',
			'settings'    => 'custom_price_range',
			'label'       => esc_html__( 'Variable prices range format', 'rey-core' ),
			'description' => esc_html__( 'Add if you want to have a custom format for the price range in variable products. You can add a prefix or suffix, or use {{min} or {{max}} variables.', 'rey-core' ),
			'default'     => '',
			'input_attrs'     => [
				'placeholder' => esc_html__('eg: {{min} - {{max}}', 'rey-core'),
			],
		] );

	}
}
