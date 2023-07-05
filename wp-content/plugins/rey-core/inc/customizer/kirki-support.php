<?php
namespace ReyCore\Customizer;

if ( ! defined( 'ABSPATH' ) ) exit;

class KirkiSupport {

	public $fonts_to_append_symbol = [];
	public $fonts_symbol;

	public function __construct(){

		add_action( 'init', [$this, 'init']);
		add_filter( 'kirki_config', [$this, 'kirki_config']);
		add_filter( 'kirki_telemetry', '__return_false');
		add_filter( 'kirki_googlefonts_transient_time', [$this, 'googlefonts_transient_time'], 10);
		add_filter( 'kirki_enqueue_google_fonts', [$this, 'enqueue_google_fonts'], 10);
		add_filter( 'kirki_styles_array', [$this, 'styles_array'], 10);

	}

	public function init() {

		// Force Kirki's Google fonts variants to load all
		if (class_exists('\Kirki_Fonts_Google')) {
			// This will force Customizer\'s (Kirki framework) Google fonts list to load all variants (font weights). Please be careful about this option as it could generate performance issues because each font variant has its own file which is pretty big. Recommended to be disabled.
			\Kirki_Fonts_Google::$force_load_all_variants = apply_filters('reycore/customizer/kirki_font_variants', false);
		}

		if( class_exists('\Kirki') ){
			// Add Config for Kirki Settings
			\Kirki::add_config(\ReyCore\Customizer\Controls::CONFIG_KEY, [
				'capability'    => 'edit_theme_options',
				'option_type'   => 'theme_mod'
			]);
		}
	}

	public function kirki_config($config) {

		$config['disable_loader'] = true;
		$config['url_path'] = REY_CORE_URI . 'inc/vendor/kirki';

		if(
			! is_admin() &&
			! is_customize_preview() &&
			! ( isset($_REQUEST['action']) && 'customize_save' === reycore__clean($_REQUEST['action']) )
		){
			// todo: find a way to prevent loading Google fonts in frontend
			// $config['disable_google_fonts'] = true;
		}

		return $config;
	}

	public function googlefonts_transient_time(){
		return MONTH_IN_SECONDS;
	}

	/**
	 * Force unload already preloaded fonts in Rey
	 *
	 * @param array $fonts
	 * @return array
	 */
	public function enqueue_google_fonts( $fonts ){

		if( $preloaded_fonts_list = \ReyCore\Plugin::instance()->fonts->get_google_fonts_list() ){
			foreach (wp_list_pluck($preloaded_fonts_list, 'font_name') as $preloaded_font) {
				unset($fonts[$preloaded_font]);
			}
		}

		return $fonts;
	}

	public function append_symbol($arr) {

		foreach ($arr as $key => $value) {

			if (is_array($value)) {
				$arr[$key] = $this->append_symbol($value);
			}

			elseif ($key === 'font-family') {

				$symbol = '';

				if( in_array($value, $this->fonts_to_append_symbol, true) ){
					$symbol = $this->fonts_symbol;
				}

				$arr[$key] = $value . $symbol;
			}
		}

		return $arr;
	}

	/**
	 * Append symbol to already existing fonts
	 *
	 * @param array $css
	 * @return array
	 */
	public function styles_array( $css ){

		if( ($webfonts = \ReyCore\Plugin::instance()->fonts ) && $fonts_list = $webfonts->get_google_fonts_list() ){

			$this->fonts_to_append_symbol = wp_list_pluck($fonts_list, 'font_name');
			$this->fonts_symbol = $webfonts::SYMBOL;

			$css = $this->append_symbol($css);
		}

		return $css;
	}
}
