<?php
namespace ReyCore\WooCommerce\PdpComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class GalleryGrid {

	public function init(){
		add_action( 'reycore/woocommerce/product_image/before_gallery', [$this, 'before_gallery']);
	}

	public function before_gallery(){

		$gallery = reycore_wc__get_pdp_component('gallery');

		add_filter( 'woocommerce_single_product_image_thumbnail_html', [$gallery, 'thumbs_to_single_size'], 10, 2);
		add_filter( 'woocommerce_single_product_image_thumbnail_html', [$gallery, 'add_animation_classes'], 10, 2);

	}

	public function get_id(){
		return 'grid';
	}

	public function get_name(){
		return 'Grid Layout';
	}

}
