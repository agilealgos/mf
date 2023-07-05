<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mini Cart
 */
if ( !class_exists('\WooCommerce') ) {
    return;
}

$panel_class = 'rey-cartPanel';

if( $args['inline_buttons'] ){
	$panel_class .= ' --btns-inline';
} ?>

<div class="rey-cartPanel-wrapper rey-sidePanel js-rey-cartPanel woocommerce">
	<div class="<?php echo esc_attr($panel_class); ?>">

		<div class="rey-cartPanel-header">

			<div class="__tabs">

				<div class="__tab --active" data-item="cart">
					<?php $title_tag = reycore__header_cart_params('title_tag'); ?>
					<<?php echo esc_html($title_tag) ?> class="rey-cartPanel-title">
						<?php echo $args['cart_title'] ?>
						<?php \ReyCore\WooCommerce\Tags\MiniCart::get_cart_count(); ?>
					</<?php echo esc_html($title_tag) ?>>
				</div>

				<?php do_action('reycore/woocommerce/mini-cart/tabs', $args); ?>

			</div>

		</div>

		<div class="__tab-content --active" data-item="cart">
			<div class="widget woocommerce widget_shopping_cart">
				<div class="widget_shopping_cart_content"></div>
			</div>
		</div>

		<?php do_action('reycore/woocommerce/mini-cart/after_content', $args); ?>
	</div>
</div>
