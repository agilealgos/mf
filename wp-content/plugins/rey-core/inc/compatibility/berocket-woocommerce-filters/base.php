<?php
namespace ReyCore\Compatibility\BerocketWoocommerceFilters;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{

	public function __construct() {
		add_action('wp_footer', [$this, 'compatibility_ajax_load_more'], 20);
	}

	public function compatibility_ajax_load_more($files) {
		?>
		<script>
			(function($){
				$(document).on('berocket_ajax_filtering_end', function(e){
					rey.hooks.doAction('product/loaded', document.querySelectorAll('.rey-siteMain ul.products li.product') );
				});
			})(jQuery);
		</script>
		<?php
	}

}
