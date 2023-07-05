<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function($html){

	if( $text = get_theme_mod('header_prl_text', esc_html__('LOADING', 'rey-module-preloaders')) ){

		$text_split = str_split($text);
		$c = count($text_split);

		$output = '<div class="rey-sitePreloader-text">';
			foreach($text_split as $i => $char){
				$output .= sprintf(
					'<span style="animation-delay:%ss;">%s</span>',
					($i / 50) + 0.007,
					$char
				);
			}
		$output .= '</div>';

		return $output;
	}
	return $html;
});
