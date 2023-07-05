<?php
namespace ReyCore\Modules\PdpCustomTabs;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AcfFields {

	const FIELDS_GROUP_KEY = 'group_5d4ff536a2684';

	public function __construct(){

		if( ! function_exists('acf_add_local_field') ){
			return;
		}

		foreach ($this->fields() as $key => $field) {
			acf_add_local_field($field);
		}

	}

	public function fields(){
		return [

			[
				'key' => 'field_5ecaea2256e70',
				'label' => 'Custom Tabs',
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'placement' => 'top',
				'endpoint' => 0,
				'parent' => self::FIELDS_GROUP_KEY,
			],
			[
				'key' => 'field_5ecae99f56e6d',
				'label' => 'Tabs',
				'name' => 'product_custom_tabs',
				'type' => 'repeater',
				'instructions' => 'These tabs are created in Customizer > WooCommerce > Product page - Tabs panel.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => 'rey-hideNewBtn',
					'id' => '',
				],
				'collapsed' => 'field_5ecae9c356e6e',
				'min' => 0,
				'max' => 0,
				'layout' => 'row',
				'button_label' => '',
				'parent' => self::FIELDS_GROUP_KEY,
				'sub_fields' => [
					[
						'key' => 'field_615be5b543708',
						'label' => 'Override Title',
						'name' => 'override_title',
						'type' => 'true_false',
						'instructions' => 'Enable if you want to override the tab title.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'message' => '',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					],
					[
						'key' => 'field_5ecae9c356e6e',
						'label' => 'Tab Title',
						'name' => 'tab_title',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_615be5b543708',
									'operator' => '==',
									'value' => '1',
								],
							],
						],
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
					],
					[
						'key' => 'field_5ecae9ef56e6f',
						'label' => 'Tab Content',
						'name' => 'tab_content',
						'type' => 'wysiwyg',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'tabs' => 'all',
						'toolbar' => 'full',
						'media_upload' => 1,
						'delay' => 1,
					],
				],
			],

		];
	}
}
