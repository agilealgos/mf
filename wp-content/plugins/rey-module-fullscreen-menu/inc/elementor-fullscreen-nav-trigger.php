<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!class_exists('ReyModuleFullScreen_MenuWidget')):

/**
 *
 * Elementor widget.
 *
 * @since 1.0.0
 */
class ReyModuleFullScreen_MenuWidget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'reymodule-fullscreen-navigation';
	}

	public function get_title() {
		return __( 'Fullscreen Nav. Icon', 'rey-module-fullscreen-menu' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return [ 'rey-header' ];
	}

	public function get_custom_help_url() {
		return 'https://support.reytheme.com/kb/rey-elements-header/#fullscreen-navigation';
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'rey-module-fullscreen-menu' ),
			]
		);

		$this->add_control(
			'notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'If you don\'t want to show this element, simply remove it from its section.', 'rey-module-fullscreen-menu' ),
				'content_classes' => 'rey-raw-html',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Icon Color', 'rey-module-fullscreen-menu' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .rey-fsNav-mobileBtn span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Button Style', 'rey-module-fullscreen-menu' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Default - 3 bars', 'rey-module-fullscreen-menu' ),
					'-2bars'  => esc_html__( '2 bars', 'rey-module-fullscreen-menu' ),
					'-hover2bars'  => esc_html__( 'Hover - 2 bars', 'rey-module-fullscreen-menu' ),
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Bars Width', 'rey-module-fullscreen-menu' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				// 'default' => '',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}} .rey-fsNav-mobileBtn' => 'font-size: {{VALUE}}px',
				],
			]
		);

		$this->end_controls_section();

	}


	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		if( REY_MODULE_FULLSCREEN_MENU__CORE_DEPENDENCY ){
			reycore__get_template_part('template-parts/rey-module-fullscreen-menu-hamburger-icon' . esc_attr($this->get_settings_for_display('layout')) , REY_MODULE_FULLSCREEN_MENU_DIR);
		}
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template() {}

}
endif;
