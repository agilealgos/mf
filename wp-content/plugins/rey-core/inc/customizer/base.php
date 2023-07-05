<?php
namespace ReyCore\Customizer;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base {

	public $controls;

	public $panels = [];

	public $sections = [];

	public $cache;

	public function __construct(){

		$this->includes();

		add_action( 'init', [$this, 'init'], 0);
		add_action( 'init', [$this, 'load_options'], 15 );
		add_action( 'customize_register', [$this, 'customize_register'] );
		add_action( 'wp_ajax_rey_transfer_mods', [$this, 'transfer_theme_settings']);
		add_action( 'customize_controls_print_styles', [$this, 'print_customizer_css'] );
		add_action( 'customize_save_after', [$this, 'customizer_to_acf'], 20);
		add_filter( 'customize_save_response', [ $this, 'run_update_action' ], 10, 2 );

	}

	public function includes(){

		if( ! class_exists('\CEI_Core') ){
			include_once __DIR__ . '/customizer-export-import/customizer-export-import.php';
		}

		if( ! class_exists('\Kirki_Field') ){
			return;
		}

		include_once __DIR__ . '/fields/title.php';
		include_once __DIR__ . '/fields/hf_global_sections.php';
		include_once __DIR__ . '/fields/number.php';
		include_once __DIR__ . '/fields/select-extended.php';
		include_once __DIR__ . '/fields/select.php';
		include_once __DIR__ . '/fields/button.php';
		include_once __DIR__ . '/fields/color.php';
	}

	public function init(){

		/**
		 * Wether it will cache Customizer's styles. Enabled by default.
		 * Use `add_filter('reycore/customizer/css_cache', '__return_false');` to disable.
		 * @since 2.4.0
		 */
		$this->cache = apply_filters('reycore/customizer/css_cache', true);

		$this->controls = new Controls();

		new Styles();
		new KirkiSupport();

	}

	/**
	 * Load Customizer Options
	 *
	 * @since 1.6.0
	 **/
	public function load_options()
	{

		if( ! $this->__can_run() ){
			return;
		}

		do_action('reycore/customizer/before_init');

		foreach ( [
			'General',
			'Header',
			'Footer',
			'Cover',
			'Woocommerce',
			'Various',
		] as $panel_class) {

			$class_name = '\\ReyCore\\Customizer\\Options\\' . $panel_class;

			$panel = new $class_name();

			$this->register_panel( $panel );

		}

		do_action('reycore/customizer/init', $this);

	}

	public function register_panel( $panel_class ){
		if( $panel_id = $panel_class::get_id() ){
			$this->panels[ $panel_id ] = $panel_class;
		}
	}


	public function __can_run(){

		if( is_admin() && ! is_customize_preview() ){
			return false;
		}

		if( isset($_REQUEST['elementor-preview']) || isset($_REQUEST['elementor_library']) ){
			if( \ReyCore\Customizer\Styles::has_cached_css() ){
				return false;
			}
		}

		if( wp_doing_ajax() ){
			// minor improvement for ajax calls getting kirki fonts
			if( isset($_REQUEST['action']) &&
			in_array( $_REQUEST['action'], ['kirki_fonts_google_all_get', 'kirki_fonts_standard_all_get'], true ) ){
				return false;
			}
			if( isset($_REQUEST['wc-ajax']) && $_REQUEST['wc-ajax'] === 'get_refreshed_fragments' ){
				return false;
			}
		}

		if( \ReyCore\Helper::is_rest_api_request() ){
			return false;
		}

		if( function_exists('wp_doing_cron') && wp_doing_cron() ){
			return false;
		}

		if( \ReyCore\Customizer\Styles::has_cached_css() ){
			return false;
		}

		if( ! apply_filters('reycore/customizer_load_options', true) ){
			return false;
		}

		return true;
	}

	/**
	 * Modify existing Customizer options
	 *
	 * @param object $wp_customize
	 * @return void
	 */
	public function customize_register($wp_customize) {

		include_once __DIR__ . '/fields/rey-panel.php';

		$wp_customize->register_panel_type( '\ReyCore_Panels_Basic' );

		add_filter( 'kirki_panel_types', function ($types){
			$types['kirki-rey-panel'] = '\ReyCore_Panels_Basic';
			return $types;
		});

		include_once __DIR__ . '/fields/rey-section.php';

		add_filter( 'kirki_section_types', function ($types){
			$types['kirki-rey-section'] = '\ReyCore_Sections_Basic';
			return $types;
		});

		// Register Group Types
		add_filter( 'kirki_control_types', function( $controls ) {
			$controls['rey_group_start'] = __NAMESPACE__ . '\\Fields\\GroupStart';
			$controls['rey_group_end'] = __NAMESPACE__ . '\\Fields\\GroupEnd';
			$controls['rey_accordion_start'] = __NAMESPACE__ . '\\Fields\\AccordionStart';
			$controls['rey_accordion_end'] = __NAMESPACE__ . '\\Fields\\AccordionEnd';
			return $controls;
		} );

		foreach ($this->panels as $panel) {
			$panel->customize_register();
		}

		/**
		 * Modify existing settings & controls
		 */

		$wp_customize->remove_panel('woocommerce');

		$wp_customize->get_setting('blogname')->transport = 'postMessage';
		$wp_customize->get_setting('blogdescription')->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {

			$wp_customize->selective_refresh->add_partial(
				'blogname',
				[
					'selector'        => '.rey-logoTitle a',
					'render_callback' => function() {
						bloginfo( 'name' );
					},
				]
			);

			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				[
					'selector'        => '.rey-logoDescription',
					'render_callback' => function() {
						bloginfo( 'description' );
					},
				]
			);
		}

		$wp_customize->remove_section('title_tagline');

		// Reassign default sections to panels.
		$wp_customize->get_section( 'static_front_page' )->panel    = Options\General::get_id();
		$wp_customize->get_section( 'static_front_page' )->priority = 50;

		// move logo option from Site identity, to Header Logo section
		$wp_customize->get_control( 'custom_logo' )->section = Options\Header\Logo::get_id();

	}

	/**
	 * Copy settings from child to parent & viceversa
	 *
	 * @since 2.0.5
	 **/
	public function transfer_theme_settings() {

		if ( ! check_ajax_referer( 'reycore-ajax-verification', 'security', false ) ) {
			wp_send_json_error( esc_html__('Invalid security nonce!', 'rey-core') );
		}

		if( ! isset($_POST['type']) ){
			wp_send_json_error( 'No type set!' );
		}

		if( ! ($type = reycore__clean($_POST['type'])) ){
			wp_send_json_error( 'No type set!' );
		}

		$success = false;

		if( $type === 'parent' ){
			$theme_name = REY_CORE_THEME_NAME;
		}
		else if( $type === 'child' ){
			$theme_name = 'rey-child';
		}

		if( ($theme = wp_get_theme($theme_name)) && isset($theme['errors']) && !empty($theme['errors']) ){
			wp_send_json_error( 'Couldn\'t find theme' );
		}

		$data = \ReyCore\Customizer\Helper::get_theme_settings( $theme_name );

		global $wp_customize;

		// Import custom options.
		if ( class_exists('\CEI_Option') && isset( $data['options'] ) ) {

			foreach ( $data['options'] as $option_key => $option_value ) {

				$option = new \CEI_Option( $wp_customize, $option_key, array(
					'default'		=> '',
					'type'			=> 'option',
					'capability'	=> 'edit_theme_options'
				) );

				$option->import( $option_value );
			}
		}

		// If wp_css is set then import it.
		if( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		if( isset($data['mods']) && ! empty($data['mods']) ){

			if( $wp_customize ){
				// Call the customize_save action.
				do_action( 'customize_save', $wp_customize );
			}

			// Loop through the mods.
			foreach ( $data['mods'] as $key => $val ) {

				if( $wp_customize ){
					// Call the customize_save_ dynamic action.
					do_action( 'customize_save_' . $key, $wp_customize );
				}

				// Save the mod.
				set_theme_mod( $key, $val );
			}

			if( $wp_customize ){
				// Call the customize_save_after action.
				do_action( 'customize_save_after', $wp_customize );
			}
		}

		wp_send_json_success();

	}


	/**
	 * Print CSS styles in Customizer Preview
	 * tp adjust preview sizes
	 *
	 * @since 1.0.0
	 */
	public function print_customizer_css()
	{ ?>
		<style type="text/css">
			:root {
				--customizer-side-width: 345px;
			}
			@media screen and (min-width: 1667px){
				.wp-full-overlay.expanded {
					margin-left: var(--customizer-side-width);
				}
				.rtl .wp-full-overlay.expanded {
					margin-right: var(--customizer-side-width);
					margin-left: 0;
				}
			}
			.wp-full-overlay-sidebar {
				width: var(--customizer-side-width);
			}
			.preview-mobile .wp-full-overlay-main {
				margin: auto 0 auto -180px;
				width: 360px;
				height: 640px;
			}
			.rtl .preview-mobile .wp-full-overlay-main {
				margin-right: -180px;
				margin-left: auto;
			}
			.preview-tablet .wp-full-overlay-main {
				margin: auto 0 auto -384px;
				width: 768px;
			}
			.rtl .preview-tablet .wp-full-overlay-main {
				margin-right: -384px;
				margin-left: auto;
			}
		</style>
		<?php
	}

	/**
	 * Push customizer option into ACF
	 *
	 * @since 1.0.0
	 */
	public function customizer_to_acf( $wp_customizer )
	{
		// Sync shop page cover with post's option
		if( class_exists('\ACF') && class_exists('\WooCommerce') && wc_get_page_id('shop') !== -1 ){
			update_field('page_cover', get_theme_mod('cover__shop_page'), wc_get_page_id('shop') );
		}
		// Sync blog page cover with post's option
		if( class_exists('\ACF') && get_option( 'page_for_posts' ) ){
			update_field('page_cover', get_theme_mod('cover__blog_home'), get_option( 'page_for_posts' ) );
		}
	}

	public function run_update_action( $response, $manager ) {

		/**
		 * Passes the modified settings
		 */
		do_action('reycore/customizer/after_save', $response['setting_validities'], $manager);

		return $response;
	}

	public static function legacy_sections(){
		return [
			'site_preloader_options' => 'general-preloader',
		];
	}

	public static function legacy_panels(){
		return [
			'header_options' => 'header',
		];
	}

}
