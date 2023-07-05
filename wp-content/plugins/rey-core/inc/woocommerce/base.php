<?php
namespace ReyCore\WooCommerce;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use ReyCore\Plugin;
use ReyCore\Helper;

class Base {

	const REY_ENDPOINT = 'rey/v1';

	public $supported;

	protected static $public_taxonomies;

	public function __construct(){

		if ( ! class_exists('\WooCommerce') ) {
			return;
		}

		$this->includes();
		$this->add_support();

		add_action( 'init', [ $this, 'init']);

		Plugin::instance()->woocommerce_loop = new Loop();
		Plugin::instance()->woocommerce_pdp = new Pdp();

		foreach ([
			'Tags/Templates',
			'Tags/Sidebar',
			'Tags/LoginRegister',
			'Tags/Reviews',
			'Tags/VariationsLoop',
			'Tags/Search',
			'Tags/Wishlist',
			'Tags/Quantity',
			'Tags/Cart',
			'Tags/MiniCart',
			'Tags/Checkout',
			'Tags/Tabs',
			'Tags/Related',
		] as $tag) {
			$class_name = Helper::fix_class_name($tag, 'WooCommerce');
			$tag_name = str_replace('tags/', '', strtolower($tag));
			Plugin::instance()->woocommerce_tags[ $tag_name ] = new $class_name();
		}

		$this->supported = true;

		do_action('reycore/woocommerce');
	}

	function includes(){

		// convert to class
		require_once __DIR__ . '/functions.php';
		require_once __DIR__ . '/tags.php';

	}

	/**
	 * General actions
	 * @since 1.0.0
	 **/
	public function init()
	{

		self::handle_catalog_mode();

		// Remove default wrappers.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		add_filter( 'woocommerce_enqueue_styles', [ $this, 'enqueue_styles'], 10000 ); // suprases Cartflows override
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts'] );
		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_filter( 'rey/main_script_params', [ $this, 'script_params'], 10 );
		add_action( 'admin_bar_menu', [$this, 'shop_page_toolbar_edit_link'], 100);
		add_filter( 'body_class', [ $this, 'body_classes'], 20 );
		add_filter( 'rey/css_styles', [ $this, 'css_styles'] );

		if( function_exists('rey_action__before_site_container') && function_exists('rey_action__after_site_container') ){
			// add rey wrappers
			add_action( 'woocommerce_before_main_content', 'rey_action__before_site_container', 0 );
			add_action( 'woocommerce_after_main_content', 'rey_action__after_site_container', 10 );
		}

		if( apply_filters('reycore/woocommerce/prevent_atc_when_not_purchasable', false) ){
			add_action( 'woocommerce_single_variation', function(){
				if( ($product = wc_get_product()) && ! $product->is_purchasable() ){
					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
				}
			}, 0 );
		}

		// disable post thumbnail (featured-image) in woocommerce posts
		add_filter( 'rey__can_show_post_thumbnail', function(){
			return ! is_woocommerce();
		} );

		// force flexslider to be disabled
		add_filter( 'woocommerce_single_product_flexslider_enabled', '__return_false', 100 );

		do_action('reycore/woocommerce/init', $this);

	}

	public function add_support(){

		// Register theme features.
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );

		add_theme_support( 'woocommerce', [
			'product_grid::max_columns' => 6,
			'product_grid' => [
				'max_columns'=> 6
			],
		 ] );
	}

	public static function handle_catalog_mode(){

		// disable shop functionality
		if( ! reycore_wc__is_catalog() ){
			return;
		}

		add_filter( 'woocommerce_is_purchasable', '__return_false');


		// Variable products - hide ATC FORM
		if( get_theme_mod('shop_catalog__variable', 'hide') === 'hide' ){
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
		}
		// Variable products - hide ATC BUTTON
		else if( get_theme_mod('shop_catalog__variable', 'hide') === 'hide_just_atc' ){
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		}
	}

	/**
	 * Enqueue CSS for this theme.
	 *
	 * @param  array $styles Array of registered styles.
	 * @return array
	 */
	public function enqueue_styles( $styles )
	{
		// Override WooCommerce general styles
		$styles['woocommerce-general'] = [
			'src'     => REY_CORE_URI . 'assets/css/woocommerce.css',
			'deps'    => '',
			'version' => REY_CORE_VERSION,
			'media'   => 'all',
			'has_rtl' => true,
		];

		// disable smallscreen stylesheet
		if( isset($styles['woocommerce-smallscreen']) ){
			unset( $styles['woocommerce-smallscreen'] );
		}

		// disable layout stylesheet
		if( isset($styles['woocommerce-layout']) ){
			unset( $styles['woocommerce-layout'] );
		}

		return $styles;
	}


	function woocommerce_styles(){

		$rtl = reyCoreAssets()::rtl();
		$is_catalog = apply_filters('reycore/woocommerce/css_is_catalog', is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() );

		return [
			'rey-wc-general' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/general/general' . $rtl . '.css',
				'deps'    => ['reycore-general', 'woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_woocommerce',
			],
			'rey-wc-forms' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/tag-forms/woo-forms' . $rtl . '.css',
				'deps'    => ['reycore-general', 'woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'callback' => function(){
					return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
				},
			],
			'rey-wc-loop' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/general/style' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-general'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog;
				},
			],
				'rey-wc-loop-header' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/header/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-general'],
					'version'   => REY_CORE_VERSION,
					'callback' => function() use ($is_catalog){
						return $is_catalog;
					},
				],
				'rey-wc-loop-grid-skin-metro' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/grid-skin-metro/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-loop-grid-skin-masonry' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/grid-skin-masonry/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-loop-grid-skin-masonry2' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/grid-skin-masonry2/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-loop-grid-skin-scattered' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/grid-skin-scattered/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-loop-grid-mobile-list-view' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/grid-mobile-list-view/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'reycore-loop-product-skin-basic' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/item-skin-basic/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
				'reycore-loop-product-skin-wrapped' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-loop/item-skin-wrapped/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-loop'],
					'version'   => REY_CORE_VERSION,
				],
			'rey-wc-product' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-product/general/style' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-general'],
				'version'   => REY_CORE_VERSION,
			],
				'rey-wc-product-gallery' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-product/gallery/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-product'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-product-mobile-gallery' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-product/mobile-gallery/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-product'],
					'version'   => REY_CORE_VERSION,
				],
				'rey-wc-product-reviews' => [
					'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-product/reviews/style' . $rtl . '.css',
					'deps'    => ['woocommerce-general', 'rey-wc-product'],
					'version'   => REY_CORE_VERSION,
				],
			'rey-wc-cart' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-cart/style' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-general'],
				'version'   => REY_CORE_VERSION,
				'callback' => function(){
					return is_cart() || is_checkout();
				},
			],
			'rey-wc-checkout' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-checkout/style' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-general', 'rey-wc-cart'],
				'version'   => REY_CORE_VERSION,
				'callback' => function(){
					return is_cart() || is_checkout();
				},
			],
			'rey-wc-myaccount' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/page-myaccount/style' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-general', 'rey-wc-cart'],
				'version'   => REY_CORE_VERSION,
				'callback' => function(){
					return is_page( wc_get_page_id( 'myaccount' ) );
				},
			],
			'rey-wc-elementor' => [
				'src'     => REY_CORE_URI . 'assets/css/elementor-components/woocommerce/woocommerce' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
			],
			'rey-wc-tag-widgets' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/tag-widgets/tag-widgets' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-loop'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog && (reycore_wc__check_filter_panel() || reycore_wc__check_filter_sidebar_top() || reycore_wc__check_shop_sidebar());
				},
			],
			'rey-wc-tag-attributes' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/tag-widgets/attributes' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog && (reycore_wc__check_filter_panel() || reycore_wc__check_filter_sidebar_top() || reycore_wc__check_shop_sidebar());
				},
			],
			'rey-wc-tag-stretch' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/tag-stretch/tag-stretch' . $rtl . '.css',
				'deps'    => ['woocommerce-general', 'rey-wc-loop'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog;
				},
			],
			'rey-wc-header-account-panel-top' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/header-account-panel-top/header-account-panel-top' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'high'
			],
			'rey-wc-header-account-panel' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/header-account-panel/header-account-panel' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'low'
			],
			'rey-wc-header-mini-cart-top' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/header-mini-cart-top/header-mini-cart-top' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'high'
			],
			'rey-wc-header-mini-cart' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/header-mini-cart/header-mini-cart' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'low'
			],
			'rey-wc-header-wishlist' => [
				'src'     => REY_CORE_URI . 'assets/css/woocommerce-components/header-wishlist/header-wishlist' . $rtl . '.css',
				'deps'    => ['woocommerce-general'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'low'
			],
		];

	}

	function woocommerce_scripts(){

		$is_catalog = is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag();

		return [
			'reycore-woocommerce' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/general.js',
				'deps'    => ['rey-script', 'reycore-scripts'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_woocommerce'
			],
			'reycore-wc-cart-update' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/cart-update.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_cart'
			],
			'reycore-wc-checkout-classic' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/checkout-classic.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_checkout'
			],
			'reycore-wc-header-account-forms' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/header-account-forms.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-header-account-panel' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/header-account-panel.js',
				'deps'    => ['rey-drop-panel', 'reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-header-wishlist' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/header-wishlist.js',
				'deps'    => ['reycore-woocommerce', 'wp-util'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-header-ajax-search' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/header-ajax-search.js',
				'deps'    => ['reycore-woocommerce', 'wp-util'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-header-minicart' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/header-minicart.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-count-loadmore' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-count-loadmore.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-equalize' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-equalize.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-filter-count' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-filter-count.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-filter-panel' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-filter-panel.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-grids' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-grids.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog;
				},
			],
			'reycore-wc-loop-slideshows' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-slideshows.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-stretch' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-stretch.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-loop-toggable-widgets' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/loop-toggable-widgets.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
				'callback' => function() use ($is_catalog){
					return $is_catalog && get_theme_mod('sidebar_shop__toggle__enable', false) && (reycore_wc__check_filter_panel() || reycore_wc__check_filter_sidebar_top() || reycore_wc__check_shop_sidebar());
				},
			],
			'reycore-wc-product-carousels' => [ // TODO leave only reycore-wc-product-grid-carousels
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-carousels.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-grid-carousels' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-grid-carousel.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-page-ajax-add-to-cart' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-ajax-add-to-cart.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-page-fixed-summary' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-fixed-summary.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-page-general' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-general.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_product'
			],
			'reycore-wc-product-gallery' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-gallery.js',
				'deps'    => ['wc-single-product', 'reycore-woocommerce', 'scroll-out'],
				'version'   => REY_CORE_VERSION,
				'callback' => 'is_product'
			],
			'reycore-wc-product-page-mobile-tabs' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-mobile-tabs.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-page-qty-controls' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-qty-controls.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-wc-product-page-sticky' => [
				'src'     => REY_CORE_URI . 'assets/js/woocommerce/product-page-sticky.js',
				'deps'    => ['reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],

		];
	}

	function register_assets($assets){
		$assets->register_asset('styles', $this->woocommerce_styles());
		$assets->register_asset('scripts', $this->woocommerce_scripts());
	}

	/**
	 * Enqueue scripts for WooCommerce
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts()
	{
		if( apply_filters('reycore/woocommerce/load_all_styles', false) ){
			foreach( $this->woocommerce_styles() as $handle => $style ){
				reyCoreAssets()->add_styles($handle);
			}
		}

		// Pass visibility style
		if( is_checkout() || is_page( wc_get_page_id( 'myaccount' ) ) ){
			reyCoreAssets()->add_styles('reycore-pass-visibility');
		}
	}

	/**
	 * Filter main script's params
	 *
	 * @since 1.0.0
	 **/
	public function script_params($params)
	{

		$params['woocommerce'] = true;
		$params['wc_ajax_url'] = \WC_AJAX::get_endpoint( '%%endpoint%%' );
		$params['rest_url'] = esc_url_raw( rest_url( self::REY_ENDPOINT ) );
		$params['rest_nonce'] = wp_create_nonce( 'wp_rest' );
		$params['catalog_cols'] = reycore_wc_get_columns('desktop');
		$params['catalog_mobile_cols'] = reycore_wc_get_columns('mobile');
		$params['added_to_cart_text'] = reycore__texts('added_to_cart_text');
		$params['added_to_cart_text_timeout'] = 10000;
		$params['cannot_update_cart'] = reycore__texts('cannot_update_cart');
		$params['site_id'] = is_multisite() ? get_current_blog_id() : 0;
		$params['after_add_to_cart'] = get_theme_mod('product_page_after_add_to_cart_behviour', 'cart');
		if( 'checkout' === $params['after_add_to_cart'] ){
			$params['checkout_url'] = get_permalink( wc_get_page_id( 'checkout' ) );
		}
		$params['js_params'] = [
			'select2_overrides' => true,
			'scattered_grid_max_items' => 7,
			'scattered_grid_custom_items' => [],
			'product_item_slideshow_nav' => get_theme_mod('loop_slideshow_nav', 'dots'),
			'product_item_slideshow_disable_mobile' => get_theme_mod('loop_extra_media_disable_mobile', get_theme_mod('loop_slideshow_disable_mobile', false) ),
			'product_item_slideshow_hover_delay' => 250,
			'scroll_top_after_variation_change' => get_theme_mod('product_page_scroll_top_after_variation_change', false),
			'scroll_top_after_variation_change_desktop' => false,
			'equalize_product_items' => [],
			'ajax_search_letter_count' => 3,
			'cart_update_threshold' => 1000,
			'cart_update_by_qty' => true,
			'photoswipe_light' => false,
			'customize_pdp_atc_text' => true,
			'infinite_cache' => get_theme_mod('loop_pagination_cache_products', true),
			'acc_animation' => 250,
			'acc_scroll_top' => false,
			'acc_scroll_top_mobile_only' => true,
		];

		$params['currency_symbol'] = get_woocommerce_currency_symbol();
		$params['price_format'] = sprintf( get_woocommerce_price_format(), $params['currency_symbol'], '{{price}}' );

		$params['total_text'] = __( 'Total:', 'woocommerce' );

		if( !isset($params['ajaxurl']) ){
			$params['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$params['ajax_nonce'] = wp_create_nonce( 'rey_nonce' );
		}


		$params['price_thousand_separator'] = wc_get_price_thousand_separator();
		$params['price_decimal_separator'] = wc_get_price_decimal_separator();
		$params['price_decimal_precision'] = wc_get_price_decimals();

		return $params;
	}

	/**
	 * Add Edit Page toolbar link for Shop Page
	 *
	 * @since 1.0.0
	 */
	function shop_page_toolbar_edit_link( $admin_bar ){
		if( is_shop() ){
			$admin_bar->add_menu( array(
				'id'    => 'edit',
				'title' => __('Edit Shop Page', 'rey-core'),
				'href'  => get_edit_post_link( wc_get_page_id('shop') ),
				'meta'  => array(
					'title' => __('Edit Shop Page', 'rey-core'),
				),
			));
		}
	}

	/**
	 * Filter body css classes
	 * @since 1.0.0
	 */
	function body_classes($classes)
	{

		if( reycore_wc__is_catalog() ) {
			$classes[] = '--catalog-mode';
		}

		return $classes;
	}

	/**
	 * Filter css styles
	 * @since 1.1.2
	 */
	function css_styles($styles)
	{
		$styles[] = sprintf( ':root{ --woocommerce-grid-columns:%d; }', reycore_wc_get_columns('desktop') );
		$styles[] = sprintf( '@media(min-width: 768px) and (max-width: 1024px){:root{ --woocommerce-grid-columns:%d; }}', reycore_wc_get_columns('tablet') );
		$styles[] = sprintf( '@media(max-width: 767px){:root{ --woocommerce-grid-columns:%d; }}', reycore_wc_get_columns('mobile') );
		return $styles;
	}

	/**
	 * Get all public taxonomies
	 *
	 * @return array
	 */
	public static function get_public_taxonomies(){

		if( self::$public_taxonomies ){
			return self::$public_taxonomies;
		}

		$p = [
			'product_cat',
			'product_tag',
		];

		foreach (wc_get_attribute_taxonomies() as $key => $attribute) {
			if( $attribute->attribute_public ){
				$p[] = wc_attribute_taxonomy_name($attribute->attribute_name);
			}
		}

		return self::$public_taxonomies = $p;
	}

	/**
	 * Check if a taxonomy has "archives" enabled.
	 * Must have the "pa_" for attributes.
	 *
	 * @param string $tax
	 * @return bool
	 */
	public static function taxonomy_is_public( $tax = '' ){

		if( ! $tax ){
			return false;
		}

		// if( strpos($tax, 'pa_') === 0 ){
		// 	$tax = substr($tax, 3);
		// }

		return in_array($tax, self::get_public_taxonomies(), true);

	}

	public static function get_term_link($term_id, $tax, $fallback_url = ''){

		// if the Tax is public, can have links
		if( self::taxonomy_is_public( $tax ) ){
			return get_term_link( $term_id, $tax );
		}

		// default URL
		return $fallback_url ? $fallback_url : '#';
	}

}
