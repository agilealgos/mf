<?php
namespace ReyCore\WooCommerce\LoopComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ratings extends Component {

	public function init(){
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		add_filter('woocommerce_product_get_rating_html', [$this, 'extend_rating_display'], 10, 3);
	}

	public function status(){
		return get_theme_mod('loop_ratings', '2') != '2';
	}

	public function get_id(){
		return 'ratings';
	}

	public function get_name(){
		return 'Ratings';
	}

	public function scheme(){

		$status = get_theme_mod('loop_ratings', '2');

		$positions = [
			'1' => [
				'tag'      => 'woocommerce_shop_loop_item_title',
				'priority' => 3,
			],
			'after' => [
				'tag'      => 'woocommerce_after_shop_loop_item_title',
				'priority' => 0,
			],
		];

		return [
			'type'          => 'action',
			'tag'           => $positions[$status]['tag'],
			'priority'      => $positions[$status]['priority'],
		];
	}

	public function render(){

		if( ! $this->maybe_render() ){
			return;
		}


		woocommerce_template_loop_rating();
	}

	public function extend_rating_display($html, $rating, $count){

		if( ! $this->status() ){
			return $html;
		}

		if( ! get_theme_mod('loop_ratings_extend', false) ){
			return $html;
		}

		if( ! ($product = wc_get_product()) ){
			return $html;
		}

		if( $product->get_id() === get_queried_object_id() ){
			return $html;
		}

		if ( 0 == $rating ) {
			return $html;
		}

		$count = $product->get_review_count();

		$text = apply_filters('reycore/woocommerce/catalog/review_link_text', sprintf('<small>%1$d %2$s</small>', $count, esc_html( _n( 'review', 'reviews', $count, 'rey-core' ) ) ), $product );

		if( apply_filters('reycore/woocommerce/catalog/review_link', false) ){
			return sprintf('<div class="star-rating-wrapper">%1$s<a href="%3$s#reviews" rel="nofollow">%2$s</a></div>',
				$html,
				$text,
				$product->get_permalink()
			);
		}

		return sprintf('<div class="star-rating-wrapper">%s%s</div>', $html, $text );

	}


}
