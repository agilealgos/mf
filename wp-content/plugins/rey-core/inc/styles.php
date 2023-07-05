<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Styles {

	const PAGE_STYLE_TRANSIENT_KEY = 'rey_page_styles_';

	public function __construct(){
		add_filter( 'rey/css_styles', [$this, 'css_styles'] );
		add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts']);
		add_filter( 'reycore/page_css_styles', [$this, 'page_styles']);
		add_action( 'acf/save_post', [$this, 'clear_page_styles'], 20);
	}

	/**
	 * Root CSS Styles
	 *
	 * @since 1.0.0
	 */
	public function root_css_styles()
	{
		$styles = '';

		$wrappers = apply_filters('reycore/styles/font_quotes', false) ? '"' : '';

		$rey_gg_fonts = [];

		if( $webfonts = Plugin::instance()->fonts ){

			if( $rey_gg_fonts_list = $webfonts->get_google_fonts_list() ){
				$rey_gg_fonts = wp_list_pluck($rey_gg_fonts_list, 'font_name');
			}

			// Font Family typography
			if( ($primary_typo = get_theme_mod('typography_primary', [])) && isset($primary_typo['font-family']) && $pff = $primary_typo['font-family'] ){
				if( in_array($pff, $rey_gg_fonts, true) ){
					$pff = $pff . $webfonts::SYMBOL;
				}
				$styles .= "--primary-ff:{$wrappers}{$pff}{$wrappers};";
			}

			if( ($secondary_typo = get_theme_mod('typography_secondary', [])) && isset($secondary_typo['font-family']) && $sff = $secondary_typo['font-family'] ){
				if( in_array($sff, $rey_gg_fonts, true) ){
					$sff = $sff . $webfonts::SYMBOL;
				}
				$styles .= "--secondary-ff:{$wrappers}{$sff}{$wrappers};";
			}

		}

		// make sure it's not inherited from Elementor
		if( ! get_theme_mod('typography_inherit_elementor', false) ) {

			// Body
			$styles .= reycore__kirki_typography_process( [
				'name' => 'typography_body',
				'prefix' => '--body-',
				'supports' => [
					'font-family', 'font-size', 'line-height', 'font-weight'
				],
				'default_values' => [
					'font-family' => 'var(--primary-ff)'
				],
			] );

		}

		// LINK HOVER

		$link_hover_color = '';
		if( $body_link_hover_color = get_theme_mod('style_link_color_hover') ){
			$link_hover_color = $body_link_hover_color;
		}
		else {
			// calculate hover color (only if not empty)
			if( $body_link_color = get_theme_mod('style_link_color') ){

				// if global color
				if( strpos($body_link_color, 'var') !== false ){
					$link_hover_color = $body_link_color;
				}
				else {
					$link_hover_color = \ReyCore\Libs\Colors::adjust_color_brightness( $body_link_color, 15 );
				}

			}
		}

		if( $link_hover_color ){
			$styles .= sprintf("--link-color-hover:%s;", $link_hover_color );
		}

		// ACCENT
		$accent_color = get_theme_mod('style_accent_color', '#212529');

		if( $accent_color ){
			$styles .= "--accent-color:{$accent_color};";
		}

		// Accent Hover
		$accent_hover_color = get_theme_mod('style_accent_color_hover');

		// if hover is not set, try to generate brighter color from accent
		if( ! $accent_hover_color && $accent_color && strpos($accent_color, 'var') === false ){
			$accent_hover_color = \ReyCore\Libs\Colors::adjust_color_brightness( $accent_color, -20 );
		}

		if( $accent_hover_color ){
			$styles .= "--accent-hover-color:". $accent_hover_color .";";
		}

		// Accent Text
		$accent_text_color = get_theme_mod('style_accent_color_text');

		if( ! $accent_text_color && $accent_color && strpos($accent_color, 'var') === false ){
			$accent_text_color = \ReyCore\Libs\Colors::readable_colour( $accent_color );
		}

		if( $accent_text_color ){
			$styles .= "--accent-text-color:". $accent_text_color .";";
		}

		// Accent Hover Text
		if( $accent_text_hover_color = get_theme_mod('style_accent_color_text_hover') ){
			$styles .= "--accent-text-hover-color:". $accent_text_hover_color .";";
		}

		return $styles;
	}

	public function css_styles( $styles_output = [] ) {

		// Body
		$root = $this->root_css_styles();

		if( $root ) {
			$styles_output[] = ':root{' . $root . '}';
		}

		$tablet_styles = reycore__kirki_typography_process( [
			'name' => 'typography_body_tablet',
			'prefix' => '--body-',
			'supports' => [
				'font-family', 'font-size', 'line-height', 'font-weight'
			],
		] );

		if( $tablet_styles ){
			$styles_output[] = sprintf('@media (min-width: 768px) and (max-width: 1024px){:root{%s}}', $tablet_styles);
		}

		$mobile_styles = reycore__kirki_typography_process( [
			'name' => 'typography_body_mobile',
			'prefix' => '--body-',
			'supports' => [
				'font-family', 'font-size', 'line-height', 'font-weight'
			],
		] );

		if( $mobile_styles ){
			$styles_output[] = sprintf('@media (max-width: 767px){:root{%s}}', $mobile_styles);
		}

		// Mobile Nav Breakpoint
		$styles_output['menu_breakpoint'] = '@media (max-width: '. get_theme_mod('nav_breakpoint', '1024') .'px) {
			:root {
				--nav-breakpoint-desktop: none;
				--nav-breakpoint-mobile: block;
			}
		}';

		$custom_container_width = get_theme_mod('custom_container_width', 'default');
		/**
		 * For VW, make sure it's applied above 1440px (default px value).
		 */
		if( $custom_container_width === 'vw' ){
			$styles_output[] = '@media (min-width: 1440px) {
				:root {
					--container-max-width: calc('. get_theme_mod('container_width_vw', 90) .'vw - (var(--page-padding-left) + var(--page-padding-right)));
				}
			}';
		}
		/**
		 * Intentionally using full as separated from vw's 100vw
		 * because 100vw means site width's with scrollbar included.
		 * Adding --site-width var, will use the real site's width (without scrollbar).
		 */
		else if( $custom_container_width === 'full' ){
			$styles_output[] = ':root {
					--container-max-width: var(--site-width, 100vw);
			}';
		}

		$header_zindex = get_theme_mod('header_af__zindex', '');

		if( $header_zindex !== '' ){
			if( $header_zindex == -1 ){
				$header_zindex = 'auto';
			}
			$styles_output[] = '.rey-siteHeader.header-pos--absolute, .rey-siteHeader.header-pos--fixed {z-index:'.$header_zindex.'}';
		}

		return $styles_output;
	}

	public function admin_scripts() {

		wp_register_style( 'reycore-gutenberg-css-styles', false );
		wp_enqueue_style( 'reycore-gutenberg-css-styles' );

		$styles = '--body-bg-color: ' . get_theme_mod('style_bg_image_color', '#fff') . ';';
		$styles .= $this->root_css_styles();

		$custom_css = '.edit-post-visual-editor.editor-styles-wrapper {'.$styles.'};';

		// Add the style
		wp_add_inline_style( 'reycore-gutenberg-css-styles', $custom_css);
	}

	private function get_page_styles(){

		$page_styles = [];

		$root_css = '';

		if( $text_color = reycore__acf_get_field( 'header_text_color') ) {
			$root_css .= '--header-text-color: ' . $text_color . ';' ;
		}

		if( $custom_container_width = reycore__acf_get_field( 'custom_container_width') ){

			$width_css = '';
			$width_css_selector = ':root';

			if( reycore__acf_get_field( 'apply_only_to_main_content') ){
				$width_css_selector = '.rey-siteContainer';
			}

			if( $custom_container_width === 'px' ){

				$width_px = 1440;
				if( $container_width_px = reycore__acf_get_field( 'container_width_px') ){
					$width_px = $container_width_px;
				}

				$width_css .= '--container-max-width: '. absint($width_px) .'px;';
			}
			/**
			 * Intentionally using full as separated from vw's 100vw
			 * because 100vw means site width's with scrollbar included.
			 * Adding --site-width var, will use the real site's width (without scrollbar).
			 */
			else if( $custom_container_width === 'full' ){
				$width_css .= '--container-max-width: var(--site-width, 100vw);';
			}

			/**
			 * For VW, make sure it's applied above 1440px (default px value).
			 */
			if( $custom_container_width === 'vw' ){

				$width_vw = 90;

				if( $container_width_vw = reycore__acf_get_field( 'container_width_vw') ){
					$width_vw = $container_width_vw;
				}

				$page_styles[] = '@media (min-width: 1440px) {
					'. $width_css_selector .' {
						--container-max-width: calc('. absint($width_vw) .'vw - (var(--page-padding-left) + var(--page-padding-right)));
					}
				}';
			}

			if( !empty($width_css) && is_array($page_styles) ) {
				$page_styles[] = $width_css_selector . '{' . $width_css . '}';
			}
		}

		if( $content_padding = reycore__acf_get_field( 'content_padding') ){
			foreach ($content_padding as $prop => $value) {
				if( $value === '' ){
					continue;
				}
				$root_css .= sprintf('--content-padding-%s:%dpx;', $prop, absint($value));
			}
		}

		if( $container_spacing = reycore__acf_get_field( 'container_spacing') ){
			$root_css .= sprintf('--rey-container-spacing:%1$dpx;--main-gutter-size:%1$dpx;', absint($container_spacing));
		}

		if( $top_sticky_gs_color = reycore__acf_get_field( 'top_sticky_gs_color') ) {
			$root_css .= '--sticky-gs-top-color: ' . $top_sticky_gs_color . ';' ;
		}

		if( $top_sticky_gs_bg_color = reycore__acf_get_field( 'top_sticky_gs_bg_color') ) {
			$root_css .= '--sticky-gs-top-bg-color: ' . $top_sticky_gs_bg_color . ';' ;
		}

		if( !empty($root_css) && is_array($page_styles) ) {
			$page_styles[] = ':root {' . $root_css . '}';
		}

		return $page_styles;
	}



	/**
	 * Get custom CSS styles
	 *
	 * @since 1.0.0
	 */
	public function page_styles($styles_output){

		$maybe_cache = is_singular() && ($pid = get_the_ID());

		if( ! apply_filters( 'reycore/page_styles/cache', $maybe_cache ) ){
			return array_merge( $styles_output, $this->get_page_styles() );
		}

		if( $maybe_cache ){

			$transient_name = self::PAGE_STYLE_TRANSIENT_KEY . $pid;

			if( false === ($page_styles = get_transient( $transient_name )) ){

				$page_styles = $this->get_page_styles();

				set_transient( $transient_name, $page_styles, MONTH_IN_SECONDS );
			}

			if( is_array($page_styles) ){
				return array_merge($styles_output, $page_styles);
			}

		}

		return $styles_output;
	}

	public function clear_page_styles( $pid ){
		delete_transient( self::PAGE_STYLE_TRANSIENT_KEY . absint($pid) );
	}

}
