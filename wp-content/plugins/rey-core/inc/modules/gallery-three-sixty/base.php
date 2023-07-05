<?php
namespace ReyCore\Modules\GalleryThreeSixty;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-pdp-gallery-360';

	public $product;

	public $product_id;

	public $positions = 1;

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
	}

	public function init() {

		add_action( 'wp', [$this, 'wp']); // `wp` bc must have page id ready

		new Customizer();
		new AcfFields();
	}

	public function wp(){

		if( ! is_product() ){
			return;
		}

		if( ! ($this->product = wc_get_product()) ){
			return;
		}

		$this->product_id = $this->product->get_id();

		$this->images = reycore__acf_get_field('product_360_images', $this->product_id );

		if( ! $this->images ){
			return;
		}

		if( empty($this->images) ){
			return;
		}

		$this->gallery_with_thumbs = reycore_wc__get_pdp_component('gallery')::is_gallery_with_thumbs();
		$this->selected_position = get_theme_mod('wc360_position', 'second');
		$this->settings = apply_filters('reycore/woocommerce/360_image/settings', [
			'autoplay_speed' => get_theme_mod('wc360_autoplay_speed', 250)
		]);

		add_action( 'wp_head', [$this, 'prevent_start']);
		add_action( 'wp_enqueue_scripts', [$this, 'load_scripts']);
		add_filter( 'woocommerce_single_product_image_gallery_classes', [$this, 'add_classes'], 20);

		// mobile
		add_filter( 'reycore/woocommerce/product_mobile_gallery/html', [$this, 'add_extra_image__mobile'], 20, 2);
		add_action( 'woocommerce_before_single_product_summary', [$this, 'display_360_block__mobile'], 5 );

		add_action( 'reycore/woocommerce/thumbs_gallery/should_wrap', '__return_true' );

		// If no main image, just replace block
		if( ! $this->product->get_image_id() ){
			add_action( 'woocommerce_before_single_product_summary', [$this, 'display_360_block'], 5 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			return;
		}

		$positions = [
			'first' => 4,
			'second' => 6,
			'last' => 20
		];

		// if gallery has thumbs
		if( $this->gallery_with_thumbs ){

			$pos = $positions[ $this->selected_position ];

			add_action( 'woocommerce_product_thumbnails', [$this, 'add_thumbnails'], $pos);

			// display 360 block
			add_action( 'woocommerce_product_thumbnails', [$this, 'display_360_block'], 1100);
		}

		// Any other gallery that doesn't have thumbnails
		else {
			if( $this->selected_position === 'first' ){
				add_filter( 'woocommerce_single_product_image_thumbnail_html', [$this, 'display_360_block__as_first'], 20, 2);
			}
			else {
				add_action( 'woocommerce_product_thumbnails', [$this, 'display_360_block'], $positions[ $this->selected_position ] );
			}
		}
	}

	public function add_thumbnails(){

		// fix gallery with a single image
		$gallery_thumbs = $this->product->get_gallery_image_ids();

		if( count($gallery_thumbs) === 0 && ($main_image_id = $this->product->get_image_id()) ){
			echo wc_get_gallery_image_html( $main_image_id );
		}

		echo $this->add_360_thumbnail();
	}

	public function add_360_thumbnail( $mobile = false ){
		$html = '<div class="splide__slide woocommerce-product-gallery__image --is-360">';
			$html .= wp_get_attachment_image($this->images[0], 'large', false, ['class' => 'woocommerce-product-gallery__mobile-img no-lazy']);
			$html .= '<span class="rey-gallery-360-icon">'. reycore__get_svg_icon(['id' => '360']) .'</span>';

			if( $mobile ){
				$html .= '<span class="rey-gallery-360-text">'. esc_html__('CLICK TO OPEN', 'rey-core') .'</span>';
			}

		$html .= '</div>';

		return $html;
	}

	public function add_extra_image__mobile( $gallery_image, $gallery_image_ids ){

		$before = $after = '';
		$thumb = $this->add_360_thumbnail( true );

		if( $this->selected_position === 'first' ){
			array_splice( $gallery_image, 0, 0, $thumb );
		}
		elseif( $this->selected_position === 'second' ){
			array_splice( $gallery_image, 1, 0, $thumb );
		}
		elseif( $this->selected_position === 'last' ){
			array_splice( $gallery_image, count($gallery_image), 0, $thumb );
		}

		return $gallery_image;
	}

	public function display_360_block__mobile(){

		$html = '<div class="rey-360image-mobile">';
		$html .= '<button class="rey-360image-mobileClose">' . reycore__get_svg_icon(['id' => 'close']) . '</button>';
		$html .= $this->the_block(true);
		$html .= '</div>';

		echo $html;
	}

	public function the_block( $mobile = false ){

		$images = [];

		foreach ($this->images as $key => $image_id) {
			$images[] = wp_get_attachment_url($image_id);
		}

		$attributes['data-hide-360-logo'] = 'true';
		$attributes['data-image-list'] = wp_json_encode($images);

		if( get_theme_mod('wc360_autoplay', true) ){
			$attributes['data-autoplay'] = 'true';
			$attributes['data-speed'] = $this->settings['autoplay_speed'];
		}

		if( get_theme_mod('wc360_fullscreen', true) ){
			$attributes['data-full-screen'] = 'true';
		}

		$classes = [
			'rey-360image'
		];

		if( ! $this->gallery_with_thumbs ){
			$classes['not_gallery_with_thumbs'] = 'woocommerce-product-gallery__image --visible';
		}
		else {
			if( $this->selected_position === 'first' ){
				$classes['is_first'] = '--visible';
			}
		}

		if( ! $this->product->get_image_id() ){
			$classes['no_gallery'] = '--no-gallery';
		}

		if( $mobile ){
			unset($classes['not_gallery_with_thumbs']);
			unset($classes['is_first']);
			unset($classes['no_gallery']);
			unset($attributes['data-full-screen']);
			// $attributes['data-responsive'] = 'true';
		}

		$html = '<div class="'. implode(' ', $classes) .'">';
			$html .= '<div class="rey-360image-inner">';
			$html .= sprintf('<div class="cloudimage-360" %s></div>', reycore__implode_html_attributes( $attributes ));
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function display_360_block(){
		echo $this->the_block();
	}

	public function display_360_block__as_first($html, $post_thumbnail_id){

		if( $this->product->get_image_id() === $post_thumbnail_id ){
			return $this->the_block() . $html;
		}

		return $html;
	}

	public function add_classes($classes){
		$classes['class_360'] = '--has-360';

		if( ($this->gallery_with_thumbs && $this->selected_position === 'first') ){
			$classes['class_360_first'] = '--activate-360';
		}

		elseif( !$this->gallery_with_thumbs ){
			$classes['class_360_first'] = '--activate-on-load';
		}

		if( $this->selected_position !== 'last' ){
			$classes['class_360_fix_index'] = '--fix-360-ps-index';
		}

		return $classes;
	}

	public function prevent_start(){
		echo'<script>window.CI360 = { notInitOnLoad: true }</script>';
	}

	public function load_scripts(){
		wp_enqueue_script( 'js-cloudimage-360-view', REY_CORE_URI . 'assets/js/lib/js-cloudimage-360-view.min.js', ['reycore-woocommerce'], '2.6.0' , true);
	}

	public function is_enabled() {
		return true;
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('360 Image in Product Page Gallery', 'Module name', 'rey-core'),
			'description' => esc_html_x('Adds support for rotating and draggable 360 image in product gallery', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page', 'product gallery'],
			'help'        => reycore__support_url('kb/product-settings-options/#360-image'),
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
				[
					'key' => 'product_360_images',
					'value'   => '',
					'compare' => 'NOT IN'
				],
			]
		]);

		return ! empty($post_ids);

	}
}
