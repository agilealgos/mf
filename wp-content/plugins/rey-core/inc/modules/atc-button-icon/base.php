<?php
namespace ReyCore\Modules\AtcButtonIcon;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	private $settings = [];

	public function __construct()
	{
		parent::__construct();

		add_action( 'init', [$this, 'init'] );
	}

	public function init()
	{
		add_filter('reycore/woocommerce/loop/add_to_cart/content', [$this, 'add_icon__catalog'], 10, 2);
		add_filter('reycore/woocommerce/single_product/add_to_cart_button/variation', [$this,'add_icon__product_page'], 20, 3);
		add_filter('reycore/woocommerce/single_product/add_to_cart_button/simple', [$this, 'add_icon__product_page'], 20, 3);
	}

	public function add_icon__catalog($html, $product) {
		$icon = ($cart_icon = get_theme_mod('loop_atc__icon', '')) ? reycore__get_svg_icon([ 'id'=> $cart_icon ]) : '';

		if( ! $icon ){
			return $html;
		}

		return apply_filters('reycore/woocommerce/loop/add_to_cart/icon', $icon) . $html;
	}

	public function add_icon__product_page($html, $product, $text) {

		$icon = ($cart_icon = get_theme_mod('single_atc__icon', '')) ? reycore__get_svg_icon([ 'id'=> $cart_icon ]) : '';

		if( ! $icon ){
			return $html;
		}

		$search = '<span class="single_add_to_cart_button-text">';
		return str_replace($search, $icon . $search, $html);
	}


	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Icon in Add To Cart Button', 'Module name', 'rey-core'),
			'description' => esc_html_x('Adds new controls for Add to Cart buttons to display an icon inside them.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['Product Catalog', 'Product Page'],
			'video' => true,
		];
	}

	public function module_in_use(){
		return (bool) (get_theme_mod('loop_atc__icon', '') || get_theme_mod('single_atc__icon', ''));
	}

}
