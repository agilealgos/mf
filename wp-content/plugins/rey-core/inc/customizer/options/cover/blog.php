<?php
namespace ReyCore\Customizer\Options\Cover;

if ( ! defined( 'ABSPATH' ) ) exit;

use \ReyCore\Customizer\Controls;

class Blog extends \ReyCore\Customizer\SectionsBase {

	public static function get_id(){
		return 'cover-blog';
	}

	public function get_title(){
		return esc_html__('Blog', 'rey-core');
	}

	public function get_priority(){
		return 10;
	}

	public function get_icon(){
		return 'cover-blog';
	}

	public function help_link(){
		return reycore__support_url('kb/customizer-page-cover/#blog');
	}

	public function controls(){

		$this->add_control( [
			'type'        => 'custom',
			'settings'    => 'cover_title__blog',
			'default'     => \ReyCore\Customizer\Options\Cover::get_main_desc(),
		] );

		// Blog Categories
		$this->add_title( esc_html__('Blog Categories', 'rey-core'), [
			'description' => esc_html__('These settings will apply on all blog categories of your website. You can always disable or change the Page Cover of a specific category, in its options.', 'rey-core'),
		]);

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'cover__blog_cat',
			'label'       => esc_html__( 'Select a Page Cover', 'rey-core' ),
			'default'     => 'no',
			'choices'     => [
				'no' => 'Disabled',
			],
			'ajax_choices' => [
				'action' => 'get_global_sections',
				'params' => [
					'type' => 'cover',
				]
			],
			'edit_preview' => true,
		] );


		// Blog Posts
		$this->add_title( esc_html__('Blog Posts', 'rey-core'), [
			'description' => esc_html__('These settings will apply on all blog posts of your website. You can always disable or change the Page Cover of a specific post, in its options.', 'rey-core'),
		]);

		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'cover__blog_post',
			'label'       => esc_html__( 'Select a Page Cover', 'rey-core' ),
			'default'     => 'no',
			'choices'     => [
				'no' => 'Disabled',
			],
			'ajax_choices' => [
				'action' => 'get_global_sections',
				'params' => [
					'type' => 'cover',
				]
			],
			'edit_preview' => true,
		] );


		// Blog HOME
		$this->add_title( esc_html__('Blog Home (Page)', 'rey-core'), [
			'description' => esc_html__('You can assign a Page Cover into the Blog page. This page is auto-generated by WordPress, chosen in Customizer > General Settings > Homepage Settings.', 'rey-core'),
		]);
		$this->add_control( [
			'type'        => 'select',
			'settings'    => 'cover__blog_home',
			'label'       => esc_html__( 'Select a Page Cover', 'rey-core' ),
			'default'     => 'no',
			'choices'     => [
				'no' => 'Disabled',
			],
			'ajax_choices' => [
				'action' => 'get_global_sections',
				'params' => [
					'type' => 'cover',
				]
			],
			'edit_preview' => true,
		] );


	}
}
