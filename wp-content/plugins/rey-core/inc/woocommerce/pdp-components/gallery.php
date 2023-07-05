<?php
namespace ReyCore\WooCommerce\PdpComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Gallery extends Component {

	protected $galleries = [];

	protected static $active_gallery_type = '';

	public function __construct(){
		add_action( 'init', [$this, 'register_galleries']);
		add_action( 'reycore/frontend/wp_head', [ $this, 'body_classes'], 30 );
		add_action( 'wp', [$this, 'gallery_init']);
		add_action( 'elementor/widget/before_render_content', [$this, 'elementor_elements_edit_mode']);

	}

	public function register_galleries(){

		$default_galleries = [
			'PdpComponents/GalleryCascadeGrid',
			'PdpComponents/GalleryCascadeScattered',
			'PdpComponents/GalleryCascade',
			'PdpComponents/GalleryGrid',
			'PdpComponents/GalleryHorizontal',
			'PdpComponents/GalleryVertical',
		];

		foreach ($default_galleries as $item) {
			$class_name = \ReyCore\Helper::fix_class_name($item, 'WooCommerce');
			$this->register_gallery( new $class_name() );
		}

		do_action('reycore/woocommerce/pdp/gallery/init', $this);

	}

	public function init(){

		add_filter( 'woocommerce_single_product_image_gallery_classes', [$this, 'gallery_classes'], 10);
		add_filter( 'woocommerce_gallery_image_html_attachment_image_params', [$this, 'filter_image_attributes'], 20);
		add_filter( 'reycore/woocommerce/product_image/params', [$this, 'gallery_params'], 10 );
		add_action( 'reycore/woocommerce/product_image/before_gallery', [$this, 'mobile_gallery'], 5);
		add_action( 'reycore/woocommerce/product_image/before_gallery', [$this, 'load_assets']);
		add_filter( 'woocommerce_single_product_image_thumbnail_html', [$this, 'add_zoom_custom_target'], 20);
		add_filter( 'woocommerce_single_product_zoom_enabled', [$this, 'enable_gallery_zoom']);
		add_filter( 'woocommerce_single_product_photoswipe_enabled', [$this, 'enable_photoswipe']);
		add_filter( 'woocommerce_single_product_zoom_options', [$this,'add_zoom_custom_target_option']);
		add_filter( 'woocommerce_get_image_size_gallery_thumbnail', [$this, 'disable_thumbs_cropping']);

	}

	public function gallery_init(){

		self::$active_gallery_type = get_theme_mod('product_gallery_layout', 'vertical');

		if( isset($this->galleries[ self::$active_gallery_type ]) ){
			$this->galleries[ self::$active_gallery_type ]->init();
		}

	}

	public function get_id(){
		return 'gallery';
	}

	public function get_name(){
		return 'Gallery';
	}

	public static function get_active_gallery_type(){
		return self::$active_gallery_type;
	}

	public function render(){

	}

	public function register_gallery( $gallery ){
		if( $gallery_id = $gallery->get_id() ){
			$this->galleries[ $gallery_id ] = $gallery;
		}
	}

	public function get_gallery_types(){

		$galleries = [];

		foreach ($this->galleries as $id => $gallery) {
			$galleries[ $id ] = $gallery->get_name();
		}

		return $galleries;
	}

	public function gallery_classes($classes)
	{
		// to remove class, use this snippet https://d.pr/n/Ocf6PF
		$classes['loading'] = '--is-loading';

		$classes['gallery_type'] = 'woocommerce-product-gallery--' . esc_attr( self::get_active_gallery_type() );

		if( self::is_gallery_with_thumbs() ){

			$classes['gallery_with_thumbs'] = '--gallery-thumbs';

			if( get_theme_mod('product_gallery_thumbs_flip', false) ){
				$classes['gallery_flip_thumbs'] = '--flip-thumbs';
			}

			if( ($max_thumbs = get_theme_mod('product_gallery_thumbs_max', '')) && absint($max_thumbs) > 1 ){
				$classes['gallery_max_thumbs'] = '--max-thumbs';
			}

			$classes['gallery_thumbs_style'] = '--thumbs-nav-' . get_theme_mod('product_gallery_thumbs_nav_style', 'boxed');

			if( get_theme_mod('product_gallery_thumbs_disable_cropping', false) ){
				$classes['gallery_thumbs_nocrop'] = '--thumbs-no-crop';
			}

		}

		// if( $product = wc_get_product() ){
		// 	$classes['thumbs-count'] = '--thumbs-count-' . count($product->get_gallery_image_ids());
		// }

		if(
			self::is_gallery_with_thumbs() &&
			get_theme_mod('custom_main_image_height', false)
		){
			$classes['main-image-container-height'] = '--main-img-height';
		}

		if( ! $this->mobile_gallery_enabled() ){
			$classes['no_mobile_gallery'] = '--no-mobile-gallery';

		}

		return $classes;
	}

	public function elementor_elements_edit_mode( $element )
	{

		$is_elementor_library_preview = (
			isset($_REQUEST['elementor_library']) && ! empty($_REQUEST['elementor_library']) &&
			isset($_REQUEST['preview_id']) && ! empty($_REQUEST['preview_id']) &&
			isset($_REQUEST['preview']) && 'true' == $_REQUEST['preview']
		);

		if( ! (reycore__elementor_edit_mode() || $is_elementor_library_preview) ){
			return;
		}

		if( $element->get_unique_name() !== 'woocommerce-product-images' ){
			return;
		}

		$this->gallery_init();
		$this->init();

		reyCoreAssets()->add_scripts('reycore-elementor-elem-woo-prod-gallery');

	}

	public function filter_image_attributes( $params ){

		if( get_theme_mod('product_page_summary_fixed', false) ){
			$params['data-skip-lazy'] = 1;
			$params['data-no-lazy'] = 1;
			$params['loading'] = 'eager';
		}

		if( self::maybe_remove_image_title() ){
			$params['title'] = '';
		}

		if( isset($params['class']) ){
			$params['class'] .= ' no-lazy';
		}
		else {
			$params['class'] = 'no-lazy';
		}

		return $params;

	}

	public function body_classes( $frontend )
	{
		if( ! is_product() ){
			return;
		}

		if( self::is_gallery_with_thumbs() && ! get_theme_mod('product_page_summary_fixed__gallery', false) ){
			$frontend->remove_body_class('fixed_summary');
		}

		$classes['gallery_type'] = '--gallery-' . self::get_active_gallery_type();

		$frontend->add_body_class($classes);

	}


	public function gallery_params($params)
	{
		$params['active_gallery_type'] = self::get_active_gallery_type();
		$params['gallery__enable_thumbs'] = self::is_gallery_with_thumbs();
		$params['gallery__enable_animations'] = in_array(self::get_active_gallery_type(), ['grid', 'cascade', 'cascade-scattered', 'cascade-grid'], true);
		$params['product_page_gallery_max_thumbs'] = get_theme_mod('product_gallery_thumbs_max', '');
		$params['product_page_gallery_thumbs_math'] = 'floor'; // or ceil
		$params['product_page_gallery_thumbs_nav_style'] = get_theme_mod('product_gallery_thumbs_nav_style', 'boxed');
		$params['cascade_bullets'] = get_theme_mod('single_skin_cascade_bullets', true);
		$params['product_page_gallery_arrows'] = get_theme_mod('product_page_gallery_arrow_nav', false);
		$params['gallery_should_min_height'] = false;
		$params['gallery_thumb_gallery_slider'] = self::is_gallery_with_thumbs();
		$params['gallery_thumb_event'] = get_theme_mod('product_gallery_thumbs_event', 'click');
		$params['gallery_wait_defaults_initial_load'] = false;
		$params['product_page_gallery_open'] = get_theme_mod('product_page_gallery__btn__enable', true);
		$params['product_page_gallery_open_icon'] = str_replace('reycore-icon-', '', get_theme_mod('product_page_gallery__btn__icon', 'plus-stroke'));
		$params['product_page_gallery_open_hover'] = get_theme_mod('product_page_gallery__btn__text_enable', false);
		$params['product_page_gallery_open_text'] = get_theme_mod('product_page_gallery__btn__text', esc_html__('OPEN GALLERY', 'rey-core'));
		$params['product_page_mobile_gallery_nav'] = get_theme_mod('product_gallery_mobile_nav_style', 'bars');
		$params['product_page_mobile_gallery_nav_thumbs'] = 4;
		$params['product_page_mobile_gallery_arrows'] = get_theme_mod('product_gallery_mobile_arrows', false);
		$params['product_page_mobile_gallery_loop'] = false;
		return $params;
	}

	public function mobile_gallery_enabled(){
		return apply_filters('reycore/woocommerce/allow_mobile_gallery', true);
	}

	/**
	 * Prepare mobile gallery slider
	 *
	 * @since 1.0.0
	 */
	public function mobile_gallery( $image_ids = [], $product_id = 0 )
	{

		if( ! $this->mobile_gallery_enabled() ){
			return;
		}

		$gallery_html = '';
		$product = $product_id ? wc_get_product($product_id) : wc_get_product();
		$gallery_images = [];
		$size = apply_filters('reycore/woocommerce/mobile_gallery_size', 'woocommerce_single');

		if( !empty($image_ids) ){
			$gallery_image_ids = $image_ids;
		}
		else {
			$gallery_image_ids = reycore_wc__get_product_images_ids();
		}

		if( empty($gallery_image_ids) ){
			$placeholder_image = get_option( 'woocommerce_placeholder_image', 0 );
			$gallery_image_ids = (array) $placeholder_image;
		}

		// get gallery
		if( $product && !empty($gallery_image_ids) ){

			$product_id = $product->get_id();

			$i = 0;

			foreach ($gallery_image_ids as $key => $gallery_img_id) {

				$gallery_img = wp_get_attachment_image_src($gallery_img_id, $size);
				if( $gallery_img ){

					if( apply_filters('reycore/woocommerce/product_mobile_gallery/lazy_load', true) ){

						$src = 'data-splide-lazy="'. $gallery_img[0] .'"';

						if( $i === 0 ){
							$src = 'src="'. $gallery_img[0] .'"';
						}

					}
					else {
						$src = 'src="'. $gallery_img[0] .'"';
					}

					$gallery_images[] = sprintf('<div class="splide__slide"><img class="woocommerce-product-gallery__mobile-img woocommerce-product-gallery__mobile--%5$s no-lazy" %1$s data-index="%2$s" data-no-lazy="1" data-skip-lazy="1" data-full=\'%3$s\' title="%4$s"/></div>',
						$src,
						$i + 1,
						wp_json_encode( wp_get_attachment_image_src($gallery_img_id, 'full') ),
						! self::maybe_remove_image_title() ? get_the_title($gallery_img_id) : '',
						$i
					);

					$i++;
				}
			}
		}

		$gallery_images = apply_filters('reycore/woocommerce/product_mobile_gallery/html', $gallery_images, $gallery_image_ids);

		if( empty($gallery_images) ){
			return;
		}

		$slider_config = [];
		$nav_html = $thumbs_html = '';

		$this->load_assets_splide();

		if( count($gallery_images) > 1 ){

			// Arrows
			if( get_theme_mod('product_gallery_mobile_arrows', false) ):
				$nav_html .= sprintf(
					'<div class="r__arrows r__arrows-%2$s">%1$s</div>',
					reycore__svg_arrows([
						'attributes' => [
							'left' => 'data-dir="<"',
							'right' => 'data-dir=">"',
						],
						'echo' => false
					]),
					$product_id
				);
				$slider_config['arrows'] = '.r__arrows-' . $product_id;
			endif;

			// Pagination
			if( count($gallery_images) > 1 ):
				if( $nav_style = get_theme_mod('product_gallery_mobile_nav_style', 'bars') ){

					// Thumbs
					if( $nav_style === 'thumbs' ) {

						$thumbs_html .= sprintf(
							'<div class="splide woocommerce-product-gallery__mobile-thumbs"><div class="splide__track"><div class="splide__list">%1$s</div></div></div>',
							implode('', apply_filters('reycore/woocommerce/product_mobile_gallery/thumbs_html', $gallery_images, $gallery_image_ids))
						);

					}

					// Basic pagination
					else {

						$bullets = '';

						$bullets_count = count($gallery_images);

						for( $i = 0; $i < $bullets_count; $i++ ){
							$bullets .= sprintf( '<button data-go="%d" class="%s"></button>', $i, ($i === 0 ? 'is-active' : '') );
						}

						$nav_html .= sprintf(
							'<div class="r__pagination r__pagination-%2$s %3$s">%1$s</div>',
							$bullets,
							$product_id,
							'--nav-' . $nav_style
						);

						$slider_config['pagination'] = '.r__pagination-' . $product_id;
					}
				}
			endif;
		}

		// markup
		$gallery_html = sprintf(
			'<div class="splide woocommerce-product-gallery__mobile --loading %5$s" data-slider-config=\'%2$s\'><div class="splide__track"><div class="splide__list">%1$s</div></div>%3$s</div>%4$s',
			implode('', $gallery_images),
			wp_json_encode($slider_config),
			$nav_html,
			$thumbs_html,
			($thumbs_html ? '--has-thumbs' : '')
		);

		if( ! wp_doing_ajax() ){
			$gallery_html = "<div class='woocommerce-product-gallery__mobileWrapper'>{$gallery_html}</div>";
		}

		echo $gallery_html;
	}

	public function load_assets_splide(){
		reyCoreAssets()->add_scripts('rey-splide');
		reyCoreAssets()->add_styles('rey-splide');
	}

	public function load_assets()
	{
		if( self::is_gallery_with_thumbs() ){
			$this->load_assets_splide();
		}
		else {
			// all galleries except vertical/horizontal
			reyCoreAssets()->add_scripts('scroll-out');
		}
	}

	/**
	 * Adds the main image's thumb
	 * Used for Vertical, Horizontal.
	 *
	 * @since 1.0.0
	 */
	function add_main_thumb(){

		if( ! ($product = wc_get_product()) ){
			return;
		}

		// if( $variation_id = reycore_wc__get_default_variation($product) ){
		// 	$variation_product = wc_get_product($variation_id);
		// 	echo wc_get_gallery_image_html( $variation_product->get_image_id() );
		// 	return;
		// }

		if(
			($post_thumbnail_id = $product->get_image_id()) &&
			($gallery_image_ids = $product->get_gallery_image_ids()) &&
			count($gallery_image_ids) > 0 ){
			echo wc_get_gallery_image_html( $post_thumbnail_id );
		}
	}

	public function disable_thumbs_cropping($size){

		if( get_theme_mod('product_gallery_thumbs_disable_cropping', false) ){
			$size['height']  = 9999;
			$size['crop']   = false;
		}

		return $size;
	}

	/**
	 * Replace all thumbs with `woocommerce_single` sized images.
	 * Used for Grid,
	 *
	 * @since 1.0.0
	 */
	public function thumbs_to_single_size($html, $post_thumbnail_id)
	{

		if( $post_thumbnail_id ){
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		}

		return $html;
	}

	/**
	 * Adds animation class to gallery item
	 *
	 * @since 1.0.0
	 */

	public function add_animation_classes($html, $post_thumbnail_id)
	{

		if( ! apply_filters('reycore/woocommerce/galleries/add_animation', ! in_array(self::$active_gallery_type, ['vertical' , 'horizontal'], true) ) ){
			return $html;
		}

		$product = wc_get_product();

		if( ! $product ){
			global $product;
		}
		if( $product && ($main_image_id = $product->get_image_id()) && $main_image_id === $post_thumbnail_id){
			return $html;
		}

		return str_replace('woocommerce-product-gallery__image', 'woocommerce-product-gallery__image --animated-entry', $html);
	}

	public static function thumbs_markup__start(){
		if( ! self::is_gallery_with_thumbs() ){
			return;
		} ?>
		<div class="woocommerce-product-gallery__thumbs">
			<div class="splide__track">
				<div class="splide__list">
		<?php
	}

	public static function thumbs_markup__end(){
		if( ! self::is_gallery_with_thumbs() ){
			return;
		}
		?></div></div></div><?php
	}

	/**
	 * Add dataset attributes for image gallery's thumbs
	 * They'll be used for transferring their full-src to active image & zoom functionality
	 * Used for Vertical, Horizontal.
	 *
	 * @since 1.0.0
	 */
	public function add_image_datasets($html, $post_thumbnail_id)
	{
		// Add Preview Source URL
		$preview_src = wp_get_attachment_image_src($post_thumbnail_id, 'woocommerce_single');
		$preview_srcset = wp_get_attachment_image_srcset($post_thumbnail_id, 'woocommerce_single');
		$preview_sizes = wp_get_attachment_image_sizes($post_thumbnail_id, 'woocommerce_single');

		if( isset($preview_src[0]) && !empty($preview_srcset) ){
			$attributes = 'data-preview-src="' . $preview_src[0] . '"';
			$attributes .= ' data-preview-srcset="' . $preview_srcset . '"';
			$attributes .= ' data-preview-sizes="' . $preview_sizes . '"';
			$attributes .= ' data-src';

			$html = str_replace('data-src', $attributes, $html);
		}

		return $html;
	}

	/**
	 * Adds custom target container for zoom image
	 * @since 1.0.0
	 */
	public function add_zoom_custom_target($html)
	{
		if( apply_filters( 'woocommerce_single_product_zoom_enabled', get_theme_support( 'wc-product-gallery-zoom' ) ) ){
			return str_replace('</a>', '</a><div class="rey-zoomContainer"></div>', $html);
		}
		return $html;
	}

	/**
	 * Specifies the custom zoom target
	 * @since 1.0.0
	 */
	public function add_zoom_custom_target_option($options){
		$options['target'] = '.rey-zoomContainer';
		return $options;
	}


	/**
	 * Enable Gallery Zoom on hover
	 * @since 1.0.0
	 */
	public function enable_gallery_zoom(){
		return get_theme_mod('product_page_gallery_zoom', true);
	}

	/**
	 * Enable Gallery Zoom on hover
	 * @since 1.6.1
	 */
	public function enable_photoswipe(){
		return get_theme_mod('product_page_gallery_lightbox', true);
	}

	public static function maybe_remove_image_title(){
		return apply_filters('reycore/woocommerce/galleries/remove_title', false);
	}

	public static function is_gallery_with_thumbs(){
		return self::get_active_gallery_type() === 'vertical' || self::get_active_gallery_type() === 'horizontal';
	}

}
