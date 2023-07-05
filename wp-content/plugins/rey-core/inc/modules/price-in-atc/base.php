<?php
namespace ReyCore\Modules\PriceInAtc;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	private $settings = [];

	private $product;

	const ASSET_HANDLE = 'reycore-price-in-atc';

	public function __construct()
	{
		add_action( 'reycore/customizer/control=single_atc__stretch', [ $this, 'add_customizer_options' ], 10, 2 );
		add_action( 'wp', [$this, 'init']);
	}

	public function init()
	{
		if( ! is_product() ){
			return;
		}

		$this->product = wc_get_product();

		if( ! $this->product ){
			return;
		}

		if( ! $this->product->is_purchasable() ){
			return;
		}

		if( ! $this->is_enabled() ){
			return;
		}

		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'reycore/woocommerce/single_product/add_to_cart_button/simple', [ $this, 'add_in_button'], 30, 3 );
		add_filter( 'reycore/woocommerce/single_product/add_to_cart_button/variation', [ $this, 'add_in_button'], 30, 3 );

		$this->settings = apply_filters('reycore/module/price_in_atc', [
			'position' => 'after',
			'separator' => ''
		]);
	}

	public function enqueue_scripts(){
		reyCoreAssets()->add_scripts(['wnumb', self::ASSET_HANDLE]);
		reyCoreAssets()->add_styles(self::ASSET_HANDLE);
	}

	public function register_assets($assets){

		$assets->register_asset('styles', [
			self::ASSET_HANDLE => [
				'src'     => self::get_path( basename( __DIR__ ) ) . '/style.css',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			]
		]);

		$assets->register_asset('scripts', [
			self::ASSET_HANDLE => [
				'src'     => self::get_path( basename( __DIR__ ) ) . '/script.js',
				'deps'    => ['rey-script', 'reycore-scripts', 'reycore-woocommerce'],
				'version'   => REY_CORE_VERSION,
			]
		]);

	}

	public function add_in_button($html, $product, $text){

		if( ! apply_filters('reycore/module/price_in_atc/should_print_price', true) ){
			return $html;
		}

		$search = '<span class="single_add_to_cart_button-text">';
		$replace = sprintf(
			'<span class="single_add_to_cart_button-text --price-in-atc" data-position="%2$s"><span class="__price" id="rey-price-in-atc" data-separator="%3$s">%1$s</span>',
			$product->get_price_html(),
			$this->settings['position'],
			$this->settings['separator']
		);
		return str_replace($search, $replace, $html);
	}

	function add_customizer_options($control_args, $section){

		$section->add_control( [
			'type'        => 'toggle',
			'settings'    => 'single_product_atc__price',
			'label'       => esc_html__( 'Add price inside the button', 'rey-core' ),
			'default'     => false,
		] );

	}

	public function is_enabled() {
		return get_theme_mod('single_product_atc__price', false);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Price in Add to Cart button', 'Module name', 'rey-core'),
			'description' => esc_html_x('Shows the product price inside the add to cart button in the product page.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => [''],
			'help'        => reycore__support_url('kb/price-features-in-product-page'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
