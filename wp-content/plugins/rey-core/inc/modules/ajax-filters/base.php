<?php
namespace ReyCore\Modules\AjaxFilters;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	protected $chosen_filters = [];

	const FILTERING_WIDGETS_TRANSIENT_NAME = 'reycore_has_filtering_widgets';

	/**
	 * Initialize the plugin.
	 */
	public function __construct()
	{

		parent::__construct();

		$this->define_constants();
		$this->includes();

		add_action( 'woocommerce_product_query', [ $this, 'woocommerce_product_query'], 10);
		add_action( 'woocommerce_product_query', [ $this, 'woocommerce_product_query__fix_out_of_stock'], 1000);

		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );
		add_action( 'reycore/customizer/panel=woocommerce', [$this, 'load_customizer_options']);
		add_filter( 'update_option_sidebars_widgets', [$this, 'flush_transient__has_filtering_widgets__on_update'], 10, 3);
		add_action( 'reycore/caching_plugins/flush', [$this, 'delete_filtering_widgets_transient']);
		add_action( 'reycore/ajaxfilters/flush_transients', [$this, 'delete_filtering_widgets_transient']);
		add_filter( 'woocommerce_is_filtered', [$this, 'is_filtered']);

		add_action('admin_head', [$this, 'add_widgets_badges_css']);

		new Frontend();
		new ElementorProductsGrid();

	}

	/**
	 * Defind constants for this module.
	 */
	public function define_constants()
	{
		define('REYAJAXFILTERS_PATH', plugin_dir_url( __FILE__ ) );
		define('REYAJAXFILTERS_CACHE_TIME', 60 * 60 * 12 );
	}

	/**
	 * Include required core files.
	 */
	public function includes()
	{
		require_once __DIR__ . '/includes/functions.php';

		$widgets = $this->widgets_list();

		foreach( $widgets as $widget ){
			require_once __DIR__ . "/widgets/{$widget}.php";
		}
	}

	public function load_customizer_options( $base ){
		$base->register_section( new Customizer() );
	}

	/**
	 * Checks for REY Filtering widgets if they exist
	 * in any of the ecommerce sidebars.
	 *
	 * @return bool
	 */
	public function filter_widgets_exist(){

		if( isset($this->__f_w_exists) ){
			return $this->__f_w_exists;
		}

		$t_name = self::FILTERING_WIDGETS_TRANSIENT_NAME;

		if( false !== ($status = get_transient($t_name)) ){
			return ($status === 'yes');
		}

		if( ! ($woo_sidebar = \ReyCore\Plugin::instance()->woocommerce_tags[ 'sidebar' ]) ){
			return false;
		}

		$check = [];

		foreach ($woo_sidebar->default_sidebars() as $sidebar_name) {
			$check[$sidebar_name] = $this->check_sidebar_for_filters($sidebar_name);
		}

		$the_check = in_array(true, $check, true) ? 'yes' : 'no';

		set_transient($t_name, $the_check, MONTH_IN_SECONDS);

		return $this->__f_w_exists = ($the_check === 'yes');
	}

	public function flush_transient__has_filtering_widgets__on_update($old_value, $value, $option){

		if( md5(wp_json_encode($value)) !== md5(wp_json_encode($old_value)) ){
			$this->delete_filtering_widgets_transient();
		}

		return $old_value;
	}

	public function delete_filtering_widgets_transient(){
		delete_transient(self::FILTERING_WIDGETS_TRANSIENT_NAME);
	}

	/**
	 * Some archive links will get formed with "?product-cato|a" / "attro"
	 * For example in Elementor Menu (with Ajax turned ON). Or for brands, with "attro"
	 * This checks if it's such a link.
	 *
	 * @return bool
	 */
	public function check_forced_link_parameters(){

		$check = [];

		// for attribute links
		foreach ($_REQUEST as $key => $value) {
			// attributes
			if( strpos($key, 'attro-') === 0 ){
				$check[] = true;
			}
			// category & OR
			else if( strpos($key, 'product-cato') === 0 ){
				$check[] = true;
			}
			// category & AND
			else if( strpos($key, 'product-cata') === 0 ){
				$check[] = true;
			}
		}

		return in_array(true, $check, true);
	}

	public function widgets_list(){
		return [
			'active-filters',
			'attribute-filter',
			'category-filter',
			'featured-filter',
			'price-filter',
			'sale-filter',
			'search-filter',
			'stock-filter',
			'tag-filter',
			'taxonomy-filter',
			'custom-fields-filter',
		];
	}

	public function register_assets($assets){

		$styles[ 'reycore-ajaxfilter-style' ] = [
			'src'     => REYAJAXFILTERS_PATH . '/assets/css/styles'. reyCoreAssets()::rtl() .'.css',
			'deps'    => [],
			'version'   => REY_CORE_VERSION,
		];

		$styles[ 'reycore-nouislider' ] = [
			'src'     => REYAJAXFILTERS_PATH . '/assets/css/nouislider.css',
			'deps'    => [],
			'version'   => REY_CORE_VERSION,
			'priority' => 'low',
			'plugin' => 'true',
		];

		$styles[ 'reycore-ajaxfilter-dropdown' ] = [
			'src'     => REYAJAXFILTERS_PATH . '/assets/css/drop-down.css',
			'deps'    => [],
			'version'   => REY_CORE_VERSION,
		];

		$styles[ 'reycore-ajaxfilter-droppanel' ] = [
			'src'     => REYAJAXFILTERS_PATH . '/assets/css/drop-panel.css',
			'deps'    => [],
			'version'   => REY_CORE_VERSION,
		];

		$styles[ 'reycore-ajaxfilter-select2' ] = [
			'src'     => REYAJAXFILTERS_PATH . '/assets/css/select2.css',
			'deps'    => [],
			'version'   => REY_CORE_VERSION,
		];

		$assets->register_asset('styles', $styles);

		$scripts = [

			'reycore-ajaxfilter-script' => [
				'src'     => REYAJAXFILTERS_PATH . '/assets/js/scripts.js',
				'deps'    => ['jquery', 'rey-script', 'reycore-scripts'],
				'version'   => REY_CORE_VERSION,
				'localize' => [
					'name' => 'reyajaxfilter_params',
					'params' => apply_filters('reycore/ajaxfilters/js_params', [
						'shop_loop_container'  => '.rey-siteMain .reyajfilter-before-products',
						'not_found_container'  => '.rey-siteMain .reyajfilter-before-products',
						'pagination_container' => '.woocommerce-pagination',
						'extra_containers'     => [
							'.rey-pageCover',
							'.rey-siteMain .rey-breadcrumbs',
							'.rey-siteMain .woocommerce-products-header',
						],
						'animation_type'          => get_theme_mod('ajaxfilter_animation_type', 'default'),
						'sorting_control'         => get_theme_mod('ajaxfilter_product_sorting', true),
						'scroll_to_top'           => get_theme_mod('ajaxfilter_scroll_to_top', true),
						'scroll_to_top_offset'    => get_theme_mod('ajaxfilter_scroll_to_top_offset', 100),
						'scroll_to_top_from'      => get_theme_mod('ajaxfilter_scroll_to_top_from', 'grid'),
						'apply_filter_fixed'      => true,
						'dd_search_threshold'     => 5,
						'prevent_mobile_popstate' => true,
						'page_url'                => reycore__page_url(),
						'minimal_tpl'             => apply_filters('reycore/woocommerce/products/minimal_tpl', true),
						'slider_margin'           => 10,
						'slider_step'             => 1,
						'apply_live_results'      => get_theme_mod('ajaxfilter_apply_filter_live', false),
						'reset_filters_text'      => esc_html__('RESET FILTERS', 'rey-core'),
						'reset_filters_link'      => reycore_wc__reset_filters_link(),
						'filter_params'           => self::filters_url_params_list(),
						'panel_keep_open'         => get_theme_mod('ajaxfilter_panel_keep_open', false),
					]),
				],
			],

			'reycore-nouislider' => [
				'src'     => REYAJAXFILTERS_PATH . '/assets/js/nouislider.min.js',
				'deps'    => ['jquery', 'reycore-ajaxfilter-script'],
				'version'   => '13.0.0',
			],

			'reycore-ajaxfilter-select2' => [
				'src'     => REYAJAXFILTERS_PATH . '/assets/js/select2.min.js',
				'deps'    => ['jquery', 'reycore-ajaxfilter-script'],
				'version'   => '4.0.13',
			],

			'reycore-ajaxfilter-select2-multi-checkboxes' => [
				'src'     => REYAJAXFILTERS_PATH . '/assets/js/select2-multi-checkboxes.js',
				'deps'    => ['reycore-ajaxfilter-select2'],
				'version'   => '1.0.0',
			],

			'reycore-ajaxfilter-droppanel' => [
				'src'     => REYAJAXFILTERS_PATH . '/assets/js/drop-panel.js',
				'deps'    => ['jquery', 'reycore-ajaxfilter-script'],
				'version'   => '1.0.0',
			],

		];

		$assets->register_asset('scripts', $scripts);

	}

	public static function load_scripts(){
		reyCoreAssets()->add_scripts('reycore-ajaxfilter-script');
		reyCoreAssets()->add_styles(['rey-widgets', 'reycore-ajaxfilter-style']);
	}

	public static function filters_url_params_list(){
		return [
			'keyword',
			'product-cata',
			'product-cato',
			'product-taga',
			'product-tago',
			'attra',
			'attro',
			'max-range',
			'min-range',
			'min-price',
			'max-price',
			'in-stock',
			'on-sale',
			'is-featured',
			'rating_filter',
			'product-meta',
		];
	}

	public function is_filtered( $status ){

		$list = self::filters_url_params_list();

		$c = [];

		foreach($_REQUEST as $key => $value){
			if( in_array($key, $list, true) ){
				$c[] = true;
			}
			else {
				if( ! empty( array_filter($list, function($k) use ($key) {
					return strpos($key, $k) === 0;
				} ) ) ){
					$c[] = true;
				}
			}
		}

		if( in_array(true, $c, true) ){
			return true;
		}

		return $status;
	}

	/**
	 * Set chosen filters.
	 *
	 * @since 1.5.4
	 */
	public function set_chosen_filters( $url_query = [] )
	{
		$query = [];

		if( !empty($url_query) ) {
			$query = $url_query;
		}
		else if( isset($_SERVER['QUERY_STRING']) ) {
			$url = $_SERVER['QUERY_STRING'];
			parse_str($url, $query);
		}

		$chosen = [];
		$active_filters = [];

		// keyword
		if (isset($_GET['keyword'])) {
			$keyword = (!empty($_GET['keyword'])) ? $_GET['keyword'] : '';
			$active_filters['keyword'] = $keyword;
		}

		// orderby
		if (isset($_GET['orderby'])) {
			$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : '';
			$active_filters['orderby'] = $orderby;
		}

		// Parse From URL

		/**
		 * Product Category
		 */

		// it's filtering a category
		if ( (($category_filters = reycore__preg_grep_keys('/product-cat/', $query)) && !empty($category_filters)) ) {

			$category_filters_keys = [
				'product-cata' => 'and',
				'product-cato' => 'or',
			];

			$category_filters_key = array_keys( array_intersect_key($category_filters_keys, $category_filters ) )[0];
			$category_filters_query_type = $category_filters_keys[$category_filters_key];

			if( ! empty($category_filters[$category_filters_key]) ) {

				$category_terms = explode(',', $category_filters[ $category_filters_key ]);
				$category_taxonomy = 'product_cat';

				$chosen[$category_taxonomy] = [
					'terms'      => $category_terms,
					'query_type' => $category_filters_query_type
				];

				foreach ($category_terms as $term_id) {
					$active_filters['term'][$category_filters_key][$term_id] = $category_taxonomy;
				}
			}
		}


		/**
		 * Product Tag
		 */
		if ( ($tag_filters = reycore__preg_grep_keys('/product-tag/', $query)) && !empty($tag_filters)) {

			$tag_filters_keys = [
				'product-taga' => 'and',
				'product-tago' => 'or',
			];

			$tag_filters_key = array_keys( array_intersect_key($tag_filters_keys, $tag_filters ) )[0];
			$tag_filters_query_type = $tag_filters_keys[$tag_filters_key];

			if( ! empty($tag_filters[$tag_filters_key]) ) {

				$tag_terms = explode(',', $tag_filters[ $tag_filters_key ]);
				$tag_taxonomy = 'product_tag';

				$chosen[$tag_taxonomy] = array(
					'terms'      => $tag_terms,
					'query_type' => $tag_filters_query_type
				);

				foreach ($tag_terms as $term_id) {
					$active_filters['term'][$tag_filters_key][$term_id] = $tag_taxonomy;
				}
			}
		}

		/**
		 * Product Attributes
		 */
		if ( ($attribute_filters = reycore__preg_grep_keys('/^attr/', $query)) && !empty($attribute_filters)) {

			$attribute_filters_keys = [
				'attra' => 'and',
				'attro' => 'or',
			];

			foreach ($attribute_filters as $akey => $avalue) {

				if( $avalue === '' ){
					continue;
				}

				$attribute_taxonomy_slug = '';
				$attribute_filters_query_type = 'and';

				foreach ($attribute_filters_keys as $k => $v) {
					if( strpos($akey, $k ) !== false ){
						$attribute_filters_query_type = $v;
						$attribute_taxonomy_slug = str_replace( $k . '-', '', $akey );
					}
				}

				if( !empty($attribute_taxonomy_slug) ){

					$attribute_terms = explode(',', $avalue);

					$attribute_taxonomy_slug_clean = wc_sanitize_taxonomy_name ( $attribute_taxonomy_slug );
					$attribute_taxonomy = $attribute_taxonomy_slug_clean;

					if( strpos($attribute_taxonomy_slug_clean, 'pa_') === false ){
						$attribute_taxonomy = wc_attribute_taxonomy_name($attribute_taxonomy_slug_clean);
					}

					$chosen[$attribute_taxonomy] = array(
						'terms'      => $attribute_terms,
						'query_type' => $attribute_filters_query_type
					);

					foreach ($attribute_terms as $term_id) {
						$active_filters['term'][$akey][$term_id] = $attribute_taxonomy;
					}
				}
			}
		}

		/**
		 * Range Attributes
		 */

		if ( ($range_attribute_filters = reycore__preg_grep_keys('/^max-range-/', $query)) && !empty($range_attribute_filters)) {
			foreach ($range_attribute_filters as $akey => $term) {
				if( strpos($akey, 'max-range' ) !== false ){
					$attribute_taxonomy_slug = wc_sanitize_taxonomy_name ( str_replace( 'max-range-', '', $akey ) );
					$attribute_taxonomy = $attribute_taxonomy_slug;

					if( strpos($attribute_taxonomy_slug, 'pa_') === false ){
						$attribute_taxonomy = wc_attribute_taxonomy_name($attribute_taxonomy_slug);
					}

					$chosen[$attribute_taxonomy]['range_max'] = floatval($term);
					$active_filters['range_max'][$attribute_taxonomy] = $term;
				}
			}
		}

		if ( ($range_attribute_filters = reycore__preg_grep_keys('/^min-range-/', $query)) && !empty($range_attribute_filters)) {
			foreach ($range_attribute_filters as $akey => $term) {
				if( strpos($akey, 'min-range' ) !== false ){
					$attribute_taxonomy_slug = wc_sanitize_taxonomy_name ( str_replace( 'min-range-', '', $akey ) );
					$attribute_taxonomy = $attribute_taxonomy_slug;

					if( strpos($attribute_taxonomy_slug, 'pa_') === false ){
						$attribute_taxonomy = wc_attribute_taxonomy_name($attribute_taxonomy_slug);
					}

					$chosen[$attribute_taxonomy]['range_min'] = floatval($term);
					$active_filters['range_min'][$attribute_taxonomy] = $term;
				}
			}
		}

		// Custom taxonomies
		$custom_taxonomies = $this->get_registered_taxonomies();

		foreach ($custom_taxonomies as $taxonomy) {

			$tid = str_replace('-', '', sanitize_title( $taxonomy['name'] ));

			if ( ($ctax_filters = reycore__preg_grep_keys("/product-{$tid}/", $query)) && !empty($ctax_filters)) {

				$ctax_filters_keys = [
					"product-{$tid}a" => "and",
					"product-{$tid}o" => "or",
				];

				$ctax_filters_key = array_keys( array_intersect_key($ctax_filters_keys, $ctax_filters ) )[0];
				$ctax_filters_query_type = $ctax_filters_keys[$ctax_filters_key];

				$ctax_terms = explode(',', $ctax_filters[ $ctax_filters_key ]);
				$ctax_taxonomy = $taxonomy['id'];

				$chosen[$ctax_taxonomy] = array(
					'terms'      => $ctax_terms,
					'query_type' => $ctax_filters_query_type
				);

				foreach ($ctax_terms as $term_id) {
					$active_filters['term'][$ctax_filters_key][$term_id] = $ctax_taxonomy;
				}
			}
		}

		// min-price
		if (isset($_GET['min-price'])) {
			$active_filters['min_price'] = $_GET['min-price'];
		}

		// max-price
		if (isset($_GET['max-price'])) {
			$active_filters['max_price'] = $_GET['max-price'];
		}

		if (isset($_GET['in-stock']) ) {
			if( 0 === absint($_GET['in-stock']) ) {
				$active_filters['in-stock'] = 0;
			}
			else if( 1 === absint($_GET['in-stock']) ) {
				$active_filters['in-stock'] = 1;
			}
			else if( 2 === absint($_GET['in-stock']) ) {
				$active_filters['in-stock'] = 2;
			}
		}

		if ( $this->query_on_sale() ) {
			$active_filters['on-sale'] = 1;
		}

		if ( $this->query_is_featured() ) {
			$active_filters['is-featured'] = 1;
		}

		// Product Meta
		if ( isset($query['product-meta']) && !empty($query['product-meta']) && $mq_hashes = reycore__clean($query['product-meta']) ) {
			$chosen['product-meta'] = explode(',', $mq_hashes);
			$active_filters['product-meta'] = $chosen['product-meta'];
		}

		$this->chosen_filters = wc_clean( apply_filters('reycore/ajaxfilters/chosen_filters', [
			'chosen'         => $chosen,
			'active_filters' => $active_filters
		]) );

	}

	public function get_chosen_filters(){
		return $this->chosen_filters;
	}

	/**
	 * Filtered product ids for given terms.
	 *
	 * @return array
	 */
	public function query_for_tax( $q = null )
	{
		$tax_query = [];

		if( empty($this->chosen_filters) ){
			return $tax_query;
		}

		$chosen_filters = $this->chosen_filters['chosen'];

		// 99% copy of WC_Query
		if (is_array($chosen_filters) && sizeof($chosen_filters) > 0) {

			global $wp_query;
			$main_query = $wp_query->is_main_query();

			$tax_query = [
				'relation' => 'AND',
			];

			foreach ( $chosen_filters as $taxonomy => $data ) {

				if( isset($data['terms']) && !empty($data['terms']) ){

					$tq = [
						'taxonomy'         => $taxonomy,
						'field'            => 'term_id',
						'terms'            => array_map('absint', array_unique($data['terms'])),
						'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
						'include_children' => false,
					];

					if( $taxonomy === 'product_cat' ){

						$tq['include_children'] = true;

						// Different scenarios when in categories
						if( is_product_category() && count($tq['terms']) > 1 ){

							$current_cat_id = get_queried_object_id();

							// This forces the main query to allow multiple categories,
							// including the current one (not just the current one)
							if( $q && empty( get_term_children( $current_cat_id , 'product_cat' ) ) ){
								unset($q->query_vars['product_cat']);
							}

							// exclude ancestors when in category
							else {

								$tq['terms'] = array_filter($tq['terms'], function($id) use ($current_cat_id){
									$anc = get_ancestors($id, 'product_cat');
									return ! empty($anc) && in_array( $current_cat_id, $anc, true );
								});
							}
						}

					}

					$tax_query[] = $tq;
				}

				else {

					if( !(isset($data['range_max']) || isset($data['range_min'])) ){
						continue;
					}

					$get_ranges_terms = get_terms([
						'taxonomy' => $taxonomy,
						'hide_empty' => true,
					] );

					$clean_range_terms = wp_list_pluck($get_ranges_terms, 'name', 'term_id');

					$__ranges = array_filter($clean_range_terms, function($item) use ($data) {

						$cond = [];

						if( isset($data['range_max']) && $max = $data['range_max'] ){
							$cond[] = floatval($item) <= floatval($max);
						}

						if( isset($data['range_min']) && $min = $data['range_min'] ){
							$cond[] = floatval($item) >= floatval($min);
						}

						return ! in_array(false, $cond, true);
					});

					if( empty($__ranges) ){
						continue;
					}

					$tax_query[] = [
						'taxonomy'         => $taxonomy,
						'field'            => 'term_id',
						'terms'            => array_keys($__ranges),
						'operator'         => 'IN',
						'include_children' => false,
					];
				}
			}

			$product_visibility_terms  = wc_get_product_visibility_term_ids();
			$product_visibility_not_in = array( is_search() && $main_query ? $product_visibility_terms['exclude-from-search'] : $product_visibility_terms['exclude-from-catalog'] );

			// Hide out of stock products.
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$product_visibility_not_in[] = $product_visibility_terms['outofstock'];
			}

			if ( ! empty( $product_visibility_not_in ) ) {
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_not_in,
					'operator' => 'NOT IN',
				);
			}

		}

		return array_filter( $tax_query );
	}

	public function query_for_post__in(){

		$post__in = [];

		if ( $this->query_on_sale() ) {
			$post__in = array_merge( $post__in, $this->onsale_products() );
		}

		if ( $this->query_is_featured() ) {
			$post__in = array_merge( $post__in, wc_get_featured_product_ids() );
		}

		return apply_filters('reycore/ajaxfilters/query_post__in', $post__in);

	}

	public function query_on_sale(){
		return apply_filters('reycore/ajaxfilters/query/on_sale', isset($_GET['on-sale']) && 1 === absint($_GET['on-sale']) );
	}

	public function query_is_featured(){
		return apply_filters('reycore/ajaxfilters/query/featured', isset($_GET['is-featured']) && 1 === absint($_GET['is-featured']) );
	}

	/**
	 * OK
	 * Query for meta that should be set to the main query.
	 *
	 * @return array
	 */
	public function query_for_meta( $custom_args = [] )
	{
		$meta_query = [];

		$_req_args = $_REQUEST;

		if( ! empty($custom_args) ){
			$_req_args = $custom_args;
		}

		// rating filter
		if (isset($_req_args['min_rating'])) {
			$meta_query[] = [
				'key'           => '_wc_average_rating',
				'value'         => isset($_req_args['min_rating']) ? floatval(reycore__clean($_req_args['min_rating'])) : 0,
				'compare'       => '>=',
				'type'          => 'DECIMAL',
				'rating_filter' => true,
			];
		}

		if( isset($_req_args['in-stock']) ){
			$meta_query = reyajaxfilter_meta_query_stock($meta_query, absint($_req_args['in-stock']));
		}

		if (isset($_req_args['min-price']) || isset($_req_args['max-price'])) {

			// $price_range = $this->get_price_range();
			$step = max( apply_filters( 'woocommerce_price_filter_widget_step', 1 ), 1 );
			$step = 1;

			$min_price = (!empty($_req_args['min-price'])) ? absint($_req_args['min-price']) : 0;
			$max_price = (!empty($_req_args['max-price'])) ? absint($_req_args['max-price']) : 0;

			if( $min_price !== $max_price ) {

				// Check to see if we should add taxes to the prices if store are excl tax but display incl.
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

				if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
					$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
					$tax_rates = \WC_Tax::get_rates( $tax_class );

					if ( $tax_rates ) {
						$min_price += \WC_Tax::get_tax_total( \WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
						$max_price += \WC_Tax::get_tax_total( \WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
					}
				}

				$min_price = apply_filters( 'woocommerce_price_filter_widget_min_amount', floor( $min_price / $step ) * $step, $min_price, $step );
				$max_price = apply_filters( 'woocommerce_price_filter_widget_max_amount', ceil( $max_price / $step ) * $step, $max_price, $step );

				// get max price from range
				if( ! (bool) $max_price ){
					if( ($prices = self::get_prices_range(['avoid_recursiveness' => true])) && isset($prices['max_price']) ) {
						$max_price = ceil( floatval( wp_unslash( $prices['max_price'] ) ) / $step ) * $step;
					}
				}

				// the_prices_range
				$meta_query[] = [
					'key'          => '_price',
					'value'        => [ $min_price, $max_price ],
					'type'         => 'numeric',
					'compare'      => 'BETWEEN',
					'price_filter' => true,
				];

			}
		}

		if ( isset($_req_args['product-meta']) && !empty($_req_args['product-meta']) && $mq_hashes = reycore__clean($_req_args['product-meta']) ) {

			$active_hashes = explode(',', $mq_hashes);

			foreach ($active_hashes as $hash) {

				if( ($rmq = reyajaxfilter_get_registered_meta_query($hash)) && !empty($rmq) ){
					$meta_query['rey-product-meta'] = $rmq;
				}

			}
		}

		return apply_filters('reycore/ajaxfilters/products/meta_query', $meta_query);
	}

	/**
	 * Get filtered min price for current products.
	 *
	 * @return int
	 */
	public static function get_prices_range($args = []) {

		global $wpdb, $wp_query;

		$tax_query  = reyajaxfilter_tax_query();

		$meta_query_args = [];

		if( isset($args['avoid_recursiveness']) && $args['avoid_recursiveness']){
			$meta_query_args = [
				'surpress_filter' => true
			];
		}

		$meta_query  = reyajaxfilter_meta_query($meta_query_args);

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new \WP_Meta_Query( $meta_query );
		$tax_query  = new \WP_Tax_Query( $tax_query );
		$search = reyajaxfilter_search_query();

		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';

		$sql = "
			SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
			)';

		$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

		return (array) $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
	}

	/**
	 * Retrive Product ids for given keyword.
	 *
	 * @return array
	 */
	public function query_for_keyword()
	{
		if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
			return wc_clean( $_GET['keyword'] );
		}

		return '';
	}

	/**
	 * Set filter.
	 *
	 * @param wp_query $q
	 */
	public function woocommerce_product_query( $q )
	{

		if( ! ( $this->filter_widgets_exist() || $this->check_forced_link_parameters() ) ){
			return;
		}

		$this->set_chosen_filters();

		if( $meta_query = $this->query_for_meta() ){
			$q->set( 'meta_query', $meta_query );
		}

		if( $tax_query = $this->query_for_tax($q) ){
			$q->set( 'tax_query', $tax_query );
		}

		if( $post__in = $this->query_for_post__in() ){
			$q->set('post__in', $post__in);
		}

		if( isset($this->chosen_filters['active_filters']['keyword']) && $keyword = reycore__clean($this->chosen_filters['active_filters']['keyword']) ){
			$q->set('s', $keyword);
		}

		/**
		 * Scenario: while in a custom taxonomy,
		 * make sure to show products of all selected tax terms (not just the active tax)
		 */

		// if( $q->is_tax() && ($current_tax = $q->get_queried_object()) && isset($current_tax->taxonomy) ){
		// 	if( ! in_array( $current_tax->taxonomy, apply_filters('reycore/ajaxfilters/tax_reset_query', [ 'product_cat' ]) , true ) ){
		// 		$q->set($current_tax->taxonomy, '');
		// 	}
		// }

	}

	public function query_for_post__not_in(){

		$p = [];

		if( $out_of_stock_variations = $this->get_out_of_stock_variations() ){
			$p = $out_of_stock_variations;
		}

		return apply_filters('reycore/ajaxfilters/query_posts_not_in', $p);
	}

	/**
	 * Fifx out of stock products when filtering.
	 *
	 * @param object $q
	 * @return void
	 */
	public function woocommerce_product_query__fix_out_of_stock( $q )
	{
		if( ! ( $this->filter_widgets_exist() || $this->check_forced_link_parameters() ) ){
			return;
		}

		if( ( $post__not_in = $this->query_for_post__not_in() ) && !empty($post__not_in) ){

			$q->set('post__not_in', $post__not_in);

			// make sure to exclude not-in's from post__in
			if( $post_in = $q->get('post__in') ){
				$q->set('post__in', array_diff($post_in, $post__not_in) );
			}

		}
	}

	public function get_out_of_stock_variations( $force_chosen_filters = false ){

		global $wpdb;

		$results = [];

		if( ! get_theme_mod('ajaxfilters_exclude_outofstock_variables', false) ){
			return $results;
		}

		if( apply_filters('reycore/ajaxfilters/oos_variations/check_if_woo_hide_oos_items', true) ){
			if( 'yes' !== get_option( 'woocommerce_hide_out_of_stock_items' ) ){
				return $results;
			}
		}

		$maybe_continue = [
			$force_chosen_filters
		];

		$chosen_filters = [];

		if( isset($this->chosen_filters['chosen']) && ($chosen_filters = $this->chosen_filters['chosen']) ){
			$maybe_continue[] = true;
		}

		if( isset($_GET['on-sale']) && 1 === absint($_GET['on-sale']) ){
			$maybe_continue[] = true;
		}

		if( ! in_array(true, $maybe_continue, true) ){
			return $results;
		}

		$query_args = [
			'post_type'     => 'product_variation',
			'meta_query'    => [
				'relation' => 'AND',
				[
					'relation' => 'OR',
					[
						'key'     => '_stock_status',
						'value'   => 'outofstock',
						'compare' => 'IN',
					],
					[
						'key'     => '_stock',
						'value'   => '0',
						'compare' => 'IN',
					],
				],
			],
			'fields'         => 'id=>parent',
			'posts_per_page' => apply_filters('reycore/ajaxfilters/out_of_stock_variations/limit', -1 ),
			'groupby'        => 'post_parent',
		];

		$meta_query = [];

		/**
		 * @note Intentionally set as Meta Query vs. Tax Query.
		 */
		foreach ($chosen_filters as $wc_tax => $value) {

			$taxonomy = $wc_tax;

			if( $wc_tax !== 'product_cat' ){
				$taxonomy = 'attribute_' . $wc_tax;
			}

			foreach ($value['terms'] as $term_id) {

				$term_object = get_term_by('id', $term_id, $wc_tax);

				if( isset($term_object->slug) ){
					$meta_query[] = [
							'key'     => $taxonomy,
							'value'   => $term_object->slug,
							'compare' => 'IN',
					];
				}
			}
		}

		if( !empty($meta_query) ){
			$meta_query['relation'] = 'OR';
			$query_args['meta_query'][] = $meta_query;
		}

		$q = new \WP_Query( apply_filters('reycore/ajaxfilters/out_of_stock_variations/query_args', $query_args, $chosen_filters ) );

		if( empty($q->posts) ){
			return $results;
		}

		$results = wp_list_pluck($q->posts, 'post_parent');

		return $results;
	}

	public function onsale_products($args = []){

		$args = wp_parse_args($args, [
			'force_chosen_filters' => false,
			'out_of_stock_variations' => true,
		]);

		$ids = wc_get_product_ids_on_sale();

		if($args['out_of_stock_variations']){
			$ids = array_diff( $ids, $this->get_out_of_stock_variations($args['force_chosen_filters']) );
		}

		return array_map('absint', array_unique( apply_filters('reycore/ajaxfilters/product_ids_on_sale', $ids) ) );
	}

	/**
	 * Get active filters
	 *
	 * @since 1.0.0
	 */
	public function get_active_filters(){

		if( isset($this->__get_active_filters) ){
			return $this->__get_active_filters;
		}

		$total_filters = 0;

		if( empty($this->chosen_filters) ){
			return $this->__get_active_filters = $total_filters;
		}

		$active_filters = $this->chosen_filters['active_filters'];

		if( ! apply_filters('reycore/ajaxfilters/active_filters/order_display', false) ){
			unset($active_filters['orderby']);
		}

		if( isset($active_filters['term']) && $active_filters['term'] > 1 ){
			$term_count = count($active_filters['term']);
			unset($active_filters['term']);
			return $this->__get_active_filters = count($active_filters) + $term_count;
		}

		return $this->__get_active_filters = count($active_filters);
	}

	public function check_sidebar_for_filters( $name ) {

		if( ! is_active_sidebar($name) ) {
			return false;
		}

		foreach( $this->widgets_list() as $widget ){
			//TODO Check for more performant way
			if( ($sidebar = is_active_widget( false, false, 'reyajfilter-' . $widget )) && $sidebar === $name ){
				return true;
			}
		}

		return false;
	}

	public function get_registered_taxonomies(){
		return apply_filters('reycore/ajaxfilters/registered_taxonomies', get_theme_mod('ajaxfilters_taxonomies', []));
	}

	public function should_hide_widget( $instance ){

		// Solution to disable Woo's Attribute lookup table when in Elementor edit mode
		// to avoid getting the `PHP Warning:  Attempt to read property "query_vars" on null in /../plugins/woocommerce/includes/class-wc-query.php on line 852`
		if(
			isset($instance['attr_name']) &&
			! isset(\WC_Query::get_main_query()->query_vars) &&
			class_exists('\Elementor\Plugin') &&
			is_callable( '\Elementor\Plugin::instance' ) &&
			( reycore__elementor_edit_mode() )
		){
			add_filter('option_woocommerce_attribute_lookup_enabled', '__return_false');
		}

		// bail if set to exclude on certain category
		if( !empty($instance['show_only_on_categories']) ) {
			$show_hide = $instance['show_hide_categories'];

			if ( $show_hide === 'hide' && is_tax( 'product_cat', $instance['show_only_on_categories'] ) ){
				return true;
			}
			elseif ( $show_hide === 'show' && !is_tax( 'product_cat', $instance['show_only_on_categories'] ) ){
				return true;
			}
		}

		if( isset($instance['selective_display']) && ($selective_display = array_filter( (array) $instance['selective_display']) ) ){

			if( ! empty($selective_display) ){

				$conditions = [];

				if( in_array('shop', $selective_display, true) ){
					if( is_shop() && ! is_search() ){
						$conditions['shop'] = true;
					}
				}

				if( in_array('cat', $selective_display, true) ){
					if( is_product_category() ){
						$conditions['cat'] = true;
					}
				}

				if( in_array('attr', $selective_display, true) ){
					if( is_product_taxonomy() ){
						$conditions['attr'] = true;
					}
				}

				if( in_array('tag', $selective_display, true) ){
					if( is_product_tag() ){
						$conditions['tag'] = true;
					}
				}

				if( in_array('search', $selective_display, true) ){
					if( is_search() ){
						$conditions['search'] = true;
					}
				}

				// legacy
				if( in_array('cat_attr_tag', $selective_display, true) ){
					if( is_product_category() || is_product_taxonomy() || is_product_tag() ){
						$conditions['cat_attr_tag'] = true;
					}
				}

				if( in_array(true, $conditions, true) ){
					return false;
				}

				return true;

			}

		}

		return apply_filters('reycore/ajaxfilters/should_hide_widget', false, $instance);
	}

	public function register_actions( $ajax_manager ){
		$ajax_manager->register_ajax_action( 'filter_get_applied_products', [$this, 'ajax__get_applied_products'], [
			'auth'   => 3,
			'nonce'  => false,
		] );
	}

	public function ajax__get_applied_products( $action_data ){

		if( ! get_theme_mod('ajaxfilter_apply_filter_live', false) ){
			return;
		}

		if ( ! ( isset($action_data['url']) && $url = reycore__clean(esc_url_raw($action_data['url'])) ) ){
			return;
		}

		if( $url ){
			$url = parse_url($url, PHP_URL_QUERY);
			parse_str($url, $url_query);
		}

		$this->set_chosen_filters( $url_query );

		$chosen_filters = $this->chosen_filters;

		$query_args = [];

		if( $meta_query = $this->query_for_meta( $url_query ) ){
			$query_args['meta_query'] = $meta_query;
		}

		if( $tax_query = $this->query_for_tax() ){
			$query_args['tax_query'] = $tax_query;
		}

		if( $post__in = $this->query_for_post__in() ){
			$query_args['post__in'] = $post__in;
		}

		/**
		 * Scenario: while in a custom taxonomy,
		 * make sure to show products of all selected tax terms (not just the active tax)
		 */
		// if( is_tax() && ($current_tax = get_queried_object()) && isset($current_tax->taxonomy) ){
		// 	if( ! in_array( $current_tax->taxonomy, apply_filters('reycore/ajaxfilters/tax_reset_query', [ 'product_cat' ]) , true ) ){
		// 		$query_args[$current_tax->taxonomy] = '';
		// 	}
		// }

		$query = new \WP_Query(array_merge([
			'post_status' => 'publish',
			'post_type'   => 'product',
			'fields'      => 'ids',
		], $query_args));

		if( ! isset($query->found_posts) ){
			return;
		}

		return $query->found_posts;
	}

	function add_widgets_badges_css(){

		if( ! reycore__get_props('branding') ){
			return;
		}

		$current_screen = get_current_screen();

		if( ! (isset($current_screen->id) && 'widgets' === $current_screen->id) ){
			return;
		}

		printf('<style>.widget[id*="reyajfilter"] .widget-title h3, .widget[id*="rey_woocommerce_product_categories"] .widget-title h3 { padding-right: 0px; }
		.widget[id*="reyajfilter"] .widget-title h3:after, .widget[id*="rey_woocommerce_product_categories"] .widget-title h3:after { content: \'%s\'; padding: 0.2em 0.5em 0.3em; font-size: 9px; color: #fff; background-color: rgba(218, 41, 28, 0.35); border-radius: 2px; margin-left: auto; font-family: Helvetica, Verdana, sans-serif; float: right; }</style>', REY_CORE_THEME_NAME);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Ajax Filters for WooCommerce', 'Module name', 'rey-core'),
			'description' => esc_html_x('Provides Ajax filtering capability for Shop and catalog pages.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['Product catalog'],
			'help'        => reycore__support_url('kb/ajax-filter-widgets/'),
			'video'       => true,
		];
	}

	public function module_in_use(){


		$in_use = false;

		foreach (wp_get_sidebars_widgets() as $sidebar_widgets) {
			foreach ($sidebar_widgets as $widget) {
				if( strpos($widget, 'reyajfilter-') === 0 ){
					$in_use = true;
					break;
				}
			}
			if( $in_use ){
				break;
			}
		}

		return $in_use;
	}

}
