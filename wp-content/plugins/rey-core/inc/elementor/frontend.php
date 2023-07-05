<?php
namespace ReyCore\Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use \ReyCore\Elementor\Helper;

class Frontend
{

	const REY_TEMPLATE = 'template-builder.php';

	/**
	 * Name of the Ajax lazy load call.
	 */
	const AJAX_LAZY_ACTION = 'element_lazy';

	public function __construct(){

		add_filter( 'rey/site_container/classes', [$this, 'site_container_classes'], 10);
		add_filter( 'template_include', [ $this, 'template_include' ], 11 /* After Plugins/WooCommerce */ );
		add_filter( 'body_class', [$this, 'add_body_class'], 20);
		add_filter( 'rey/main_script_params', [ $this, 'script_params'], 10 );
		add_filter( 'reycore/html_class_attr', [ $this, 'html_classes'], 10 );
		add_action( 'reycore/elementor/widget/construct', [$this, 'widget_init'] );
		add_filter( 'elementor/frontend/builder_content_data', [$this, 'set_post_id'], 5, 2 );
		add_action( 'elementor/frontend/get_builder_content', [$this, 'unset_post_id'], 20);
		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );
		add_action( 'save_post', [$this, 'flush_lazy_transients'], 20, 2 );
		add_action( 'delete_post', [$this, 'flush_lazy_transients'], 20, 2 );
		add_filter( 'rey/site_container/attributes', [$this, 'site_container_attributes']);
	}


	/**
	 * Register Ajax Actions
	 *
	 * @param object $ajax_manager
	 * @return void
	 */
	public function register_actions( $ajax_manager ){

		$ajax_manager->register_ajax_action( self::AJAX_LAZY_ACTION, [$this, 'ajax__load_lazy_element'], [
			'auth'       => 3,
			'nonce'      => false,
			'assets'     => true,
			'transient' => [
				'expiration' => 2 * DAY_IN_SECONDS,
				'unique_id' => ['pid', 'element_id'],
				'unique_id_sanitize' => ['absint', 'reycore__sanitize_elementor_id'],
			],
			'after_data' => $this->get_lazy_popover(),
		]);

		$ajax_manager->register_ajax_action( 'product_grid_load_more', [$this, 'ajax__product_grid_load_more'], [
			'auth'   => 3,
			'nonce'  => false,
		]);

	}

	/**
	 * Show a hovering popover to administrators
	 * to allow quick clearing the cache
	 *
	 * @return mixed
	 */
	public function get_lazy_popover(){

		if( current_user_can('administrator') && ! \ReyCore\Ajax::is_debug() ){

			$r_params = [];

			if( isset($_REQUEST[ \ReyCore\Ajax::DATA_KEY ]) && $action_data = reycore__clean($_REQUEST[ \ReyCore\Ajax::DATA_KEY ]) ){
				$r_params = $action_data;
				$r_params['refresh'] = true;
			}

			return sprintf(
				'<div class="rey-simplePopover --lazy" data-lazy-load=\'%s\'>%s</div>',
				wp_json_encode( $r_params ),
				_x('<p>This element is lazy loaded through Ajax and <strong>cached</strong>.<br><u>Click here</u> to refresh this element\'s cache. You can disable "Lazy loading" in this widget\'s settings in Elementor mode.</p><p><small>This notice shows only for Administrators.</small></p>', 'Various admin. texts', 'rey-core')
			);

		}

		return false;
	}

	public function site_container_classes( $classes ) {

		// if no meta, not an Elementor page
		if( ! ($elementor_meta = get_post_meta( get_the_ID(), \Elementor\Core\Base\Document::PAGE_META_KEY, true )) ){
			return $classes;
		}

		if( isset($elementor_meta['rey_stretch_page']) && ($stretch = $elementor_meta['rey_stretch_page']) && $stretch === 'rey-stretchPage' ){
			$classes[] = $stretch;
		}

		return $classes;
	}


	/**
	 * Include Rey builder template
	 */
	public function template_include( $template ) {

		if ( ! is_singular() ) {
			return $template;
		}

		$document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );

		if ( $document && $document->get_meta( '_wp_page_template' ) == self::REY_TEMPLATE ) {
			$template_path = trailingslashit( get_template_directory() ) . self::REY_TEMPLATE;
			if ( is_readable($template_path) ) {
				$template = $template_path;
			}
		}

		return $template;
	}


	public function add_body_class( $classes ){

		/**
		 * Adds version class
		 * @since 1.6.12
		 */
		$classes['optimized-dom'] = 'elementor-' . (Helper::get_props('optimized_dom') ? 'opt' : 'unopt');

		/**
		 * Fix for Product pages edited with Elementor
		 * but only the description.
		 * This will unset a CSS class in order to adjust the container padding
		 * @since 2.4.0
		 */
		if(
			// is product page
			function_exists('is_product') && is_product() &&
			// built with Elementor
			($classname = 'elementor-page elementor-page-' . get_queried_object_id()) &&
			in_array($classname, $classes, true) &&
			// product pages are supported
			($elementor_cpt_support = get_option( 'elementor_cpt_support' )) &&
			is_array($elementor_cpt_support) &&
			in_array('product', $elementor_cpt_support, true) &&
			// template types, only Canvas and Full-width
			in_array( get_page_template_slug(), ['', 'elementor_theme'], true )

		){
			$classes = array_diff( $classes, [$classname] );
		}

		return $classes;
	}

	public function script_params($params)
	{
		$params['optimized_dom'] = Helper::get_props('optimized_dom');
		$params['el_pushback_fallback'] = Helper::get_props('pushback_fallback_enabled');
		$params['header_fix_elementor_zindex'] = get_theme_mod('header_af__zindex_elementor', false);
		$params['elementor_edit_url'] = admin_url( 'post.php?post={{PID}}&action=elementor' );

		return $params;
	}

	/**
	 * Adds Elementor Kit class to the html tag.
	 * Useful because of the Customizer's global colors/fonts.
	 *
	 * @since 1.9.6
	 **/
	public function html_classes($classes)
	{
		if( class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->kits_manager) ){
			$classes[] = "elementor-kit-" . \Elementor\Plugin::$instance->kits_manager->get_active_id();
		}
		return $classes;
	}

	public function site_container_attributes($attributes){

		global $post;

		if( isset($post->ID) && ($document = \Elementor\Plugin::$instance->documents->get( $post->ID )) &&
			$document->is_built_with_elementor() ){
			$attributes .= sprintf(' data-page-el-selector="body.elementor-page-%d"', $post->ID);
		}

		return $attributes;
	}

	public function widget_init($data){

		if( ! $data ){
			return;
		}

		if ( isset($data['settings']) && ($settings = $data['settings']) && isset($data['widgetType']) && ($widgetType = $data['widgetType']) ) {
			add_filter('body_class', function($classes) use ($widgetType){
				$classes[$widgetType] = 'el-' . $widgetType;
				return $classes;
			});
		}

	}

	public function set_post_id( $data, $post_id ){

		if( ! isset($GLOBALS['elem_post_id']) && ! isset($GLOBALS['elem_post_id_prev']) ){
			$GLOBALS['elem_post_id_prev'] = $post_id;
		}

		$GLOBALS['elem_post_id'] = $post_id;

		return $data;
	}

	public function unset_post_id(){

		if ( isset($GLOBALS['elem_post_id']) && isset($GLOBALS['elem_post_id_prev']) ){
			$GLOBALS['elem_post_id'] = $GLOBALS['elem_post_id_prev'];
			return;
		}

		unset($GLOBALS['elem_post_id']);
	}

	/**
	 * Generate output for some specific element's Lazy Load option.
	 *
	 * @param array $data
	 * @return void
	 */
	public function ajax__load_lazy_element( $data ){

		if( ! (isset($data['qid']) && $qid = absint($data['qid'])) ){
			return;
		}

		if( ! (isset($data['element_id']) && ($element_id = reycore__clean($data['element_id'])) ) ){
			return;
		}

		if( strlen($element_id) > 8 ){
			return;
		}

		do_action('reycore/elementor/load_lazy_element');

		$document_data = '';

		$document = \Elementor\Plugin::$instance->documents->get( $qid );

		if ( $document ) {
			$document_data = $document->get_elements_data();
		}

		if ( empty( $document_data ) ) {
			return;
		}

		$findings = [];

		\Elementor\Plugin::$instance->db->iterate_data( $document_data, function( $element ) use ($element_id, &$findings) {
			if( $element_id === $element['id'] ){
				$findings[] = $element;
			}
		} );

		if( ! (isset($findings[0]) && $element_data_instance = $findings[0]) ){
			return ['errors' => 'No element found in page.'];
		}

		$element_data_instance['settings']['lazy_load'] = '';

		if( isset($data['options']) && $options = reycore__clean($data['options']) ){
			foreach ($options as $key => $value) {
				$element_data_instance['settings'][$key] = $value;
			}
		}

		$new_element = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data_instance );

		ob_start();
		$new_element->print_element();
		$element_data = ob_get_clean();

		if( empty($element_data) ){
			return ['errors' => 'Empty element data!'];
		}

		return $element_data;

	}

	public function ajax__product_grid_load_more( $request_data ){

		if( ! (isset($request_data['qid']) && $qid = absint($request_data['qid'])) ){
			return;
		}

		if( ! (isset($request_data['element_id']) && $element_id = reycore__clean($request_data['element_id'])) ){
			return;
		}

		if( strlen($element_id) > 8 ){
			return;
		}

		$document_data = '';

		$document = \Elementor\Plugin::$instance->documents->get( $qid );

		if ( $document ) {
			$document_data = $document->get_elements_data();
		}

		if ( empty( $document_data ) ) {
			return;
		}

		$findings = [];

		\Elementor\Plugin::$instance->db->iterate_data( $document_data, function( $element ) use ($element_id, &$findings) {
			if( $element_id === $element['id'] ){
				$findings[] = $element;
			}
		} );

		if( ! (isset($findings[0]) && $element_data_instance = $findings[0]) ){
			return ['errors'=> 'No element found in page.'];
		}

		$element_data_instance['settings']['lazy_load'] = '';
		$element_data_instance['settings']['limit'] = 4;

		if( isset($request_data['limit']) && $limit = absint($request_data['limit']) ){
			$element_data_instance['settings']['limit'] = $limit;
		}

		if( isset($request_data['offset']) && $offset = absint($request_data['offset']) ){
			$element_data_instance['settings']['offset'] = $offset;
		}

		if( isset($request_data['options']) && $options = reycore__clean($request_data['options']) ){
			foreach ($options as $key => $value) {
				$valid[] = $value === 'yes';
				$valid[] = $value === 'no';
				$valid[] = $value === '';
				if( in_array(true, $valid, true) ){
					$element_data_instance['settings'][$key] = $value;
				}
			}
		}

		$new_element = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data_instance );

		ob_start();
		$new_element->print_element();
		$element_data = ob_get_clean();

		if( empty($element_data) ){
			return ['errors'=> 'Empty element data.'];
		}

		return $element_data;
	}

	/**
	 * Delete transients for lazy elements
	 *
	 * @param int $post_id
	 * @param object $post
	 * @return void
	 */
	public function flush_lazy_transients( $post_id, $post ){

		if(
			(isset($_REQUEST['post_ID']) && 'product' === $post->post_type) ||
			(isset($_REQUEST['post']) && 'post' === $post->post_type)
		 ){
			\ReyCore\Helper::clean_db_transient( sprintf('%s_%s_%s', \ReyCore\Ajax::AJAX_TRANSIENT_NAME, self::AJAX_LAZY_ACTION, $post_id) );
		}

	}

}
