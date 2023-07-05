<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Admin {

	const POPOVER_KEY = 'rey_page_settings_popover';

	public function __construct(){

		add_action('init', [$this, 'init']);

		new \ReyCore\Libs\IgTokenManager;
	}

	public function init(){
		add_action( 'admin_init', [$this, 'admin_init']);
		add_action( 'admin_head', [$this, 'admin_bar_css'] );
		add_action( 'wp_head', [$this, 'admin_bar_css'] );
		add_action( 'admin_menu', [$this, 'admin_menu'] );
		add_action( 'admin_bar_menu', [$this, 'admin_bar_links'], 200 );
		add_action( 'admin_enqueue_scripts', [$this, 'register_admin_scripts'], 5);
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action( 'wp_footer', [$this, 'add_page_settings_popover'], 9999);
		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );

	}

	public function admin_init(){

		if( ! class_exists('\ACF') ){
			return false;
		}

		$this->keep_dashboard_menu_open();

		if( reycore__acf_get_field('rey_widgets_blocks_layout', REY_CORE_THEME_NAME) === true ){
			return;
		}

		// Disables the block editor from managing widgets in the Gutenberg plugin.
		add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
		// Disables the block editor from managing widgets.
		add_filter( 'use_widgets_block_editor', '__return_false' );
	}

	public function keep_dashboard_menu_open()
	{
		if( ! (isset($_REQUEST['action']) && 'edit' === $_REQUEST['action']) ){
			return;
		}

		if( ! (isset($_REQUEST['post']) && ($pid = $_REQUEST['post']) ) ){
			return;
		}

		if( get_post_type($pid) !== \ReyCore\Elementor\GlobalSections::POST_TYPE ){
			return;
		}

		global $menu;

		foreach( $menu as $key => $value )
		{
			if( reycore__get_dashboard_page_id() == $value[2] ){
				$menu[$key][4] .= " wp-has-current-submenu";
			}
		}
	}

	public function admin_bar_css() {

		if ( ! is_admin_bar_showing() ) {
			return;
		} ?>

		<style type="text/css">
			#wpadminbar .rey-abQuickMenu .rey-abQuickMenu-logo {
				/* width: 45px; */
				height: inherit;
				line-height: inherit;
				display: flex;
			}
			#wpadminbar .rey-abQuickMenu .rey-abQuickMenu-logo img {
				display: block;
				width: 100%;
				max-width: 32px;
				opacity: .5;
				margin: 5px auto 0;
			}
			.rey-abQuickMenu:hover .rey-abQuickMenu-logo img {
				opacity: .7;
			}
			#wpadminbar .rey-abQuickMenu .rey-abQuickMenu-top {
				margin-top: 10px;
			}
			#wpadminbar .rey-abQuickMenu .rey-abQuickMenu-notices {
				margin-top: 15px;
			}
		</style>

		<?php
		wp_enqueue_style('reycore-frontend-admin');

	}

	public function admin_menu(){

		if( apply_filters('reycore/admin_menu', reycore__get_props('admin_menu')) ){
			return;
		}

		remove_menu_page( reycore__get_dashboard_page_id() );

	}

	/**
	 * Admin bar links
	 *
	 * @since 1.4.0
	 **/
	function admin_bar_links( $admin_bar ) {

		if( ! current_user_can('administrator') ){
			return;
		}

		if( ! apply_filters('reycore/admin_bar_menu', reycore__get_props('admin_bar_menu') ) ){
			return;
		}

		if( !($dashboard_id = reycore__get_dashboard_page_id()) ){
			return;
		}

		$parent_content = reycore__get_props('theme_title');

		if( $parent_custom_text = reycore__get_props('button_text') ){
			$parent_content = $parent_custom_text;
		}

		if( ($parent_icon = reycore__get_props('button_icon')) ){
			$parent_content = sprintf('<img src="%1$s" alt="%2$s"><span class="screen-reader-text">%2$s - Quick Menu</span>', $parent_icon, reycore__get_props('theme_title') );
		}

		$nodes['main'] = [
			'title'  => apply_filters('reycore/admin_bar_menu/html', sprintf('<span class="rey-abQuickMenu-logo">%s</span>', $parent_content)),
			'href'  => add_query_arg([
				'page' => $dashboard_id
				], admin_url( 'admin.php' )
			),
			'meta_title' => esc_html__( 'Rey - Quick Menu', 'rey-core' ),
			'class' => 'rey-abQuickMenu'
		];

		$nodes['dashboard'] = [
			'title'  => esc_html__( 'Dashboard', 'rey-core' ),
			'href'  => add_query_arg([
				'page' => $dashboard_id
				], admin_url( 'admin.php' )
			),
			'top' => true,
		];

		if( class_exists('\ReyCore\Elementor\GlobalSections') ):
			$nodes['gs'] = [
				'title'  => esc_html__( 'Global Sections', 'rey-core' ),
				'href'  => add_query_arg([
					'post_type' => \ReyCore\Elementor\GlobalSections::POST_TYPE
					], admin_url( 'edit.php' )
				),
			];
		endif;

		$nodes['settings'] = [
			'title'  => esc_html__( 'Theme Settings', 'rey-core' ),
			'href'  => add_query_arg([
				'page' => REY_CORE_THEME_NAME . '-settings',
				], admin_url( 'admin.php' )
			),
			'meta_title' => esc_html__( 'Generic theme settings.', 'rey-core' ),
		];

		$customize_url = urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		$nodes['customize'] = [
			'title'  => esc_html__( 'Customize Options', 'rey-core' ),
			'href'  => add_query_arg([
				'url' => $customize_url,
				], admin_url( 'customize.php' )
			),
			'meta_title' => esc_html__( 'Customize Options', 'rey-core' ),
			'top' => true,
			'nodes' => [
				[
					'title' => esc_html__( 'General settings', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[panel]' => \ReyCore\Customizer\Options\General::get_id()
						], admin_url( 'customize.php' )
					),
				],
				[
					'title' => esc_html__( 'Header', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[panel]' => \ReyCore\Customizer\Options\Header::get_id()
						], admin_url( 'customize.php' )
					),
					'meta_title' => esc_html__( 'Customize the site header.', 'rey-core' ),
					'top' => true
				],
				[
					'title' => esc_html__( 'Footer', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[panel]' => \ReyCore\Customizer\Options\Footer::get_id()
						], admin_url( 'customize.php' )
					),
					'meta_title' => esc_html__( 'Customize the footer.', 'rey-core' ),
				],
				[
					'title' => esc_html__( 'Page Cover', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[panel]' => \ReyCore\Customizer\Options\Cover::get_id()
						], admin_url( 'customize.php' )
					),
					'meta_title' => esc_html__( 'Customize the page covers (page headers).', 'rey-core' ),
				],
				[
					'title' => esc_html__( 'Blog', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[section]' => \ReyCore\Customizer\Options\General\BlogPage::get_id()
						], admin_url( 'customize.php' )
					),
					'meta_title' => esc_html__( 'Customize the site blog.', 'rey-core' ),
				],

			]
		];

		if( class_exists('\WooCommerce') ):

			$nodes['customize']['nodes'][] = [
				'title' => esc_html__( 'Woo > Catalog > Layout', 'rey-core' ),
				'href'  => add_query_arg([
					'url' => $customize_url,
					'autofocus[section]' => \ReyCore\Customizer\Options\Woocommerce\CatalogGrid::get_id()
					], admin_url( 'customize.php' )
				),
				'meta_title' => esc_html__( 'Customize the looks of the product catalog (eg: categories pages)', 'rey-core' ),
				'top' => true,
			];

			$nodes['customize']['nodes'][] = [
					'title' => esc_html__( 'Woo > Catalog > Components', 'rey-core' ),
					'href'  => add_query_arg([
						'url' => $customize_url,
						'autofocus[section]' => \ReyCore\Customizer\Options\Woocommerce\CatalogGridComponents::get_id()
						], admin_url( 'customize.php' )
					),
					'meta_title' => esc_html__( 'Enable or disable components in product catalog (categories).', 'rey-core' ),
				];

			$nodes['customize']['nodes'][] = [
				'title'  => esc_html__( 'Woo > Catalog > Misc.', 'rey-core' ),
				'href'  => add_query_arg([
					'url' => $customize_url,
					'autofocus[section]' => \ReyCore\Customizer\Options\Woocommerce\CatalogMisc::get_id()
					], admin_url( 'customize.php' )
				),
				'meta_title' => esc_html__( 'Various options', 'rey-core' ),
			];

			$nodes['customize']['nodes'][] = [
				'title'  => esc_html__( 'Woo > Product Page > Layout', 'rey-core' ),
				'href'  => add_query_arg([
					'url' => $customize_url,
					'autofocus[section]' => \ReyCore\Customizer\Options\Woocommerce\ProductPageLayout::get_id()
					], admin_url( 'customize.php' )
				),
				'meta_title' => esc_html__( 'Customize the looks of the product page.', 'rey-core' ),
				'top' => true,
			];

			$nodes['customize']['nodes'][] = [
				'title'  => esc_html__( 'Woo > Product Page > Components', 'rey-core' ),
				'href'  => add_query_arg([
					'url' => $customize_url,
					'autofocus[section]' => \ReyCore\Customizer\Options\Woocommerce\ProductPageComponents::get_id()
					], admin_url( 'customize.php' )
				),
				'meta_title' => esc_html__( 'Enable or disable components in product page.', 'rey-core' ),
			];

		endif;

		$nodes['css'] = [
			'title'  => esc_html__( 'Custom CSS', 'rey-core' ),
			'href'  => add_query_arg([
				'url' => $customize_url,
				'autofocus[section]' => 'custom_css'
				], admin_url( 'customize.php' )
			),
			'meta_title' => esc_html__( 'Add additional CSS.', 'rey-core' ),
		];

		$refresh_nodes = [];

		if( \ReyCore\Plugin::instance()->customizer->cache ){

			$refresh_nodes['refresh_css'] = [
				'title'  => esc_html__( 'Customizer CSS Cache', 'rey-core' ),
				'href'  => '#',
				'meta_title' => esc_html__( 'Refresh the dynamic CSS generated in Customizer.', 'rey-core' ),
				'class' => 'qm-refresh-css qm-refresher',
			];

		}

		if( ! empty($refresh_nodes) ){

			$nodes['refresh'] = [
				'title'      => esc_html__( 'Refresh', 'rey-core' ),
				'href'       => reycore__support_url('kb/refresh-buttons/'),
				'meta_title' => esc_html__( 'Refresh various caches.', 'rey-core' ),
				'top'        => true,
				'new'        => true,
				'nodes'      => $refresh_nodes,
			];
		}

		if( reycore__get_props('kb_links') ){

			$nodes['help'] = [
				'title'  => esc_html__( 'Help KB', 'rey-core' ),
				'href'  => reycore__support_url(),
				'meta_title' => esc_html__( 'Get help online.', 'rey-core' ),
				'new' => true,
				'top' => true,
				'nodes' => [
					[
						'title' => esc_html__( 'Getting started', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/getting-started/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Settings', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/settings/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Elementor', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/elementor/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'WooCommerce', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/woocommerce/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Global Sections', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/global-sections/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Customization FAQ\'s', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/customization-faqs/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Demos FAQ\'s', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/demos-faq/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Rey Modules', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/rey-modules/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Troubleshooting', 'rey-core' ),
						'href'  => reycore__support_url('kbtopic/troubleshooting/'),
						'new' => true,
					],
				]
			];

			$nodes['faq'] = [
				'title'  => esc_html__( 'FAQ', 'rey-core' ),
				'href'  => reycore__support_url(),
				'meta_title' => esc_html__( 'Frequently asked questions', 'rey-core' ),
				'new' => true,
				'nodes' => [
					[
						'title' => esc_html__( 'How to work faster with Rey?', 'rey-core' ),
						'href'  => reycore__support_url('kb/design-work-faster-with-rey/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'What are Global Sections?', 'rey-core' ),
						'href'  => reycore__support_url('kb/what-exactly-are-global-sections/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'How to add custom CSS?', 'rey-core' ),
						'href'  => reycore__support_url('kb/how-to-add-custom-css/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'How to get started with Elementor?', 'rey-core' ),
						'href'  => reycore__support_url('kb/getting-started-with-elementor/'),
						'new' => true,
					],
					[
						'title' => esc_html__( 'Optimising your website’s speed', 'rey-core' ),
						'href'  => reycore__support_url('kb/optimising-your-websites-speed/'),
						'new' => true,
					],
				]
			];

		}

		$nodes = apply_filters('reycore/admin_bar_menu/nodes', $nodes);

		if( isset($nodes['refresh']['nodes']) ){
			$nodes['refresh']['nodes']['refresh_nodes_help'] = [
				'title' => esc_html__( 'What are these buttons?', 'rey-core' ),
				'href'  => reycore__support_url('kb/refreshing-various-data/'),
				'class' => 'qm-refresh-help',
				'new'   => true,
			];
		}

		foreach($nodes as $i => $node){

			$admin_bar->add_node(
				[
					'id'     => $i === 'main' ? $dashboard_id : $dashboard_id . $i,
					'title'  => $node['title'],
					'href'  => $node['href'],
					'parent' => $i === 'main' ? '' : $dashboard_id,
					'meta'   => [
						'title' => isset($node['meta_title']) ? $node['meta_title'] : '',
						'target' => isset($node['new']) ? '_blank' : '',
						'class' => (isset($node['top']) ? 'rey-abQuickMenu-top' : '') . ' ' . (isset($node['class']) ? $node['class'] : ''),
					],
				]
			);

			if( isset($node['nodes']) ){
				foreach ($node['nodes'] as $k => $subnode) {
					$admin_bar->add_node(
						[
							'id'     => $dashboard_id . $i . $k,
							'title'  => $subnode['title'],
							'href'  => $subnode['href'],
							'parent' => $dashboard_id . $i,
							'meta'   => [
								'title' => isset($subnode['meta_title']) ? $subnode['meta_title'] : '',
								'target' => isset($subnode['new']) ? '_blank' : '',
								'class' => (isset($subnode['top']) ? 'rey-abQuickMenu-top' : '') . ' ' . (isset($subnode['class']) ? $subnode['class'] : ''),
							],
						]
					);
				}
			}

		}

		if( $edit_node = $admin_bar->get_node('edit') ){

			$admin_bar->add_node(
				[
					'id'     => 'edit-page-components',
					'title'  => esc_html__('Edit Page Components', 'rey-core'),
					'href'   => $edit_node->href,
					'parent' => 'edit',
				]
			);

			if( $header = reycore__get_option('header_layout_type', 'default') ){
				if( ! in_array($header, ['none', 'default'], true) ){
					$admin_bar->add_node(
						[
							'id'     => 'edit-rey-header',
							'title'  => esc_html__('Edit Header', 'rey-core'),
							'href'   => admin_url( sprintf('post.php?post=%d&action=elementor', $header) ),
							'parent' => 'edit-page-components',
						]
					);
				}
			}

			if( $footer = reycore__get_option('footer_layout_type', 'default') ){
				if( ! in_array($footer, ['none', 'default'], true) ){
					$admin_bar->add_node(
						[
							'id'     => 'edit-rey-footer',
							'title'  => esc_html__('Edit Footer', 'rey-core'),
							'href'   => admin_url( sprintf('post.php?post=%d&action=elementor', $footer) ),
							'parent' => 'edit-page-components',
						]
					);
				}
			}

			do_action('reycore/admin_bar_menu/page_components/nodes', $admin_bar, 'edit-page-components');
		}

	}

	public function register_admin_scripts(){

		wp_register_script( 'rey-core-admin-script', REY_CORE_URI . 'assets/js/admin.js', ['jquery', 'underscore', 'wp-util', 'rey-helpers'], REY_CORE_VERSION, true );

		wp_localize_script( 'rey-core-admin-script', 'reyCoreAdmin', apply_filters('reycore/admin_script_params', [
			'rey_core_version' => REY_CORE_VERSION,
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'    => wp_create_nonce('reycore-ajax-verification'),
			'back_btn_text' => esc_html__('Back to List', 'rey-core'),
			'back_btn_url'  => admin_url('edit.php?post_type=rey-global-sections'),
			'is_customizer' => false,
			'strings' => [
				'refresh_demos_error' => esc_html__('Error. Please retry!', 'rey-core'),
				'reloading' => esc_html__('Reloading page!', 'rey-core'),
				'refresh_btn_text' => esc_html__('Refresh Demos List', 'rey-core'),
				'refreshing_btn_text' => esc_html__('Refreshing..', 'rey-core'),
				'migrating_btn_text' => esc_html__('Migrating..', 'rey-core'),
				'help' => esc_html__('Need help?', 'rey-core'),
			],
			'sound_effect' => REY_CORE_URI . 'assets/audio/ding.mp3',
			'customizer_icons' => REY_CORE_URI . 'assets/images/customizer-icons.svg',
			'support_url__post_tax_cat' => reycore__support_url('kb/settings-for-individual-page-post-taxonomy-category/'),
			'site_url' => site_url()
		]) );

		$rtl = reyCoreAssets()::rtl();

		$reycore_styles = [
			'rey-core-admin-style' => [
				'src'     => REY_CORE_URI . 'assets/css/admin'. $rtl .'.css',
				'deps'    => [],
				'version' => REY_CORE_VERSION,
			],
			'reycore-frontend-admin' => [
				'src'     => REY_CORE_URI . 'assets/css/general-components/frontend-admin/frontend-admin' . $rtl . '.css',
				'deps'    => [],
				'version' => REY_CORE_VERSION,
			],
		];

		foreach($reycore_styles as $handle => $style ){
			wp_register_style($handle, $style['src'], $style['deps'], $style['version']);
		}
	}

	public function enqueue_admin_scripts(){
		wp_enqueue_script( 'rey-core-admin-script');
		wp_enqueue_style( 'rey-core-admin-style');
	}

	public function enqueue_scripts(){

		if( is_user_logged_in() ){

			wp_enqueue_script(
				'reycore-frontend-admin',
				REY_CORE_URI . 'assets/js/general/c-frontend-admin.js',
				['jquery', 'reycore-scripts'],
				REY_CORE_VERSION,
				true
			);

			wp_localize_script( 'reycore-frontend-admin', 'reyFrontendAdminParams', $this->get_frontend_admin_localized_settings() );
		}
	}

	public function get_frontend_admin_localized_settings(){
		$params = [];

		if( current_user_can('administrator') ){

			// get header
			// get footer
			// get cover
			// custom template

		}

		return $params;
	}

	public function register_actions( $ajax_manager ){
		$ajax_manager->register_ajax_action( 'disable_page_settings_popover', [$this, 'ajax__hide_popover'], [
			'auth' => 1,
			'capability' => 'administrator',
		] );
	}

	/**
	 * Hides the popover settings forever (per page)
	 *
	 * @param array $data
	 * @return bool
	 */
	public function ajax__hide_popover($data){

		if( ! isset($data['type'], $data['page_id']) ){
			return;
		}

		if( ! in_array($data['type'], ['header'], true) ){
			return;
		}

		return update_option(self::POPOVER_KEY . $data['type'] . $data['page_id'], true);
	}

	public function get_id(){

		$page_id = get_queried_object_id();

		if( function_exists('is_shop') && is_shop() ){
			$page_id = wc_get_page_id( 'shop' );
		}

		return $page_id;
	}

	/**
	 * Shows a popover notice for the header if its settings
	 * are overridden in the page's settings
	 *
	 * @return string
	 */
	public function page_settings_popover__header(){

		if( is_404() ){
			return;
		}

		if( ! apply_filters('reycore/admin/show_page_settings_notice', true) ){
			return;
		}

		$page_id = $this->get_id();

		if( get_option(self::POPOVER_KEY . 'header' . $page_id) ){
			return;
		}

		if( 'none' === reycore__get_option('header_layout_type', 'default') ){
			return;
		}

		$settings = [
			'header_layout_type' => [
				'title' => esc_html__('Header Type', 'rey-core'),
				'default' => 'default',
				'global_section' => true,
			],
			'header_text_color' => [
				'title' => esc_html__('Text Color', 'rey-core'),
				'default' => '',
			],
			'header_position' => [
				'title' => esc_html__('Header Position', 'rey-core'),
				'default' => 'rel',
				'definitions' => [
					'rel' => esc_html__('Relative', 'rey-core'),
					'absolute' => esc_html__('Absolute', 'rey-core'),
					'fixed' => esc_html__('Fixed', 'rey-core'),
				]
			],
			'header_fixed_overlap' => [
				'title' => esc_html__('Header Overlap', 'rey-core'),
				'default' => true,
			],
			'header_fixed_overlap_tablet' => [
				'title' => esc_html__('Header Overlap (tablet)', 'rey-core'),
				'default' => true,
			],
			'header_fixed_overlap_mobile' => [
				'title' => esc_html__('Header Overlap (mobile)', 'rey-core'),
				'default' => true,
			],
			'custom_logo' => [
				'title' => esc_html__('Logo', 'rey-core'),
				'default' => '',
				'image' => true
			],
			'logo_mobile' => [
				'title' => esc_html__('Logo (mobile)', 'rey-core'),
				'default' => '',
				'image' => true
			],
		];

		$overrides = $page_settings = $global_settings = [];

		foreach ($settings as $k => $s) {

			if( ! ($page_setting = get_field($k, $page_id)) ){
				continue;
			}

			$page_settings[$k] = $page_setting;
			$global_settings[$k] = get_theme_mod($k, $s['default']);

			if( $page_settings[$k] !== $global_settings[$k] ){
				$overrides[$k] = $s['title'];
			}
		}

		if( empty($overrides) ){
			return;
		}

		$output = _x('<p>Some of the Header\'s settings (from Customizer > Header) <strong>will not work</strong> because they are overridden in this page, such as:</p>', 'Various admin. texts', 'rey-core');

		$output .= '<table>';
		$output .= '<tr>';
		$output .= sprintf('<th>%s</th>', esc_html__('Setting', 'rey-core'));
		$output .= sprintf('<th>%s</th>', esc_html__('Current', 'rey-core'));
		$output .= sprintf('<th>%s</th>', esc_html__('Global', 'rey-core'));
		$output .= '</tr>';

		foreach ($overrides as $key => $title) {

			if( ! ($current = self::settings_popover_data($page_settings[$key], $settings[$key]))){
				$current = $page_settings[$key];
			}

			$was = '-';
			if( ($global_setting = $global_settings[$key]) ){
				$was = self::settings_popover_data($global_setting, $settings[$key]);
			}

			$output .= '<tr>';
			$output .= sprintf('<td class="_title">%s</td>', $title);
			$output .= sprintf('<td>%s</td>', $current);
			$output .= sprintf('<td>%s</td>', $was);
			$output .= '</tr>';
		}

		$output .= '</table>';

		$output .= sprintf( _x('<p>No action is required.  <a href="%s" target="_blank"><u>Review</u></a> this page\'s Header settings, or <a href="#" class="js-hide"><u>hide this notice</u></a></p>', 'Various admin. texts', 'rey-core'), get_edit_post_link() . '#header-settings' );

		$output .= _x('<p><small>This notice shows only for Administrators.</small></p>', 'Various admin. texts', 'rey-core');

		printf('<div class="__ps-popover --is-active" data-id="%d" data-type="header">', esc_attr($page_id));
			echo '<div class="__ps-popover-icon"></div>';
			printf('<div class="rey-simplePopover --arr-top-left">%s</div>', $output );
		echo '</div>';

		return $output;
	}

	public static function settings_popover_data($data, $default_settings){

		$image = isset($default_settings['image']) && $default_settings['image'] === true;

		// Boolean (Yes/No)
		if( is_bool($data) || 'true' === $data || 'false' === $data ){
			return $data === true ? esc_html__('Yes', 'rey-core') : esc_html__('No', 'rey-core');
		}
		// Numeric
		else if( is_numeric($data) && ! $image ){

			$prefix = '';

			if( isset($default_settings['global_section']) && $default_settings['global_section'] === true ){
				$prefix = '<span title="Global Section">G.S.</span> ';
			}

			return sprintf('%s<a href="%s" target="_blank"><u>%d</u></a>', $prefix, get_edit_post_link($data), $data);
		}
		// Image
		else if( $image ){
			return wp_get_attachment_image($data, 'thumbnail');
		}
		// find definition
		else if(isset($default_settings['definitions'][ $data ]) ){
			return $default_settings['definitions'][ $data ];
		}

		return '';
	}

	/**
	 * Popovers for various notices
	 *
	 * @return void
	 */
	public function add_page_settings_popover(){

		if( is_admin() ){
			return;
		}

		if( ! current_user_can('administrator') ){
			return;
		}

		$this->page_settings_popover__header();
	}

}
