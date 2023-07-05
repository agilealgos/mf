<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Assets {

	protected static $css_to_exclude_from_loading = [];

	public function __construct(){

		add_action( 'init', [$this, 'init']);

	}

	public function init(){
		add_action( 'reycore/assets/register_scripts', [$this, 'register_scripts']);
		add_action( 'wp_enqueue_scripts', [$this, 'fallback_enqueue_scripts']);
		add_action( 'wp_enqueue_scripts', [$this, 'admin_bar_scripts']);
		add_action( 'admin_enqueue_scripts', [$this, 'admin_bar_scripts']);
		add_action( 'wp', [$this, 'collect_excludes_from_loading']);
		add_filter( 'rey/main_script_params', [$this, 'core_script_params'], 5);
		add_filter( 'rey/assets/helper_path', [$this, 'set_helpers_path'], 10, 2);
		add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_styles']);
		add_filter( 'reycore/buffer/css/excluded', [$this, 'exclude_styles_from_buffer']);
		add_filter('theme_mod_perf__enable_flying_scripts', [$this, 'disable_flying_pages'], 10);
	}

	public static function styles(){

		$rtl = reyCoreAssets()::rtl();

		$styles = [
			'reycore-general' => [
				'src'      => REY_CORE_URI . 'assets/css/general-components/general/general' . $rtl . '.css',
				'priority' => 'high',
				'enqueue'  => true,
				'desc' => 'Core General'
			],
			'reycore-header-search-top' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/header-search-top/header-search-top' . $rtl . '.css',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
				'priority' => 'high'
			],
			'reycore-header-search' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/header-search/header-search' . $rtl . '.css',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
				'priority' => 'low'
			],
			'reycore-main-menu' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/main-menu/main-menu' . $rtl . '.css',
				'priority' => 'high'
			],
			'reycore-ajax-load-more' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/ajax-load-more/ajax-load-more' . $rtl . '.css',
				'priority' => 'low'
			],

			'reycore-language-switcher' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/language-switcher/language-switcher' . $rtl . '.css',
				'priority' => 'high',
			],
			'reycore-menu-icons' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/menu-icons/menu-icons' . $rtl . '.css',
				'priority' => 'high',
			],
			'reycore-modals' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/modals/modals' . $rtl . '.css',
				'priority' => 'low',
			],
			'reycore-post-social-share' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/post-social-share/post-social-share' . $rtl . '.css',
			],
			'reycore-side-panel' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/side-panel/side-panel' . $rtl . '.css',
				'priority' => 'high',
			],
			'reycore-sticky-social' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/sticky-social/sticky-social' . $rtl . '.css',
				'priority' => 'high',
			],
			'reycore-utilities' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/utilities/utilities' . $rtl . '.css',
				'priority' => 'low',
			],
			'reycore-pass-visibility' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/pass-visibility/pass-visibility' . $rtl . '.css',
				'priority' => 'low',
			],
			'reycore-videos' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/videos/videos' . $rtl . '.css',
				'priority' => 'high',
			],
			'rey-simple-scrollbar' => [
				'src'     => REY_CORE_URI . 'assets/css/lib/simple-scrollbar.css',
				'deps'      => [],
				'priority' => 'low',
			],
			'rey-splide' => [
				'src'     => REY_CORE_URI . 'assets/css/lib/splide.css',
				'priority' => 'high',
				'deps'      => [],
			],
			'reycore-slider-components' => [
				'src'     => REY_CORE_URI . 'assets/css/lib/slider-components.css',
				'deps'      => [],
				'priority' => 'low',
			],
		];

		foreach ($styles as $key => $style) {

			if( ! isset($style['deps']) ){
				$styles[$key]['deps'] = function_exists('reyAssets') ? reyAssets()::STYLE_HANDLE : [];
			}

			if( ! isset($style['version']) ){
				$styles[$key]['version'] = REY_CORE_VERSION;
			}
		}

		return $styles;
	}

	public static function scripts(){

		return [
			'flying-pages' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/flying-pages.js',
				'deps'    => [],
				'version' => '2.1.2-r',
				'enqueue' => get_theme_mod('perf__enable_flying_scripts', false),
				'defer' => true,
				'localize' => [
					'name' => 'FPConfig',
					'params' => [
						'delay' => 0,
						'ignoreKeywords' => ['wp-admin', 'logout', 'wp-login.php', 'add-to-cart=', 'customer-logout', 'remove_item=', 'apply_coupon=', 'remove_coupon=', 'undo_item=', 'update_cart=', 'proceed=', 'removed_item=', 'added-to-cart=', 'order_again='],
						'maxRPS' => 3,
						'hoverDelay' => 50,
					],
				],
				'plugin' => true
			],
			'animejs' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/anime.min.js',
				'deps'    => ['jquery'],
				'version' => '3.1.0',
				'plugin' => true
			],
			'rey-simple-scrollbar' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/simple-scrollbar.js',
				'deps'    => ['jquery'],
				'version' => '0.4.0',
				'plugin' => true
			],
			'wnumb' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/wnumb.js',
				'deps'    => [],
				'version' => '1.2.0',
				'plugin' => true
			],
			'slick' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/slick.js',
				'deps'    => ['jquery'],
				'version' => '1.8.1',
				'plugin' => true
			],
			'splidejs' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/splide.js',
				'deps'    => [],
				'version' => '4.1.2',
				'plugin' => true,
			],
			'rey-splide' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-slider.js',
				'deps'    => ['splidejs'],
				'version' => REY_CORE_VERSION,
			],
			'scroll-out' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/scroll-out.js',
				'deps'    => ['jquery'],
				'version' => '2.2.3',
				'callback' => 'rey__is_blog_list',
				'plugin' => true
			],
			'reycore-scripts' => [
				'src'      => REY_CORE_URI . 'assets/js/general/c-general.js',
				'deps'     => ['jquery', 'rey-script'],
				'version'  => REY_CORE_VERSION,
				'enqueue' => true
			],
			'rey-videos' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-videos.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
			'rey-horizontal-drag' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-horizontal-drag.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
			'reycore-header-search' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-header-search.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
			'reycore-load-more' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-load-more.js',
				'deps'    => ['reycore-scripts', 'scroll-out'],
				'version' => REY_CORE_VERSION,
			],
			'reycore-modals' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-modal.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
			'reycore-sticky-global-sections' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-sticky-global-sections.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
			'reycore-sticky' => [
				'src'     => REY_CORE_URI . 'assets/js/general/c-sticky.js',
				'deps'    => ['reycore-scripts'],
				'version' => REY_CORE_VERSION,
			],
		];
	}

	/**
	 * Override Helpers.js path if Core is newer then Theme.
	 *
	 * @param string $path
	 * @param string $version
	 * @return string
	 * @since 2.4.0
	 */
	public function set_helpers_path($path, $version){

		if( version_compare(REY_CORE_VERSION, $version, '>') ){
			return REY_CORE_URI . 'assets/js/general/c-helpers.js';
		}

		return $path;
	}

	public function core_script_params($params){

		$params['delay_js_event'] = false;

		$params['lazy_attribute'] = AssetsManager::LAZY_ATTRIBUTE;

		$params['core'] = apply_filters('reycore/script_params', [
			'js_params'     => [
				'sticky_debounce' => 200,
				'dir_aware' => false,
				'panel_close_text' => esc_html__('Close Panel', 'rey-core')
			],
			'v' => substr( md5( REY_CORE_VERSION ), 0, 12),
		]);

		return $params;
	}

	function register_scripts( $assets_manager )
	{

		$assets_manager->register_asset('styles', self::styles());
		$assets_manager->register_asset('scripts', self::scripts());

		if( is_user_logged_in() ){
			wp_register_style(
				'reycore-frontend-admin',
				REY_CORE_URI . 'assets/css/general-components/frontend-admin/frontend-admin' . $assets_manager::rtl() . '.css',
				[],
				REY_CORE_VERSION
			);
		}
	}

	public function exclude_styles_from_buffer($styles){
		$styles[] = 'rey-wp-style'; // it's admin only
		$styles[] = 'reycore-frontend-admin'; // it's admin only
		return $styles;
	}

	function fallback_enqueue_scripts()
	{

		if( function_exists('reyAssets') ){
			return;
		}

		$excludes = self::get_excludes();

		foreach (['styles', 'scripts'] as $type) {

			$func = 'all_' . $type;

			if( !( is_callable([$this, $func]) && $assets = call_user_func([$this, $func]) ) ){
				continue;
			}

			foreach( $assets as $handle => $asset ){

				$enqueue = false;

				// always enqueue
				if( isset($asset['enqueue']) && $asset['enqueue'] ){
					$enqueue = ! in_array($handle, $excludes, true);
				}

				else {
					// check callback
					if( isset($asset['callback']) ){
						if( is_callable($asset['callback']) && call_user_func($asset['callback']) ){
							$enqueue = true;
						}
					}
				}

				if( $enqueue ){
					call_user_func( [ reyCoreAssets(), 'add_' . $type ], $handle );
				}
			}
		}
	}

	public function admin_bar_scripts(){
		if( is_admin_bar_showing() ){
			wp_enqueue_script(
				'reyadminbar',
				REY_CORE_URI . 'assets/js/general/c-adminbar.js',
				['jquery'],
				REY_CORE_VERSION
			);
		}
	}

	public static function get_excludes_choices( $has_empty = true ){

		$styles = apply_filters('reycore/assets/excludes_choices', wp_list_filter( self::styles(), [
			'enqueue' => true
		] ) );

		$list = $has_empty ? [ '' => '- Select -' ] : [];

		foreach ($styles as $key => $style) {

			// grab only the ones that load automatically
			// but exclude several mandatory
			if( isset($style['enqueue']) ){
				$list[$key] = isset($style['desc']) ? $style['desc'] : $key;
			}

		}

		return $list;
	}

	public function collect_excludes_from_loading(){

		self::$css_to_exclude_from_loading = reycore__get_option( 'perf__css_exclude', ['rey-presets'] );

		if( self::maybe_dequeue_wp_gutenberg_blocks() ){
			self::$css_to_exclude_from_loading[] = 'rey-gutenberg';
		}

	}

	public static function get_excludes(){
		return self::$css_to_exclude_from_loading;
	}

	public function dequeue_styles(){

		if( self::maybe_dequeue_wp_gutenberg_blocks() ){

			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );

		}

		// Remove WooCommerce block CSS
		if( (bool) reycore__get_option( 'perf__disable_wcblock', false ) ){
			wp_dequeue_style( 'wc-blocks-vendors-style' );
			wp_dequeue_style( 'wc-blocks-style' );
		}
	}

	public static function maybe_dequeue_wp_gutenberg_blocks(){

		$maybe_dequeue = false;

		if( (bool) reycore__get_option( 'perf__disable_wpblock', false ) ){

			$maybe_dequeue = true;

			if( get_theme_mod('perf__disable_wpblock__posts', true) && is_single() && 'post' == get_post_type() ){
				$maybe_dequeue = false;
			}
		}

		return $maybe_dequeue;
	}

	public static function caching_plugins(){

		$plugins = [];

		foreach (\ReyCore\Helper::caching_plugins() as $key => $plugin) {
			$plugins[$key] = $plugin['enabled'];
		}

		return $plugins;
	}

	public function disable_flying_pages( $status ){

		if( class_exists('\WooCommerce') && (is_cart() || is_checkout()) ){
			return false;
		}

		if( reycore__elementor_edit_mode() ){
			return false;
		}

		if( ($caching_plugins = self::caching_plugins()) && ($caching_plugins['wprocket'] || $caching_plugins['litespeed']) ){
			return false;
		}

		return $status;
	}

}
