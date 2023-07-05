<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="rey-dashBox">
	<div class="rey-dashBox-inner">

		<h2 class="rey-dashBox-title">
			<span><?php printf( esc_html__('Register %s', 'rey'), rey__get_props('theme_title')) ?></span>
			<?php if( rey__get_props('branding') ){
				echo rey__get_svg_icon(['id'=>'logo', 'class'=>'rDash-logo']);
			} ?>
		</h2>

		<div class="rey-dashBox-content">

			<p><?php printf( esc_html__('Register your copy of %s theme to enable importing demos, updates, premium plugins and other features.', 'rey'), rey__get_props('theme_title') ) ?></p>

			<form class="rey-adminForm js-dashBox-registerForm" method="post" action="#">

				<?php include __DIR__ . '/tpl-register-form.php'; ?>

				<button type="submit" class="rey-adminBtn rey-adminBtn-secondary"><?php esc_html_e( 'REGISTER', 'rey' ); ?></button>
			</form>

		</div>
	</div>
</div>
