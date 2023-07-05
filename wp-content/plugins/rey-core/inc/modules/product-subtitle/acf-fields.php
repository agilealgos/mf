<?php
namespace ReyCore\Modules\ProductSubtitle;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AcfFields {

	public function __construct(){

		if( ! function_exists('acf_add_local_field_group') ){
			return;
		}

		acf_add_local_field_group($this->field());

	}

	public function field(){

		return [
			'key' => 'group_6304bca9cacf6',
			'title' => 'Product Subtitle',
			'fields' => [
				[
					'key' => 'field_6304bccf7852f',
					'label' => 'Add product subtitle text',
					'name' => 'product_subtitle_text',
					'type' => 'text',
					'instructions' => 'Insert a text to be displayed under the product title, in catalog and product page.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				]
			],
			'location' => [
				[
					[
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'product',
					]
				]
			],
			'menu_order' => 0,
			'position' => 'normal',
			// 'position' => 'acf_after_title',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
			'show_in_rest' => 0,
		];

	}
}
