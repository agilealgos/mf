<?php
namespace ReyCore\Compatibility\WoocommerceProductBundles;

if ( ! defined( 'ABSPATH' ) ) exit;

class Base extends \ReyCore\Compatibility\CompatibilityBase
{
	private $settings = [];

	const ASSET_HANDLE = 'reycore-wc-bundles-styles';

	public function __construct()
	{
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'reycore/assets/register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'reycore/woocommerce/modules/discount/init', [ $this, 'set_product_type_for_discounts' ] );
	}

	public function init(){
		$this->settings = apply_filters('reycore/compat/wc_bundles/params', []);
	}

	public function enqueue_scripts(){
		reyCoreAssets()->add_styles(self::ASSET_HANDLE);
	}

	public function set_product_type_for_discounts($manager){
		$manager::$product_types[] = 'bundle';
	}

	public function register_scripts(){
		wp_register_style( self::ASSET_HANDLE, self::get_path( basename( __DIR__ ) ) . '/style.css', [], REY_CORE_VERSION );
	}
}
