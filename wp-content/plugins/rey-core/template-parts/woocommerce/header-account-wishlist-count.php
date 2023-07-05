<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = reycore_wc__get_account_panel_args(); ?>

<span class="rey-headerAccount-count">

	<?php
	if( class_exists('\ReyCore\WooCommerce\Tags\Wishlist') && $args['wishlist'] && $args['counter'] != '' ){
		echo \ReyCore\WooCommerce\Tags\Wishlist::get_wishlist_counter_html();
	}

	echo reycore__get_svg_icon(['id' => 'close', 'class' => '__close-icon']) ?>

</span>
