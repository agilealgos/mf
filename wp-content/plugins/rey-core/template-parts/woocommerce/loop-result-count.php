<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="woocommerce-result-count">
	<?php

	$first = $last = '';

	if ( 1 === intval( $total ) ) {
		$text = __( 'Showing the single result', 'woocommerce' );
	}
	elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %d: total results */
		$text = sprintf( _n( 'Showing all %d result', 'Showing all %d results', $total, 'woocommerce' ), $total );
	}
	else {

		$first = ( $per_page * $current ) - $per_page + 1;
		$last  = min( $total, $per_page * $current );

		/* translators: 1: first result 2: last result 3: total results */
		$text = sprintf( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'woocommerce' ), $first, $last, $total );
	}

	if( $custom_text = get_theme_mod('loop_product_count__text', '') ){

		if( 1 < intval( $total ) ){

			if( strpos($custom_text, '{{FIRST}}') !== false && empty($first) ){
				$result_count_custom_text = sprintf('<span class="total-count">%d</span> / <span class="total-count">%d</span>', $total, $total);
			}
			else {

				$result_count_custom_text = $custom_text;

				foreach ([
					[
						'placeholder' => '{{TOTAL}}',
						'replacement' => $total,
						'css_class' => 'total-count',
					],
					[
						'placeholder' => '{{FIRST}}',
						'replacement' => $first,
						'css_class' => 'first-count',
					],
					[
						'placeholder' => '{{LAST}}',
						'replacement' => $last,
						'css_class' => 'last-count',
					],
				] as $item) {
					$result_count_custom_text = str_replace($item['placeholder'], sprintf('<span class="%s">%s</span>', $item['css_class'], $item['replacement']), $result_count_custom_text);
				}

			}

			echo apply_filters('reycore/woocommerce/loop/result_count_text', $result_count_custom_text, $custom_text, $total, $first, $last);
		}

	}
	else {
		echo $text;
	} ?>
</div>


<?php

global $wp_query;

// $total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$total   = $wp_query->max_num_pages;
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}


reyCoreAssets()->add_styles('rey-pagination');

echo '<nav class="woocommerce-pagination rey-pagination categorypagetoppagination">';
	echo paginate_links( apply_filters( 'woocommerce_pagination_args', [
		'base'         => $base,
		'format'       => $format,
		'add_args'     => false,
		'current'      => max( 1, $current ),
		'total'        => $total,
		'mid_size'     => 3,
		'show_all'     => false,
		'end_size'     => 1,
		'prev_next'    => true,
		'prev_text'    => reycore__arrowSvg(false),
		'next_text'    => reycore__arrowSvg(),
		'type'         => 'plain',
		'add_fragment' => ''
	] ) );
echo '</nav>';
?>
<style>
	.categorypagetoppagination .page-numbers {
		margin: 0px !important;
	}
	.categorypagetoppagination{
		margin-top: 0px !important;
		margin-right: 40px !important;
	}
</style>