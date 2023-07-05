<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
} ?>

<div class="rey-pppSelector rey-loopInlineList">
	<span class="rey-loopInlineList__label"><?php echo $args['label'] ?></span>
	<ul class="rey-loopInlineList-list">
	<?php
	foreach ($args['options'] as $key => $value) {
		printf( '<li data-count="%1$s" class="%2$s">%1$s</li>',
			$value,
			$value === $args['selected'] ? 'is-active': ''
		);
	}
	?>
	</ul>
</div>
