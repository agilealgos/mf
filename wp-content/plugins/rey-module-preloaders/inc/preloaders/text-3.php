<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function($html){

	if( $text = get_theme_mod('header_prl_text', esc_html__('LOADING', 'rey-module-preloaders')) ){

		ob_start();
		?>
		<div class="rey-sitePreloader-text3">

			<svg class="textWrapper" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="70 70 350 350">
				<title><?php echo esc_html($text) ?></title>
				<defs>
					<path d="M243.2, 382.4c-74.8,
					0-135.5-60.7-135.5-135.5s60.7-135.5,135.5-135.5s135.5, 60.7, 135.5,
					135.5 S318, 382.4, 243.2, 382.4z" id="textcircle" />
				</defs>
				<text dy="0" class="rey-sitePreloader-text">
					<textPath xlink:href="#textcircle">
						<?php echo esc_html($text) ?>
					</textPath>
				</text>
			</svg>

			<div class="imgWrapper">
				<?php
					if( $img = get_theme_mod('header_prl_image', '') ){
						echo wp_get_attachment_image($img, 'full', false);
					}
				?>
			</div>

		</div>
		<?php

		return ob_get_clean();
	}
	return $html;
});
