<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if( class_exists('\ReyCore\Customizer\SectionsBase') ):

class ReyModuleSideHeader__Customizer extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'side_header_options';
	}

	public function get_title(){
		return esc_html__('Side Header', 'rey-module-side-header');
	}

	public function get_priority(){
		return 15;
	}

	public function controls(){

		$this->add_control( [
			'type'        => 'custom',
			'settings'    => 'side_header_title',
			'default'     => '<h2>' . esc_html__('Side Header', 'rey-module-side-header') . '</h2> <hr>',
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'side_header_enable',
			'label'       => esc_html__( 'Enable Side Header', 'rey-module-side-header' ),
			'default'     => false,
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'side_header_basic_style',
			'label'       => esc_html__( 'Layout Style', 'rey-module-side-header' ),
			'default'     => '',
			'choices'     => [
				'' => esc_html__( 'Default', 'rey-module-side-header' ),
				'--s1' => esc_html__( 'Middle Navigation', 'rey-module-side-header' ),
				'--s2' => esc_html__( 'Middle Logo', 'rey-module-side-header' ),
			],
			'active_callback' => [
				[
					'setting'  => 'side_header_enable',
					'operator' => '==',
					'value'    => true,
				],
				[
					'setting'  => 'header_layout_type',
					'operator' => '==',
					'value'    => 'default',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'side_header_x_align',
			'label'       => esc_html__( 'Horizontal Align', 'rey-module-side-header' ),
			'tooltip'       => esc_html__( 'Select a horizontal alignment for the inner header components.', 'rey-module-side-header' ),
			'default'     => 'start',
			'priority'    => 10,
			'choices'     => [
				'start' => esc_html__( 'Start', 'rey-module-side-header' ),
				'center' => esc_html__( 'Center', 'rey-module-side-header' ),
				'end' => esc_html__( 'End', 'rey-module-side-header' ),
			],
			'active_callback' => [
				[
					'setting'  => 'side_header_enable',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'side_header_hover_effect',
			'label'       => esc_html__( 'Enable Hover Effect', 'rey-module-side-header' ),
			'default'     => true,
			'active_callback' => [
				[
					'setting'  => 'side_header_enable',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->add_control( array(
			'type'        		=> 'slider',
			'settings'    		=> 'side_header_width',
			'label'       		=> esc_attr__( 'Width', 'rey-module-side-header' ),
			'default'     		=> 250,
			'choices'     		=> array(
				'min'  => 30,
				'max'  => 400,
				'step' => 1,
			),
			// 'transport'   => 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--side-header--width',
					'units'    		=> 'px',
				],
			],
			'active_callback' => [
				[
					'setting'  => 'side_header_enable',
					'operator' => '==',
					'value'    => true,
				],
			],
		));

		$this->add_control( [
			'type'        => 'dimensions',
			'settings'    => 'side_header_padding',
			'label'       => esc_html__( 'Side Padding', 'rey-module-side-header' ),
			'description' => __( 'Will add padding around the header container. Dont forget to include unit (eg: px, em, rem).', 'rey-module-side-header' ),
			'default'     => [
				'padding-top'    => '',
				'padding-right'  => '',
				'padding-bottom' => '',
				'padding-left'   => '',
			],
			'choices'     => [
				'labels' => [
					'padding-top'  => esc_html__( 'Top', 'rey-module-side-header' ),
					'padding-right' => esc_html__( 'Right', 'rey-module-side-header' ),
					'padding-bottom'  => esc_html__( 'Bottom', 'rey-module-side-header' ),
					'padding-left' => esc_html__( 'Left', 'rey-module-side-header' ),
				],
			],
			'transport'   		=> 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--side-header-',
				],
			],
			'input_attrs' => [
				'data-needs-unit' => 'px',
				'data-control-class' => 'dimensions-4-cols',
			],
			'active_callback' => [
				[
					'setting'  => 'side_header_enable',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

	}

}

endif;
