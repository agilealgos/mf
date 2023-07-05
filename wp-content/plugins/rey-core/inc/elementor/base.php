<?php
namespace ReyCore\Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use \ReyCore\Elementor\Helper;

class Base
{

	public static $props = [];

	public $frontend;

	public $widgets;

	public $widgets_manager;

	public function __construct()
	{

		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		new TemplateLibrary\Base();
		new Editor();
		new WidgetsOverrides();

		add_action( 'init', [ $this, 'init' ], 0 );
		add_action( 'rey/flush_cache_after_updates', [ $this, 'flush_cache' ] );
		add_action( 'reycore/assets/cleanup', [ $this, 'flush_cache' ] );
	}

	public function init(){

		self::$props = [
			'elementor_major_version'   => Helper::get_elementor_major_version(),
			'pushback_fallback_enabled' => Helper::is_pushback_fallback_enabled(),
			'optimized_dom'             => Helper::is_optimized_dom(),
			'container'                 => Helper::is_experiment_active('container'),
			'elementor36'               => defined('ELEMENTOR_VERSION') ? version_compare(ELEMENTOR_VERSION, '3.6', '>=') : false,
		];

		$this->widgets = new Widgets();
		$this->widgets_manager = new WidgetsManager;

		new WidgetsAssets();
		new Assets();
		$this->frontend = new Frontend();
		new GlobalSections();
		$this->cover = TagCover::instance();

		do_action('reycore/elementor');

	}

	/**
	 * Flush Elementor cache
	 * - after updates
	 */
	public function flush_cache(){
		if( is_multisite() ){
			$blogs = get_sites();
			foreach ( $blogs as $keys => $blog ) {
				$blog_id = $blog->blog_id;
				switch_to_blog( $blog_id );
					\Elementor\Plugin::$instance->files_manager->clear_cache();
				restore_current_blog();
			}
		}
		else {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

}
