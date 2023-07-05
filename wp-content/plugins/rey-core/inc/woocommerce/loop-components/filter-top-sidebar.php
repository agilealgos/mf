<?php
namespace ReyCore\WooCommerce\LoopComponents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FilterTopSidebar extends Component {

	public $position;

	public function late_init(){

		$this->position = get_theme_mod('ajaxfilter_topbar_position', 'before');

		if( $this->get_status() && $this->position === 'before' ){
			add_action('rey/get_sidebar', [$this, 'render__before_title']);
		}

	}

	public function status(){
		return reycore_wc__get_tag('sidebar')::can_output_shop_sidebar() && reycore_wc__check_filter_sidebar_top();
	}

	public function get_id(){
		return 'filter_top_sidebar';
	}

	public function get_name(){
		return 'Filter Top Sidebar';
	}

	public function loop_type(){
		return 'grid';
	}

	public function scheme(){

		$scheme = [];

		if( $this->position === 'after' ){
			$scheme['type'] = 'action';
			$scheme['tag'] = 'reycore/woocommerce/loop/before_grid';
			$scheme['callback'] = [$this, 'render__after_title'];
			$scheme['priority'] = 15;
		}

		return $scheme;

	}

	/**
	 * Renders before the Archive title
	 *
	 * @param string $position
	 * @return void
	 */
	public function render__before_title( $position )
	{
		if( ! wc_get_loop_prop( 'is_paginated' ) ){
			return;
		}

		if( 'left' !== $position ){
			return;
		}

		if( ! $this->maybe_render() ){
			return;
		}

		reycore__get_template_part('template-parts/woocommerce/filter-top-sidebar');
	}

	/**
	 * Renders after the Archive title
	 *
	 * @return void
	 */
	public function render__after_title()
	{

		if( ! wc_get_loop_prop( 'is_paginated' ) ){
			return;
		}

		if( ! $this->maybe_render() ){
			return;
		}

		reycore__get_template_part('template-parts/woocommerce/filter-top-sidebar');
	}

}
