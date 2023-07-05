<?php
namespace ReyCore\Modules\MiniCartCarousel;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-minicart-carousel';

	public $settings;

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
	}

	public function init() {

		if( ! $this->is_enabled() ){
			return;
		}

		add_action( 'reycore/customizer/section=header-mini-cart', [ $this, 'add_customizer_options' ] );
		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_filter( 'theme_mod_header_cart__cross_sells_carousel', [ $this, 'mod_header_cart__cross_sells_carousel' ] );
		add_action( 'reycore/woocommerce/minicart/cart_panel', [$this, 'render_markup'], 10);
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'cross_sells_data_fragment' ] );
		add_action( 'woocommerce_before_mini_cart', [$this, 'add_cart_assets']);
		add_filter( 'reycore/woocommerce/minicarts/supports_products', '__return_true' );
	}

	public function set_settings(){

		$this->settings = apply_filters('reycore/woocommerce/cartpanel/cross_sells_carousel', [
			'enable' => $this->is_enabled(),
			'limit' => get_theme_mod('header_cart__cross_sells_carousel_limit', 10),
			'title' => get_theme_mod('header_cart__cross_sells_carousel_title', __( 'You may also like&hellip;', 'woocommerce' )),
			'mobile' => get_theme_mod('header_cart__cross_sells_carousel_mobile', true),
			'autoplay' => false,
			'autoplay_duration' => 3000,
		]);

	}

	public function register_assets($assets){

		$assets->register_asset('styles', [
			self::ASSET_HANDLE => [
				'src'      => self::get_path( basename( __DIR__ ) ) . '/style.css',
				'deps'     => [],
				'version'  => REY_CORE_VERSION,
				'priority' => 'low',
			]
		]);

		$assets->register_asset('scripts', [
			self::ASSET_HANDLE => [
				'src'     => self::get_path( basename( __DIR__ ) ) . '/script.js',
				'deps'    => ['rey-script', 'reycore-scripts', 'reycore-woocommerce', 'wp-util'],
				'version'   => REY_CORE_VERSION,
			]
		]);

	}

	public function add_cart_assets(){

		if( WC()->cart->is_empty() && ! wp_doing_ajax() ){
			return;
		}

		reyCoreAssets()->add_styles( [ 'rey-wc-general', 'rey-splide', self::ASSET_HANDLE] );
		reyCoreAssets()->add_scripts( [ 'underscore', 'wp-util', 'splidejs', 'rey-splide', self::ASSET_HANDLE ] );

	}

	/**
	 * Append special Cross-sells fragment into the Cart's Fragments
	 *
	 * @param array $fragments
	 * @return array
	 */
	public function cross_sells_data_fragment( $fragments ){

		if( ! $this->settings ){
			$this->set_settings();
		}

		if( ! ($cross_sells = WC()->cart->get_cross_sells()) ){
			return $fragments;
		}

		if( ! ($minicart_tag = reycore_wc__get_tag('minicart')) ){
			return $fragments;
		}

		$cross_sells = array_unique( $this->settings['limit'] > 0 ? array_slice( $cross_sells, 0, $this->settings['limit'] ) : $cross_sells );

		$cs_fragment = $minicart_tag->prepare_products_data_fragment($cross_sells);

		if( ! empty($cs_fragment) ){
			$fragments['_crosssells_'] = $cs_fragment;
		}

		return $fragments;
	}

	public function render_markup(){

		if( ! $this->settings ){
			$this->set_settings();
		}

		if( ! $this->settings['enable'] ){
			return;
		}

		if( ! ($minicart_tag = reycore_wc__get_tag('minicart')) ){
			return;
		}

		$class = 'splide rey-crossSells-carousel --loading';

		if( $this->settings['mobile'] ){
			$class .= ' --dnone-desktop --dnone-tablet';
		}

		$slider_config = wp_json_encode([
			'autoplay' => $this->settings['autoplay'],
			'autoplaySpeed' => $this->settings['autoplay_duration'],
		]); ?>

		<script type="text/html" id="tmpl-reyCrossSellsCarousel">

		<# var items = data.items; #>
		<# if( items.length ){ #>
		<?php
			printf('<div class="%1$s" data-slider-config=\'%2$s\'>',
				$class,
				$slider_config
			);
		?>

			<?php if( $title = $this->settings['title'] ): ?>
			<h3 class="rey-crossSells-carousel-title"><?php echo $title ?></h3>
			<?php endif; ?>

			<div class="splide__track">
				<div class="rey-crossSells-itemsWrapper splide__list">
					<?php $minicart_tag->render_cross_sells(['class' => 'splide__slide']) ?>
				</div>
			</div>
		<# } #>

		</script><?php

	}

	public function add_customizer_options( $section ){

		$section->add_title( esc_html__('CROSS-SELLS CAROUSEL', 'rey-core') );

		$section->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__cross_sells_carousel',
			'label'       => esc_html__( 'Enable Carousel', 'rey-core' ),
			'description' => esc_html__( 'This will display a carousel containing all the cross-sells products linked to the products in the cart.', 'rey-core' ),
			'default'     => true,
		] );

		$section->add_control( [
			'type'        => 'text',
			'settings'    => 'header_cart__cross_sells_carousel_title',
			'label'       => esc_html__( 'Title', 'rey-core' ),
			'default'     => '',
			'input_attrs'     => [
				'placeholder' => __( 'You may also like&hellip;', 'woocommerce' ),
			],
			'active_callback' => [
				[
					'setting'  => 'header_cart__cross_sells_carousel',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$section->add_control( [
			'type'        => 'rey-number',
			'settings'    => 'header_cart__cross_sells_carousel_limit',
			'label'       => esc_html__( 'Products Limit', 'rey-core' ),
			'default'     => 10,
			'choices'     => [
				'min'  => 1,
				'max'  => 20,
				'step' => 1,
			],
			'active_callback' => [
				[
					'setting'  => 'header_cart__cross_sells_carousel',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

		$section->add_control( [
			'type'        => 'toggle',
			'settings'    => 'header_cart__cross_sells_carousel_mobile',
			'label'       => esc_html__( 'Show only on Mobile', 'rey-core' ),
			'default'     => true,
			'active_callback' => [
				[
					'setting'  => 'header_cart__cross_sells_carousel',
					'operator' => '==',
					'value'    => true,
				],
			],
		] );

	}

	/**
	 * This feature is exclusive to mobile if specified.
	 * No reason to load it on desktop.
	 */
	public function mod_header_cart__cross_sells_carousel($mod){

		if( get_theme_mod('header_cart__cross_sells_carousel_mobile', true) ){
			if( ! reycore__is_mobile() && reycore__supports_mobile_caching() ){
				return false;
			}
		}

		return $mod;
	}

	public function is_enabled() {
		return get_theme_mod('header_cart__cross_sells_carousel', true);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Mini-Cart Carousel with Cross-Sells', 'Module name', 'rey-core'),
			'description' => esc_html_x('Shows a carousel of products inside the mini-cart, based on added to cart product\'s cross-sell products.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => [''],
			'help'        => reycore__support_url('kb/shopping-cart-popup-side-panel/#display-a-carousel-of-cross-sells-products'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
