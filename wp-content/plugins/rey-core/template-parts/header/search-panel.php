<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = reycore_wc__get_header_search_args();

$classes = '';

if( 'side' === $args['search_style'] ){
	$classes = 'rey-sidePanel';
}
elseif( 'wide' === $args['search_style'] ){
	$classes = '--hidden';
} ?>

<div class="rey-searchPanel rey-searchForm rey-searchAjax js-rey-ajaxSearch <?php echo esc_attr( $classes ) ?>" data-style="<?php echo esc_attr( $args['search_style'] ) ?>" id="rey-searchPanel">

	<div class="rey-searchPanel-inner">

		<form role="search" action="<?php echo esc_url(home_url('/')) ?>" method="get">

			<?php
			$id = uniqid('search-input-');

			/**
			 * To remove the title's link, please use:
			 *
			 *   add_filter('rey/search_form/title', function($text, $id){
			 * 	  	return sprintf( '<label for="%2$s">%1$s</label>', esc_html__('Search', 'rey-core'), esc_attr($id) );
			 *   }, 10, 2);
			 */

			echo apply_filters(
				'rey/search_form/title',
				sprintf(
					'<label for="%3$s">%1$s %2$s</label>',
					esc_html__('Search', 'rey-core'),
					str_replace(['http://', 'https://'], '', get_site_url()),
					esc_attr($id)
				),
				$id
			); ?>

			<div class="rey-searchPanel-innerForm">

				<input type="search" name="s" placeholder="<?php echo esc_attr( get_theme_mod('header_search__input_placeholder', __( 'type to search..', 'rey-core' )) ); ?>" id="<?php echo esc_attr($id) ?>" value="<?php echo (isset($_REQUEST['s']) && ($s = reycore__clean($_REQUEST['s']))) ? $s : ''; ?>" />

				<div class="rey-headerSearch-actions">
					<?php do_action('rey/search_form'); ?>
				</div>

			</div>
			<?php do_action('wpml_add_language_form_field'); ?>

		</form>

		<?php do_action('reycore/search_panel/after_search_form', $args); ?>
		<!-- .row -->
	</div>

</div>
<?php if( 'wide' === $args['search_style'] ): ?>
	<div class="rey-searchPanel-wideOverlay rey-overlay --no-js-close"></div>
<?php endif; ?>
<!-- .rey-searchPanel -->
