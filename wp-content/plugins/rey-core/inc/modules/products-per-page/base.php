<?php
namespace ReyCore\Modules\ProductsPerPage;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-ppp';

	const COOKIE_NAME = 'reycore-ppp';

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );
		add_action( 'reycore/woocommerce/loop/init', [$this, 'register_component']);

	}

	public function init(){

		new Customizer();

		if( ! $this->is_enabled() ) {
			return;
		}

		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_action( 'woocommerce_product_query', [ $this, 'woocommerce_product_query'], 100);

	}

	public function register_component($base){
		$base->register_component( new LoopComponent );
	}

	function get_settings(){

		$posts_per_page = reycore_wc_get_columns('desktop') * wc_get_default_product_rows_per_page();

		$settings = apply_filters('reycore/woocommerce/ppp_selector/settings', [
			'label' => esc_html_x('SHOW', 'Label for "show selector" in catalog', 'rey-core'),
			'options' => [
				$posts_per_page,
				$posts_per_page * 2,
				$posts_per_page * 4
			],
			'selected' => absint($posts_per_page),
		], $this);

		if ( ($custom_ppp = $this->get_ppp()) && isset($settings['options']) && in_array($custom_ppp, $settings['options'], true) ) {
			$settings['selected'] = $custom_ppp;
		}

		return $settings;
	}

	public function woocommerce_product_query( $q )
	{
		if ( ! ($custom_ppp = $this->get_ppp() ) ) {
			return;
		}

		if ( ! ( ($settings = $this->get_settings()) && isset($settings['options']) && in_array($custom_ppp, $settings['options'], true) ) ) {
			return;
		}

		$q->set( 'posts_per_page', $custom_ppp );
	}

	public function get_ppp(){

		$sel = 0;

		if ( isset($_COOKIE[self::COOKIE_NAME]) && ! empty( $_COOKIE[self::COOKIE_NAME] ) ) {
			$sel = reycore__clean( wp_unslash( $_COOKIE[self::COOKIE_NAME] ) );
		}

		return absint($sel);
	}

	public function register_actions( $ajax_manager ){
		$ajax_manager->register_ajax_action( 'set_ppp', [$this, 'ajax__set_ppp'], [
			'auth'   => 3,
			'nonce'  => false,
		] );
	}

	public function ajax__set_ppp( $data ){

		if( ! $this->is_enabled() ) {
			return;
		}

		if( ! ( isset($data['ppp']) && $count = absint($data['ppp']) ) ){
			return ['errors' => esc_html__('Product per page count not found.', 'rey-core')];
		}

		$settings = $this->get_settings();

		// force only predefined options to be allowed
		if( ! in_array($count, $settings['options'], true) ){
			return ['errors' => esc_html__('Invalid count.', 'rey-core')];
		}

		wc_setcookie(self::COOKIE_NAME, $count, time() + DAY_IN_SECONDS * 2);

		return $count;
	}

	public function register_assets($assets){

		$assets->register_asset('styles', [
			self::ASSET_HANDLE => [
				'src'     => self::get_path( basename( __DIR__ ) ) . '/frontend-style.css',
				'deps'    => ['woocommerce-general', 'rey-wc-product'],
				'version'   => REY_CORE_VERSION,
			],
		]);

		$assets->register_asset('scripts', [
			self::ASSET_HANDLE => [
				'src'     => self::get_path( basename( __DIR__ ) ) . '/frontend-script.js',
				'deps'    => ['rey-script', 'reycore-scripts', 'reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			],
		]);

	}

	public function is_enabled(){
		return get_theme_mod('loop_switcher_ppp', false);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Products per page switcher in catalog', 'Module name', 'rey-core'),
			'description' => esc_html_x('Adds a switcher to change how many numbers of products to be listed in catalog.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product catalog'],
			// 'help'        => reycore__support_url('kb/'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}

}
