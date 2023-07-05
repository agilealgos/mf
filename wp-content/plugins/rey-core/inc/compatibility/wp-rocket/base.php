<?php
namespace ReyCore\Compatibility\WpRocket;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{

	public function __construct()
	{
		// add_filter( 'rocket_cdn_reject_files', [$this, 'exclude_files_cdn'], 20);
		add_filter( 'rocket_excluded_inline_js_content', [$this, 'exclude_inline_js'], 10);
		// add_filter('rocket_exclude_defer_js', [$this, 'exclude_defer_js'], 10);
		// add_filter('rocket_defer_inline_exclusions', [$this, 'exclude_inline_defer_js'], 10);
		add_filter( 'rocket_cpcss_excluded_post_types', [$this, 'exclude_cpt_cpcss'], 10);
		add_action( 'rocket_critical_css_generation_process_complete', [$this, 'add_extra_cpcss']);
		add_action( 'reycore/customizer/control=perf__preload_assets', [ $this, 'add_customizer_options' ], 10, 2 );
		add_action( 'rey/flush_cache_after_updates', [ $this, 'empty_theme_mod' ] );
		add_filter( 'reycore/elementor/revslider/waitforinit', [ $this, 'revslider_defer' ] );
		add_filter( 'reycore/is_mobile', [ $this, 'is_mobile' ] );
		add_filter( 'reycore/supports_mobile_caching', [ $this, 'cache_separately' ] );
		add_filter( 'reycore/mobile_improvements', [ $this, 'supports_mobile_improvements' ] );
		add_filter( 'theme_mod_site_preloader', [ $this, 'disable_site_preloader' ] );
		add_action( 'reycore/customizer/control=site_preloader', [ $this, 'add_preloader_option_notice' ], 10, 2 );
		add_filter( 'rocket_delay_js_exclusions', [$this, 'exclude_delay_js'], 10);
		add_filter( 'reycore/delay_js', [$this, 'rey_delay_js']);
		add_filter( 'rey/main_script_params', [ $this, 'set_delay_event' ] );
		add_filter( 'theme_mod_loop_animate_in', [ $this, 'disable_loop_animation_delay_js' ] );
		add_filter( 'rocket_usedcss_content', [ $this, 'get_fonts_css' ] );
		add_filter( 'reycore/critical_css/disable', [ $this, 'disable_rey_critical' ] );
		add_filter( 'woocommerce_single_product_image_gallery_classes', [$this, 'disable_pdp_gallery_loading'], 100);
		add_filter( 'rocket_rucss_safelist', [$this, 'unused_css']);
		add_filter( 'rocket_rucss_inline_atts_exclusions', [$this, 'unused_css_inline_exclusions']);
		add_filter( 'rocket_cache_wc_empty_cart', '__return_false' );

	}

	public function exclude_files_cdn($files) {

		// (.*).svg

		return $files;
	}

	function exclude_defer_js( $pattern ) {

		return $pattern;
	}

	function exclude_inline_defer_js( $pattern ) {

		if( is_array($pattern) ){
			// $pattern[] = 'revapi';
		}
		else {
			// $pattern .= '|revapi';
		}

		return $pattern;
	}

	function exclude_inline_js( $pattern ) {

		$pattern[] = 'rey-no-js';

		return $pattern;
	}

	function exclude_cpt_cpcss( $cpt ) {

		if( class_exists('\ReyCore\Elementor\GlobalSections') ){
			$cpt[] = \ReyCore\Elementor\GlobalSections::POST_TYPE;
		}

		return $cpt;
	}

	function revslider_defer( $status ) {

		// defer is disabled, can return predefined
		if ( ! get_rocket_option( 'defer_all_js' ) ) {
			return $status;
		}

		// defer per post is disabled, can return predefined
		if ( is_rocket_post_excluded_option( 'defer_all_js' ) ) {
			return $status;
		}

		return false;
	}

	function disable_rey_critical(){
		return get_rocket_option( 'async_css' );
	}

	function add_extra_cpcss(){

		$css = ( $cc = \ReyCore\Plugin::instance()->critical_css ) ? $cc->css() : [];

		$css[] = get_theme_mod('perf__wprocket_extra_critical', '');

		if( !($filesystem = reycore__wp_filesystem()) ){
			return;
		}

		$dir_path = WP_CONTENT_DIR . '/cache/critical-css/' . (is_multisite() ? get_current_blog_id() : 1);

		if ( ! $filesystem->is_dir( $dir_path ) ) {
			return;
		}

		if( ! ($list = $filesystem->dirlist( $dir_path )) ){
			return;
		}

		$css_files = array_filter($list, function($file){
			return ($info = pathinfo($file['name'])) && $info['extension'] === 'css';
		});

		if( empty($css_files) ){
			return;
		}

		$success = [];

		foreach ($css_files as $key => $css_file) {

			$file_path = trailingslashit($dir_path) . $key;

			$data = $filesystem->get_contents( $file_path );
			$data .= implode('', $css);

			if( $filesystem->put_contents( $file_path, $data ) ){
				$success[] = true;
			}

		}

		if( in_array(true, $success, true) ){
			rocket_clean_domain();
		}
	}

	function empty_theme_mod(){
		set_theme_mod('perf__wprocket_extra_critical', '');
	}

	function is_mobile( $status ){

		if ( ! class_exists( '\WP_Rocket_Mobile_Detect' ) ) {
			return $status;
		}

		$detect = new \WP_Rocket_Mobile_Detect();

		if ( $detect->isMobile() ) {
			return true;
		}

		return $status;
	}

	function cache_separately( $status ){

		if( get_rocket_option( 'cache_mobile' ) && get_rocket_option( 'do_caching_mobile_files' ) ){
			return true;
		}

		return $status;
	}

	function supports_mobile_improvements( $status ){

		if( get_theme_mod('perf__wprocket_mobile_improvements', false) ){
			return true;
		}

		return $status;
	}

	function add_customizer_options( $control_args, $section ){

		$section->add_control( [
			'type'        => 'textarea',
			'settings'    => 'perf__wprocket_extra_critical',
			'label'       => esc_html_x( 'Extra Critical CSS styles', 'Customizer control title', 'rey-core' ),
			'description' => esc_html_x( 'Append extra styles to WPRocket\'s CPCSS in case something is not rendering properly. Works only if "Optimize CSS delivery" is enabled.', 'Customizer control description', 'rey-core' ),
			'default'     => '',
			'css_class' => '--block-label',
			'input_attrs'     => [
				'placeholder' => esc_html_x('eg: .selector {}', 'Customizer control description', 'rey-core'),
			],
		] );

		$section->add_control( [
			'type'        => 'toggle',
			'settings'    => 'perf__wprocket_mobile_improvements',
			'label'       => esc_html__( 'Add Mobile Improvements', 'rey-core' ),
			'description' => sprintf(_x( 'Requires WPRocket Mobile Cache & Cache Separately to be enabled. Please <a href="%s" target="_blank">read more here</a> on how to use this option.', 'Customizer control description', 'rey-core' ), reycore__support_url('kb/mobile-improvements/') ),
			'default'     => false,
		] );

	}

	function is_javascript_delayed(){

		if( defined('WP_ROCKET_VERSION') ){
			if ( version_compare( WP_ROCKET_VERSION, '3.9', '<' ) ) {
				return;
			}
		}

		if( defined('DONOTCACHEPAGE') && DONOTCACHEPAGE ){
			return;
		}

		if ( function_exists('rocket_bypass') && rocket_bypass() ) {
			return false;
		}

		if( defined('DONOTROCKETOPTIMIZE') && DONOTROCKETOPTIMIZE ){
			return;
		}

		if ( function_exists('is_rocket_post_excluded_option') && is_rocket_post_excluded_option( 'delay_js' ) ) {
			return false;
		}

		return get_rocket_option( 'delay_js' );
	}

	function rey_delay_js($status){

		if ( $this->is_javascript_delayed() ) {
			return true;
		}

		return $status;
	}

	function set_delay_event($params){

		if ( $this->is_javascript_delayed() ) {
			$params['delay_js_event'] = 'rocket-allScriptsLoaded';
		}

		return $params;
	}

	function disable_loop_animation_delay_js($mod){

		if ( $this->is_javascript_delayed() ) {
			return false;
		}

		return $mod;
	}

	function disable_pdp_gallery_loading($classes){
		if ( $this->is_javascript_delayed() ) {
			unset($classes['loading']);
		}
		return $classes;
	}

	function exclude_delay_js( $pattern ) {

		/**
		 * Excluding scripts beats the purpose of this option.
		 * When an exclusion is added, its dependencies must also be added and it's jQuery, Rey Core, Elementor frontend, etc.
		 */

		$pattern[] = 'rey-helpers';
		$pattern[] = 'reycore-critical-css-js';
		$pattern[] = 'rey-no-js';

		return $pattern;
	}

	function disable_site_preloader($mod){

		if ( $this->is_javascript_delayed() ) {
			return false;
		}

		return $mod;
	}

	function add_preloader_option_notice($control_args, $section){

		if ( $this->is_javascript_delayed() ) {

			$section->add_control_before($control_args, $section->prepare_notice([
				'default'     => esc_html_x('Heads up! The preloader is disabled because WPRocket\'s Delay JS option is enabled.', ' Customizer control label', 'rey-core')
			]) );

		}

	}

	function unused_css_inline_exclusions( $list ){
		return array_merge($list, [
			'wp-custom-css',
			'reycore-critical-css',
		]);
	}


	/**
	 * Selectors to exclude from removing
	 *
	 * @param array $safelist
	 * @return array
	 *
	 * https://docs.wp-rocket.me/article/1529-remove-unused-css#css-safelist
	 */
	public function unused_css( $safelist ){

		$rey_safelist = [
			'.screen-reader-text',
			'(.*).--at-top(.*)',
			'(.*).--active-tab(.*)',
			'(.*).--tabs-loaded(.*)',
			'(.*).--animated-in(.*)',
			'.header-overlay--is-opened(.*)',
			'.site-overlay--is-opened(.*)',
			'.--overlay-darken(.*)',
			'.search-inline--active(.*)',
			'(.*).search-panel--active(.*)',
			'(.*).search-panel--wide(.*)',
			'(.*).header-account--active(.*)',
			'(.*).--drop-panel-active(.*)',
			'(.*).--cart-active(.*)',
			'(.*).--side-panel-active(.*)',
			'(.*).--side-panel-active--right(.*)',
			'(.*).--side-panel-active--left(.*)',
			'(.*).--hover(.*)',
			'(.*)[aria-expanded="true"](.*)',
			'(.*).--submenu-indicator(.*)',
			'(.*).rey-searchAjax(.*)',
			'(.*).rey-searchResults(.*)',
			'(.*).rey-searchItem(.*)',
			'(.*).--loading(.*)',
			'.woocommerce-js(.*)',
			'(.*).rey-crossSells(.*)',
			'.rey-cartPanel(.*)',
			'(.*).--in-wishlist(.*)',
			'.rey-wishlistItem(.*)',
			'.rey-quickviewPanel',
			'.qty',
			'(.*).--filter-panel-active(.*)',
			'.rey-wishlist-notice-wrapper',
			'.rey-compare-notice-wrapper',
			'(.*).--visible(.*)',
			'.btn-primary',
			'.btn-secondary',
			'.alt',
			'(.*).--no-scroll',
			'.--mobileNav--active(.*)',
			'.rey-acPopup(.*)',
			'.rey-cartRecent(.*)',
			'.noUi(.*)',
			'ol.commentlist',
			'.star-rating',
			'.meta',
			'.avatar',
			'.comment-text',
			'.rey-productLoop-variationsForm',
			'div.product',
			'.rey-productSummary',
			'(.*).--offcanvas(.*)',
			'(.*).--is-active(.*)',
			'(.*).--active(.*)',
			'(.*).is-active(.*)',
			'(.*).--cSslide-active(.*)',
			'(.*).--cNest-active(.*)',
			'(.*).--initialised(.*)',
			'(.*).--init(.*)',
			'(.*).--animate-out(.*)',
			'(.*).--is-filtering(.*)',
			'(.*).--empty(.*)',
			'.--cover-split-header(.*)',
			'(.*)ul.products(.*)',
			'(.*).rey-modal(.*)',
			'(.*).reymodal(.*)',
			'(.*)ul.products(.*)', // because of lazy loading
		];

		$list = array_merge($rey_safelist, $safelist);

		return $list;
	}

	public function get_fonts_css( $css ) {

		if( ! ( $webfonts = \ReyCore\Plugin::instance()->fonts ) ){
			return $css;
		}

		return implode('', $webfonts->enqueue_css()) . $css;
	}

}
