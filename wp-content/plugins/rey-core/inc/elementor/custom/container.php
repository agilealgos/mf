<?php
namespace ReyCore\Elementor\Custom;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Container {

	function __construct(){
		add_action( 'elementor/frontend/container/before_render', [$this, 'before_render']);
		add_action( 'elementor/frontend/container/after_render', [$this, 'after_render']);
		add_action( 'elementor/element/container/_section_responsive/after_section_end', [$this, 'custom_css_settings']);
		add_action( 'elementor/element/container/section_layout_additional_options/before_section_end', [$this, 'offset_for_mobile']);
		add_action( 'elementor/element/container/section_layout/before_section_end', [$this, 'section_layout_settings']);
		add_action( 'elementor/element/container/section_layout_container/before_section_end', [$this, 'section_layout_container_settings']);
		add_filter( 'elementor/frontend/container/should_render', ['\ReyCore\Elementor\WidgetsOverrides', 'should_render_element_or_widget'], 10, 2 );
	}


	/**
	* Render some attributes before rendering
	*
	* @since 1.0.0
	**/
	function before_render( $element )
	{

		if( ! apply_filters( "elementor/frontend/container/should_render", true, $element ) ){
			return;
		}

		$element_id = $element->get_id();

		do_action('reycore/frontend/container/before_render', $element);

		$this->should_render[$element_id] = true;

	}

	/**
	* Add HTML after container rendering
	*
	* @since 1.0.0
	**/
	function after_render( $element )
	{

		$element_id = $element->get_id();

		if( ! isset($this->should_render[$element_id]) ){
			return;
		}

		if( ! $this->should_render[$element_id] ){
			return;
		}

		do_action('reycore/frontend/container/after_render', $element);

	}

	public function custom_css_settings( $element ){
		\ReyCore\Elementor\WidgetsOverrides::custom_css_controls($element);
	}

	public function section_layout_settings( $element ){

		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		$unique_name = $element->get_unique_name();

		// Zindex
		$z_index = $controls_manager->get_control_from_stack( $unique_name, 'z_index' );
		$z_index['prefix_class'] = '--zindexed-';
		$element->update_control( 'z_index', $z_index );

		// "Hide On" Selector
		\ReyCore\Elementor\WidgetsOverrides::hide_element_on($element);
	}

	public function offset_for_mobile( $element ){
		// Horizontal Mobile Offset
		\ReyCore\Elementor\WidgetsOverrides::horizontal_offset_for_mobile(
			$element ,
			esc_html__('Only works with an inner Container, set as Row.', 'rey-core')
		);
	}

	public function section_layout_container_settings( $element ){

		$element->start_injection( [
			'of' => 'content_width',
			'at' => 'before',
		] );

		$element->add_control(
			'rey_stretch_section',
			[
				'label' => __( 'Stretch Container', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'section-stretched',
				'prefix_class' => 'rey-',
				'hide_in_inner' => true,
				'description' => __( 'Stretch the container to the full width of the page using plain CSS.', 'rey-core' ),
			]
		);

		$element->end_injection();

		$element->add_responsive_control(
			'rey_cols',
			[
				'label' => esc_html__( 'Columns [beta]', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 2,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'condition' => [
					'flex_direction' => ['row', 'row-reverse'],
					'flex_wrap' => 'wrap',
				],
				'prefix_class' => 'rey-con-cols-',
				'selectors' => [
					'{{WRAPPER}}' => '--cols-count: {{VALUE}}',
				],

			]
		);

	}
}
