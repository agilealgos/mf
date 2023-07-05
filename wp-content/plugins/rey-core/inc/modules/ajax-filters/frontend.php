<?php
namespace ReyCore\Modules\AjaxFilters;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Frontend {

	public function __construct()
	{

		// Grid
		add_action( 'woocommerce_before_shop_loop', [$this, 'before_products_holder'], 0);
		add_action( 'woocommerce_after_shop_loop', [$this, 'after_products_holder'], 200);
		add_action( 'woocommerce_no_products_found', [$this, 'before_no_products'], 5);
		add_action( 'woocommerce_no_products_found', [$this, 'after_no_products'], 20);
		add_filter( 'paginate_links', [$this, 'paginate_links']);

		// Misc.
		add_filter( 'reycore/filters/btn_class', [$this, 'panel_button_class']);
		add_action( 'reycore/woocommerce/filters/panel__button', [ $this, 'panel_button']);
		add_action( 'reycore/woocommerce/filters/panel__close_button', [ $this, 'panel_close_button']);
		add_action( 'reycore/filters_sidebar/before_panel', [ $this, 'apply_button_filter_panel_sidebar' ] );
		add_action( 'dynamic_sidebar_after', [ $this, 'apply_button_filter_general_sidebar' ], 200 );
		add_action( 'reycore/woocommerce/filters/top_bar__button', [ $this, 'top_bar_button']);
		add_filter( 'reycore/woocommerce/brands/url', [$this, 'brand_url'], 10, 3);
		add_action( 'reycore/loop_products/before_header_end', [$this, 'active_filters_top'] );
		add_filter( 'rey/woocommerce/loop/header_classes', [$this, 'active_filters_top_classes']);
		add_action( 'reycore/filters_sidebar/panel_header', [$this, 'add_panel_reset_button'] );

		// Elementor
		add_filter( 'reycore/elementor/menu/product_categories_skin/render_menu', [$this, 'elementor_menu_cat_skin_html'], 10, 4);
		add_action( 'elementor/element/reycore-menu/section_settings/before_section_end', [ $this, 'elementor_menu_cat_skin_option' ], 20 );
	}

	public function panel_button_class($classes){

		if( Base::instance()->get_active_filters() ){
			$classes[] = '--has-filters';
		}

		return $classes;
	}

	public function panel_button(){

		if( $count = Base::instance()->get_active_filters() ){
			printf('<span class="rey-filterBtn__count" data-count="%d"></span>', absint($count));
		}

	}

	public function panel_close_button(){

		if( ! Base::instance()->get_active_filters() ){
			return;
		}

		printf(
			'<button class="rey-filterBtn__reset js-rey-filter-reset" data-rey-tooltip="%2$s" data-location="%1$s" aria-label="%2$s">%3$s</button>',
			reycore_wc__reset_filters_link(),
			esc_html__('RESET FILTERS', 'rey-core'),
			reycore__get_svg_icon(['id' => 'close'])
		);

	}

	public function apply_button_filter_panel_sidebar(){

		if( ! Base::instance()->filter_widgets_exist() ){
			return;
		}

		if(
			get_theme_mod('ajaxfilter_apply_filter', false) &&
			reycore_wc__check_filter_panel()
		){
			$this->print_apply_filters_button('filter-panel');
		}

		reyCoreAssets()->add_styles('reycore-side-panel');

	}

	public function apply_button_filter_general_sidebar( $index ){

		if( ! Base::instance()->filter_widgets_exist() ){
			return;
		}

		if( ! get_theme_mod('ajaxfilter_apply_filter', false) ){
			return;
		}

		if(
			($index === 'filters-top-sidebar' && reycore_wc__check_filter_sidebar_top()) ||
			($index === 'shop-sidebar' && reycore_wc__check_shop_sidebar()) ||
			// checks for custom sidebars
			($index !== 'filters-top-sidebar' && strpos($index, 'filters-top-sidebar') === 0) ||
			($index !== 'shop-sidebar' && strpos($index, 'shop-sidebar') === 0)
		){
			$this->print_apply_filters_button($index);
		}

	}

	public function print_apply_filters_button($sidebar_id){

		if( is_admin() ){
			return;
		}

		$btn_style = 'btn-primary';

		if( $sidebar_id === 'filters-top-sidebar' ){
			$btn_style = 'btn-line-active';
		}

		printf(
			'<div class="rey-applyFilters-btn-wrapper" id="rey-applyFilters-btn-wrapper-%2$s"><button class="rey-applyFilters-btn js-rey-applyFilters-btn btn %3$s  --disabled"><span class="rey-applyFilters-btnText">%1$s</span><span class="rey-lineLoader"></span></button></div>',
			get_theme_mod('ajaxfilter_apply_filter_text', esc_html__('Apply Filters', 'rey-core')),
			$sidebar_id,
			$btn_style
		);

	}


	public function top_bar_button(){

		if( ! Base::instance()->get_active_filters() ){
			echo reycore__get_svg_icon(['id' => 'sliders']);
			return;
		}

		printf(
			'<button class="rey-filterTop-reset js-rey-filter-reset" data-rey-tooltip="%2$s" data-location="%1$s" aria-label="%2$s">%3$s</button>',
			reycore_wc__reset_filters_link(),
			esc_html__('RESET FILTERS', 'rey-core'),
			reycore__get_svg_icon(['id' => 'close'])
		);
	}

	public function brand_url( $brand_url, $brand_attribute_name, $url ){

		if( ! Base::instance()->filter_widgets_exist() ){
			return $brand_url;
		}

		$attribute_taxonomy = $brand_attribute_name;

		if( strpos($brand_attribute_name, 'pa_') === false ){
			$attribute_taxonomy = wc_attribute_taxonomy_name($brand_attribute_name);
		}

		$brand_id = wc_get_product_terms( get_the_ID(), $attribute_taxonomy, [ 'fields' => 'ids' ] );

		if( isset($brand_id[0]) ){
			$brand_url = $url . '?attro-'. $brand_attribute_name .'='. $brand_id[0];
		}

		return $brand_url;
	}

	public function elementor_menu_cat_skin_option($element){

		$element->add_control(
			'reycore_ajaxify',
			[
				'label' => __( 'Filter capability?', 'rey-core' ),
				'description' => __( 'Adds the ability to filter product results inside the page where this Menu widget is published (ideally inside a Cover Global Section).', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'_skin' => 'product-categories',
				],
			]
		);
	}

	public function elementor_menu_cat_skin_html( $html, $cats, $settings, $element )
	{

		if( !(isset($settings['reycore_ajaxify']) && $settings['reycore_ajaxify'] === 'yes') ){
			return $html;
		}

		if( ! ( is_tax('product_cat') || is_shop() ) ){
			return $html;
		}

		if( ! Base::instance()->filter_widgets_exist() ){
			return $html;
		}

		reyCoreAssets()->add_scripts('reycore-ajaxfilter-script');
		reyCoreAssets()->add_scripts( $element->rey_get_script_depends() );

		$html = sprintf('<ul class="reyEl-menu-nav rey-navEl reyajfilter-ajax-term-filter --menuHover-%2$s" id="reyajfilter-ajax-term-filter-%1$s">', $element->get_id(), $settings['hover_style']);

		if(  $settings['pcat_type'] === 'all' ){

			$orderby = 'name';

			if( isset($settings['orderby']) ){
				$orderby = $settings['orderby'];
			}

			$cats = reycore_wc__product_categories( [
				'hide_empty' => $settings['hide_empty'] === 'yes',
				'orderby' => $orderby,
				'hide_uncategorized' => $settings['hide_uncateg'] !== ''
			] );

			if( $settings['all_button'] !== '' ){
				$html .= sprintf(
					'<li class="menu-item %3$s"><a href="%2$s" class="js-reset-filter" data-location="%2$s"><span>%1$s</span></a></li>',
					$settings['all_button_text'],
					get_permalink( wc_get_page_id('shop') ),
					is_shop() && ! (isset($_REQUEST['product-cato']) || isset($_REQUEST['product-cata'])) ? 'current-menu-item' : ''
				);
			}
		}

		$current_qo = get_queried_object();

		foreach ($cats as $i => $cat) {

			$get_term_by = 'slug';

			if( in_array( $settings['pcat_type'], ['manual_order'], true ) ){
				$get_term_by = 'id';
			}

			$term = get_term_by($get_term_by, $i, 'product_cat' );
			$term_id = isset($term->term_id) ? $term->term_id : 0;

			$attributes = 'data-key="product-cato" data-taxonomy-type="product_cat" data-value="'. $term_id .'"';

			$is_active = isset($current_qo->$get_term_by) && $current_qo->$get_term_by === $i;

			if( ($chosen_filters = Base::instance()->get_chosen_filters()) && isset($chosen_filters['chosen']['product_cat']['terms']) &&
				in_array($term_id, $chosen_filters['chosen']['product_cat']['terms'] )
			){
				$is_active = true;
			}

			$term_link = get_term_link( $i, 'product_cat' );

			$html .= sprintf(
				'<li class="menu-item %3$s"><a href="%2$s" %4$s><span>%1$s</span></a></li>',
				$cat,
				(is_wp_error($term_link) ? $term_link : ''),
				$is_active ? 'current-menu-item' : '',
				$attributes
			);
		}

		$html .= '</ul>';

		return $html;
	}

	public function add_panel_reset_button(){
		printf('<button class="rey-filterPanel__reset btn btn-line-active js-rey-filter-reset" data-location="%2$s" aria-label="%1$s">%1$s</button>', esc_html__('RESET FILTERS', 'rey-core'), reycore_wc__reset_filters_link());
	}

	public function active_filters_top(){

		if( ! Base::instance()->filter_widgets_exist() ){
			return;
		}

		if( get_theme_mod('ajaxfilter_active_position', '') === '' ){
			return;
		}

		the_widget( 'REYAJAXFILTERS_Active_Filters_Widget', array(
			'title' => '',
			'button_text' => get_theme_mod('ajaxfilter_active_clear_text', esc_html__('Clear all', 'rey-core')),
		));
	}

	public function active_filters_top_classes( $classes ){

		if( ! Base::instance()->filter_widgets_exist() ){
			return $classes;
		}

		if( ($pos = get_theme_mod('ajaxfilter_active_position', '')) === '' ){
			return $classes;
		}

		$classes['active_filter_pos'] = '--active-pos-' . $pos;

		return $classes;
	}


	private function __supports_product_holder(){

		if( ! wc_get_loop_prop( 'is_paginated' ) ){
			return;
		}

		if( ! ( Base::instance()->filter_widgets_exist() || Base::instance()->check_forced_link_parameters() ) ){
			return;
		}

		return apply_filters('reycore/ajax_filters/supports_product_holder', true);
	}

	/**
	 * HTML wrapper to insert before the shop loop.
	 *
	 * @return string
	 */
	public function before_products_holder()
	{

		if( ! $this->__supports_product_holder() ){
			return;
		}

		$anim_type = get_theme_mod('ajaxfilter_animation_type', 'default');

		printf('<div class="reyajfilter-before-products %s">', '--anim-' . esc_attr($anim_type));

		if( $anim_type === 'default' ){
			echo '<div class="reyajfilter-updater --vis-hidden"><div class="rey-lineLoader"></div></div>';
		}

	}


	/**
	 * HTML wrapper to insert after the shop loop.
	 *
	 * @return string
	 */
	public function after_products_holder()
	{
		if( ! $this->__supports_product_holder() ){
			return;
		}
		echo '</div><!-- .reyajfilter-before-products -->';
	}

	/**
	 * HTML wrapper to insert before the not found product loops.
	 */
	public function before_no_products() {
		$this->before_products_holder();
	}

	/**
	 * HTML wrapper to insert after the not found product loops.
	 */
	public function after_no_products() {

		if( ! $this->__supports_product_holder() ){
			return;
		}

		$this->after_products_holder();

		// Check if filters are active
		if( ! Base::instance()->get_active_filters() ){
			return;
		}

		// Show buttons to refine filtering
		if( reycore_wc__check_filter_panel() ) {

			reycore__get_template_part('template-parts/woocommerce/filter-panel-button-not-found');

			reyCoreAssets()->add_scripts(['reycore-wc-loop-filter-panel']);

			// force filter panel to show, even if there's no products
			if( wc_get_loop_prop( 'is_paginated' ) ){
				add_filter( 'reycore/woocommerce/loop/can_add_filter_panel_sidebar', '__return_true' );
			}

		}

		echo '</div>';
	}

	/**
	 * Decode pagination links.
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	public function paginate_links($link)
	{

		if( ! $this->__supports_product_holder() ){
			return $link;
		}

		$link = urldecode($link);
		$link = str_replace('?reynotemplate=1', '', $link);
		$link = str_replace('&reynotemplate=1', '', $link);

		return $link;
	}

}
