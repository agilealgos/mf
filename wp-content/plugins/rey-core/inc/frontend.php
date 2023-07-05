<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Frontend {

	protected $body_classes = [];

	public function __construct(){
		add_action('wp_head', [$this, 'wp_head'], 100);
		add_filter('body_class', [$this, 'filter_body_class'], 20 );
		add_filter( 'rey/body/tag_attributes', [$this, 'add_post_id_tag']);
	}

	public function wp_head(){
		$this->set_default_body_classes();

		do_action('reycore/frontend/wp_head', $this);

	}

	public function add_post_id_tag($attr){

		$id = get_queried_object_id();

		if( function_exists('is_shop') && is_shop() ){
			$id = wc_get_page_id( 'shop' );
		}

		$attr['data-id'] = $id;

		return $attr;
	}

	/**
	 * Filter the body class
	 *
	 * @param array $classes
	 * @return array
	 */
	public function filter_body_class($classes)
	{

		unset($classes['search_style']);

		return array_merge($classes, $this->body_classes);
	}

	/**
	 * Public method to append body classes
	 *
	 * @param array|string $classes
	 * @return void
	 */
	public function add_body_class( $classes ){

		foreach ((array) $classes as $key => $class) {
			$this->body_classes[$key] = $class;
		}

	}

	/**
	 * Public method to remove body classes
	 *
	 * @param string Class name or key
	 * @param string Use key or not
	 * @return void
	 */
	public function remove_body_class( $data, $key = true ){

		if( $key ){
			unset($this->body_classes[ $data ]);
		}
		else {
			$the_key = array_search($data, $this->body_classes, true);
			unset($this->body_classes[$the_key]);
		}

	}

	/**
	 * Add the default body classes
	 *
	 * @return void
	 */
	private function set_default_body_classes(){

		$classes = [];

		/**
		 * Add custom class for container width
		 *
		 * @since 2.1.2
		 **/
		if( $custom_container_width = get_theme_mod('custom_container_width', 'default') ){
			$classes['container_width'] = 'rey-cwidth--' . esc_attr($custom_container_width);
		}

		/**
		 * Mark JS delayed
		 *
		 * @since 2.1.2
		 **/
		if ( ! is_user_logged_in() && reycore__js_is_delayed() ) {
			// $classes['rey_wpr'] = '--not-ready';
		}

		/**
		 * Adds custom class defined in page options
		 */
		if( $rey_body_class = reycore__get_option('rey_body_class', '') ){
			$classes[] = esc_attr($rey_body_class);
		}

		/**
		 * Hide button focuses
		 */
		if( get_theme_mod('accessibility__hide_btn_focus', true) ){
			$classes['acc_focus'] = '--no-acc-focus';
		}

		$this->body_classes = $classes;

	}
}
