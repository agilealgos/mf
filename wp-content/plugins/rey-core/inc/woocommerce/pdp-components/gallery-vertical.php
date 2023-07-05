<?php
namespace ReyCore\WooCommerce\PdpComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class GalleryVertical {

	public function init(){
		add_action( 'reycore/woocommerce/product_image/before_gallery', [$this, 'before_gallery']);
	}

	public function before_gallery(){

		$gallery = reycore_wc__get_pdp_component('gallery');

		add_action( 'woocommerce_product_thumbnails', [$this, 'start_wrap'], 0);
		add_action( 'woocommerce_product_thumbnails', [$gallery, 'add_main_thumb'], 5);
		add_action( 'woocommerce_product_thumbnails', [$this, 'end_wrap'], 1000);
		add_filter( 'woocommerce_single_product_image_thumbnail_html', [$gallery, 'add_image_datasets'], 10, 2); // add datasets to image thumbs

	}

	public function start_wrap(){

		$this->should_wrap = apply_filters('reycore/woocommerce/thumbs_gallery/should_wrap',
			(
				($product = wc_get_product()) &&
				($gallery_image_ids = $product->get_gallery_image_ids()) &&
				count($gallery_image_ids) > 0
			),
			$this->get_id()
		);

		if ( ! $this->should_wrap ){
			return;
		}

		reycore_wc__get_pdp_component('gallery')::thumbs_markup__start();
	}

	public function end_wrap(){
		if ( $this->should_wrap ){
			reycore_wc__get_pdp_component('gallery')::thumbs_markup__end();
		}
	}

	public function get_id(){
		return 'vertical';
	}

	public function get_name(){
		return 'Vertical Layout';
	}

}
