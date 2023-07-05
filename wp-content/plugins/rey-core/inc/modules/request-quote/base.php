<?php
namespace ReyCore\Modules\RequestQuote;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	public static $settings = [];

	private $defaults = [];

	private $type = '';

	// used in loop
	public $loop_loaded = false;

	const ASSET_HANDLE = 'reycore-request-quote';

	public function __construct()
	{
		add_action('init', [$this, 'init']);
		add_action( 'reycore/templates/register_widgets', [$this, 'register_widgets']);
	}

	public function init(){

		new Customizer();

		if( ! $this->is_enabled() ) {
			return;
		}

		$this->type = $this->get_type();

		$this->set_defaults();

		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_filter( 'reycore/modal_template/show', '__return_true' );
		add_action( 'rey/after_site_wrapper', [$this, 'add_form_modal'], 50);
		add_action( 'woocommerce_single_product_summary', [$this, 'get_button_html'], 30);
		add_shortcode( 'rey_request_quote', [$this, 'get_button_html']);
		add_action( 'reycore/woocommerce/loop/quickview_button', [$this, 'load_in_quickview']);

		new CompatCf7();
		new CompatWpforms();
	}

	public function register_widgets($widgets_manager){
		$widgets_manager->register_widget_type( new Element );
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
				'localize' => [
					'name' => 'reycoreRequestQuoteParams',
					'params' => [
						'variation_aware' => $this->defaults['variation_aware'],
						'disabled_text' => $this->defaults['disabled_text'],
						'close_position' => $this->defaults['close_position'],
					],
				],
			],
		]);

	}

	/**
	 * Set defaults
	 *
	 * @since 1.2.0
	 **/
	public function set_defaults()
	{
		$this->defaults = apply_filters('reycore/woocommerce/request_quote_defaults', [
			'title' => get_theme_mod('request_quote__btn_text', esc_html__( 'Request a Quote', 'rey-core' ) ),
			'product_title' => esc_html__( 'PRODUCT: ', 'rey-core' ),
			'close_position' => 'inside',
			'show_in_quickview' => false,
			'variation_aware' => get_theme_mod('request_quote__var_aware', false ),
			'disabled_text' => esc_html__('Please select some product options before requesting quote.', 'rey-core')
		]);
	}

	public function maybe_show(){
		return apply_filters('reycore/woocommerce/request_quote/display', $this->__check_display(), $this);
	}

	private function __check_display(){

		// if shows in quickview, load everywhere
		if( ! reycore_wc__is_product() ){
			return false;
		}

		if( $this->type === 'products' ){

			if( ! ($products = get_theme_mod('request_quote__products', '')) ){
				return false;
			}

			$get_products_ids = array_map( 'absint', array_map( 'trim', explode( ',', $products ) ) );

			if( ! in_array(get_the_ID(), $get_products_ids) ){
				return false;
			}
		}

		elseif( $this->type === 'categories' ){

			if( ! ($categories = get_theme_mod('request_quote__categories', [])) ){
				return false;
			}

			if( $product = reycore_wc__get_product() ){
				if( ! has_term($categories, 'product_cat', $product->get_id()) ){
					return false;
				}
			}
		}

		if( get_query_var('rey__is_quickview', false) === true && $this->defaults['show_in_quickview'] === false ) {
			return false;
		}

		return true;
	}

	public function add_form_modal(){

		if( ! $this->maybe_show() ){
			return;
		}

		$form = apply_filters('reycore/woocommerce/request_quote/output', '', [
			'class' => 'rey-form--basic'
		] );

		if( empty($form) ){
			return;
		}

		reycore__get_template_part('template-parts/woocommerce/request-quote-modal', false, false, [
			'form' => $form,
			'defaults' => $this->defaults
		]);

	}

	public function load_scripts(){

		reyCoreAssets()->add_styles(self::ASSET_HANDLE);
		reyCoreAssets()->add_scripts(self::ASSET_HANDLE);

		// load modal scripts
		add_filter('reycore/modals/always_load', '__return_true');

	}

	/**
	* Add the button
	*
	* @since 1.2.0
	*/
	public function get_button_html( $args = [] ){

		if( ! $this->maybe_show() ){
			return;
		}

		if( ! empty($args) ){
			$this->defaults = array_merge($this->defaults, $args);
		}

		reycore__get_template_part('template-parts/woocommerce/request-quote-button', false, false, [
			'button_text' => $this->defaults['title']
		]);

		$this->load_scripts();
	}

	public function load_in_quickview(){

		if( $this->loop_loaded ){
			return;
		}

		if( $this->defaults['show_in_quickview'] ){

			add_filter('reycore/woocommerce/request_quote/display', '__return_true');

			$this->load_scripts();

		}

		$this->loop_loaded = true;

	}

	public function get_type(){
		return get_theme_mod('request_quote__type', '');
	}

	public function is_enabled(){
		return $this->get_type() !== '';
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Request a quote', 'Module name', 'rey-core'),
			'description' => esc_html_x('Allow customers to request quotes or informations about a specific product.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/request-a-quote-form/'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}

}
