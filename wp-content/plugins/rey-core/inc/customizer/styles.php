<?php
namespace ReyCore\Customizer;

if ( ! defined( 'ABSPATH' ) ) exit;

class Styles {

	const CSS_OPTION_NAME = 'rey__custom_css_option';

	public static $cache_enabled;

	public function __construct(){

		add_filter( 'rey/allow_enqueue_custom_styles', '__return_false'); // prevent enqueue rey styles, instead to hook into kirki's css
		add_action( 'wp', [$this, 'make_dynamic_css']);

		if( apply_filters('reycore/inline_styles/position/head', true) ){
			add_action( 'wp_head', [$this, 'print_styles'], 998 );
		}
		else {
			add_action( 'wp_footer', [$this, 'print_styles'], 50 );
		}

		add_action( 'init', [$this, 'disable_kirki_css'], 5 );
		add_action( 'wp_ajax_refresh_dynamic_css', [$this, 'refresh_dynamic_css_ajax']);
		add_filter( 'kirki_rey_core_kirki_dynamic_css', [$this, 'enqueue_styles_in_kirki']);
		add_action( 'acf/save_post', [$this, 'theme_settings_save'], 20);
		add_action( 'customize_save_after', [$this, 'custom_css_option_delete'], 20);
		add_action( 'activated_plugin', [$this, 'custom_css_option_delete'], 20);
		add_action( 'deactivated_plugin', [$this, 'custom_css_option_delete'], 20);
		add_action( 'wp_update_nav_menu', [$this, 'custom_css_option_delete'], 20);
		add_action( 'rey/flush_cache_after_updates', [$this, 'custom_css_option_delete'], 20);
	}

	/**
	 * Check if CSS cache is enabled
	 * Control if want Customizer\'s generated css to be cached. This should result in performance improvements.
	 *
	 * @since 1.6.0
	 **/
	public function css_cache_is_enabled()
	{
		return \ReyCore\Plugin::instance()->customizer->cache;
	}

	public static function has_cached_css(){
		global $wp_customize;
		return \ReyCore\Plugin::instance()->customizer->cache && get_option( self::CSS_OPTION_NAME ) && ! $wp_customize;
	}

	/**
	 * Prevent loading Kirki's CSS module
	 * in frontend.
	 *
	 * @since 1.6.2
	 */
	public function disable_kirki_css() {

		// it's frontend and styles exist, bail
		if( ! self::has_cached_css() ){
			return;
		}

		if( ! class_exists('Kirki_Modules_CSS') ){
			return;
		}

		// Kirki 4+
		if( class_exists('\Kirki\Module\CSS') ){
			reycore__remove_filters_for_anonymous_class('init', 'Kirki\Module\CSS', 'init', 10);
			return;
		}

		// Kirki 3
		remove_action( 'init', [ \Kirki_Modules_CSS::get_instance(), 'init'] );

	}

	/**
	 * Minify CSS
	 *
	 * @since 1.6.2
	 */
	public static function get_minified_css(){
		if( function_exists('rey__custom_styles') && ($styles_output = rey__custom_styles()) && is_array($styles_output) ){
			$styles_output = implode(' ', $styles_output);
			$styles_output = str_replace(array("\r\n", "\r", "\n"), '', $styles_output);
			return $styles_output;
		}
	}

	/**
	 * Cache CSS
	 *
	 * @since 1.6.2
	 */
	public function make_dynamic_css(){

		if(
			is_customize_preview() || wp_doing_ajax() || wp_doing_cron() || is_admin() ||
			is_feed() || is_preview() || (defined( 'REST_REQUEST' ) && REST_REQUEST) ||
			(isset($_REQUEST['editor']) && 1 === absint($_REQUEST['editor']))
			){
			return;
		}

		if( ! $this->css_cache_is_enabled() ){
			return;
		}

		if( get_option( self::CSS_OPTION_NAME ) ) {
			return;
		}

		// Force setting fonts
		\ReyCore\Plugin::instance()->fonts->set_fonts( true );

		// Grab Kirki's styles
		ob_start();
			if( class_exists('\Kirki_Modules_CSS') ){
				\Kirki_Modules_CSS::get_instance()->print_styles();
			}
		$styles = ob_get_clean();

		do_action('reycore/customizer/make_dynamic_css', $styles, $this);

		// bail if no styles from Kirki
		if( ! $styles ){
			return;
		}

		// Grab Rey's styles
		$styles .= self::get_minified_css();

		// cache CSS
		update_option( self::CSS_OPTION_NAME, $styles );
	}

	public function print_styles(){
		$this->print_inline_style();
		$this->print_page_inline_style();
	}

	/**
	 * Print Inline Styles
	 *
	 * @since 1.5.4
	 **/
	public function print_inline_style()
	{
		if( ! $this->css_cache_is_enabled() ){
			return;
		}

		if( $css = get_option( self::CSS_OPTION_NAME ) ) {
			echo '<style id="reycore-inline-styles">' . $css . '</style>';
		}
	}

	/**
	 * Print Inline Styles for pages
	 *
	 * @since 1.5.4
	 **/
	public function print_page_inline_style()
	{
		if( $css = apply_filters('reycore/page_css_styles', []) ) {
			echo '<style id="reycore-page-inline-styles">' . implode( '', $css ) . '</style>';
		}
	}

	/**
	 * Clearn Custom CSS Option
	 *
	 * @since 1.5.4
	 **/
	public function custom_css_option_delete() {

		delete_transient('rey_early_js');

		if( ! $this->css_cache_is_enabled() ){
			return;
		}

		return delete_option( self::CSS_OPTION_NAME );
	}

	/**
	 * Clearn Custom CSS Transients
	 *
	 * @since 1.5.4
	 **/
	public function theme_settings_save( $post_id ) {
		if ( $post_id === REY_CORE_THEME_NAME ) {
			$this->custom_css_option_delete();
		}
	}

	/**
	 * Refresh Dynamic CSS through Ajax
	 *
	 * @since 1.6.0
	 **/
	public function refresh_dynamic_css_ajax()
	{
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error();
		}

		if( $this->custom_css_option_delete() ){
			do_action('reycore/refresh_dynamic_css_ajax');
			wp_send_json_success();
		}

	}

	/**
	 * Load Rey's custom CSS in HEAD
	 */
	public function enqueue_styles_in_kirki( $styles ){

		if( ! $this->css_cache_is_enabled() || is_customize_preview() ){
			$styles .= self::get_minified_css();
		}

		return $styles;
	}

}
