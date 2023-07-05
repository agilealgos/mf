<?php
/**
 * Plugin Name: Rey Module - Side Header
 * Description: A module for Rey Theme to display the site header as a side fixed panel.
 * Plugin URI: http://www.reytheme.com/
 * Version: 1.1.0
 * Author: ReyTheme
 * Author URI:  https://twitter.com/mariushoria
 * Text Domain: rey-module-side-header
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if( class_exists('ReyCore') && !class_exists('ReyModuleSideHeader') ):

class ReyModuleSideHeader
{
	public function __construct()
	{
		$this->define_constants();
		$this->load_hooks();
		$this->includes();
	}

	/**
	 * Load Hooks
	 *
	 * @since 1.0.0
	 */
	public function load_hooks()
	{
		add_action( 'plugins_loaded', [$this, 'plugins_loaded']);
		add_action( 'init', [$this, 'init']);
		add_action('reycore/customizer/panel=header', [$this, 'load_customizer_options']);
		add_action('reycore/customizer/control=header_nav_items_spacing', [$this, 'add_nav_options'], 10, 2);
		add_action('reycore/customizer/section=header-general', [$this, 'disable_customizer_controls'], 20);
	}

	public function init()
	{
		if( !$this->header_enabled() ){
			return;
		}

		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
		add_filter( 'rey/header/header_classes', [$this, 'filter_header_classes'], 20);
		add_filter( 'body_class', [$this, 'filter_body_classes'], 20);
		add_filter( 'rey/main_script_params', [ $this, 'script_params'], 11 );
		// Restrict search style
		add_filter( 'theme_mod_header_search_style', [$this, 'restrict_search_style']);
		add_filter( 'reycore/header/search_params', [$this, 'restrict_search_style_elementor']);
		// Restrict Account
		add_filter( 'theme_mod_header_account_type', [$this, 'header_account_type']);
		add_filter( 'reycore/elementor/header_account/vars', [$this, 'header_account_type_elementor']);


	}

	function header_enabled(){
		return get_theme_mod('side_header_enable', false) && reycore__get_option('header_layout_type', 'default') !== 'none' & !is_404();
	}

	function header_is_small(){
		return absint( get_theme_mod('side_header_width', 250) ) < apply_filters('reymodule/side_header/small_threadshold', 120);
	}

	/**
	 * Cleanup header classes
	 */
	function filter_header_classes($classes){

		$classes['position'] = 'header-pos--side';
		// $classes['position'] = 'header-pos--rel header-pos--side';

		if( reycore__get_option('header_layout_type', 'default') === 'default' ){

			if ($sh_style = get_theme_mod('side_header_basic_style', '')){
				$classes['side_header_default_style'] = 'side-header-def' . $sh_style;
			}

		}
				if ($sh_align = get_theme_mod('side_header_x_align', 'start')) {
					$classes['side_header_default_align'] = 'side-header-def-align-' . $sh_align;
				}

		if( get_theme_mod('side_header_hover_effect', true) ){
			$classes['side_header_hover'] = 'side-header--hover';
		}

		if( $this->header_is_small() ){
			$classes['side_header_small'] = 'side-header--small';
		}

		unset($classes['separator-bar']);
		unset($classes['separator-bar-mobile']);
		unset($classes['fixed-shrink']);

		return $classes;
	}

	/**
	 * Add class for body
	 */
	function filter_body_classes($classes){

		$classes['side-header-wrapper'] = '--side-header';

		return $classes;
	}

	/**
	 * Change search style
	 */
	function restrict_search_style( $style ){

		if( $style === 'wide' || $style === 'inline' ){
			return 'side';
		}

		return $style;
	}

	/**
	 * Change search style in elementor element
	 */
	function restrict_search_style_elementor( $vars ){

		if( isset($vars['search_style']) ){
			if( $vars['search_style'] === 'wide' || $vars['search_style'] === 'inline' ){
				$vars['search_style'] = 'side';
			}
		}

		return $vars;
	}

	/**
	 * Change account type
	 */
	function header_account_type( $style ){

		if( $this->header_is_small() ){
			return 'icon';
		}

		return $style;
	}

	/**
	 * Change account type in elementor element
	 */
	function header_account_type_elementor( $vars ){

		if( $this->header_is_small() ){
			$vars['icon_type'] = 'icon';
		}

		return $vars;
	}

	/**
	 * Filter Script params
	 */
	public function script_params($params)
	{
		$params['account_panel_custom_position'] = true;
		return $params;
	}

	public function load_customizer_options( $base ){
		$base->register_section( new ReyModuleSideHeader__Customizer() );
	}

	public function add_nav_options($control, $section){

		if( ! get_theme_mod('side_header_enable', false) ){
			return;
		}

		$section->add_control( [
			'type'        => 'slider',
			'settings'    => 'header_nav_items_spacing_y',
			'label'       => esc_html__( 'Vertical Spacing (px)', 'rey-module-side-header' ),
			'section'     => 'header-navigation',
			'default'     => 5,
			'transport'   => 'auto',
			'choices'     => [
				'min'  => 1,
				'max'  => 100,
				'step' => 1,
			],
			'output'      		=> [
				[
					'media_query'	=> '@media (min-width: 1025px)',
					'element'  		=> ':root',
					'property' 		=> '--header-nav-y-spacing',
					'units'    		=> 'px',
				],
			],
		] );

	}

	public function disable_customizer_controls( $section ){

		if( ! get_theme_mod('side_header_enable', false) ){
			return;
		}

		foreach ([
			'header_position',
			'custom_header_width',
			'header_width',
			'header_height',
			'header_separator',
			'header_separator_bar',
			'header_separator_bar_color',
			'header_separator_bar_mobile',
			'header_fixed_overlap',
			'header_fixed_disable_mobile',
			'header_nav_items_spacing',
		] as $field) {
			$section->remove_control($field);
		}

	}

	/**
	 * Define Constants.
	 * @since 1.0.0
	 */
	private function define_constants()
	{
		$this->define( 'REY_MODULE_SIDE_HEADER_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'REY_MODULE_SIDE_HEADER_URI', plugin_dir_url( __FILE__ ) );

		if( !defined('REY_DEV_MODE') ){
			$this->define( 'REY_MODULE_SIDE_HEADER_VERSION', '1.1.0' );
		}
		else {
			// cache buster
			$this->define( 'REY_MODULE_SIDE_HEADER_VERSION', rand(100, 99999) );
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	function enqueue_frontend_scripts()
	{
		// Enqueue scripts
		wp_enqueue_script( 'reyModuleSideHeader-scripts', REY_MODULE_SIDE_HEADER_URI . 'assets/js/script.js', ['jquery', 'reycore-scripts'], REY_MODULE_SIDE_HEADER_VERSION, true );
		// wp_localize_script( 'reyModuleSideHeader-scripts', 'reyModuleSideHeaderParams', [] );
		// Enqueue Styles
		wp_enqueue_style('reyModuleSideHeader-styles', REY_MODULE_SIDE_HEADER_URI . 'assets/css/styles.css', false, REY_MODULE_SIDE_HEADER_VERSION, null);
	}

	// Load localization file
	function load_plugin_textdomain(){
		load_plugin_textdomain( 'rey-module-side-header', false, plugin_basename(dirname(__FILE__)) . '/languages');
	}

	function plugins_loaded(){
		$this->load_plugin_textdomain();
	}

	function includes(){
		//#! Load core files
		require_once REY_MODULE_SIDE_HEADER_DIR . 'inc/customizer-options.php';
	}

}

new ReyModuleSideHeader;

endif;
