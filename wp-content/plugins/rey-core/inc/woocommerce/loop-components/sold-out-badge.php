<?php
namespace ReyCore\WooCommerce\LoopComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class SoldOutBadge extends Component {

	public function status(){
		return get_theme_mod('loop_sold_out_badge', '1') !== '2';
	}

	public function get_id(){
		return 'sold_out_badge';
	}

	public function get_name(){
		return 'Sold Out Badge';
	}

	public function scheme(){
		return [
			'type'          => 'action',
			'tag'           => 'reycore/loop_inside_thumbnail/top-right',
			'priority'      => 10,
		];
	}

	/**
	 * Item Component - NEW badge to product entry for any product added in the last 30 days.
	*
	* @since 1.0.0
	*/
	public function render() {

		if( ! $this->maybe_render() ){
			return;
		}

		if( ! ($product = reycore_wc__get_product()) ){
			return;
		}

		$badge = '';

		if( $product->is_in_stock() ){
			if( get_theme_mod('loop_sold_out_badge', '1') === 'in-stock' ){
				$badge = apply_filters('reycore/woocommerce/loop/in_stock_text', esc_html__( 'IN STOCK', 'rey-core' ) );
			}
		}
		else {
			if( get_theme_mod('loop_sold_out_badge', '1') === '1' ) {
				$badge = apply_filters('reycore/woocommerce/loop/sold_out_text', esc_html__( 'SOLD OUT', 'rey-core' ) );
			}
		}

		if( empty($badge) ){
			return;
		}

		printf('<div class="rey-itemBadge rey-soldout-badge">%s</div>', $badge );

	}

}
