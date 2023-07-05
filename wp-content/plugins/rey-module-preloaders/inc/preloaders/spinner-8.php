<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<div class="rey-preloader8">
			<?php
				if( $img = get_theme_mod('header_prl_image', '') ){
					echo wp_get_attachment_image($img, 'full', false);
				}
			?>
		</div>
	<?php
	return ob_get_clean();
});
