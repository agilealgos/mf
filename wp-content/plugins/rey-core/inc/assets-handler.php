<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AssetsHandler
{
	/**
	 * Store Filesystem
	 */
	private static $fs;

	/**
	 * Path where to save files.
	 */
	private static $dir_path;

	/**
	 * WP Uploads Folder
	 *
	 * @var array
	 */
	private static $wp_uploads_dir = [];

	/**
	 * Assets manager
	 *
	 * @var AssetsManager
	 */
	public $assets_manager;

	/**
	 * Holds collected stylesheets
	 *
	 * @var array
	 */
	private $stylesheets = [];

	/**
	 * Holds collected scripts
	 *
	 * @var array
	 */
	private $scripts = [];

	/**
	 * Can run CSS
	 *
	 * @var boolean
	 */
	private $can_run_css = false;

	/**
	 * Can run JS
	 *
	 * @var boolean
	 */
	private $can_run_js = false;

	/**
	 * Buffer manager instance
	 *
	 * @var BufferManager
	 */
	private $buffer;

	/**
	 * Handle log times
	 *
	 * @var string
	 */
	private static $current_time;

	/**
	 * Determine what to log
	 *
	 * @var array
	 */
	public static $logs = [
		'time'          => false,
		'styles'        => false,
		'scripts'       => false,
		'print_handles' => true,
	];

	/**
	 * Head stylesheet placeholder
	 */
	const HEAD_PLACEHOLDER = '<!-- REY_HEAD_STYLESHEET -->';

	/**
	 * Detects paths in CSS.
	 */
	const ASSETS_REGEX = '/url\s*\(\s*(?!["\']?data:)(?![\'|\"]?[\#|\%|])([^)]+)\s*\)([^;},\s]*)/i';

	public function __construct()
	{

		self::set_filesystem();

		add_action( 'reycore/buffer/assets', [$this, 'handle_assets']);

		add_action( 'wp_head', [$this, 'add_head_stylesheet_placeholder'], 100 ); // 100, before `wp_custom_css_cb`

		add_action( 'init', [$this, 'handle_clear_data']);
		add_action( 'customize_save_perf__css_exclude', [$this, 'clear__customize_save_perf__css_exclude']);
		add_action( 'rey/flush_cache_after_updates', [$this, 'clear__basic'], 20);
		add_action( 'elementor/admin/after_create_settings/elementor', [$this, 'clear__basic'], 10);

		add_filter( 'reycore/admin_bar_menu/nodes', [$this, 'adminbar__add_refresh'], 20);
		add_action( 'wp_ajax__refresh_assets', [$this, 'adminbar__clear_assets']);

	}

	public function handle_assets($buffer){
		$this->buffer = $buffer;

		if( is_404() ){
			return;
		}

		$this->assets_manager = \ReyCore\Plugin::instance()->assets_manager;

		$this->handle_css();
		$this->handle_js();

	}

	/**
	 * Start finding CSS occurances and handle CSS merging
	 *
	 * @return void
	 */
	public function handle_css(){

		if( ! ($this->can_run_css = $this->assets_manager->get_settings('save_css')) ){
			return;
		}

		$content = $this->buffer->get_buffer();

		if ( ! preg_match_all( '#(<link[^>]*stylesheet[^>]*>)#Usmi', $content, $matches ) ) {
			return self::log('Can\'t find any stylesheets.');
		}

		// start timing and logging
		self::start_log_time();

		// get the registered styles for their data
		$registered_styles = $this->assets_manager->get_register_assets('styles');

		// define excludes from merging
		$excludes = apply_filters('reycore/buffer/css/excluded', []);

		foreach ($matches[0] as $tag) {

			// get IDs
			if ( ! preg_match( '#<link.*id=("|\')(.*)("|\')#Usmi', $tag, $source ) ) {
				continue;
			}

			$id_attribute = $source[2];

			// add a placeholder before the Elementor's first custom stylesheet
			if( ! isset($this->added_head_placeholder) && strpos($id_attribute, 'elementor-post-') !== false ){
				// prepare placeholder
				$placeholder = str_replace($tag, self::HEAD_PLACEHOLDER . $tag, $content);
				// update bufer
				$this->buffer->set_buffer($placeholder);
				// prevent from doing it again
				$this->added_head_placeholder = true;
			}

			// just Rey and Rey Core IDs
			$is_rey = (
				(strpos($id_attribute, 'rey-') !== false || strpos($id_attribute, 'reycore-') !== false) &&
				(strpos($id_attribute, 'rey-head') === false || strpos($id_attribute, 'rey-lazy') === false)
			);

			// validate
			if( ! $is_rey ){
				continue;
			}

			// cleanup
			$stylesheet_id = str_replace('-css', '', $id_attribute);

			// check for excludes and skip
			if( in_array($stylesheet_id, $excludes, true) ){
				continue;
			}

			// check if stylesheet was registered
			// and collect it for writing
			if( ! in_array($stylesheet_id, array_keys($registered_styles), true) ){
				continue;
			}

			// establish the type
			$type = strpos($tag, AssetsManager::LAZY_ATTRIBUTE) !== false ? 'lazy' : 'head';

			$this->stylesheets[$type][$stylesheet_id] = [
				'tag' => $tag,
				'data' => $registered_styles[$stylesheet_id],
			];

		}

		// start processing the CSS stylesheets
		$this->process_stylesheets();

		// end timing and logging
		self::end_log_time('Handled CSS in ');

	}

	/**
	 * Handle the CSS for processing and replacing with
	 * the combined tags
	 *
	 * @return void
	 */
	public function process_stylesheets(){

		if( ! self::$fs ){
			return;
		}

		if( empty($this->stylesheets) ){
			return;
		}

		$main_stylesheet = '';

		foreach ($this->stylesheets as $type => $stylesheets) {

			if( self::$logs['styles'] ){
				self::log( sprintf('%s Styles: %s', $type, implode(', ', $stylesheets)) );
			}

			// create unique hash
			$hash = self::hash( array_keys($stylesheets) );

			$file = [
				'path' => self::$dir_path . self::get__stylesheet__basename($hash, $type), // server
				'url' => self::get__base_uploads__url() . self::get__stylesheet__basename($hash, $type), // url
			];

			// if it already exists, don't rewrite it, just stop
			if( ! empty($stylesheets) && ! self::$fs->is_file( $file['path'] ) ){
				// write the CSS file
				if( ! $this->write_css($stylesheets, $type, $file['path']) ){
					// stop if cannot write the file
					continue;
				}
			}

			// determine last
			$index = 0;

			// run through each stylesheets type
			foreach ($stylesheets as $id => $stylesheet_data) {

				$replace_with = '';

				if( self::is_debug_log_assets() ){
					$replace_with = sprintf('<!-- CSS: %s -->', $stylesheet_data['tag']);
				}

				if( 'lazy' === $type ){
					// place the combined file in the last's place
					if( $index === (count($stylesheets) - 1) ){
						$replace_with = self::print_css_tag($type, $file, $this->assets_manager::LAZY_ATTRIBUTE );
					}
				}

				else if( 'head' === $type ){
					// just set the main stylesheet, the others can be removed
					$main_stylesheet = self::print_css_tag($type, $file);
				}

				// update the buffer
				$this->update_buffer( $stylesheet_data['tag'], $replace_with );

				$index++;
			}

		}

		if( $main_stylesheet ){
			$this->update_buffer( self::HEAD_PLACEHOLDER, $main_stylesheet, true );
		}

	}

	/**
	 * Write stylesheet to the uploads folder.
	 *
	 * @param array $stylesheets
	 * @param string $type
	 * @param string $filepath Target stylesheet
	 * @return void
	 */
	private function write_css( $stylesheets, $type, $filepath){

		$css = [];
		$data_to_log = [];

		// go through stylesheets
		foreach ($stylesheets as $handle => $stylesheet_data) {

			// check for path and if the file actually exists
			if( ! (isset($stylesheet_data['data']['path']) && self::$fs->is_file( $stylesheet_data['data']['path'] )) ) {
				continue;
			}

			// grabs CSS
			$stylesheet_css = self::$fs->get_contents( $stylesheet_data['data']['path'] );
			// fix urls
			$stylesheet_css = self::fixurls($stylesheet_data['data']['path'], $stylesheet_css);
			// collect css
			$css[$handle] = $stylesheet_css;
			// collect logging handles
			$data_to_log[] = $handle;
		}

		// check if CSS data exists
		if( ! empty($css) ){

			// minify the css data
			$css_contents = self::minify_css($css);

			// log
			self::log( sprintf('Stored %2$s stylesheet in "%3$s", FN: "%1$s".', $filepath, strtoupper($type), reycore__get_page_title() ) );

			// append handles
			$css_contents .= $this->debug_print_handles($data_to_log);

			// actually write the file
			return self::$fs->put_contents( $filepath, $css_contents );
		}

		return false;
	}

	/**
	 * Start finding JS occurances and handle JS merging
	 *
	 * @return void
	 */
	public function handle_js(){

		if( ! ($this->can_run_js = $this->assets_manager->get_settings('save_js')) ){
			return;
		}

		$content = $this->buffer->get_buffer();

		if ( ! preg_match_all( '#<script.*</script>#Usmi', $content, $matches ) ) {
			return self::log('Can\'t find any scripts.');
		}

		// start timing and logging
		self::start_log_time();

		// get the registered styles for their data
		$registered_scripts = $this->assets_manager->get_register_assets('scripts');

		// define excludes from merging
		$excludes = apply_filters('reycore/buffer/js/excluded', [
			'rey-helpers',
			'reyadminbar',
			'reycore-frontend-admin',
		]);

		foreach ($matches[0] as $tag) {

			// only src* tags
			if ( ! preg_match( '#<script[^>]*src=("|\')([^>]*)("|\')#Usmi', $tag, $src_attribute ) ) {
				continue;
			}

			// get IDs
			if ( ! preg_match( '#<script[^>]*id=("|\')([^>]*)("|\')#Usmi', $tag, $source ) ) {
				continue;
			}

			$id_attribute = $source[2];

			// just Rey and Rey Core IDs
			$is_rey = strpos($id_attribute, 'rey-') !== false || strpos($id_attribute, 'reycore-') !== false;

			// validate
			if( ! $is_rey ){
				continue;
			}

			// cleanup
			$script_id = str_replace('-js', '', $id_attribute);

			// check for excludes and skip
			if( in_array($script_id, $excludes, true) ){
				continue;
			}

			// check if script was registered
			// and collect it for writing
			if( ! in_array($script_id, array_keys($registered_scripts), true) ){
				continue;
			}

			$this->scripts[$script_id] = [
				'tag' => $tag,
				'data' => $registered_scripts[$script_id],
			];

		}

		$this->process_scripts();

		// end timing and logging
		self::end_log_time('Handled JS in ');
	}

	/**
	 * Handle the JS for processing and replacing with
	 * the combined tags
	 *
	 * @return void
	 */
	public function process_scripts(){

		if( ! self::$fs ){
			return;
		}

		if( empty($this->scripts) ){
			return;
		}

		if( self::$logs['scripts'] ){
			self::log( sprintf('Scripts: %s', implode(', ', array_keys($this->scripts) )) );
		}

		// create unique hash
		$hash = self::hash( array_keys($this->scripts) );

		$file = [
			'path' => self::$dir_path . self::get__scripts__basename($hash), // server
			'url' => self::get__base_uploads__url() . self::get__scripts__basename($hash), // url
		];

		// if it already exists, don't rewrite it, just stop
		if( ! self::$fs->is_file( $file['path'] ) ){
			// write the JS file
			if( ! $this->write_js($file['path']) ){
				// stop if cannot write the file
				return;
			}
		}

		$index = 0;

		// remove occurances
		foreach ($this->scripts as $script_data) {

			$replace_with = '';

			if( self::is_debug_log_assets() ){
				$replace_with = sprintf('<!-- JS: %s -->', $script_data['tag']);
			}

			// place the combined file in the last's place
			if( $index === (count($this->scripts) - 1) ){
				$replace_with = sprintf(
					'<script defer type="text/javascript" id="rey-combined-js" src="%1$s?ver=%2$s"></script>',
					$file['url'],
					self::get_version($file)
				);
			}

			// update the buffer
			$this->update_buffer( $script_data['tag'], $replace_with );

			$index++;
		}

	}
	/**
	 * Write scripts to the uploads folder.
	 *
	 * @param string $filepath Target script
	 * @return void
	 */
	private function write_js($filepath){

		$js = [];
		$data_to_log = [];

		// go through scripts
		foreach ($this->scripts as $handle => $script) {

			// check for path and if the file actually exists
			if( ! (isset($script['data']['path']) && self::$fs->is_file( $script['data']['path'] )) ) {
				continue;
			}

			// collect css
			$js[$handle] = self::$fs->get_contents( $script['data']['path'] );

			// collect logging handles
			$data_to_log[] = $handle;
		}

		// check if CSS data exists
		if( ! empty($js) ){

			// log
			self::log( sprintf('Stored SCRIPT in "%2$s", FN: "%1$s".', $filepath, reycore__get_page_title() ) );

			// append handles
			$js[] = $this->debug_print_handles($data_to_log);

			// actually write the file
			return self::$fs->put_contents( $filepath, implode('', $js) );
		}

		return false;
	}

	/**
	 * Update the buffer by replacing the existing tags.
	 *
	 * @param string $tag
	 * @param string $replace_with
	 * @return void
	 */
	private function update_buffer($tag, $replace_with = '', $first_only = false){

		// get existing buffer content
		$buffer_content = $this->buffer->get_buffer();

		if( $first_only ){
			$new_content = preg_replace('/' . $tag . '/', $replace_with, $buffer_content, 1);
		}
		else {
			// remove existing ones
			$new_content = str_replace($tag, $replace_with, $buffer_content);
		}

		// update buffer content
		$this->buffer->set_buffer($new_content);

	}

	/**
	 * Adds a placeholder in the HEAD tag
	 * to be replaced by the main stylesheet's tag
	 *
	 * @return void
	 */
	public function add_head_stylesheet_placeholder(){
		echo self::HEAD_PLACEHOLDER;
	}

	/**
	 * Render the CSS tags
	 *
	 * @param string $type
	 * @param array $file
	 * @param string $attribute
	 * @return string
	 */
	public static function print_css_tag($type, $file, $attribute = 'href' ){
		return sprintf(
			'<link rel="stylesheet" id="rey-%1$s-css" %2$s="%3$s?ver=%4$s" type="text/css" media="all" />',
			$type,
			$attribute,
			$file['url'],
			self::get_version($file)
		);
	}

	/**
	 * Returns the current version with or without a suffix
	 *
	 * @param array $file
	 * @return string
	 */
	public static function get_version($file){

		$suffix = '';
		if( isset($file['path']) && self::$fs->is_file( $file['path'] )){
			$suffix = '.' . filemtime( $file['path'] );
		}

		return REY_CORE_VERSION . $suffix;
	}

	/**
	 * Very basic and minimal minification of the CSS.
	 * Should be actually done by caching plugins
	 *
	 * @param array $css
	 * @return string
	 */
	public static function minify_css($css = []){

		$css_content = str_replace(
			[
				': ',
				';  ',
				'; ',
				'  '
			],
			[
				':',
				';',
				';',
				' '
			],
			preg_replace( "/\r|\n/", '', implode('', $css) )
		);

		// // comments
		// $string = preg_replace('!/\*.*?\*/!s','', $string);
		// $string = preg_replace('/\n\s*\n/',"\n", $string);

		// // space
		// $string = preg_replace('/[\n\r \t]/',' ', $string);
		// $string = preg_replace('/ +/',' ', $string);
		// $string = preg_replace('/ ?([,:;{}]) ?/','$1',$string);

		// // trailing;
		// $string = preg_replace('/;}/','}',$string);

		return $css_content;
	}

	/**
	 * Create a hash based on an array
	 *
	 * @param array $data
	 * @return void
	 */
	public static function hash( $data ){
		return substr( md5( wp_json_encode( $data ) ), 0, 10 );
	}

	/**
	 * Set the filesystem app and the upload
	 * folders
	 *
	 * @return void
	 */
	public static function set_filesystem(){

		if( self::$fs ){
			return self::$fs;
		}

		if( !($fs = reycore__wp_filesystem()) ){
			return;
		}

		self::$fs = $fs;

		$dir_path = self::get__base_uploads__dir();

		if ( ! self::$fs->is_dir( $dir_path ) ) {
			self::$fs->mkdir( $dir_path );
		}

		self::$dir_path = trailingslashit( $dir_path );

		return self::$fs;
	}

	/**
	 * Get WordPress Uploads folder absolute path
	 *
	 * @return string
	 */
	private static function get__wp_uploads__dir() {
		global $blog_id;

		if ( empty( self::$wp_uploads_dir[ $blog_id ] ) ) {
			self::$wp_uploads_dir[ $blog_id ] = wp_upload_dir( null, false );
		}

		return self::$wp_uploads_dir[ $blog_id ];
	}

	/**
	 * Get Rey's Uploads folder absolute path
	 *
	 * @return string
	 */
	public static function get__base_uploads__dir() {
		$wp_upload_dir = self::get__wp_uploads__dir();
		return trailingslashit($wp_upload_dir['basedir']) . REY_CORE_THEME_NAME . '/';
	}

	/**
	 * Get Rey's Uploads folder relative site path
	 *
	 * @return string
	 */
	public static function get__base_uploads__url() {
		$wp_upload_dir = self::get__wp_uploads__dir();
		return trailingslashit(set_url_scheme( $wp_upload_dir['baseurl'] )) . REY_CORE_THEME_NAME . '/';
	}

	/**
	 * Get the stylesheets file base name
	 *
	 * @param string $hash
	 * @param string $type
	 * @return string
	 */
	private static function get__stylesheet__basename( $hash, $type = 'head' ){
		return sprintf('%s-%s%s.css', $type, $hash, AssetsManager::rtl());
	}

	/**
	 * Get the combined script file base name
	 *
	 * @param string $hash
	 * @return string
	 */
	private static function get__scripts__basename( $hash ){
		return sprintf('scripts-%s.js', $hash);
	}

	/**
	 * Log messages to console if debug enabled
	 *
	 * @param string $message
	 * @return void
	 */
	public static function log($message){
		if( self::is_debug_log_assets() ){
			error_log(var_export( '::Assets: ' . $message ,1));
		}
	}

	public static function start_log_time(){
		if( self::$logs['time'] && self::is_debug_log_assets() ){
			self::$current_time = microtime(true);
		}
	}

	public static function end_log_time($prefix = ''){
		if( self::$logs['time'] && self::is_debug_log_assets() ){
			if( ! is_null( self::$current_time ) ){
				echo self::log( $prefix . (microtime(true) - self::$current_time) );
			}
		}
	}

	/**
	 * Adds comments inside the files
	 *
	 * @param array $data_to_log
	 * @return string
	 */
	public function debug_print_handles( $data_to_log ){

		if( self::$logs['print_handles'] && self::is_debug_log_assets() ){
			return "\r\n" . '/** ' . "\r\n" . implode( "\r\n", $data_to_log ) . "\r\n" . reycore__get_page_title() . "\r\n" . '*/';
		}

	}

	/**
	 * Checks if logging is enabled
	 *
	 * @return boolean
	 */
	public static function is_debug_log_assets(){
		return defined('REY_DEBUG_LOG_ASSETS') && REY_DEBUG_LOG_ASSETS;
	}

	/**
	 * Retrieve assets paths
	 *
	 * @param array $assets
	 * @param string $type
	 * @return array
	 */
	public function get_assets_paths( $assets, $type = 'styles' ){

		$assets_to_return = [];

		if( empty($assets) ){
			return $assets_to_return;
		}

		$wp_assets = $type === 'styles' ? wp_styles() : wp_scripts();

		foreach ($assets as $handler) {

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

			$assets_to_return[$handler] = $src_;
		}

		return $assets_to_return;
	}


	/**
	 * Make sure URL's are absolute iso relative to original CSS location.
	 *
	 * @param string $file filename of optimized CSS-file.
	 * @param string $code CSS-code in which to fix URL's.
	 */
	static function fixurls( $file, $code )
	{
		// Switch all imports to the url() syntax.
		$code = preg_replace( '#@import ("|\')(.+?)\.css.*?("|\')#', '@import url("${2}.css")', $code );


		if ( preg_match_all( self::ASSETS_REGEX, $code, $matches ) ) {

			$wp_content_name = '/' . wp_basename( WP_CONTENT_DIR );
			$wp_root_dir = substr( WP_CONTENT_DIR, 0, strlen( WP_CONTENT_DIR ) - strlen( $wp_content_name ) );
			$wp_root_url = str_replace( $wp_content_name, '', content_url() );

			$file = str_replace( $wp_root_dir, '/', $file );
			/**
			 * Rollback as per https://github.com/futtta/autoptimize/issues/94
			 * $file = str_replace( AUTOPTIMIZE_WP_CONTENT_NAME, '', $file );
			 */
			$dir = dirname( $file ); // Like /themes/expound/css.

			/**
			 * $dir should not contain backslashes, since it's used to replace
			 * urls, but it can contain them when running on Windows because
			 * fixurls() is sometimes called with `ABSPATH . 'index.php'`
			 */
			$dir = str_replace( '\\', '/', $dir );
			unset( $file ); // not used below at all.

			$replace = array();
			foreach ( $matches[1] as $k => $url ) {
				// Remove quotes.
				$url      = trim( $url, " \t\n\r\0\x0B\"'" );
				$no_q_url = trim( $url, "\"'" );
				if ( $url !== $no_q_url ) {
					$removed_quotes = true;
				} else {
					$removed_quotes = false;
				}

				if ( '' === $no_q_url ) {
					continue;
				}

				$url = $no_q_url;
				if ( '/' === $url[0] || preg_match( '#^(https?://|ftp://|data:)#i', $url ) ) {
					// URL is protocol-relative, host-relative or something we don't touch.
					continue;
				} else { // Relative URL.

					$newurl = preg_replace( '/https?:/', '', str_replace( ' ', '%20', $wp_root_url . str_replace( '//', '/', $dir . '/' . $url ) ) );


					/**
					 * Hash the url + whatever was behind potentially for replacement
					 * We must do this, or different css classes referencing the same bg image (but
					 * different parts of it, say, in sprites and such) loose their stuff...
					 */
					$hash = md5( $url . $matches[2][ $k ] );
					$code = str_replace( $matches[0][ $k ], $hash, $code );

					if ( $removed_quotes ) {
						$replace[ $hash ] = "url('" . $newurl . "')" . $matches[2][ $k ];
					} else {
						$replace[ $hash ] = 'url(' . $newurl . ')' . $matches[2][ $k ];
					}
				}
			}

			$code = self::replace_longest_matches_first( $code, $replace );
		}

		return $code;
	}

	/**
	 * Given an array of key/value pairs to replace in $string,
	 * it does so by replacing the longest-matching strings first.
	 *
	 * @param string $string string in which to replace.
	 * @param array  $replacements to be replaced strings and replacement.
	 *
	 * @return string
	 */
	protected static function replace_longest_matches_first( $string, $replacements = array() )
	{
		if ( ! empty( $replacements ) ) {
			// Sort the replacements array by key length in desc order (so that the longest strings are replaced first).
			$keys = array_map( 'strlen', array_keys( $replacements ) );
			array_multisort( $keys, SORT_DESC, $replacements );
			$string = str_replace( array_keys( $replacements ), array_values( $replacements ), $string );
		}

		return $string;
	}

	// CLEAR DATA

	public function adminbar__add_refresh($nodes){

		if( ! current_user_can('administrator') ){
			return $nodes;
		}

		$assets_settings = \ReyCore\Plugin::instance()->assets_manager->get_settings();

		if( ! ($assets_settings['save_css'] || $assets_settings['save_js']) ){
			return $nodes;
		}

		if( isset($nodes['refresh']) ){
			$nodes['refresh']['nodes']['refresh_assets'] = [
				'title'  => esc_html__( 'Assets Cache', 'rey-core' ) . ' (CSS/JS)',
				'href'  => '#',
				'meta_title' => esc_html__( 'Refresh the combined and minified assets.', 'rey-core' ),
				'class' => 'qm-refresh-assets qm-refresher',
			];
		}

		return $nodes;
	}

	/**
	 * Refresh Assets through Ajax
	 *
	 * @since 2.0.0
	 **/
	public function adminbar__clear_assets()
	{
		if( $this->clear_assets() ){
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	private function clear_assets(){

		if( ! current_user_can('administrator') ){
			self::log( 'Incorrect permissions for clearing assets!' );
			return;
		}

		if( ! self::$fs ){
			self::log( 'Filesystem missing for clearing assets!' );
			return;
		}

		$assets_settings = \ReyCore\Plugin::instance()->assets_manager->get_settings();

		if( ! ($assets_settings['save_css'] || $assets_settings['save_js']) ){
			return;
		}

		$cleared = [];

		if( is_multisite() ){
			foreach ( get_sites() as $blog ) {
				switch_to_blog( $blog->blog_id );

					$dir_path = self::get__base_uploads__dir();

					if ( self::$fs->rmdir( $dir_path, true ) ) {
						self::$fs->mkdir( $dir_path );
						$cleared[] = true;
					}

				restore_current_blog();
			}
		}
		else {
			if ( self::$fs->rmdir( self::$dir_path, true ) ) {
				self::$fs->mkdir( self::$dir_path );
				$cleared[] = true;
			}
		}

		$status = in_array(true, $cleared, true);

		if( ! $status ){
			self::log( 'Assets not deleted!' );
		}
		else {
			self::log( 'Assets successfully cleaned-up!' );
		}

		do_action('reycore/assets/cleanup', $status);

		return true;
	}

	/**
	 * Cleanup after excludes settings modified
	 *
	 * @since 1.0.0
	 */
	public function clear__customize_save_perf__css_exclude( $setting )
	{
		if( ! method_exists($setting, 'value') ){
			return;
		}

		$this->clear_assets();
	}

	public function clear__basic() {
		$this->clear_assets();
	}

	public function handle_clear_data(){
		if( isset($_REQUEST['clear_assets']) && absint($_REQUEST['clear_assets']) === 1 ){
			$this->clear_assets();
		}
	}

}
