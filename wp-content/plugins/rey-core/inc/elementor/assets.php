<?php
namespace ReyCore\Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use \ReyCore\Elementor\Helper;

class Assets
{

	public function __construct(){

		add_action( 'reycore/assets/register_scripts', [$this, 'register_assets']);
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles'] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts'] );

	}

	public function elementor_styles(){

		$elementor_edo_suffix = Helper::get_props('optimized_dom') ? 'opt' : 'unopt';
		$direction_suffix = is_rtl() ? '-rtl' : '';
		$elementor_frontend_dependencies = ['elementor-frontend', 'reycore-elementor-frontend-dom'];

		$styles = [];

		// Use Rey Grid
		if( self::maybe_load_rey_grid() ){

			$styles['reycore-elementor-frontend-grid'] = [
				'src'     => REY_CORE_URI . 'assets/css/elementor-components/grid-'. $elementor_edo_suffix .'/grid-'. $elementor_edo_suffix . $direction_suffix . '.css',
				'deps'    => ['elementor-frontend'],
				'version'   => REY_CORE_VERSION,
				'priority' => 'high'
			];

			$elementor_frontend_dependencies[] = 'reycore-elementor-frontend-grid';
		}

		// Elementor "Optimized DOM Output" specific styles
		$styles['reycore-elementor-frontend-dom'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/dom-'. $elementor_edo_suffix .'/dom-'. $elementor_edo_suffix . $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
			'priority' => 'high'
		];

		// TODO extra cleanup, make high-priority stylesheet
		$styles['reycore-elementor-frontend'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/general/general'. $direction_suffix . '.css',
			'deps'    => $elementor_frontend_dependencies,
			'version'   => REY_CORE_VERSION,
		];

		$styles['reycore-elementor-heading-animation'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/heading-animation/heading-animation'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
		];

		$styles['reycore-elementor-heading-highlight'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/heading-highlight/heading-highlight'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
		];

		$styles['reycore-elementor-heading-special'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/heading-special/heading-special'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
		];

		$styles['reycore-elementor-scroll-deco'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/scroll-deco/scroll-deco'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
		];

		$styles['reycore-elementor-sticky-gs'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/sticky-gs/sticky-gs'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
			'priority' => 'high'
		];


		$styles['reycore-elementor-text-links'] = [
			'src'     => REY_CORE_URI . 'assets/css/elementor-components/text-links/text-links'. $direction_suffix . '.css',
			'deps'    => ['elementor-frontend'],
			'version'   => REY_CORE_VERSION,
		];

		return $styles;
	}


	public function elementor_scripts(){
		return [

			'jquery-mousewheel' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/jquery-mousewheel.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
				'plugin' => true
			],

			'threejs' => [
				'external' => true,
				'src'     => 'https://cdn.jsdelivr.net/npm/three@0.144/build/three.min.js',
				'deps'    => [],
				'version'   => 'r144',
				'localize' => [
					'name' => 'reyThreeConfig',
					'params' => [
						'displacements' => [
							'https://i.imgur.com/t4AA2A8.jpg',
							'https://i.imgur.com/10UwPUy.jpg',
							'https://i.imgur.com/tO1ukJf.jpg',
							'https://i.imgur.com/iddaUQ7.png',
							'https://i.imgur.com/YbFcFOJ.png',
							'https://i.imgur.com/JzGo2Ng.jpg',
							'https://i.imgur.com/0toUHNF.jpg',
							'https://i.imgur.com/NPnfoR8.jpg',
							'https://i.imgur.com/xpqg1ot.jpg',
							'https://i.imgur.com/Ttm5Vj4.jpg',
							'https://i.imgur.com/wrz3VyW.jpg',
							'https://i.imgur.com/rfbuWmS.jpg',
							'https://i.imgur.com/NRHQLRF.jpg',
							'https://i.imgur.com/G29N5nR.jpg',
							'https://i.imgur.com/tohZyaA.jpg',
							'https://i.imgur.com/YvRcylt.jpg',
						],
					],
				],
			],

			'distortion-app' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/distortion-app.js',
				'deps'    => ['threejs', 'jquery'],
				'version'   => '1.0.0',
			],

			'lottie' => [
				'src'     => REY_CORE_URI . 'assets/js/lib/lottie.min.js',
				'deps'    => ['threejs', 'jquery'],
				'version'   => '5.6.8',
				'plugin' => true
			],

			'reycore-elementor-frontend' => [
				'src'      => REY_CORE_URI . 'assets/js/elementor/general.js',
				'deps'     => ['elementor-frontend', 'rey-script', 'reycore-scripts'],
				'version'  => REY_CORE_VERSION,
				'localize' => [
					'name'   => 'reyElementorFrontendParams',
					'params' => [
						'compatibilities' => Helper::get_compatibilities(),
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
						'ajax_nonce'      => wp_create_nonce('reycore-ajax-verification'),
						'is310'           => version_compare('3.1.0', ELEMENTOR_VERSION, '<='),
					],
				],
			],

			'reycore-elementor-scroll-deco' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/scroll-deco.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-accordion' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-accordion.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-button-add-to-cart' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-button-add-to-cart.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-carousel-links' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-carousel-links.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-column-click' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-column-click.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-column-sticky' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-column-sticky.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-column-video' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-column-video.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-header-navigation' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-header-navigation.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-header-wishlist' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-header-wishlist.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-heading' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-heading.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-image-carousel' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-image-carousel.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-section-pushback' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-section-pushback.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-section-video' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-section-video.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-woo-prod-gallery' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-woo-prod-gallery.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],
			'reycore-elementor-elem-lazy-load' => [
				'src'     => REY_CORE_URI . 'assets/js/elementor/elem-lazy-load.js',
				'deps'    => [],
				'version'   => REY_CORE_VERSION,
			],

		];
	}


	public static function maybe_load_rey_grid(){

		$val = get_theme_mod('elementor_grid', 'rey');
		$opt = ! ( $val === 'default' );

		if( get_page_template_slug() === 'template-canvas.php' ){
			$opt = false;
		}

		return apply_filters('reycore/elementor/load_grid', $opt);
	}


	public function register_assets($assets){
		$assets->register_asset('styles', $this->elementor_styles());
		$assets->register_asset('scripts', $this->elementor_scripts());
	}

	/**
	 * Enqueue ReyCore's Elementor Frontend CSS
	 */
	public function enqueue_frontend_styles() {

		reyCoreAssets()->add_styles([
			'reycore-elementor-frontend-dom',
			'reycore-elementor-frontend-grid',
			'reycore-elementor-frontend',
			'rey-wc-elementor'
		]);

		if( reycore__elementor_edit_mode() ) {
			wp_enqueue_style('reycore-frontend-admin');
		}
	}

	/**
	 * Load Frontend JS
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_scripts()
	{
		reyCoreAssets()->add_scripts('reycore-elementor-frontend');

		if( Helper::get_props('pushback_fallback_enabled') ){
			reyCoreAssets()->add_scripts('reycore-elementor-elem-section-pushback');
		}
	}

}
