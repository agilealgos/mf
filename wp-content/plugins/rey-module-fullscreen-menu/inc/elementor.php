<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('ReyModuleFullscreen_Elementor')):
	/**
	 * Elementor integration
	 */
	class ReyModuleFullscreen_Elementor
	{
		public function __construct()
		{
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widget' ] );
		}

		/**
		 * Register widget
		 *
		 * @param  object $widgets_manager Widgets manager instance.
		 * @return void
		 */
		public function register_widget( $widgets_manager )
		{
			require_once REY_MODULE_FULLSCREEN_MENU_DIR . 'inc/elementor-fullscreen-nav-trigger.php';

			if ( class_exists( 'ReyModuleFullScreen_MenuWidget' ) ) {
				$widgets_manager->register_widget_type( new ReyModuleFullScreen_MenuWidget );
			}
		}
	}

	new ReyModuleFullscreen_Elementor;

endif;
