<?php
namespace ReyCore\Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WidgetsAssets
{

	private $inline_already_added = [];

	public function __construct()
	{
		add_action( 'reycore/assets/register_scripts', [$this, 'register_widgets_assets']);
		add_action( 'elementor/element/before_parse_css', [$this, 'add_inline_widgets_styles_to_post_file'], 0, 2 );
		add_action( 'elementor/element/parse_css', [$this, 'add_section_custom_css_to_post_file'], 10, 2 );
	}

	/**
	 * Register elements widgets assets
	 *
	 * @since 2.0.0
	 */
	public function register_widgets_assets( $assets ){

		$styles = [];

		$source_styles = \ReyCore\Plugin::instance()->elementor->widgets->widgets_styles;

		// load the inlined styles too (when in edit mode)
		if( reycore__elementor_edit_mode() ){
			$source_styles = array_merge($source_styles, \ReyCore\Plugin::instance()->elementor->widgets->inline_widgets_styles_paths);
		}

		foreach ( $source_styles as $id => $style ) {

			$styles[ Widgets::ASSET_PREFIX . $id ] = [
				'src'     => $style,
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			];

		}

		$assets->register_asset('styles', $styles);

		$scripts = [];

		foreach ( \ReyCore\Plugin::instance()->elementor->widgets->widgets_scripts as $id => $js_file_path ) {
			$scripts[ Widgets::ASSET_PREFIX . $id ] = [
				'src'     => $js_file_path,
				'deps'    => ['elementor-frontend', 'reycore-elementor-frontend'],
				'version'   => REY_CORE_VERSION,
			];
		}

		$assets->register_asset('scripts', $scripts);
	}

	/**
	 * Appends Section custom css to stylesheet
	 *
	 * @param object $post_css
	 * @param object $element
	 * @return string
	 */
	public function add_section_custom_css_to_post_file( $post_css, $element ){

		if ( $post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS ) {
			return;
		}

		$rey_custom_css = $element->get_settings('rey_custom_css');

		if( ! ($css = trim( $rey_custom_css )) ) {
			return;
		}

		$type = $element->get_type();
		$unique_selector = $post_css->get_element_unique_selector( $element );

		$map_inner = [
			'section' => ' > .elementor-container',
			'container' => ' > .e-con-inner',
			'column' => ' > .elementor-widget-wrap',
			'widget' => ' > .elementor-widget-container',
		];

		$sr = [
			'SELECTOR-INNER' => $unique_selector . $map_inner[ $type ],
			'SELECTOR' => $unique_selector,
			'SECTION-ID' => $unique_selector, // legacy
		];

		$styles = str_replace( array_keys($sr), $sr, $css );

		$post_css->get_stylesheet()->add_raw_css( $styles );

	}

	/**
	 * Appends inline widgets styles css to stylesheet
	 *
	 * @param object $post_css
	 * @param object $element
	 * @return string
	 */
	public function add_inline_widgets_styles_to_post_file( $post_css, $element ){

		if ( $post_css instanceof \Elementor\Core\DynamicTags\Dynamic_CSS ) {
			return;
		}

		if( 'widget' !== $element->get_type() ){
			return;
		}

		$inline_styles = \ReyCore\Plugin::instance()->elementor->widgets->inline_widgets_styles;

		$el_name = Helper::unprefixed_widget_name( $element->get_unique_name() );

		// bail if not this element
		if( ! (isset($inline_styles[$el_name]) && ($element_styles = $inline_styles[$el_name])) ){
			return;
		}

		// bail if already added
		if( in_array($el_name, $this->inline_already_added, true) ){
			return;
		}

		if( ! ( $wp_filesystem = reycore__wp_filesystem() ) ){
			return;
		}

		$styles = '';

		foreach ($element_styles as $css_file) {
			if( $wp_filesystem->is_file( $css_file ) ){
				$styles .= $wp_filesystem->get_contents( $css_file );
			}
		}

		$this->inline_already_added[] = $el_name;

		$post_css->get_stylesheet()->add_raw_css( $styles );
	}

}
