<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = reycore_wc__get_account_panel_args();

$wishlist_counter = $wishlist_title = '';

if( class_exists('\ReyCore\WooCommerce\Tags\Wishlist') ){
	$wishlist_counter = \ReyCore\WooCommerce\Tags\Wishlist::get_wishlist_counter_html();
	$wishlist_title = \ReyCore\WooCommerce\Tags\Wishlist::title();
}

if( ! $args['wishlist'] ) {
	return;
} ?>

<div class="rey-accountTabs">

	<div class="rey-accountTabs-item --active" data-item="account">
		<?php
		printf( '<span>%s</span>',
			(is_user_logged_in() && $args['button_text_logged_in']) ? do_shortcode($args['button_text_logged_in']) : do_shortcode($args['button_text'])
		); ?>
	</div>

	<div class="rey-accountTabs-item" data-item="wishlist">
		<?php printf( '<span>%s</span>%s', $wishlist_title, $wishlist_counter ); ?>
	</div>

</div>
