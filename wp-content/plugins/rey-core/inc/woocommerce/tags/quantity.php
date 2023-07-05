<?php
namespace ReyCore\WooCommerce\Tags;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Quantity {

	public function __construct() {
		add_action('init', [$this, 'init']);
	}

	public function init(){

		$this->add_to_cart_button_wrap();

		add_action( 'reycore/woocommerce/quantity/add_to_cart_button_wrap', [$this, 'add_to_cart_button_wrap']);

		add_action( 'woocommerce_before_quantity_input_field', [$this, 'add_quantity_controls__start']);
		add_action( 'woocommerce_after_quantity_input_field', [$this, 'add_quantity_controls__end']);

		add_action( 'woocommerce_grouped_product_list_before_quantity', [$this, 'grouped__wrap_cart_qty_start']);
		add_action( 'woocommerce_grouped_product_list_after_quantity', [$this, 'grouped__wrap_cart_qty_end']);

		add_filter( 'reycore/woocommerce/loop/add_to_cart/before', [$this, 'loop_quantity_start'], 10, 3);
		add_filter( 'reycore/woocommerce/loop/add_to_cart/after', [$this, 'loop_quantity_end'], 10, 3);

	}

	/**
	 * Wrap Quantity
	 *
	 * @since 1.0.0
	 **/
	public function add_quantity_controls__start()
	{
		if( ! apply_filters('reycore/woocommerce/add_quantity_controls', false) ){
			return;
		}

		// return if product is sold individually
		if ( ($product = wc_get_product()) && $product->is_sold_individually() ) {
			return;
		}

		$classes = [
			'rey-qtyField'
		];

		$style    = get_theme_mod('single_atc_qty_controls_styles', 'default');
		$controls = get_theme_mod('single_atc_qty_controls', false);

		$can_add_select_box = $style === 'select' && apply_filters('reycore/woocommerce/quantity_field/can_add_select', true);
		$can_add_controls = ($controls && !$can_add_select_box);

		// will also be added in the cart
		if( $can_add_controls ){
			$classes[] = 'cartBtnQty-controls';
			reyCoreAssets()->add_scripts( 'reycore-wc-product-page-qty-controls' );
		}

		// start
		$content = sprintf('<div class="%s">', implode(' ', $classes));

		// show QTY controls
		// - when enabled in product page
		// - in cart
		if( $can_add_controls ){
			$content .= sprintf('<span class="cartBtnQty-control --minus">%s</span>', reycore__get_svg_icon(['id'=>'minus']));
			$content .= sprintf('<span class="cartBtnQty-control --plus">%s</span>', reycore__get_svg_icon(['id'=>'plus']));
		}

		// Select box
		if( $can_add_select_box ) :

			$product = wc_get_product();

			$defaults = array(
				'input_name'  	=> 'quantity',
				'input_value'  	=> '1',
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product ? $product->get_min_purchase_quantity() : '', $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product ? $product->get_max_purchase_quantity() : '', $product ),
				'step' 		=> apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
				'style'		=> apply_filters( 'woocommerce_quantity_style', '', $product )
			);

			$min = ! empty( $defaults['min_value'] ) ? $defaults['min_value'] : 1;
			$max = ! empty( $defaults['max_value'] ) && $defaults['max_value'] != '-1' ? $defaults['max_value'] : 20;
			$step = ! empty( $defaults['step'] ) ? $defaults['step'] : 1;

			$options = '';
			for ( $count = $min; $count <= $max; $count = $count+$step ) {
				$options .= '<option value="' . $count . '">' . $count . '</option>';
			}

			$content .= '<div class="rey-qtySelect" style="' . $defaults['style'] . '">';
			$content .= '<span class="rey-qtySelect-title">'. reycore__texts('qty') .'</span>';
			$content .= reycore__get_svg_icon(['id'=>'arrow']);
			$content .= sprintf('<select name="%1$s" title="%2$s" class="qty" data-min="%4$s" data-max="%5$s" >%3$s</select>',
				esc_attr( $defaults['input_name'] ),
				reycore__texts('qty'),
				$options,
				$min,
				$max
			);
			$content .= '</div>';

		endif;

		echo $content;
	}

	/**
	 * Wrap Quantity
	 *
	 * @since 1.0.0
	 **/
	public function add_quantity_controls__end()
	{
		if( ! apply_filters('reycore/woocommerce/add_quantity_controls', false) ){
			return;
		}

		// return if product is sold individually
		if ( ($product = wc_get_product()) && $product->is_sold_individually() ) {
			return;
		}

		echo '</div>';
	}

	/**
	 * Wrap Add to cart & Quantity
	 *
	 * @since 1.0.0
	 **/
	public function wrap_cart_qty_start()
	{

		if( ! apply_filters('reycore/woocommerce/wrap_quantity', false) ){
			return;
		}

		add_filter( 'woocommerce_quantity_input_args', [$this, 'disable_quantity_input'], 200);

		$classes = [ 'rey-cartBtnQty', '--atc-normal-hover' ];

		$product = wc_get_product();

		if ( $product && ! $product->is_sold_individually() ) {
			if( $style = get_theme_mod('single_atc_qty_controls_styles', 'default') ){
				$classes[] = '--style-' . $style;
			}
		}

		if( get_theme_mod('single_atc__stretch', false) ){
			$classes[] = '--stretch';
			if( get_theme_mod('single_atc__stretch_btn', false) ){
				$classes[] = '--stretch-btn';
			}
		}

		$classes = apply_filters('reycore/woocommerce/cart_wrapper/classes', $classes, $product);

		printf('<div class="%s">', implode(' ', array_map('esc_attr', $classes)));

		// used for breaking row
		echo '<div class="__spacer"></div>';

	}

	public function wrap_cart_qty_end()
	{
		if( ! apply_filters('reycore/woocommerce/wrap_quantity', false) ){
			return;
		} ?>
		</div>
		<!-- .rey-cartBtnQty -->
		<?php

		remove_filter( 'woocommerce_quantity_input_args', [$this, 'disable_quantity_input'], 200);
	}

	public function disable_quantity_input($args){

		if( get_theme_mod('single_atc_qty_controls_styles', 'default') === 'disabled' ){
			$args['max_value'] = 1;
			$args['min_value'] = 1;
		}

		return $args;
	}

	public function add_to_cart_button_wrap(){

		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'wrap_cart_qty_start' ], 10);
		add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'wrap_cart_qty_end' ], 5);

	}

	public function grouped__wrap_cart_qty_start(){
		add_filter( 'reycore/woocommerce/wrap_quantity', '__return_true');
		add_filter( 'reycore/woocommerce/add_quantity_controls', '__return_true');
		add_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'wrap_cart_qty_start' ], 10);
	}

	public function grouped__wrap_cart_qty_end(){
		add_action( 'woocommerce_after_add_to_cart_quantity', [ $this, 'wrap_cart_qty_end' ], 10);
		remove_filter( 'reycore/woocommerce/add_quantity_controls', '__return_true');
		remove_filter('reycore/woocommerce/wrap_quantity', '__return_true');
	}


	public function loop_override_qty_style(){
		return 'basic';
	}

	public function loop_quantity_start($html, $product, $args){

		if( ! $this->loop_maybe_add_quantity($product, $args) ){
			return $html;
		}

		$GLOBALS['loop_qty_start'] = true;

		add_filter('theme_mod_single_atc_qty_controls', '__return_true');
		add_filter('theme_mod_single_atc_qty_controls_styles', [$this, 'loop_override_qty_style']);

		$defaults = array_map('intval', [
			'input_value'  	=> 1,
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'step' 		=> apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
		] );

		add_filter( 'reycore/woocommerce/add_quantity_controls', '__return_true');

		$quantity = woocommerce_quantity_input( $defaults, $product, false );

		remove_filter( 'reycore/woocommerce/add_quantity_controls', '__return_true');

		$quantity = str_replace('cartBtnQty-control --minus --disabled', 'cartBtnQty-control --minus', $quantity);
		$quantity = str_replace('cartBtnQty-control --plus --disabled', 'cartBtnQty-control --plus', $quantity);

		if( $defaults['input_value'] === $defaults['min_value'] ){
			$quantity = str_replace('cartBtnQty-control --minus', 'cartBtnQty-control --minus --disabled', $quantity);
		}
		else if( $defaults['max_value'] > $defaults['min_value'] && $defaults['input_value'] === $defaults['max_value'] ) {
			$quantity = str_replace('cartBtnQty-control --plus', 'cartBtnQty-control --plus --disabled', $quantity);
		}

		$classes = ['rey-loopQty'];

		if( get_theme_mod('loop_add_to_cart_mobile', false) ){
			$classes[] = '--mobile-on';
		}

		if( $btn_style = reycore_wc__get_setting('loop_add_to_cart_style') ){
			$classes[] = '--btn-style-' . esc_attr($btn_style);
		}

		return sprintf('<div class="%s">%s', implode(' ', $classes), $quantity);

	}

	public function loop_quantity_end($html, $product, $args){

		if( ! ( isset($GLOBALS['loop_qty_start']) && $GLOBALS['loop_qty_start'] ) ){
			return $html;
		}

		unset($GLOBALS['loop_qty_start']);

		remove_filter('theme_mod_single_atc_qty_controls', '__return_true');
		remove_filter('theme_mod_single_atc_qty_controls_styles', [$this, 'loop_override_qty_style']);

		return '</div>';
	}

	public function loop_maybe_add_quantity($product, $args){

		if( ! get_theme_mod('loop_supports_qty', false) ){
			return;
		}

		if( ! $product->is_purchasable() ){
			return;
		}

		if( ! $product->is_in_stock() ){
			return;
		}

		if( isset($args['supports_qty']) && ! $args['supports_qty'] ){
			return;
		}

		if( ! apply_filters('reycore/woocommerce/maybe_add_loop_qty', $product->get_type() === 'simple', $product) ){
			return;
		}

		reyCoreAssets()->add_scripts( 'reycore-wc-product-page-qty-controls' );

		return true;
	}

}
