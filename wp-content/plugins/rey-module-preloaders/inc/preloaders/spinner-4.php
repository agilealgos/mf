<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<!-- https://tobiasahlin.com/spinkit/ -->
		<div class="rey-preloaderSpinner4"></div>
	<?php
	return ob_get_clean();
});
