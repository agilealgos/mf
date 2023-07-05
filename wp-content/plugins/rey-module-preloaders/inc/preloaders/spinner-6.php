<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<div class="rey-preloaderSpinner6-sk-folding-cube">
			<div class="rey-preloaderSpinner6-sk-cube1 rey-preloaderSpinner6-sk-cube"></div>
			<div class="rey-preloaderSpinner6-sk-cube2 rey-preloaderSpinner6-sk-cube"></div>
			<div class="rey-preloaderSpinner6-sk-cube4 rey-preloaderSpinner6-sk-cube"></div>
			<div class="rey-preloaderSpinner6-sk-cube3 rey-preloaderSpinner6-sk-cube"></div>
		</div>
	<?php
	return ob_get_clean();
});
