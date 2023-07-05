<?php
/**
 * Plugin Name: Rey Module - Preloaders Pack
 * Description: This module will add extra preloader styles into Rey Theme. Access Customizer > General > Site preloader and you can find different styles.
 * Plugin URI: http://www.reytheme.com/
 * Version: 1.1.1
 * Author: ReyTheme
 * Author URI:  https://twitter.com/mariushoria
 * Text Domain: rey-module-preloaders
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if( class_exists('ReyCore') && !class_exists('ReyModulePreloaders') ):

class ReyModulePreloaders
{

	public function __construct()
	{
		$this->define_constants();

		add_action( 'plugins_loaded', [$this, 'plugins_loaded']);
		add_action( 'init', [$this, 'init']);
	}

	public function init(){
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);

		new ReyModulePreloadersCustomizer();
	}

	/**
	 * Define Constants.
	 * @since 1.0.0
	 */
	private function define_constants()
	{
		$this->define( 'REY_MODULE_PRELOADERS_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'REY_MODULE_PRELOADERS_URI', plugin_dir_url( __FILE__ ) );

		if( !defined('REY_DEV_MODE') ){
			$this->define( 'REY_MODULE_PRELOADERS_VERSION', '1.1.1' );
		}
		else {
			// cache buster
			$this->define( 'REY_MODULE_PRELOADERS_VERSION', rand(100, 99999) );
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

	function get_preloader_type(){
		return esc_html( get_theme_mod('header_prl_type', '') );
	}

	function enqueue_frontend_scripts()
	{
		if( $type = $this->get_preloader_type() ){
			wp_enqueue_style('reyModulePreloaders-styles', REY_MODULE_PRELOADERS_URI . "assets/css/{$type}.css", false, REY_MODULE_PRELOADERS_VERSION, null);
		}
	}

	// Load localization file
	function load_plugin_textdomain(){
		load_plugin_textdomain( 'rey-module-preloaders', false, plugin_basename(dirname(__FILE__)) . '/languages');
	}

	function plugins_loaded(){

		$this->load_plugin_textdomain();

		//#! Load core files
		require_once REY_MODULE_PRELOADERS_DIR . 'inc/customizer-options.php';

		if( $type = $this->get_preloader_type() ){
			require_once REY_MODULE_PRELOADERS_DIR . "inc/preloaders/{$type}.php";
		}
	}

}

new ReyModulePreloaders;

endif;
