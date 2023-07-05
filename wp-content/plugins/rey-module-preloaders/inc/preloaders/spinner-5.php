<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<!-- https://tobiasahlin.com/spinkit/ -->
		<div class="rey-preloaderSpinner5">
			<div class="rey-preloaderSpinner5-rect1"></div>
			<div class="rey-preloaderSpinner5-rect2"></div>
			<div class="rey-preloaderSpinner5-rect3"></div>
			<div class="rey-preloaderSpinner5-rect4"></div>
			<div class="rey-preloaderSpinner5-rect5"></div>
		</div>
	<?php
	return ob_get_clean();
});
