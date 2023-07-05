<?php
namespace ReyCore\Compatibility\Pitchprint;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{
	const ASSET_HANDLE = 'reycore-pitchprint';

	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', [$this, 'load_scripts']);
	}

	public function load_scripts(){
		wp_enqueue_script(
			self::ASSET_HANDLE,
			self::get_path( basename( __DIR__ ) ) . '/script.js',
			['rey-script', 'reycore-scripts'],
			REY_CORE_VERSION,
			true
		);
	}


}
