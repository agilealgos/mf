<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$panel_type = get_theme_mod('header_fs_type', '');

?>

<div class="rey-fsNav-panelWrapper" id="js-fsNav-panelWrapper">
	<div class="rey-fsNav-panel --menu-items-fix">

		<button class="btn rey-fsNav-panelClose" aria-label="<?php esc_html__('Close', 'rey-module-fullscreen-menu') ?>"></button>

		<?php if( get_theme_mod('header_fs_theme_animcurtain', true) ): ?>
			<div class="rey-fsNav-panelMask rey-fsNav-panelMask--1"></div>
			<div class="rey-fsNav-panelMask rey-fsNav-panelMask--2"></div>
		<?php endif; ?>

		<div class="rey-fsNav-panelContent" data-panel-type="<?php echo esc_attr($panel_type) ?>">

			<?php if( $panel_type === 'menu' ): ?>

			<?php
				if( $logo = get_theme_mod('header_fs_logo', '') ){
					printf( '<div class="rey-fsNav-panelLogo">%s</div>', wp_get_attachment_image($logo, 'full', false) );
				}
			?>

			<div class="rey-fsNav-panelMenu ">
				<?php

				if( $menu_source = get_theme_mod('header_fs_menu', '') ){

					if( class_exists('\Elementor\Plugin') ){
						$fancy_menu = \Elementor\Plugin::instance()->elements_manager->create_element_instance(
							[
								'elType' => 'widget',
								'widgetType' => 'reycore-menu-fancy',
								'id' => 'reycore-menu-fancy-' . uniqid(),
								'settings' => [
									'menu_id' => $menu_source,
									'menu_depth' => 5,
									'size' => get_theme_mod('header_fs_size', 'xl')
								],
							]
						);
						$fancy_menu->print_element();
					}
				}

				?>
			</div>
			<!-- .rey-fsNav-panelMenu -->

			<?php elseif( class_exists('ReyCore_GlobalSections') && $panel_type === 'gs' && $gs = get_theme_mod('header_fs_gs', '')): ?>
				<?php echo ReyCore_GlobalSections::do_section( $gs ); ?>
			<?php endif; ?>

		</div>
		<!-- .rey-fsNav-panelContent -->
	</div>
	<!-- .rey-fsNav-panel -->
</div>
<!-- .rey-fsNav-panelWrapper -->
