<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<!-- https://codepen.io/nicholasruggeri/pen/YNRwYW -->
		<div class="rey-preloaderSpinner7"><div></div></div>
	<?php
	return ob_get_clean();
});
