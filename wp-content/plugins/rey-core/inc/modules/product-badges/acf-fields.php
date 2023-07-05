<?php
namespace ReyCore\Modules\ProductBadges;

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
				'key' => 'field_5e541a33cdb3d',
				'label' => 'Badge',
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
				'key' => 'field_5efdd6127b753',
				'label' => 'Add badges',
				'name' => 'badges',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '--acf-repeater-row',
					'id' => '',
				],
				'collapsed' => '',
				'min' => 0,
				'max' => 0,
				'layout' => 'row',
				'button_label' => 'Add new badge',
				'parent' => self::FIELDS_GROUP_KEY,
				'sub_fields' => [
					[
						'key' => 'field_5efdd6317b754',
						'label' => 'Type',
						'name' => 'type',
						'type' => 'select',
						'instructions' => 'Select the product badge type',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '--size-3',
							'id' => '',
						],
						'choices' => [
							'text' => 'Text',
							'image' => 'Image',
						],
						'default_value' => false,
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					],
					[
						'key' => 'field_5efdd67c7b755',
						'label' => 'Text',
						'name' => 'text',
						'type' => 'text',
						'instructions' => 'Add the text inside the badge.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => 'eg: HOT',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					],
					[
						'key' => 'field_5efdd6ab7b756',
						'label' => 'Text Color',
						'name' => 'text_color',
						'type' => 'color_picker',
						'instructions' => 'Select the text color',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'enable_opacity' => 1,
					],
					[
						'key' => 'field_5efdd6cf7b757',
						'label' => 'Background Color',
						'name' => 'text_bg_color',
						'type' => 'color_picker',
						'instructions' => 'Select the background color',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'enable_opacity' => 1,
					],
					[
						'key' => 'field_5efdd6ef7b758',
						'label' => 'Text Size',
						'name' => 'text_size',
						'type' => 'number',
						'instructions' => 'Select the text size.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '20',
							'class' => '--size-1 rey-acf-responsive --desktop',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 8,
						'max' => 60,
						'step' => 1,
					],
					[
						'key' => 'field_5efdd7337b759',
						'label' => 'Text Size (Tablet]',
						'name' => 'text_size_tablet',
						'type' => 'number',
						'instructions' => 'Select the text size on tablet.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '20',
							'class' => '--size-1 rey-acf-responsive --tablet',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 8,
						'max' => 60,
						'step' => 1,
					],
					[
						'key' => 'field_5efdd7667b75a',
						'label' => 'Text Size (Mobile]',
						'name' => 'text_size_mobile',
						'type' => 'number',
						'instructions' => 'Select the text size on tablet.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'text',
								],
							],
						],
						'wrapper' => [
							'width' => '20',
							'class' => '--size-1 rey-acf-responsive --mobile',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 8,
						'max' => 60,
						'step' => 1,
					],
					[
						'key' => 'field_5efdd77e7b75b',
						'label' => 'Badge Image (s]',
						'name' => 'images',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'image',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'collapsed' => '',
						'min' => 0,
						'max' => 0,
						'layout' => 'block',
						'button_label' => 'Add new image',
						'sub_fields' => [
							[
								'key' => 'field_5efdd7a37b75c',
								'label' => 'Select image',
								'name' => 'select_image',
								'type' => 'image',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => [
									'width' => '',
									'class' => '',
									'id' => '',
								],
								'return_format' => 'id',
								'preview_size' => 'thumbnail',
								'library' => 'all',
								'min_width' => '',
								'min_height' => '',
								'min_size' => '',
								'max_width' => '',
								'max_height' => '',
								'max_size' => '',
								'mime_types' => '',
							],
						],
					],
					[
						'key' => 'field_5efdd7d97b75d',
						'label' => 'Image size',
						'name' => 'image_size',
						'type' => 'number',
						'instructions' => 'Select the badge maximum width.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'image',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '--size-1 rey-acf-responsive --desktop',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 5,
						'max' => 200,
						'step' => 1,
					],
					[
						'key' => 'field_5efdd8127b75e',
						'label' => 'Image size (tablet]',
						'name' => 'image_size_tablet',
						'type' => 'number',
						'instructions' => 'Select the badge maximum width for tablets.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'image',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '--size-1 rey-acf-responsive --tablet',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 5,
						'max' => 200,
						'step' => 1,
					],
					[
						'key' => 'field_5efdd82d7b75f',
						'label' => 'Image size (mobile]',
						'name' => 'image_size_mobile',
						'type' => 'number',
						'instructions' => 'Select the badge maximum width for mobiles.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efdd6317b754',
									'operator' => '==',
									'value' => 'image',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '--size-1 rey-acf-responsive --mobile',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'px',
						'min' => 5,
						'max' => 200,
						'step' => 1,
					],
					[
						'key' => 'field_5efd755d67c7b',
						'label' => 'Custom Link',
						'name' => 'link',
						'type' => 'text',
						'instructions' => 'Add a link on the badge. Supports shortcodes.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => 'eg: https://website.com/',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					],
					[
						'key' => 'field_5faebdb8d1573',
						'label' => 'Add on catalog page',
						'name' => 'catalog_page',
						'type' => 'true_false',
						'instructions' => 'Select if you want to display on product catalog page.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'message' => '',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					],
					[
						'key' => 'field_5efdd84a7b760',
						'label' => 'Show badge on mobile',
						'name' => 'show_on_mobile',
						'type' => 'true_false',
						'instructions' => 'Select if you want to show or hide the badge on mobiles.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5faebdb8d1573',
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
						'message' => '',
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					],
					[
						'key' => 'field_5efdd88d7b761',
						'label' => 'Badge Position',
						'name' => 'position',
						'type' => 'select',
						'instructions' => 'Choose position in product item in catalog page.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5faebdb8d1573',
									'operator' => '==',
									'value' => '1',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '--size-2',
							'id' => '',
						],
						'choices' => [
							'top_left' => 'Top Left',
							'top_right' => 'Top Right',
							'bottom_left' => 'Bottom Left',
							'bottom_right' => 'Bottom Right',
							'before_title' => 'Before Title',
							'after_content' => 'After content',
						],
						'default_value' => 'top_left',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					],
					[
						'key' => 'field_5efe3426f25ae',
						'label' => 'Add on product page',
						'name' => 'product_page',
						'type' => 'true_false',
						'instructions' => 'Select if you want to display on product page too.',
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
						'key' => 'field_5efe344bf25af',
						'label' => 'Product page Position',
						'name' => 'product_page_position',
						'type' => 'select',
						'instructions' => 'Select the position where you want to display the badge in the product page',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efe3426f25ae',
									'operator' => '==',
									'value' => '1',
								],
							],
						],
						'wrapper' => [
							'width' => '',
							'class' => '--size-3',
							'id' => '',
						],
						'choices' => [
							'before_title' => 'Before Title',
							'before_meta' => 'Before Meta',
							'after_meta' => 'After Meta',
						],
						'default_value' => 'before_title',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					],
					[
						'key' => 'field_5efe6f23425ae',
						'label' => 'Show as block',
						'name' => 'product_page_as_block',
						'type' => 'true_false',
						'instructions' => 'Select if you want to display the badge as a block.',
						'required' => 0,
						'conditional_logic' => [
							[
								[
									'field' => 'field_5efe3426f25ae',
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
						'message' => '',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					],
				],
			],
		];
	}
}
