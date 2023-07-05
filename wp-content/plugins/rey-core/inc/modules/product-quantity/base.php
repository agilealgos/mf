<?php
namespace ReyCore\Modules\ProductQuantity;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'woo_init']);
	}

	public function woo_init(){

		add_filter( 'woocommerce_quantity_input_min', [$this, 'quantity_min'], 100, 2);
		add_filter( 'woocommerce_quantity_input_max', [$this, 'quantity_max'], 100, 2);
		add_filter( 'woocommerce_quantity_input_step', [$this, 'quantity_step'], 100, 2);
		add_filter( 'woocommerce_available_variation', [$this, 'quantity_variations'], 100, 3);

		new AcfFields();

	}

	public static function get_qty_value( $product_id, $type = 'minimum' ){

		if( ! ($qty_data = get_field( 'quantity_options', $product_id )) ){
			return;
		}

		if( isset($qty_data[$type]) && $val = absint($qty_data[$type]) ){
			return $val;
		}

	}

	public function quantity_variations( $available_variation, $_product_variable, $variation){

		if( ! ($available_variation['variation_is_active'] && $available_variation['variation_is_visible'] && $available_variation['is_purchasable']) ){
			return $available_variation;
		}

		if( ! ($product_id = $variation->get_parent_id()) ){
			return $available_variation;
		}

		if( $min = self::get_qty_value($product_id, 'minimum') ){
			$available_variation['min_qty'] = $min;
		}

		if( $max = self::get_qty_value($product_id, 'maximum') ){
			$available_variation['max_qty'] = $max;
		}

		return $available_variation;
	}

	public function quantity_min($val, $product){

		if( ! $product ){
			return $val;
		}

		$product_id = $product->get_id();

		if( 'variation' === $product->get_type() ){
			$product_id = $product->get_parent_id();
		}

		if( $custom = self::get_qty_value($product_id, 'minimum') ){
			return $custom;
		}

		return $val;
	}

	public function quantity_max($val, $product){

		if( !$product ){
			return $val;
		}

		$product_id = $product->get_id();

		if( 'variation' === $product->get_type() ){
			$product_id = $product->get_parent_id();
		}

		if( $custom = self::get_qty_value($product_id, 'maximum') ){
			return $custom;
		}

		return $val;
	}

	public function quantity_step($val, $product){

		if( !$product ){
			return $val;
		}

		$product_id = $product->get_id();

		if( 'variation' === $product->get_type() ){
			$product_id = $product->get_parent_id();
		}

		if( $custom = self::get_qty_value($product_id, 'step') ){
			return $custom;
		}

		return $val;
	}

	public function is_enabled() {
		return true;
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Product Quantity Min/Max', 'Module name', 'rey-core'),
			'description' => esc_html_x('Limit a product\'s minimum and maximum number of items the can be bought.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/product-settings-options/#quantity-min-max-limits'),
			'video' => true,
		];
	}

	public function module_in_use(){

		$post_ids = get_posts([
			'post_type' => 'product',
			'numberposts' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
			'meta_query' => [
				'relation' => 'OR',
				[
					'key' => 'quantity_options_minimum',
					'value'   => '',
					'compare' => 'NOT IN'
				],
				[
					'key' => 'quantity_options_maximum',
					'value'   => '',
					'compare' => 'NOT IN'
				],
			]
		]);

		return ! empty($post_ids);

	}
}
