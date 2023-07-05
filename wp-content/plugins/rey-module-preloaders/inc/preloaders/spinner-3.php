<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<!-- https://codepen.io/teodormoq/pen/WbXRWj -->
		<div class="rey-preloaderSpinner3">
			<div class="rey-preloaderSpinner3-inner"></div>
		</div>
	<?php
	return ob_get_clean();
});
