<?php
namespace ReyCore\Elementor\Custom;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Kit {

	public function __construct(){
		add_action( 'elementor/element/kit/section_settings-layout/before_section_end', [$this, 'kit_layout_settings']);
		add_action( 'elementor/element/kit/section_layout-settings/before_section_end', [$this, 'kit_layout_settings']);
		add_action( 'elementor/element/kit/section_buttons/before_section_end', [$this, 'kit_button_settings']);
	}

	/**
	 * Remove Container width as it directly conflicts with Rey's container settings
	 *
	 * @since 1.6.12
	 */
	public function kit_layout_settings( $stack ){
		$stack->remove_responsive_control( 'container_width' );
	}

	public function kit_button_settings( $stack ){

		if( ! apply_filters('reycore/elementor/kit/custom_button_selectors', true) ){
			return;
		}

		$button_selectors = [
			'{{WRAPPER}} .elementor-button',
		];

		$button_hover_selectors = [
			'{{WRAPPER}} .elementor-button:hover',
			'{{WRAPPER}} .elementor-button:focus',
		];

		$button_selector = implode( ',', $button_selectors );
		$button_hover_selector = implode( ',', $button_hover_selectors );

		$controls = [
			// default
			'button_text_color' => $button_selector,
			'button_background_color' => $button_selector,
			'button_box_shadow_box_shadow' => $button_selector,
			'button_border_radius' => $button_selector,
			'button_border_width' => $button_selector,
			'button_border_color' => $button_selector,
			'button_padding' => $button_selector,
			// hover
			'button_hover_text_color' => $button_hover_selector,
			'button_hover_background_color' => $button_hover_selector,
			'button_hover_border_width' => $button_hover_selector,
			'button_hover_border_color' => $button_hover_selector,
			'button_hover_box_shadow_box_shadow' => $button_hover_selector,
			'button_hover_border_radius' => $button_hover_selector,
		];

		foreach ($controls as $control_key => $control_selector) {

			$kit_element = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $stack->get_unique_name(), $control_key );

			$old_selector = array_values($kit_element['selectors']);

			if( ! isset($old_selector[0]) ){
				continue;
			}

			$kit_element['selectors'] = [];
			$kit_element['selectors'][$control_selector] = $old_selector[0];

			$stack->update_control( $control_key, $kit_element );
		}

	}

}
