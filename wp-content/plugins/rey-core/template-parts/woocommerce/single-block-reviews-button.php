<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="rey-reviewsBtn js-reviewsBtn <?php echo implode(' ', array_map('esc_attr', apply_filters('reycore/woocommerce/single/reviews_btn', ['btn', 'style' => 'btn-secondary-outline', 'btn--block']))) ?>" role="button">
	<span><?php echo $args['text'] ?></span>
</div>
