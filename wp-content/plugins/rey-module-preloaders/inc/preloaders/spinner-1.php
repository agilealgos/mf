<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<div class="rey-preloaderSpinner1-root rey-preloaderSpinner1-indeterminate" role="progressbar">
			<svg class="rey-preloaderSpinner1-svg" viewBox="22 22 44 44">
				<circle class="rey-preloaderSpinner1-circle rey-preloaderSpinner1-circleIndeterminate" cx="44" cy="44" r="20.2" fill="none" stroke-width="3.6">

				</circle>
			</svg>
		</div>
	<?php
	return ob_get_clean();
});
