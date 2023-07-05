<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Menus {

	const CACHE = true;
	const NAV_TRANSIENT = 'rey_menu_cache_';
	const CAT_TRANSIENT = 'rey_cat_menu_cache_';

	public function __construct(){

		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );
		add_filter( 'pre_wp_nav_menu', [$this, 'pre_wp_nav_menu'], 5, 2 );
		add_filter( 'wp_nav_menu', [$this, 'wp_nav_menu'], 5, 2 );
		add_filter( 'nav_menu_css_class', [ $this, 'add_target_css_class'], 5, 3 );

		add_action( 'wp_create_nav_menu', [$this, 'flush_menu_cache_by_id'] );
		add_action( 'wp_delete_nav_menu', [$this, 'flush_menu_cache_by_id'] );
		add_action( 'wp_update_nav_menu', [$this, 'flush_menu_cache_by_id'] );

		add_action( 'reycore/megamenus/save_delete', [$this, 'flush_mega_cache'], 10, 2 );

		add_action( 'create_term', [$this, 'flush_cat_menu_cache'], 10, 3 );
		add_action( 'edit_term', [$this, 'flush_cat_menu_cache'], 10, 3 );
		add_action( 'delete_term', [$this, 'flush_cat_menu_cache'], 10, 3 );

		add_filter( 'reycore/admin_bar_menu/nodes', [$this, 'adminbar__add_refresh'], 20);
		add_action( 'wp_ajax__refresh_menus', [$this, 'adminbar__refresh']);

	}

	public function adminbar__add_refresh($nodes){

		if( ! current_user_can('administrator') ){
			return $nodes;
		}

		if( ! self::can_cache() ){
			return $nodes;
		}

		if( isset($nodes['refresh']) ){
			$nodes['refresh']['nodes']['refresh_menus'] = [
				'title'  => esc_html__( 'Menus Cache', 'rey-core' ),
				'href'  => '#',
				'meta_title' => esc_html__( 'Refresh the cached menus.', 'rey-core' ),
				'class' => 'qm-refresh-menus qm-refresher',
			];
		}

		return $nodes;
	}

	/**
	 * Cleanup Menus cache from AdminBar
	 *
	 * @since 2.4.5
	 **/
	public function adminbar__refresh()
	{

		$cleanups[] = (bool) \ReyCore\Helper::clean_db_transient( self::CAT_TRANSIENT );
		$cleanups[] = (bool) \ReyCore\Helper::clean_db_transient( self::NAV_TRANSIENT );

		if( in_array(true, $cleanups, true) ){
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	public function flush_cat_menu_cache($term_id, $tt_id, $taxonomy) {

		if( 'product_cat' !== $taxonomy ){
			return;
		}

		\ReyCore\Helper::clean_db_transient( self::CAT_TRANSIENT );

	}

	public function flush_mega_cache($post_id, $mega){
		foreach (get_option($mega::SUPPORTED_MENUS, []) as $menu_id) {
			$this->flush_menu_cache_by_id($menu_id);
		}
	}

	/**
	 * Determines if the menus can be cached
	 *
	 * @return boolean
	 */
	public static function can_cache(){
		return
			// no need to cache in Ajax requests
			! wp_doing_ajax()
			// no need in Elementor editor
			&& ! reycore__elementor_edit_mode()
			// can use a special constant.
			&& ( self::CACHE || (defined('REY_MENU_CACHE') && REY_MENU_CACHE) );
	}

	/**
	 * Retrieve transient name, composed by the menu ID (or slug)
	 * and the arguments encoded.
	 *
	 * @param array $args
	 * @return string
	 */
	public static function get_transient_name( $args ){

		if( ! ($menu_id = isset($args['menu']) ? $args['menu'] : '') ){
			return;
		}

		if( ! (is_string($menu_id) || is_numeric($menu_id)) ){
			return;
		}

		$walker = $args['walker'];

		// remove walker's instance params
		if( $walker ){
			$args['walker'] = get_class($walker);
		}

		return self::NAV_TRANSIENT . $menu_id . '_' . md5(wp_json_encode($args));
	}

	/**
	 * Shortcircuit the Nav output and retrieve the cached version,
	 * if available
	 *
	 * @param mixed $menu
	 * @param array $args
	 * @return string
	 */
	public function pre_wp_nav_menu( $menu, $args ){

		$args = (array) $args;

		if( ! isset($args['cache_menu']) ){
			return $menu;
		}

		if( ! $args['cache_menu'] ){
			return $menu;
		}

		if( ! $args['menu'] ){
			return $menu;
		}

		if( ! self::can_cache() ){
			return $menu;
		}

		if( ! ($transient_name = self::get_transient_name( $args )) ){
			return $menu;
		}

		// get transient and return the output
		// and load assets
		if( false !== ( $menu_data = get_transient( $transient_name ) ) ){

			if( isset($menu_data['assets']['scripts']) && ($scripts = $menu_data['assets']['scripts']) ){
				reyCoreAssets()->add_scripts(array_keys($scripts));
			}

			if( isset($menu_data['assets']['styles']) && ($styles = $menu_data['assets']['styles']) ){
				reyCoreAssets()->add_styles(array_keys($styles));
			}

			// Load global sections styles
			if( isset($menu_data['gs']) && ($gss = $menu_data['gs']) ){
				foreach( $gss as $section_id ){
					if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
						$css_file = new \Elementor\Core\Files\CSS\Post( $section_id );
						if( $css_file ){
							$css_file->enqueue();
						}
					}
				}
			}

			return self::process_menu($menu_data['output']);
		}

		reyCoreAssets()->collect_start();

		\ReyCore\Elementor\GlobalSections::collect_start();

		return $menu;
	}

	/**
	 * Cache the menu.
	 *
	 * @param string $menu
	 * @param array $args
	 * @return string
	 */
	public function wp_nav_menu( $menu, $args ){

		$args = (array) $args;

		if( ! isset($args['cache_menu']) ){
			return $menu;
		}

		if( ! $args['cache_menu'] ){
			return $menu;
		}

		if( ! $args['menu'] ){
			return $menu;
		}

		if( ! self::can_cache() ){
			return $menu;
		}

		if( ! ($transient_name = self::get_transient_name( $args )) ){
			return $menu;
		}

		// cleanup current menu item classes
		$menu = str_replace('current-menu-item', '', $menu);

		$data = [
			'output' => $menu,
			'assets' => reyCoreAssets()->collect_end(true),
			'gs'     => \ReyCore\Elementor\GlobalSections::collect_end(),
		];

		set_transient( $transient_name, $data, MONTH_IN_SECONDS);

		// processing must be done separately,
		// to avoid being cached
		return self::process_menu($data['output']);
	}

	/**
	 * Add the Current Queried Object ID to the UL tag
	 * to be picked up by JS to set the correct active css class
	 *
	 * @param string $menu
	 * @return string
	 */
	public static function process_menu( $menu ){

		$search_for = '<ul ';
		$replace_with = sprintf('data-menu-qid="%d" ', get_queried_object_id());
		$menu = preg_replace('/' . $search_for . '/', $search_for . $replace_with, $menu, 1);

		return $menu;
	}

	/**
	 * Adds special CSS classes to manu items,
	 * based on the `object_id`
	 *
	 * @param array $classes
	 * @param object $item
	 * @param array $args
	 * @return array
	 */
	public function add_target_css_class( $classes, $item, $args ) {

		if( ! self::can_cache() ){
			return $classes;
		}

		if( 'custom' === $item->type ){
			// check if it's a custom link, but internal
			if( $item->url && strpos($item->url, home_url()) !== false ){
				if( $post_id = absint(url_to_postid( $item->url )) ){
					$classes['object_id'] = 'o-id-' . $post_id;
				}
			}
		}
		else {
			$classes['object_id'] = 'o-id-' . $item->object_id;
		}

		return $classes;
	}

	public function flush_menu_cache_by_id($menu_id){

		// if numeric
		\ReyCore\Helper::clean_db_transient( self::NAV_TRANSIENT . $menu_id );

		// if saved as slug
		if( ($term = get_term_by('term_id', $menu_id, 'nav_menu')) && ! is_wp_error($term) && isset($term->slug) ){
			\ReyCore\Helper::clean_db_transient( self::NAV_TRANSIENT . $term->slug );
		}

	}

	/**
	 * Get WordPress Nav menus terms
	 *
	 * @since 1.0.0
	 */
	public static function get_term_menus(){
		return get_terms( [
			'taxonomy' => 'nav_menu',
			'hide_empty' => false,
			// 'fields' => 'id=>slug', // needs `name` too
		] );
	}

	/**
	 * Register Ajax request to
	 * get menus list into Customizer controls
	 *
	 * @param object $ajax_manager
	 * @return void
	 */
	public function register_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'get_menus_list', [$this, 'customizer__get_nav_menus'], 1 );
	}

	/**
	 * Get menus list into Customizer controls
	 *
	 * @param object $ajax_manager
	 * @return void
	 */
	public function customizer__get_nav_menus(){

		$terms = self::get_term_menus();
		$menus = [];

		foreach ($terms as $term) {
			$menus[$term->slug] = $term->name;
		}

		return $menus;
	}

	/**
	 * Get menus list into Elementor controls
	 * which are formatted in a way to be able to
	 * render and edit button.
	 *
	 * @since 2.4.5
	 */
	public static function get_nav_menus_options( $data ){

		$options[] = [
			'id' => '',
			'title' => esc_html__('- Select -', 'rey-core'),
		];

		$terms = self::get_term_menus();

		$edit = isset($data['edit_link']) && $data['edit_link'];

		foreach ($terms as $term) {

			$item = [];

			$item['id'] = $term->slug;
			$item['title'] = $term->name;

			if( $edit ){
				$item['link'] = admin_url('nav-menus.php?action=edit&menu=') . $term->term_id;
			}

			$options[] = $item;

		}

		return $options;
	}
}
