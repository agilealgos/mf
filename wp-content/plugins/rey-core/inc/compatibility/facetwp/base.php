<?php
namespace ReyCore\Compatibility\FacetWP;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase {

	public function __construct() {
		add_action('wp_footer', [$this, 'scripts'], 20);
	}

	public function scripts() {
		?>
		<script>
			(function($){
				$(document).on('facetwp-loaded', function(e){
					rey.hooks.doAction('ajaxfilters/finished', $('.rey-siteMain ul.products ')[0]);
				})
			})(jQuery);
		</script>
		<?php
	}

}
