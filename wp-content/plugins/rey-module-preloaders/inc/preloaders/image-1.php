<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function($html){

	if( $img = get_theme_mod('header_prl_image', '') ){
		return sprintf('<div class="rey-sitePreloader-img">%s</div>', wp_get_attachment_image($img, 'full', false));
	}

	return $html;
});
