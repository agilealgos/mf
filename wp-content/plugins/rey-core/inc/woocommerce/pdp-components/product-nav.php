<?php
namespace ReyCore\WooCommerce\PdpComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ProductNav extends Component {

	public function init(){
		add_shortcode('rey_product_navigation', [$this, 'render']);
	}

	public function get_id(){
		return 'product_nav';
	}

	public function get_name(){
		return 'Product Navigation';
	}

	function get_type(){
		return get_theme_mod('product_navigation', '1');
	}

	public function get_settings(){

		$same_term = get_theme_mod('product_navigation_same_term', true);

		$this->settings = apply_filters('reycore/woocommerce/product_nav_settings', [
			'title_limit'  => 5,
			'in_same_term' => $same_term,
			'only_childless_categories' => $same_term,
		]);
	}

	/**
	 * Exclude parent categories
	 *
	 * @param array $terms
	 * @return array
	 * @since 2.3.4
	 */
	public function only_childless_categories($terms, $object_ids, $taxonomies, $args){

		if( ! (isset($args['fields']) && 'ids' === $args['fields']) ){
			return $terms;
		}

		foreach ($terms as $k => $term) {
			if( ! empty( get_term_children($term, 'product_cat') ) ){
				unset($terms[$k]);
			}
		}

		return $terms;
	}

	public function get_nav_item( $same_term, $previous = true ){

		$adjacent_post = get_adjacent_post( $same_term, '', $previous, 'product_cat' );

		if( !$adjacent_post ){
			return;
		}

		$product = wc_get_product( $adjacent_post->ID );
		$product_id = $product->get_id();

		return sprintf('
			<span class="rey-productNav__meta" data-id="%1$s" aria-hidden="true" title="%2$s">%3$s</span>
			%7$s
			<div class="rey-productNav__metaWrapper --%4$s">
				<div class="rey-productNav__thumb">%5$s</div>
				%6$s
			</div>',
			esc_attr( $product_id ),
			esc_attr( $product->get_title() ),
			reycore__arrowSvg(['right' => !$previous]),
			esc_attr($this->get_type()),
			$this->get_thumbnail($product),
			$this->get_extended($product),
			$this->get_screen_text($previous)
		);
	}

	public function get_screen_text($previous = true){

		$text = __( 'Previous product:', 'rey-core' );

		if( !$previous ){
			$text = __( 'Next product:', 'rey-core' );
		}

		return sprintf('<span class="screen-reader-text">%s</span>', $text );
	}

	public function get_thumbnail($product){

		$thumbnail_id = $product->get_image_id();
		$thumbnail_size = 'woocommerce_gallery_thumbnail';

		if( $this->get_type() === 'full' ){
			$thumbnail_size = 'woocommerce_single';
		}

		return !empty($thumbnail_id) ? wp_get_attachment_image( absint($thumbnail_id), $thumbnail_size ) : '';
	}

	public function get_extended($product){

		if( $this->get_type() !== 'extended' && $this->get_type() !== 'full' ){
			return;
		}

		$title = $this->get_title($product, $this->get_type() === 'extended');
		$price = wp_kses_post( $product->get_price_html() );

		return sprintf('<div class="rey-productNav__metaExtend"><div class="rey-productNav__title">%1$s</div><div class="rey-productNav__price">%2$s</div></div>', $title, $price);
	}

	public function get_title($product, $limited = false){

		if( $limited ){
			return reycore__limit_text( $product->get_title(), $this->settings['title_limit'] );
		}

		return $product->get_title();
	}

	public function render( $force = false )
	{

		if( ! $this->maybe_render() ){
			return;
		}

		if( $this->get_type() === '2' && ! $force ) {
			return;
		}

		$this->get_settings();

		add_filter( 'get_previous_post_where', [$this, 'get_adjacent_product_where']);
		add_filter( 'get_next_post_where', [$this, 'get_adjacent_product_where']);
		add_filter( 'get_previous_post_join', [$this, 'get_adjacent_product_join']);
		add_filter( 'get_next_post_join', [$this, 'get_adjacent_product_join']);

		if( $this->settings['only_childless_categories'] ){
			add_filter( 'wp_get_object_terms', [$this, 'only_childless_categories'], 10, 4 );
		}

		the_post_navigation(
			[
				'screen_reader_text' => esc_html__( 'Product navigation', 'rey-core' ),
				'prev_text' => $this->get_nav_item( $this->settings['in_same_term'], true ),
				'next_text' => $this->get_nav_item( $this->settings['in_same_term'], false ),
				'in_same_term' => $this->settings['in_same_term'],
				'taxonomy' => 'product_cat',
			]
		);

		if( $this->settings['only_childless_categories'] ){
			remove_filter( 'wp_get_object_terms', [$this, 'only_childless_categories'], 10 );
		}

		remove_filter( 'get_previous_post_where', [$this, 'get_adjacent_product_where']);
		remove_filter( 'get_next_post_where', [$this, 'get_adjacent_product_where']);
		remove_filter( 'get_previous_post_join', [$this, 'get_adjacent_product_join']);
		remove_filter( 'get_next_post_join', [$this, 'get_adjacent_product_join']);
	}


	/**
	 * Filter Adjacent Post JOIN query
	 * to exclude out of stock items
	 *
	 * @since 1.3.7
	 */
	function get_adjacent_product_join($join){
		global $wpdb;

		if ( 'yes' == get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$join = $join . " INNER JOIN $wpdb->postmeta ON ( p.ID = $wpdb->postmeta.post_id )";
		}

		return $join;
	}

	/**
	 * Filter Adjacent Post WHERE query
	 * to exclude out of stock items
	 *
	 * @since 1.3.7
	 */
	function get_adjacent_product_where($where){

		global $wpdb;

		if ( 'yes' == get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$where = $wpdb->prepare("$where AND ($wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value NOT LIKE %s)", '_stock_status', 'outofstock');
		}

		return $where;
	}
}
