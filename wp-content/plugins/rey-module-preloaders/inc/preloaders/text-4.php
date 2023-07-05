<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function($html){

	if( $text = get_theme_mod('header_prl_text', esc_html__('LOADING', 'rey-module-preloaders')) ){
		return sprintf('<div class="rey-sitePreloader-text">%s</div>', esc_html($text));
	}

	return $html;
});
