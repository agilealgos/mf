<?php
namespace ReyCore\Modules\RelatedProducts;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-related-products';

	const META_KEY = '_rey_related_ids';

	protected $product_ids = [];

	protected $to_check = [];

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
	}

	public function init(){

		new Customizer();

		add_action( 'wp', [$this, 'disable_section']);

		if( ! $this->is_enabled() ){
			return;
		}
		add_action( 'woocommerce_product_options_related', [$this, 'add_extra_product_edit_options'] );
		add_action( 'woocommerce_update_product', [$this, 'process_extra_product_edit_options'] );
		add_action( 'reycore/woocommerce/loop/before_grid', [$this, 'before_grid'], 9);
		add_action( 'reycore/woocommerce/loop/after_grid', [$this, 'after_grid'], 11);
		add_filter( 'woocommerce_related_products', [$this, 'related_products_query'], 10, 3 );
		add_filter( 'woocommerce_product_related_products_heading', [$this, 'change_title']);
		add_filter( 'woocommerce_output_related_products_args', [$this, 'output_related_products_args'], 20 );
		add_filter( 'woocommerce_upsell_display_args', [$this, 'filter_upsells_products_args'], 20 );
		add_action( 'reycore/woocommerce/related_products/placeholder_params', [$this, 'lazy_params']);
		add_action( 'reycore/woocommerce/upsells_products/placeholder_params', [$this, 'lazy_params']);
		add_action( 'reycore/customizer/after_save', [ $this, 'flush_transients' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [$this, 'before_render'], 10);
		add_action( 'elementor/frontend/widget/after_render', [$this, 'after_render'], 10);

		$this->to_check[] = 'related';

		if( $this->is_upsells_enabled() ){
			$this->to_check[] = 'up-sells';
		}

	}

	public function disable_section(){

		if( ! is_product() ){
			return;
		}

		// Disable
		if( ! $this->is_enabled() ){
			if( apply_filters('reycore/woocommerce/related_products/hide_upsells', true) ){
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			}
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			return;
		}

	}

	function before_grid(){

		if( ! ( ($q_name = wc_get_loop_prop( 'name' )) && in_array($q_name, $this->to_check, true) ) ){
			return;
		}

		wc_set_loop_prop( 'is_paginated', false );

		if( $this->supports_carousel() ){
			reyCoreAssets()->add_scripts(['rey-splide', 'reycore-wc-product-grid-carousels']);
			reyCoreAssets()->add_styles('rey-splide');
		}

		add_filter( 'woocommerce_post_class', [$this,'add_product_classes'], 30 );
		add_filter( 'reycore/woocommerce/product_loop_classes', [$this, 'add_grid_classes']);
		add_filter( 'reycore/woocommerce/product_loop_attributes/v2', [$this, 'add_grid_attributes'], 10, 2);
		add_filter( 'reycore/woocommerce/catalog/before_after/enable', '__return_false');
		add_filter( 'reycore/woocommerce/catalog/stretch_product/enable', '__return_false');
		add_filter( 'reycore/woocommerce/loop_components/disable_grid_components', '__return_true');
		add_filter( 'reycore/woocommerce/loop/render/thumbnails_second', 'reycore__elementor_edit_mode__return_false');
		add_filter( 'reycore/woocommerce/loop/render/thumbnails_slideshow', 'reycore__elementor_edit_mode__return_false');

	}

	function after_grid(){

		if( ! (($q_name = wc_get_loop_prop( 'name' )) && in_array($q_name, $this->to_check, true) ) ){
			return;
		}

		remove_filter( 'woocommerce_post_class', [$this,'add_product_classes'], 30 );
		remove_filter( 'reycore/woocommerce/product_loop_classes', [$this, 'add_grid_classes']);
		remove_filter( 'reycore/woocommerce/product_loop_attributes/v2', [$this, 'add_grid_attributes'], 10, 2);
		remove_filter( 'reycore/woocommerce/catalog/before_after/enable', '__return_false');
		remove_filter( 'reycore/woocommerce/catalog/stretch_product/enable', '__return_false');
		remove_filter( 'reycore/woocommerce/loop_components/disable_grid_components', '__return_true');
		remove_filter( 'reycore/woocommerce/loop/render/thumbnails_second', 'reycore__elementor_edit_mode__return_false');
		remove_filter( 'reycore/woocommerce/loop/render/thumbnails_slideshow', 'reycore__elementor_edit_mode__return_false');
	}

	public function change_title( $title ){
		if( $this->is_enabled() && ($related_title = get_theme_mod('single_product_page_related_title', '')) ){
			return $related_title;
		}
		return $title;
	}

	public function add_grid_attributes( $attributes, $settings ){

		if( isset($settings['_skin']) && $settings === 'carousel' ){
			return $attributes;
		}

		// if up-sells
		// must have same settings, otherwise bail
		if( 'up-sells' === wc_get_loop_prop( 'name' ) ){
			if( ! $this->is_upsells_enabled() ){
				return $attributes;
			}
		}

		// $attributes['data-cols'] = get_theme_mod('single_product_page_related_columns', '') || reycore_wc_get_columns('desktop');
		$attributes['data-cols-tablet'] = reycore_wc_get_columns('tablet');
		$attributes['data-cols-mobile'] = reycore_wc_get_columns('mobile');

		if( $this->supports_carousel() ){

			$params = [
				'autoplay'            => false,
				'interval'            => 6000,
				'per_page'            => reycore_wc_get_columns('desktop'),
				'per_page_tablet'     => 3,
				'per_page_mobile'     => 2,
				'auto_width'          => true,
				'desktop_only_arrows' => true,
				// 'side_offset_mobile'  => false
			];

			if( $desktop_cols = get_theme_mod('single_product_page_related_columns', '') ){
				$params['per_page' ] = $desktop_cols;
			}

			foreach(['tablet', 'mobile'] as $device){
				if( $cols = get_theme_mod('single_product_page_related_columns_' . $device, '') ){
					$params['per_page_' . $device ] = $cols;
					$attributes['data-cols-' . $device ] = $cols;
				}
			}

			$params = apply_filters('reycore/woocommerce/related_products/grid_config', $params, $this);

			$attributes['data-prod-carousel-config'] = wp_json_encode($params);
		}

		return $attributes;
	}


	function supports_carousel(){

		if( isset($this->supports_carousel) ){
			return $this->supports_carousel;
		}

		return $this->supports_carousel = get_theme_mod('single_product_page_related_carousel', false);
	}

	function add_extra_product_edit_options(){

		if( ! $this->custom_enabled() ){
			return;
		}

		?>
		<div class="options_group">

			<p class="form-field hide_if_grouped hide_if_external">
				<label for="rey_related_products"><?php esc_html_e( 'Related products', 'rey-core' ); ?></label>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="rey_related_products" name="<?php echo self::META_KEY ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'rey-core' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( get_the_ID() ); ?>">
					<?php
					$product_ids = $this->get_products_ids();

					if( is_array($product_ids) ):
						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
							}
						}
					endif;
					?>
				</select> <?php echo wc_help_tip( __( 'Select custom related products.', 'rey-core' ) ); // WPCS: XSS ok. ?>
			</p>

		</div>

		<?php
	}

	public function process_extra_product_edit_options( $product_id ) {

		if ( $this->custom_enabled() ) {

			$save = [];

			if( isset($_POST[ self::META_KEY ]) ){
				$save = wc_clean( $_POST[ self::META_KEY ] );
			}

			update_post_meta( $product_id, self::META_KEY, $save );
		}
	}

	public function related_products_query($related_posts, $product_id, $args) {

		// get from same category
		$related_posts = $this->get_same_category($related_posts, $product_id, $args);

		// get custom selected products
		$related_posts = $this->get_custom_selected_products($product_id, $related_posts);

		// remove out of stock products
		$related_posts = $this->exclude_out_of_stock($related_posts, $args['excluded_ids']);

		return $related_posts;
	}

	public function get_same_category($related_posts, $product_id, $args){

		if( ! $this->same_category_enabled() ){
			return $related_posts;
		}

		if( $this->custom_enabled() ){
			return $related_posts;
		}

		$cats_array = [];
		$terms = wp_get_post_terms( $product_id, 'product_cat' );

		//Select only the category which doesn't have any children
		foreach ( $terms as $term ) {
			$children = get_term_children( $term->term_id, 'product_cat' );
			if ( empty($children) )
				$cats_array[] = $term->term_id;
		}

		// Manipulate the categories array
		// for example splice to grab only the first found category
		// $cats_array = array_splice($cats_array, 1 );
		$cats_array = apply_filters('reycore/woocommerce/related_products/same_category/categories', $cats_array);

		if( empty($cats_array) ){
			return $related_posts;
		}

		global $wp_query;

		$meta_query[] = $wp_query->visibility_meta_query();

		// should it be added?
		// $meta_query[] = $wp_query->stock_status_meta_query();

		return get_posts(array(
			'orderby'        => 'rand',
			'posts_per_page' => $args['limit'],
			'post_type'      => 'product',
			'meta_query'     => $meta_query,
			'post__not_in'   => $args['excluded_ids'],
			'fields'         => 'ids',
			'tax_query'      => [
				[
					'taxonomy'  => 'product_cat',
					'field'     => 'id',
					'terms'     => $cats_array
				]
			],
		));

	}

	public function exclude_out_of_stock($related_posts, $excluded_ids){

		if( ! get_theme_mod('single_product_page_related_hide_outofstock', false) ){
			return $related_posts;
		}

		$related_posts = array_map('absint', $related_posts);

		// get out of stock products
		$the_query = new \WP_Query( [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'fields'         => 'ids',
			'suppress_filters' => true,
			'meta_query'     => [
				[
					'key'   => '_stock_status',
					'value' => 'outofstock',
				],
			],
		] );

		if( empty($the_query->posts) ){
			return $related_posts;
		}

		return array_diff($related_posts, $the_query->posts, $excluded_ids);
	}

	public function get_custom_selected_products($product_id, $related_posts){

		if( ! $this->custom_enabled() ){
			return $related_posts;
		}

		if( $this->same_category_enabled() ){
			return $related_posts;
		}

		$hide_if_empty = apply_filters('reycore/woocommerce/related_products/custom_hide_empty', false);

		if ( $custom_related = $this->get_products_ids($product_id)) {

			if( get_theme_mod('single_product_page_related_custom_replace', true) ){
				$related_posts = $custom_related;
			}

			else {

				if( ! $hide_if_empty ){
					$related_posts = array_unique( $custom_related + $related_posts );
					add_filter('woocommerce_product_related_posts_shuffle', '__return_false');
				}
				else {
					$related_posts = $custom_related;
				}

			}

		}
		else if( $hide_if_empty ) {

			return [];

		}

		return $related_posts;
	}

	public function filter_products_args($args, $type = 'related') {

		if( $cols = get_theme_mod('single_product_page_related_columns', '') ){
			$args['columns'] = $cols;
		}

		if( $per_page = get_theme_mod('single_product_page_related_per_page', '') ){
			$args['posts_per_page'] = $per_page;
		}

		if( $this->supports_carousel() ){

			if( absint($args['posts_per_page']) < 8 ){
				$args['posts_per_page'] = 8;
			}

		}

		if ( $type === 'related' && $this->custom_enabled() && get_theme_mod('single_product_page_related_custom_replace', true) ) {
			$args['orderby'] = 'ID';
		}

		return $args;
	}

	public function output_related_products_args($args) {
		return $this->filter_products_args($args, 'related');
	}

	public function filter_upsells_products_args($args) {

		if( ! $this->is_upsells_enabled() ){
			return $args;
		}

		return $this->filter_products_args($args, 'upsells');
	}

	function add_product_classes($classes){

		if( $this->supports_carousel() ){
			unset($classes['animated-entry']);
		}

		return $classes;
	}

	function add_grid_classes($classes){

		// bail if it's not a carousel
		if( ! $this->supports_carousel() ){
			return $classes;
		}

		$custom_classes = [
			'--prevent-metro',
			'--prevent-thumbnail-sliders', // make sure it does not have thumbnail slideshow
			'--prevent-scattered', // make sure scattered is not applied
			'--prevent-masonry', // make sure masonry is not applied
		];

		return $classes + $custom_classes;
	}

	public function get_products_ids( $product_id = '' ){

		if( ! empty( $this->product_ids ) ){
			return $this->product_ids;
		}

		if( empty($product_id) ){
			$product_id = get_the_ID();
		}

		return get_post_meta($product_id, self::META_KEY, true);
	}

	public function lazy_params( $params ){

		if( 'upsells_products' === $params['filter_title'] ){
			if( ! $this->is_upsells_enabled() ){
				return $params;
			}
		}

		if( $custom_desktop_cols = get_theme_mod('single_product_page_related_columns', '') ){
			$params['desktop'] = $custom_desktop_cols;
		}

		if( $custom_tablet_cols = get_theme_mod('single_product_page_related_columns_tablet', '') ){
			$params['tablet'] = $custom_tablet_cols;
		}

		if( $custom_mobile_cols = get_theme_mod('single_product_page_related_columns_mobile', '') ){
			$params['mobile'] = $custom_mobile_cols;
		}

		if( ($custom_limit = get_theme_mod('single_product_page_related_per_page', '')) ){
			$params['limit'] = $custom_limit;
		}

		if( $this->supports_carousel() ){
			$params['limit'] = $params['desktop'];
			$params['nowrap'] = true;
		}

		return $params;
	}

	public function flush_transients( $controls, $manager ) {

		$checks = [];

		foreach ($controls as $control_key => $value) {
			if( strpos( $control_key, 'single_product_page_related' ) !== false ) {
				$checks[] = true;
			}
		}

		if( in_array(true, $checks, true) ){
			\ReyCore\Helper::clean_db_transient( implode('_', [ \ReyCore\Ajax::AJAX_TRANSIENT_NAME, 'related_products' ]) );
			\ReyCore\Helper::clean_db_transient( implode('_', [ \ReyCore\Ajax::AJAX_TRANSIENT_NAME, 'upsells_products' ]) );
		}

	}

	function before_render( $element )
	{
		if( ! in_array($element->get_unique_name(), ['woocommerce-product-related', 'woocommerce-product-upsell'], true) ){
			return;
		}
		add_filter( 'reycore/woocommerce/product_loop_classes', [$this, 'epro_grid_classes']);
	}

	function after_render( $element )
	{
		if( ! in_array($element->get_unique_name(), ['woocommerce-product-related', 'woocommerce-product-upsell'], true) ){
			return;
		}
		remove_filter( 'reycore/woocommerce/product_loop_classes', [$this, 'epro_grid_classes']);
	}

	function epro_grid_classes($classes){
		return array_merge($classes, [
			'grid_layout' => 'rey-wcGrid-default',
			'--prevent-scattered', // make sure scattered is not applied
			'--prevent-masonry', // make sure masonry is not applied
		]);
	}

	public function custom_enabled(){
		return $this->is_enabled() && get_theme_mod('single_product_page_related_custom', false);
	}

	public function same_category_enabled(){
		return get_theme_mod('single_product_page_related_same_category', false);
	}

	public function is_enabled(){
		return get_theme_mod('single_product_page_related', true);
	}

	public function is_upsells_enabled(){
		return get_theme_mod('single_product_page_related_upsells', true);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Extended Related Products', 'Module name', 'rey-core'),
			'description' => esc_html_x('Extends the related products in product pages, with various customisation options.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/select-custom-related-products/'),
			'video' => true,
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
