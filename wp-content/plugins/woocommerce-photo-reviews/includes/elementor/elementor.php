<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
	return;
}
add_action( 'elementor/widgets/widgets_registered', function () {
	$args = array(
		'WCPR_Elementor_Reviews_Widget'        => 'reviews-widget.php',
		'WCPR_Elementor_Review_Form_Widget'    => 'review-form-widget.php',
		'WCPR_Elementor_Rating_Widget'         => 'rating-widget.php',
		'WCPR_Elementor_Overall_Rating_Widget' => 'overall-rating-widget.php',
	);
	foreach ( $args as $k => $v ) {
		if ( is_file( WOOCOMMERCE_PHOTO_REVIEWS_INCLUDES . 'elementor/' . $v ) ) {
			require_once( $v );
			$widget = new $k();
			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				Elementor\Plugin::instance()->widgets_manager->register( $widget );
			} else {
				Elementor\Plugin::instance()->widgets_manager->register_widget_type( $widget );
			}
		}
	}
} );