<?php
namespace ReyCore\WooCommerce\PdpComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class StockHtml extends Component {

	public function init(){
		add_filter( 'woocommerce_get_stock_html', [ $this, 'adjust_stock_html' ], 9, 2);
	}

	public function get_id(){
		return 'stock_html';
	}

	public function get_name(){
		return 'Stock Html';
	}

	public function adjust_stock_html($html, $product){

		if( ! $this->maybe_render() ){
			return $html;
		}

        if ( ! apply_filters( 'reycore/woocommerce/pdp/render/stock_html', true ) ) {
            return $html;
        }

        if ( ! apply_filters_deprecated( 'reycore/woocommerce/stock_display', [true], '2.3.0', 'reycore/woocommerce/pdp/render/stock_html' ) ) {
            return $html;
        }

		if( ! is_product() ){
			return $html;
		}

		$stock_status = $product->get_stock_status();

		if( get_theme_mod('product_page__hide_stock', false) && $stock_status !== 'onbackorder' ){
			return '';
		}

		$availability = $product->get_availability();

		switch( $stock_status ):
			// onbackorder
			case "instock":
				return sprintf('<p class="stock %s">%s <span>%s</span></p>',
					esc_attr( $availability['class'] ),
					reycore__get_svg_icon(['id' => 'check']),
					$availability['availability'] ? $availability['availability'] : esc_html__( 'In stock', 'rey-core' )
				);
				break;
			case "outofstock":
				return sprintf('<p class="stock %s">%s <span>%s</span></p>',
					esc_attr( $availability['class'] ),
					reycore__get_svg_icon(['id' => 'close']),
					$availability['availability'] ? $availability['availability'] : esc_html__( 'Out of stock', 'rey-core' )
				);
				break;
		endswitch;

		return $html;
	}

}
