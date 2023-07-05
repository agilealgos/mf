<?php
namespace ReyCore\Modules\ProductVideo;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-product-video';

	public $video_url;
	public $product_id;

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'woo_init']);
		add_action( 'reycore/templates/register_widgets', [$this, 'register_widgets']);
	}

	public function register_widgets($widgets_manager){
		$widgets_manager->register_widget_type( new Element );
	}

	public function woo_init(){

		new AcfFields();

		add_action('wp', [$this, 'init'], 5);

		// adds video button into gallery
		add_action('rey/after_site_wrapper', [$this, 'add_video_button_into_gallery_template']);

		// handle desktop gallery
		add_action( 'woocommerce_product_thumbnails', [$this, 'desktop_gallery'], 20);
		add_action( 'woocommerce_product_thumbnails', [$this, 'desktop_gallery_inline'], 1100);

		// extra variation images
		if( apply_filters('reycore/woocommerce/product_video/variations', false) ){
			add_action( 'reycore/woocommerce/extra_images/woocommerce_product_thumbnails/inside', [$this, 'desktop_gallery'], 20);
			add_action( 'reycore/woocommerce/extra_images/woocommerce_product_thumbnails', [$this, 'desktop_gallery_inline'], 1100);
		}

		// handle mobile gallery
		add_filter('reycore/woocommerce/product_mobile_gallery/html', [$this, 'mobile_gallery'], 20, 2);
		add_filter('reycore/woocommerce/product_mobile_gallery/thumbs_html', [$this, 'mobile_gallery_thumbs'], 20, 2);

	}

	public function set_data(){

		if( $this->product_id ){
			return;
		}

		$product_id = false;

		if( wp_doing_ajax() && isset($_REQUEST['product_id']) && ($ajax_pid = absint($_REQUEST['product_id'])) ){
			$product_id = $ajax_pid;
		}

		$product = wc_get_product( $product_id );

		if( ! $product ){
			global $product;
		}

		if( ! $product ){
			return;
		}

		if( ! is_object($product) ){
			return;
		}

		$this->product_id = $product_id ? $product_id : $product->get_id();
		$this->video_url = reycore__acf_get_field('product_video_url', $this->product_id );

		if( ! $this->video_url ){
			return;
		}

		$this->button_over_main_image = reycore__acf_get_field('product_video_main_image', $this->product_id );
		$this->show_inline = reycore__acf_get_field('product_video_inline', $this->product_id );
		$this->image_id = (($image = reycore__acf_get_field('product_video_gallery_image', $this->product_id )) && isset($image['id'])) ? $image['id'] : false;

	}

	function init(){

		if( ! reycore_wc__is_product() ){
			return;
		}

		$this->set_data();

		// adds regarless of other setting
		$this->add_to_summary();

	}

	function desktop_gallery(){

		$this->set_data();

		if( ! $this->video_url ){
			return;
		}

		if( $this->button_over_main_image ){
			return;
		}

		// // needs image to render
		// if( ! $this->image_id ){
		// 	return;
		// }

		$button_config = [
			'text' => wp_get_attachment_image($this->image_id, 'large'),
			'icon' => '<span class="rey-galleryPlayVideo-icon">'. reycore__get_svg_icon(['id' => 'play']) .'</span>',
			'tag' => 'a',
			'attr' => [
				'href' => '#',
				'class' => 'rey-galleryPlayVideo --prev-img-click',
			],
		];

		if( $this->show_inline ){

			// if gallery with thumbs, needs to have a thumb trigger
			if( reycore_wc__get_pdp_component('gallery')::is_gallery_with_thumbs() ){
				$button_config['modal'] = false;
				$button_config['text'] = wp_get_attachment_image($this->image_id, 'medium');
				$button_config['attr']['class'] .= ' --inline-video ';
			}
			// just bail, the inline video is rendered later
			else {
				return;
			}

		}

		// just show the image and play button over
		echo '<div class="woocommerce-product-gallery__image">';
			$this->print_button($button_config);
		echo '</div>';
	}

	function desktop_gallery_inline(){

		$this->set_data();

		if( ! $this->video_url ){
			return;
		}

		if( $this->button_over_main_image ){
			return;
		}

		if( ! $this->show_inline ){
			return;
		}

		echo '<section class="rey-gallery-inlineVideo">';

			if( $this->image_id ){
				echo wp_get_attachment_image($this->image_id, 'medium', false, [
					'class' => 'rey-gallery-inlineVideo-bg'
				]);
			}

			echo $this->render_video();

		echo '</section>';

		reyCoreAssets()->add_scripts(['scroll-out']);

	}

	function mobile_gallery_thumbs($gallery_images, $gallery_image_ids){

		$this->set_data();

		if( ! $this->video_url ){
			return $gallery_images;
		}

		if( $this->button_over_main_image ){
			return $gallery_images;
		}

		if( ! $this->show_inline ){
			return $gallery_images;
		}

		if( ! array_key_exists('inline_video', $gallery_images) ){
			return $gallery_images;
		}

		$button_config = [
			'echo' => false,
			'modal' => false,
			'text' => wp_get_attachment_image($this->image_id, 'thumbnail', false, [
				'class' => 'woocommerce-product-gallery__mobile-img no-lazy',
				'data-no-lazy' => 1,
				'data-skip-lazy' => 1,
			]),
			'icon' => '<span class="rey-galleryPlayVideo-icon">'. reycore__get_svg_icon(['id' => 'play']) .'</span>',
			'tag' => 'a',
			'attr' => [
				'href' => '#',
				'class' => 'rey-galleryPlayVideo',
			],
		];

		$image_slide = '<div class="splide__slide --inline-video-thumb">';
		$image_slide .= $this->print_button($button_config);
		$image_slide .= '</div>';

		$gallery_images['inline_video'] = $image_slide;

		return $gallery_images;
	}

	function mobile_gallery($gallery_images, $gallery_image_ids){

		$this->set_data();

		if( ! $this->video_url ){
			return $gallery_images;
		}

		if( $this->button_over_main_image ){
			return $gallery_images;
		}

		$output = '';

		if( $this->show_inline ){

			$output = $this->render_video();

			$gallery_images['inline_video'] = sprintf('<div class="splide__slide --inline-video">%s</div>', $output);

		}
		else {

			if( $this->image_id ){

				$output = $this->print_button([
					'text' => wp_get_attachment_image($this->image_id, 'large', false, [
						'class' => 'woocommerce-product-gallery__mobile-img no-lazy',
						'data-no-lazy' => 1,
						'data-skip-lazy' => 1,
					]),
					'icon' => '<span class="rey-galleryPlayVideo-icon">'. reycore__get_svg_icon(['id' => 'play']) .'</span>',
					'tag' => 'a',
					'attr' => [
						'href' => '#',
						'class' => 'rey-galleryPlayVideo --prev-img-click',
					],
					'echo' => false
				]);

				$gallery_images['video_slide'] = sprintf('<div class="splide__slide --video-slide">%s</div>', $output);

			}
		}

		return $gallery_images;
	}

	function render_video(){

		$styles = '';

		if( $ratio = reycore__acf_get_field('product_video_modal_ratio', $this->product_id ) ){
			$styles = '--custom-video-ratio:' . reycore__clean($ratio) . '%';
		}

		return \ReyCore\Helper::get_embed_video( [
			'url' => $this->video_url,
			'style' => $styles,
			'id' => 'pdp-product-video',
		] );
	}

	function add_video_button_into_gallery_template(){

		$this->set_data();

		if( ! $this->video_url ){
			return;
		}

		if( $this->button_over_main_image ){

			echo '<script type="text/html" id="tmpl-rey-btn-gallery">';

			$this->print_button([
				'text' => '',
				'attr' => [
					'class' => 'rey-singlePlayVideo d-none',
				],
			]);

			echo '</script>';
		}
	}

	function summary_button(){
		$this->print_button([
			'wrap' => true
		]);
	}

	function print_button( $args = [] ){

		$video_url = $this->video_url;

		if( ! $video_url ){
			return;
		}

		$text_ = apply_filters( 'reycore/woocommerce/video/link_text', esc_html__('PLAY PRODUCT VIDEO', 'rey-core') );

		if( $custom_text = reycore__acf_get_field('product_video_link_text', $this->product_id ) ){
			$text_ = $custom_text;
		}

		if( isset($args['custom_text']) ){
			$text_ = $args['custom_text'];
		}

		$args = wp_parse_args($args, [
			'text' => $text_,
			'icon' => reycore__get_svg_icon(['id' => 'play']),
			'tag' => 'span',
			'attr' => [
				'title' => $text_,
				'class' => 'btn btn-line u-btn-icon-sm',
				'data-elementor-open-lightbox' => 'no',
			],
			'wrap' => false,
			'modal' => true,
			'echo' => true
		]);

		$options = [
			'width'        => 750,
			'wrapperClass' => 'rey-productVideo',
			'id'           => 'pdp-product-video',
			'type'         => 'iframe',
			'src'          => esc_url(str_replace('youtu.be/', 'youtube.com/watch?v=', $video_url)),
		];

		if( isset($args['modal_width']) ){
			$options['width'] = absint($args['modal_width']);
		}
		elseif( $width = reycore__acf_get_field('product_video_modal_size', $this->product_id ) ){
			$options['width'] = absint($width);
		}

		if( isset($args['modal_video_ratio']) ){
			$options['ratio'] = absint($args['modal_video_ratio']);
		}
		elseif( $ratio = reycore__acf_get_field('product_video_modal_ratio', $this->product_id ) ){
			$options['ratio'] = reycore__clean($ratio);
		}

		if( $args['modal'] ){

			$args['attr']['data-reymodal'] = wp_json_encode($options);

			// load modal scripts
			add_filter( 'reycore/modals/always_load', '__return_true');
		}

		$button = apply_filters( 'reycore/woocommerce/video_button', sprintf('<%3$s %4$s>%2$s %1$s</%3$s>',
			$args['text'],
			$args['icon'],
			$args['tag'],
			reycore__implode_html_attributes($args['attr'])
		, $args ));

		$print_before = $print_after = '';

		if( $args['wrap'] ){
			$print_before = '<div class="rey-singlePlayVideo-wrapper">';
			$print_after = '</div>';
		}

		if( $args['echo'] ){
			echo $print_before . $button . $print_after;
		}
		else {
			return $print_before . $button . $print_after;
		}
	}

	function add_to_summary(){

		if( ! ($summary_position = reycore__acf_get_field('product_video_summary', $this->product_id )) ) {
			return;
		}

		if( $summary_position === 'disabled' ){
			return;
		}

		$available_positions = [
			'after_title'         => 6,
			'before_add_to_cart'  => 29,
			'before_product_meta' => 39,
			'after_product_meta'  => 41,
			'after_share'         => 51,
		];

		add_action( 'woocommerce_single_product_summary', [ $this, 'summary_button' ], $available_positions[$summary_position] );
	}

	public function is_enabled() {
		return true;
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Product Video', 'Module name', 'rey-core'),
			'description' => esc_html_x('Adds support for video link or popup for products, in product page or gallery.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/product-settings-options/#video'),
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
					'key' => 'product_video_url',
					'value'   => '',
					'compare' => 'NOT IN'
				]
			]
		]);

		return ! empty($post_ids);
	}
}
