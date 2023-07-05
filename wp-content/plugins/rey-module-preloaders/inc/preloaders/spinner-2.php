<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('rey/site_preloader_html', function(){
	ob_start();
	?>
		<!-- https://codepen.io/matchboxhero/pen/bomWGK -->
		<svg class="rey-preloaderSpinner2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 340 340">
			<circle cx="170" cy="170" r="160" stroke="currentColor"/>
			<circle cx="170" cy="170" r="135" stroke="currentColor" opacity="0.4"/>
			<circle cx="170" cy="170" r="110" stroke="currentColor"/>
			<circle cx="170" cy="170" r="85" stroke="currentColor" opacity="0.4"/>
		</svg>
	<?php
	return ob_get_clean();
});
