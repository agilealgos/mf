<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

final class AssetsManager
{

	public $debug = false;
	public $settings = [];

	/**
	 * All registered styles and scripts.
	 */
	protected $registered_styles = [];
	protected $registered_scripts = [];
	protected $low_priority_styles = [];
	protected $cancel_low_priority_styles = [];
	protected $styles_to_remove = [];
	protected $scripts_to_remove = [];

	/**
	 * Styles and scripts that has been added throughout the page load
	 */
	protected $styles = [];
	protected $scripts = [];

	protected $__collecting = null;

	/**
	 * Should cache separately for mobiles.
	 * Causes issues invalidating cache, and regenerates data.
	 */
	public $mobile = false;

	/**
	 * Html attribute used for the lazy stylesheets
	 */
	const LAZY_ATTRIBUTE = 'data-lazy-stylesheet';

	public function __construct()
	{

		add_action( 'init', [$this, 'init']);
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_mandatory']);
		add_filter( 'style_loader_tag', [$this, 'style_loader_tag'], 10, 2);
		add_action( 'wp_footer', [$this, 'enqueue_footer'], 15 );
		add_filter( 'script_loader_tag', [$this, 'script_loader_tag'], 10, 2);
	}

	public function init(){

		$this->debug = defined('REY_DEBUG_ASSETS') && REY_DEBUG_ASSETS;

		$this->settings = [
			'save_css'    => true, // combines and minifies CSS
			'save_js'     => true, // combines and minifies JS
			'defer_js'    => true,
			'lazy_styles' => true, // loads the footer styles (hidden content css), on first user interaction
			'mobile'      => false, // should behave differently in mobile (Work in progress)
		];

		// disable in debug mode or if theme/core unsynced
		if( $this->debug || (defined('REY_OUTDATED') && REY_OUTDATED) ){
			$this->settings['save_css'] = false;
			$this->settings['save_js'] = false;
		}

		$this->settings = apply_filters('reycore/assets/settings', $this->settings);

		$this->mobile = $this->settings['mobile'] && reycore__is_mobile();
	}

	public function get_settings($setting = ''){

		if( isset($this->settings[$setting]) ){
			return $this->settings[$setting];
		}

		return $this->settings;
	}

	/**
	 * Register an asset
	 *
	 * @param string $type
	 * @param array $assets
	 * @return void
	 */
	public function register_asset( $type, $assets ){
		if( $type === 'styles' ){
			$this->registered_styles = array_merge($this->registered_styles, (array) $assets);
		}
		elseif( $type === 'scripts' ){
			$this->registered_scripts = array_merge($this->registered_scripts, (array) $assets);
		}
	}

	/**
	 * De-Register an asset
	 *
	 * @param string $type
	 * @param array $assets
	 * @return void
	 */
	public function deregister_asset( $type, $assets ){
		if( $type === 'styles' ){
			foreach ((array) $assets as $asset) {
				unset($this->registered_styles[$asset]);
			}
		}
		elseif( $type === 'scripts' ){
			foreach ((array) $assets as $asset) {
				unset($this->registered_scripts[$asset]);
			}
		}
	}

	/**
	 * Register and collect assets
	 *
	 * @return void
	 */
	public function register_assets(){

		/**
		 * Hook to register Rey styles.
		 * @since 2.0.0
		 */
		do_action('reycore/assets/register_scripts', $this);

		foreach( $this->registered_styles as $handle => $style ){
			if( ! isset($this->registered_styles[$handle]['path']) && strpos($style['src'], REY_CORE_URI) !== false ){
				$this->registered_styles[$handle]['path'] = str_replace(REY_CORE_URI, REY_CORE_DIR, $style['src']);
			}
			wp_register_style($handle, $style['src'], $style['deps'], $style['version']);
		}

		foreach( $this->registered_scripts as $handle => $script ){
			if( isset($script['src']) ){
				if( ! isset($this->registered_scripts[$handle]['path']) && strpos($script['src'], REY_CORE_URI) !== false ){
					$this->registered_scripts[$handle]['path'] = str_replace(REY_CORE_URI, REY_CORE_DIR, $script['src']);
				}
				wp_register_script(
					$handle,
					$script['src'],
					isset($script['deps']) ? $script['deps'] : [],
					isset($script['version']) ? $script['version'] : REY_CORE_VERSION,
					isset($script['in_footer']) ? $script['in_footer'] : true
				);
				if( isset($script['localize']) && is_array($script['localize']['params']) ){
					wp_localize_script($handle, $script['localize']['name'], $script['localize']['params']);
				}
			}
		}

		// error_log(var_export( array_keys($this->registered_styles), true));
		// error_log(var_export( array_keys($this->registered_scripts), true));
	}

	public function get_register_assets( $type = 'styles' ){
		if( 'styles' === $type ){
			return $this->registered_styles;
		}
		else if( 'scripts' === $type ){
			return $this->registered_scripts;
		}
	}

	/**
	 * Enqueue and add styles to enqueue collection
	 *
	 * @param array $handlers
	 * @return void
	 */
	public function add_styles( $handlers, $force_high_priority = false ){

		foreach ((array) $handlers as $key => $handler) {

			if( in_array($handler, $this->styles_to_remove, true) ){
				continue;
			}

			// exclude if purposely specified
			if( $force_high_priority ){
				$this->cancel_low_priority_styles[] = $handler;
			}

			if( isset($this->collected_styles) ){
				$this->collected_styles[] = $handler;
			}

			if( wp_style_is($handler, 'enqueued') ){
				continue;
			}

			if( $this->settings['lazy_styles'] ){
				if(
					isset($this->registered_styles[$handler]) &&
					isset($this->registered_styles[$handler]['priority']) &&
					'low' === $this->registered_styles[$handler]['priority']
				){
					$this->low_priority_styles[ $handler ] = $this->registered_styles[$handler];
				}
			}

			$this->styles[] = $handler;

			wp_enqueue_style($handler);
		}
	}

	public function remove_styles($handlers){
		foreach ((array) $handlers as $handler) {
			$this->styles_to_remove[$handler] = $handler;
		}
	}

	public function remove_scripts($handlers){
		foreach ((array) $handlers as $handler) {
			$this->scripts_to_remove[$handler] = $handler;
		}
	}

	/**
	 * Determine if an asset should be enqueued
	 *
	 * @param array $asset
	 * @param string $handle
	 * @return bool
	 */
	public function maybe_enqueue_mandatory( $asset, $handle = '' ){

		$enqueue = false;

		// always enqueue
		if( isset($asset['enqueue']) && $asset['enqueue'] ){

			$enqueue = true;

			if( in_array($handle, \ReyCore\Assets::get_excludes(), true) ){
				$enqueue = false;
			}

		}

		else {
			// check callback
			if( isset($asset['callback']) ){
				if( is_callable($asset['callback']) && call_user_func($asset['callback']) ){
					$enqueue = true;
				}
			}
		}

		return $enqueue;
	}

	/**
	 * Enqueue mandatory scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_mandatory(){

		do_action('reycore/assets/enqueue', $this);

		// Just load everything in elementor mode
		// Todo: find a better way.
		if( reycore__elementor_edit_mode() ){
			foreach (array_keys($this->registered_styles) as $style) {
				wp_enqueue_style($style);
			}
			return;
		}

		foreach( $this->registered_styles as $handle => $style ){

			if( in_array($handle, $this->styles_to_remove, true) ){
				continue;
			}

			if( $this->maybe_enqueue_mandatory( $style, $handle ) ){
				$this->add_styles($handle);
			}
		}

		foreach( $this->registered_scripts as $handle => $script ){

			if( in_array($handle, $this->scripts_to_remove, true) ){
				continue;
			}

			if( $this->maybe_enqueue_mandatory( $script, $handle ) ){
				$this->add_scripts($handle);
			}
		}

	}

	/**
	 * Override style tag output
	 *
	 * @param string $tag
	 * @param string $handle
	 * @return string
	 */
	public function style_loader_tag($tag, $handle){

		if( in_array($handle, ['woocommerce-general'], true) ){
			return '';
		}

		if( in_array($handle, $this->styles_to_remove, true) ){
			return '';
		}

		if( isset( $this->low_priority_styles[$handle] ) && ! in_array($handle, $this->cancel_low_priority_styles, true) ){
			return str_replace(' href=', sprintf(' %s=', self::LAZY_ATTRIBUTE), $tag);
		}

		return $tag;
	}

	/**
	 * Sort the css stylesheets by priorities
	 *
	 * @param array $data
	 * @return array
	 */
	public function sort_css_priorities($data){

		$high = $mid = $low = [];

		foreach($data as $key => $handle){

			if( ! isset( $this->registered_styles[ $handle ] ) ){
				continue;
			}

			$style = $this->registered_styles[$handle];

			if( isset($style['priority']) ){
				if( $style['priority'] === 'high' ){
					$high[] = $handle;
				}
				else if( $style['priority'] === 'low' ){
					$low[] = $handle;
				}
			}
			else{
				$mid[] = $handle;
			}
		}

		return [
			'head' => array_merge($high, $mid),
			'lazy' => $low,
		];
	}

	/**
	 * Get the page styles. Used for collecting enqueued styles.
	 *
	 * @return array
	 */
	public function get_styles(){
		return $this->sort_css_priorities( array_unique($this->styles) );
	}

	/**
	 * Create an object with the styles which have been added to the page.
	 *
	 * @param array $styles
	 * @return void
	 */
	public function output_inserted_styles($styles = []){

		if( empty($styles) ){
			$styles = $this->get_styles();
		}

		printf("<script type='text/javascript' id='reystyles-loaded'>\n window.reyStyles=%s; \n</script>", wp_json_encode(array_values($styles)));
	}

	/**
	 * Retrive scripts that have been requested in the page
	 *
	 * @return array
	 */
	public function get_scripts(){
		return array_unique($this->scripts);
	}

	/**
	 * Add script to collection of to be enqueued
	 *
	 * @param array $handlers
	 * @return void
	 */
	public function add_scripts( $handlers ){
		foreach ((array) $handlers as $key => $handler) {

			if( in_array($handler, $this->scripts_to_remove, true) ){
				continue;
			}

			$this->scripts[] = $handler;

			if( isset($this->collected_scripts) ){
				$this->collected_scripts[] = $handler;
			}
		}
	}

	/**
	 * Run late footer scripts
	 *
	 * @return void
	 */
	public function enqueue_footer(){
		do_action('reycore/assets/enqueue_footer', $this);
		$this->output_inserted_styles();
		$this->enqueue_js();
	}

	/**
	 * Render a JS global object containing all scripts that have been added to the page
	 *
	 * @param array $scripts
	 * @return void
	 */
	public function output_inserted_scripts($scripts){
		printf("<script type='text/javascript' id='reyscripts-loaded'>\n window.reyScripts=%s; \n</script>", wp_json_encode(array_values($scripts)));
	}

	/**
	 * Enqueue JS scripts
	 *
	 * @return void
	 */
	public function enqueue_js(){

		if( reycore__elementor_edit_mode() ){
			unset($this->registered_scripts['flying-pages']);
			foreach (array_keys($this->registered_scripts) as $script) {
				wp_enqueue_script($script);
			}
			return;
		}

		if( ! ($scripts = $this->get_scripts()) ){
			return;
		}

		$this->output_inserted_scripts($scripts);

		foreach ($scripts as $script) {
			wp_enqueue_script($script);
		}
	}

	/**
	 * Filter scripts output
	 *
	 * @param string $tag
	 * @param string $handle
	 * @return string
	 */
	public function script_loader_tag($tag, $handle){

		if( in_array($handle, $this->scripts_to_remove, true) ){
			return $tag;
		}

		// Defer JS
		if( $this->settings['defer_js'] && in_array($handle, $this->get_scripts(), true) ){

			if( in_array($handle, ['wp-util'], true) ){
				return $tag;
			}

			if(
				isset($this->registered_scripts[$handle]) &&
				! isset($this->registered_scripts[$handle]['external'])
			) {
				return str_replace( ' src', ' defer src', $tag );
			}
		}

		return $tag;
	}

	/**
	 * Retrieve RTL stylesheet suffix
	 *
	 * @return string
	 */
	public static function rtl(){
		return is_rtl() ? '-rtl' : '';
	}

	public function collect_start(){

		// stop if it's already collecting
		if( $this->__collecting ){
			return;
		}

		// if not, set to collect
		$this->__collecting = true;

		// reset collections
		$this->collected_styles = [];
		$this->collected_scripts = [];
	}

	public function collect_end( $src = false ){

		$collected = [
			'scripts' => [],
			'styles' => [],
		];

		if( isset($this->collected_scripts) ){
			$collected['scripts'] = array_unique($this->collected_scripts);
		}

		if( isset($this->collected_styles) ){
			$collected['styles'] = array_unique($this->collected_styles);
		}

		// stop collecting
		$this->__collecting = null;

		if( $src ){
			return [
				'scripts' => $this->get_assets_uri($collected, 'scripts'),
				'styles' => $this->get_assets_uri($collected, 'styles'),
			];
		}

		return $collected;
	}

	public function get_assets_uri( $assets, $type = 'styles' ){

		$assets_to_return = [];

		$single_asset = ! isset( $assets['styles'] ) && ! isset( $assets['scripts'] );

		if( ! isset($assets[$type]) && ! $single_asset ){
			return $assets_to_return;
		}

		$wp_assets = $type === 'styles' ? wp_styles() : wp_scripts();

		$the_assets = $single_asset ? (array) $assets : $assets[$type];

		foreach ($the_assets as $key => $handler) {

			if( ! (isset($wp_assets->registered[ $handler ]) && ($script = $wp_assets->registered[ $handler ])) ){
				continue;
			}
			if( ! (isset($script->src) && ($src = $script->src)) ){
				continue;
			}

			if ( 0 === strpos( $src, site_url() ) || 0 === strpos( $src, 'http' ) ) {
				$src_ = $src;
			} else {
				$src_ = site_url() . $src;
			}

			if( $single_asset ){
				$assets_to_return = $src_;
			}
			else {
				$assets_to_return[$handler] = $src_;
			}
		}

		return $assets_to_return;
	}


}

function reyCoreAssets(){
	return Plugin::instance()->assets_manager;
}

reyCoreAssets();
