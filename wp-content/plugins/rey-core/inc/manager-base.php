<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

abstract class ManagerBase
{

	/**
	 * Holds the DB option where the disabled items are stored
	 */
	const DB_OPTION = '';

	/**
	 * Holds the current disabled items
	 *
	 * @var array
	 */
	protected $disabled_items;

	/**
	 * Groups of items, sorted by category
	 *
	 * @var array
	 */
	protected $groups = [];

	/**
	 * Categories
	 *
	 * @var array
	 */
	public $categories = [];

	public function __construct()
	{

		add_action( 'admin_menu', [$this, 'register_admin_menu'], 120 );
		add_action( 'reycore/ajax/register_actions', [ $this, 'register_actions' ] );
		add_action( 'admin_head', [ $this, 'load_google_font' ] );

	}

	/**
	 * Set the manager ID
	 *
	 * @return string
	 */
	abstract public function get_id();

	/**
	 * Set the admin menu slug
	 *
	 * @return string
	 */
	public function get_menu_slug(){
		return sprintf('%s-%s-manager', REY_CORE_THEME_NAME , $this->get_id());
	}

	/**
	 * Set the admin menu title
	 *
	 * @return string
	 */
	abstract public function get_menu_title();

	/**
	 * Adds the admin menu item
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		if( $dashboard_id = reycore__get_dashboard_page_id() ){
			$title = $this->get_menu_title();
			add_submenu_page(
				$dashboard_id,
				$title,
				$title,
				'manage_options',
				$this->get_menu_slug(),
				[ $this, 'render_page' ]
			);
		}
	}

	/**
	 * Register Ajax Actions
	 *
	 * @param object $ajax_manager
	 * @return void
	 */
	public function register_actions( $ajax_manager ){
		$id = $this->get_id();
		$ajax_manager->register_ajax_action( "change_item_status_$id", [$this, 'ajax__change_item_status'], 1 );
		$ajax_manager->register_ajax_action( "activate_all_$id", [$this, 'ajax__activate_all_items'], 1 );
		$ajax_manager->register_ajax_action( "deactivate_all_$id", [$this, 'ajax__deactivate_all_items'], 1 );
		$ajax_manager->register_ajax_action( "disable_unused_items_$id", [$this, 'ajax__disable_unused_items'], 1 );
		$ajax_manager->register_ajax_action( "scan_unused_items_$id", [$this, 'ajax__scan_unused'], 1 );
	}

	/**
	 * Sets the manager's page data
	 *
	 * @return array
	 */
	public function set_page_config(){}

	/**
	 * Retrieve the page config data
	 *
	 * @param string $key
	 * @return string
	 */
	public function get_page_config_data( $key ){
		$config = (array) $this->set_page_config();
		return $config[$key];
	}

	/**
	 * Retrieve the list of default disabled items
	 *
	 * @return array
	 */
	abstract public function get_default_disabled_items();

	/**
	 * Items prefix in DB options
	 *
	 * @return string
	 */
	public function get_items_prefix(){}

	/**
	 * Check if item is enabled
	 *
	 * @param string $item_id
	 * @return boolean
	 */
	public function is_enabled( $item_id ){
		return ! in_array( $this->get_items_prefix() . $item_id, $this->get_disabled_items(), true );
	}

	/**
	 * Get all disabled widgets
	 *
	 * @return array
	 */
	public function get_disabled_items(){

		if( ! is_null($this->disabled_items) ){
			return $this->disabled_items;
		}

		return $this->disabled_items = (array) get_option( static::DB_OPTION, $this->get_default_disabled_items() );

	}

	/**
	 * Adds content before the page
	 *
	 * @return string
	 */
	public function before_render_page(){}

	/**
	 * Adds a header search box
	 *
	 * @return string
	 */
	public function render_header_search(){
		?>
		<div class="rey-itemManager-search">
			<?php echo reycore__get_svg_icon(['id'=>'search']); ?>
			<input type="search" placeholder="<?php printf(esc_html__('Type to search %s ..', 'rey-core'), strtolower($this->get_page_config_data('plural_item'))) ?>" />
		</div>
		<?php

	}

	/**
	 * Render the buttons bar
	 *
	 * @return string
	 */
	public function render_buttons_bar(){
		?>
		<div class="rey-itemManager-buttons">

			<button class="rey-adminBtn --btn-outline elManager-disableUnused">
				<span><?php echo sprintf(esc_html__('Disable {{count}} unused %s', 'rey-core'), $this->get_page_config_data('plural_item')) ?></span>
				<span class="rey-spinnerIcon"></span>
			</button>

			<button class="rey-adminBtn --btn-outline elManager-scanButton">
				<span class="__perc">0</span>
				<span><?php echo sprintf(esc_html__('Scan unused %s', 'rey-core'), $this->get_page_config_data('plural_item')) ?></span>
				<span class="rey-spinnerIcon"></span>
			</button>

			<div class="elManager-toggleButtons">

				<button class="rey-adminBtn elManager-toggleButtons-icon">
					<?php echo reycore__get_svg_icon(['id'=>'dots']) ?>
					<?php echo reycore__get_svg_icon(['id'=>'close']) ?>

				</button>

				<div class="elManager-toggleButtons-drop">

					<button class="rey-adminBtn elManager-activateAll">
						<svg class="rey-icon" viewBox="0 0 18 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon fill="currentColor" fill-rule="nonzero" points="6 10.7 1.8 6.5 0.4 7.9 6 13.5 18 1.5 16.6 0.1"></polygon>
							</g>
						</svg>
						<span><?php esc_html_e('ACTIVATE ALL', 'rey-core') ?></span>
						<span class="rey-spinnerIcon"></span>
					</button>

					<button class="rey-adminBtn elManager-deactivateAll">
						<svg class="rey-icon" viewBox="0 0 14 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon fill="currentColor" fill-rule="nonzero" points="14 1.4 12.6 0 7 5.6 1.4 0 0 1.4 5.6 7 0 12.6 1.4 14 7 8.4 12.6 14 14 12.6 8.4 7"></polygon>
							</g>
						</svg>
						<span><?php esc_html_e('DEACTIVATE ALL', 'rey-core') ?></span>
						<span class="rey-spinnerIcon"></span>
					</button>

				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Render the filters menu
	 *
	 * @return string
	 */
	public function render_filters_menu(){

		$all = count( $this->get_all_items() );
		$disabled_count = count( $this->get_disabled_items() );
		?>

		<span class="rey-itemManager-filterMenu-show"><?php echo esc_html__('Show: ', 'rey-core') ?></span>

		<ul class="rey-itemManager-filterMenu" data-type="status">
			<?php
			foreach ([
				'all' => [
					'name' => esc_html__('All', 'rey-core'),
					'count' => $all,
				],
				'enabled' => [
					'name' => esc_html__('Enabled', 'rey-core'),
					'count' => $all - $disabled_count,
				],
				'disabled' => [
					'name' => esc_html__('Disabled', 'rey-core'),
					'count' => $disabled_count,
				],
			] as $key => $value) {
				printf('<li><a data-key="%1$s" href="#" class="%2$s" data-count="%3$d">%4$s</a></li>',
					$key,
					$key === 'all' ? 'current' : '',
					isset($value['count']) ? $value['count'] : 0,
					$value['name']
				);
			} ?>
		</ul>

		<ul class="rey-itemManager-filterMenu" data-type="category">
			<?php

			printf('<li><a data-key="all" href="#" class="current">%s</a></li>', esc_html__('All', 'rey-core') );

			foreach ($this->categories as $key => $value) {

				if( ! isset($this->groups[ $key ]['items']) ){
					continue;
				}

				printf('<li><a data-key="%1$s" href="#" class="%2$s" data-count="%3$d">%4$s</a></li>',
					$key,
					$key === 'all' ? 'current' : '',
					count($this->groups[ $key ]['items']),
					$value
				);
			} ?>
		</ul>

		<?php
	}

	/**
	 * Prepare the data to be rendered
	 * into the correct format
	 *
	 * @return void
	 */
	public function prepare_items(){}

	/**
	 * Load Google Font
	 *
	 * @return void
	 */
	public function load_google_font(){

		if( ! (isset($_REQUEST['page']) && $this->get_menu_slug() === reycore__clean($_REQUEST['page'])) ){
			return;
		}

		echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&amp;display=swap">';

		echo '<style>
		.rey-theme_page_rey-modules-manager #wpbody-content > .notice,
		.rey-itemManager-wrapper .notice,
		.rey-itemManager-wrapper .error,
		.rey-itemManager-wrapper .updated,
		.rey-itemManager-wrapper div.fs-notice,
		.rey-itemManager-wrapper div.fs-slug-size-chart-get-started { display: none !important; }
		</style>';
	}

	/**
	 * Render the manager page
	 *
	 * @return void
	 */
	public function render_page(){

		$this->prepare_items();
		$this->before_render_page(); ?>

		<div class="wrap rey-itemManager-wrapper" data-type="<?php echo $this->get_id(); ?>">

			<header class="rey-itemManager-header">

				<div class="rey-itemManager-headings">
					<h1><?php echo $this->get_page_config_data('title'); ?></h1>
					<p class="description"><?php echo $this->get_page_config_data('description'); ?></p>
				</div>

				<?php $this->render_header_search(); ?>
				<?php $this->render_buttons_bar(); ?>

			</header>

			<?php $this->render_filters_menu(); ?>

			<div class="rey-itemManager">
				<?php $this->render_items(); ?>
			</div>

		</div><!-- /.wrap -->
		<?php
	}

	/**
	 * Render the items by category
	 *
	 * @return void
	 */
	public function render_items(){

		if( empty($this->groups) ){
			return;
		}

		foreach ($this->groups as $key => $groups) {

			if( isset($groups['category']) ){
				printf('<h2 data-value="%s">%s</h2>', $key, $groups['category']);
			}

			if( isset($groups['items']) ){
				foreach ($groups['items'] as $key => $item) {
					$this->_item = $item;
					$this->render_item();
				}
			}
		}
	}

	public function render_media(){}

	/**
	 * Render HTML output of a widget
	 * with toggler for enabling or disabling it
	 *
	 * @param array $widget
	 * @return void
	 */
	public function render_item(){

		$id = $this->_item['id'];
		$is_active = array_search( $id, $this->get_disabled_items() ) === false;
		$category = isset($this->_item['categories']) && !empty($this->_item['categories']) ? $this->_item['categories'][0] : '';

		printf('<div class="rey-itemManager-item" data-item="%s" data-category="%s" data-status="%s">',
			esc_attr($id),
			$category,
			($is_active ? 'enabled' : 'disabled')
		); ?>

			<?php $this->render_media(); ?>

			<div class="rey-itemManager-notice">
				<?php echo $this->get_page_config_data('not_in_use'); ?>
			</div>

			<?php if( isset($this->_item['icon']) && ($icon = $this->_item['icon']) ): ?>
				<i class="<?php echo $icon ?>" aria-hidden="true"></i>
			<?php endif; ?>

			<h2><?php echo $this->_item['title'] ?></h2>

			<div class="rey-toggleSwitch">
				<span class="rey-spinnerIcon"></span>
				<div class="rey-toggleSwitch-box">
					<input id="rey-toggSwitch<?php echo esc_attr($id) ?>" type="checkbox" <?php echo $is_active ? 'checked' : '' ?>>
					<label for="rey-toggSwitch<?php echo esc_attr($id) ?>">
						<span class="rey-toggleSwitch-label" data-activate="<?php echo esc_attr( sprintf(__('Activate %s', 'rey-core'), $this->get_page_config_data('singular_item')) ) ?>" data-deactivate="<?php echo esc_attr( sprintf(__('Deactivate %s', 'rey-core'), $this->get_page_config_data('singular_item')) ) ?>"></span>
					</label>
				</div>
			</div>
			<?php if( isset($this->_item['description']) && ($description = $this->_item['description']) ): ?>
				<p class="rey-itemManager-itemDesc">
					<?php echo $description ?>
					<?php
					if( isset($this->_item['help']) && $help_link = $this->_item['help'] ){
						printf('<a class="rey-itemManager-help" href="%s" target="_blank"><span class="__icon"></span> %s</a>',
							$help_link,
							esc_html_x('More information', 'Manager text', 'rey-core')
						);
					} ?>
				</p>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Change an item's status
	 *
	 * @return void
	 */
	public function change_item_status( $item_id, $status, $return = 'status' ){

		// need proper permissions
		if ( ! current_user_can('manage_options') ) {
			// ignore if doing cron. Probably updates.
			if( ! wp_doing_cron() ){
				return false;
			}
		}

		$disabled_items = (array) get_option( static::DB_OPTION, $this->get_default_disabled_items() );

		do_action('reycore/manager_base/change_item_status', $this );

		// activate
		if( $status ){
			if (($key = array_search($item_id, $disabled_items)) !== false) {
				unset($disabled_items[$key]);
			}
		}

		// deactivate
		elseif( ! $status ){
			$disabled_items[] = $item_id;
		}

		// cleanup
		$disabled_items = array_values(array_unique($disabled_items));

		$update_status = update_option(static::DB_OPTION, $disabled_items);

		if( 'status' === $return ){
			return $update_status;
		}

		if( $update_status ){
			return $disabled_items;
		}

		return [];
	}

	/**
	 * AJAX Change an item's status
	 *
	 * @return void
	 */
	public function ajax__change_item_status( $data ){

		if ( ! current_user_can('install_plugins') ) {
			return [
				'errors' => [ 'Operation not allowed!' ]
			];
		}

		if( ! (isset($data['status']) && isset($data['item'])) ){
			return [
				'errors' => [ 'Something went wrong!' ]
			];
		}

		return $this->change_item_status($data['item'], ($data['status'] === 'true' || $data['status'] === true));
	}

	/**
	 * AJAX Activate all items
	 *
	 * @return void
	 */
	public function ajax__activate_all_items(){

		if ( ! current_user_can('install_plugins') ) {
			return [
				'errors' => [ 'Operation not allowed!' ]
			];
		}

		if( update_option( static::DB_OPTION, [] ) ){

			do_action('reycore/manager_base/activate_all_items', $this );

			return true;
		}

		return [
			'errors' => [ 'Failed. Please retry later.' ]
		];

	}

	abstract public function get_all_items();

	/**
	 * AJAX Activate all items
	 *
	 * @return void
	 */
	public function ajax__deactivate_all_items(){

		if ( ! current_user_can('install_plugins') ) {
			return [
				'errors' => [ 'Operation not allowed!' ]
			];
		}

		if( update_option( static::DB_OPTION, (array) $this->get_all_items() ) ){

			do_action('reycore/manager_base/deactivate_all_items', $this );

			return true;
		}

		return [
			'errors' => [ 'Failed. Please retry later.' ]
		];

	}

	/**
	 * Ajax Scan for unused items
	 *
	 * @param array $action_data
	 * @return void
	 */
	abstract public function ajax__scan_unused( $action_data );

	/**
	 * Ajax Change item status
	 */
	public function ajax__disable_unused_items( $action_data ){

		if ( ! current_user_can('install_plugins') ) {
			return [
				'errors' => [ 'Operation not allowed!' ]
			];
		}

		$disabled_elements = $this->get_disabled_items();

		if( isset($action_data['items']) && is_array($action_data['items']) && !empty($action_data['items']) ){

			do_action('reycore/manager_base/disable_unused_items', $this );

			foreach( $action_data['items'] as $item ){
				$disabled_elements[] = reycore__clean($item);
			}

			if( update_option(static::DB_OPTION, array_unique($disabled_elements)) ){
				return $disabled_elements;
			}
		}
	}

}
