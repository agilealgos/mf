<?php
defined( 'ABSPATH' ) || exit; ?>

<div class="rey-requestQuote-modal --hidden" data-id="<?php echo get_the_ID(); ?>">
	<?php

		printf('<h3 class="rey-requestQuote-modalTitle">%s</h3>', $args['defaults']['title'] );

		if( is_product() ){

			$sku = '';

			if( ($product = wc_get_product()) && $psku = $product->get_sku() ){
				$sku = sprintf(' (%s)', $psku);
			}

			printf('<p class="rey-requestQuote-productData"><strong class="__title">%s</strong></p>', get_the_title() . $sku );
		}

		echo $args['form'];
	?>
</div>
