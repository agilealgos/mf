<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if( class_exists('\ReyCore\Customizer\SectionsBase') ):

class ReyModuleFullscreenMenuCustomizer extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'header_fs_options';
	}

	public function get_title(){
		return esc_html__('FullScreen Navigation', 'rey-module-fullscreen-menu');
	}

	public function get_priority(){
		return 10;
	}

	public function help_link(){
		return 'https://support.reytheme.com/kb/customizer-header-settings/#fullscreen-navigation';
	}

	public function controls(){

		$this->add_control( [
			'type'        => 'custom',
			'settings'    => 'header_fs_title',
			'default'     => '<h2>' . esc_html__('Fullscreen Navigation', 'rey-module-fullscreen-menu') . '</h2> <hr>',
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_fs_type',
			'label'       => esc_html__( 'Content Type', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'priority'    => 10,
			'choices'     => [
				'' => esc_html__( '- Select -', 'rey-module-fullscreen-menu' ),
				'menu' => esc_html__( 'Menu', 'rey-module-fullscreen-menu' ),
				'gs' => esc_html__( 'Global Section', 'rey-module-fullscreen-menu' ),
			],
		] );

		if( class_exists('\ReyCore\Elementor\GlobalSections') ){

			$sections = \ReyCore\Elementor\GlobalSections::get_global_sections('generic', [
				''  => esc_html__( '- Select -', 'rey-module-fullscreen-menu' )
			]);

			$this->add_control( [
				'type'        => 'select',
				'settings'    => 'header_fs_gs',
				'label'       => esc_html__( 'Global Section', 'rey-module-fullscreen-menu' ),
				'default'     => '',
				'priority'    => 10,
				'choices'     => $sections,
				'active_callback' => [
					[
						'setting'  => 'header_fs_type',
						'operator' => '==',
						'value'    => 'gs',
					],
				],
			] );
		}

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_fs_menu',
			'label'       => esc_html__( 'Menu source', 'rey-module-fullscreen-menu' ),
			'description' => esc_html__( 'Please select the menu to be shown inside the menu.', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'choices'     => ['' => esc_html__('- Select -', 'rey-module-fullscreen-menu')],
			'ajax_choices' => 'get_menus_list',
			'active_callback' => [
				[
					'setting'  => 'header_fs_type',
					'operator' => '==',
					'value'    => 'menu',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'header_fs_size',
			'label'       => esc_html__( 'Menu Items Size', 'rey-module-fullscreen-menu' ),
			'description' => esc_html__( 'Select the menu items size.', 'rey-module-fullscreen-menu' ),
			'default'     => 'xl',
			'choices'     => [
				'sm' => esc_html__('Small', 'rey-module-fullscreen-menu'),
				'default' => esc_html__('Normal', 'rey-module-fullscreen-menu'),
				'lg' => esc_html__('Large', 'rey-module-fullscreen-menu'),
				'xl' => esc_html__('Extra Large', 'rey-module-fullscreen-menu'),
				'xxl' => esc_html__('Extra Extra Large', 'rey-module-fullscreen-menu'),
			],
			'active_callback' => [
				[
					'setting'  => 'header_fs_type',
					'operator' => '==',
					'value'    => 'menu',
				],
				[
					'setting'  => 'header_fs_menu',
					'operator' => '!=',
					'value'    => '',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'image',
			'settings'    => 'header_fs_logo',
			'label'       => esc_html__( 'Logo Image', 'rey-module-fullscreen-menu' ),
			'description' => esc_html__( 'This logo will be shown above the menu.', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'transport'   => 'auto',
			'choices'     => [
				'save_as' => 'id',
			],
			'active_callback' => [
				[
					'setting'  => 'header_fs_type',
					'operator' => '==',
					'value'    => 'menu',
				],
				[
					'setting'  => 'header_fs_menu',
					'operator' => '!=',
					'value'    => '',
				],
			],
		] );

		$this->add_control( [
			'type'        => 'color',
			'settings'    => 'header_fs_theme_custom',
			'label'       => esc_html__( 'Background Color', 'rey-module-fullscreen-menu' ),
			'description' => esc_html__( 'Text color is automatically calculated to be in contrast.', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
		] );

		$this->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_fs_theme_animcurtain',
			'label'       => esc_html__( 'Enable animated curtain.', 'rey-module-fullscreen-menu' ),
			'default'     => true,
		] );

		$this->add_control( [
			'type'        => 'color',
			'settings'    => 'header_fs_theme_custom__1',
			'label'       => esc_html__( 'Animated Mask #1 Color', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
			'transport'   => 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--fs-menu-bg-color-1',
				],
			],
			'active_callback' => [
				[
					'setting'  => 'header_fs_theme_animcurtain',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$this->add_control( [
			'type'        => 'color',
			'settings'    => 'header_fs_theme_custom__2',
			'label'       => esc_html__( 'Animated Mask #2 Color', 'rey-module-fullscreen-menu' ),
			'default'     => '',
			'choices'     => [
				'alpha' => true,
			],
			'transport'   => 'auto',
			'output'      		=> [
				[
					'element'  		=> ':root',
					'property' 		=> '--fs-menu-bg-color-2',
				],
			],
			'active_callback' => [
				[
					'setting'  => 'header_fs_theme_animcurtain',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

	}

}

endif;
