<?php
namespace ReyCore\WooCommerce\Tags;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Checkout {

	const CUSTOM_LAYOUT = 'custom';
	const DEFAULT_LAYOUT = 'classic';

	protected $layout;

	public function __construct() {
		add_action( 'init', [$this, 'init']);
	}

	function init(){

		add_action( 'wp', [$this, 'distraction_free_checkout']);
		add_action( 'elementor/ajax/register_actions', [$this, 'ajax_set_pages']);
		add_action( 'woocommerce_checkout_order_review', [$this,'checkout_add_title'], 0);
		add_filter( 'theme_mod_social__enable', [$this, 'checkout_disable_social_icons'], 20);
		add_filter( 'woocommerce_cart_item_name', [$this, 'checkout__classic_add_thumb'], 100, 3);
		add_action( 'woocommerce_thankyou', [$this, 'checkout__add_buttons_order_confirmation']);
		add_action( 'woocommerce_before_cart', [$this, 'cart_progress'] );
		add_action( 'woocommerce_before_checkout_form', [$this, 'cart_progress'], 5 );
		add_action( 'woocommerce_check_cart_items', [$this, 'load_styles'], 10 );
		add_filter( 'rey/main_script_params', [ $this, 'script_params'], 20 );
		add_action( 'wp_enqueue_scripts', [$this, 'load_checkout_styles'] );


		add_action( 'woocommerce_checkout_before_customer_details', [$this, 'add_settings_fields']);
		add_action( 'woocommerce_checkout_update_order_review', [$this, 'handle_early_ajax']);
		add_filter( 'woocommerce_update_order_review_fragments', [$this, 'update_order_fragments']);
		add_action( 'reycore/woocommerce/checkout/before_information', [$this, 'checkout_express'], 10);
		add_filter( 'woocommerce_registration_error_email_exists', [$this, 'checkout_change_login_link'], 20);
		add_filter( 'woocommerce_checkout_redirect_empty_cart', [$this, 'checkout_redirect_empty_cart'], 20);
		add_filter( 'reycore/woocommerce/wc_get_template', [$this, 'add_templates'], 20);
		add_filter( 'woocommerce_checkout_fields', [$this, 'customize_checkout_fields'], 20);
	}

	public function script_params($params)
	{
		$params['checkout'] = [
			'error_text' => esc_html__('This information is required.', 'rey-core')
		];

		return $params;
	}

	public function load_checkout_styles($classes)
	{

		if ( true === wc_string_to_bool( get_option( 'woocommerce_checkout_highlight_required_fields', 'yes' ) ) ) {
			wp_add_inline_style( 'woocommerce-inline', '.woocommerce form .form-row abbr.required { visibility: visible; }' );
		}

		return $classes;
	}

	/**
	 * Retrieve Element settings
	 *
	 * @return array
	 */
	public function get_element_settings( $setting = '' ){

		$settings = get_query_var('reycore_checkout_settings', false);

		if( $setting ){
			if( isset($settings[$setting]) ){
				return $settings[$setting];
			}
			else {
				return; // null
			}
		}

		return $settings;
	}

	/**
	 * Determines if it's an element or shortcode
	 *
	 * @return boolean
	 */
	public function is_element(){
		return $this->get_element_settings() !== false;
	}

	/**
	 * Get checkout layout option
	 *
	 * @since 2.0.0
	 **/
	function get_checkout_layout() {

		$layout = self::DEFAULT_LAYOUT;

		// ajax calls
		if( wp_doing_ajax() ){
			// returns empty if not custom layout
			if( ! empty( $this->get_custom_layout_ajax_settings() ) ){
				return self::CUSTOM_LAYOUT;
			}
		}

		if( $settings = $this->get_element_settings() ){
			$layout = isset($settings['layout']) ? $settings['layout'] : self::CUSTOM_LAYOUT;
		}

		return $layout;
	}

	public function is_custom_layout(){
		return apply_filters('reycore/woocommerce/checkout/force_custom_layout', $this->get_checkout_layout() === self::CUSTOM_LAYOUT);
	}

	function update_order_fragments($fragments){

		if( $this->is_custom_layout() ){

			ob_start();
			wc_cart_totals_order_total_html();
			$fragments['#rey-checkoutPage-review-toggle__total'] = sprintf('<span id="rey-checkoutPage-review-toggle__total" class="__total">%s</span>', ob_get_clean());

			ob_start();
			reycore__get_template_part('template-parts/woocommerce/checkout/custom-shipping-methods');
			$fragments['.rey-checkout-shipping'] = ob_get_clean();

		}

		return $fragments;
	}

	public function get_custom_layout_ajax_settings(){

		// Must receive the checkout post data
		if( ! (isset($_REQUEST['post_data']) && ($post_data = reycore__clean($_REQUEST['post_data']))) ){
			return;
		}

		parse_str($post_data, $ajax_settings);

		// check for the checkout layout value
		if( ! isset($ajax_settings['rey_checkout_layout']) ){
			return;
		}

		// if it's not Custom, bail
		if( self::CUSTOM_LAYOUT !== $ajax_settings['rey_checkout_layout'] ){
			return;
		}

		return $ajax_settings;
	}

	public function add_settings_fields(){

		if( ! ( $settings = $this->get_element_settings() ) ){
			return;
		}

		printf('<input type="hidden" name="rey_checkout_layout" value="%s">',
			isset($settings['layout']) ? esc_attr($settings['layout']) : self::CUSTOM_LAYOUT
		);

		printf('<input type="hidden" name="rey_review_coupon_enable" value="%s">',
			isset($settings['review_coupon_enable']) && $settings['review_coupon_enable'] === '' ? '' : 'yes'
		);

		printf('<input type="hidden" name="rey_review_coupon_toggle" value="%s">',
			isset($settings['review_coupon_toggle']) && $settings['review_coupon_toggle'] !== '' ? 'yes' : ''
		);

	}

	/**
	 * Handle Checkout's Ajax early calls
	 *
	 * @since 2.0.0
	 **/
	function handle_early_ajax() {

		if( ! ($post_data_params = $this->get_custom_layout_ajax_settings()) ){
			return;
		}

		if( isset($post_data_params['rey_review_coupon_enable']) && '' === $post_data_params['rey_review_coupon_enable'] ){
			add_filter('woocommerce_coupons_enabled', '__return_false', 20);
			return; // no need to go further
		}

		if( isset($post_data_params['rey_review_coupon_toggle']) && '' !== $post_data_params['rey_review_coupon_toggle'] ){
			add_filter('reycore/woocommerce/checkout/coupon_toggle', '__return_true', 20);
		}

	}

	/**
	 * Sets checkout page in Elementor element
	 * @since 1.8.0
	 */
	function ajax_set_pages( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'rey_set_wc_page', function ( $data ){

			if( isset($data['editor_post_id']) && isset($data['page']) && $page = reycore__clean($data['page']) ){

				$pages = [
					'checkout' => 'woocommerce_checkout_page_id',
					'cart' => 'woocommerce_cart_page_id',
					'myaccount' => 'woocommerce_myaccount_page_id',
					'terms' => 'woocommerce_terms_page_id',
				];

				do_action('reycore/woocommerce/rey_set_wc_page', $data['editor_post_id'], $page);

				return update_option($pages[ $page ], $data['editor_post_id']);

			}

		} );
	}

	/**
	 * Add title inside order table
	 *
	 * @since 1.0.0
	 **/
	function checkout_add_title()
	{
		?>
			<h3 class="order_review_heading"><?php esc_html_e( 'Your order', 'rey-core' ); ?></h3>
		<?php
	}


	/**
	 * Disable Side social icons for checkout and cart pages
	 *
	 * @since 1.9.2
	 **/
	function checkout_disable_social_icons($status)
	{
		if( is_checkout() || is_cart() ){
			return false;
		}

		return $status;
	}

	/**
	 * Classic layout, add thumbnails
	 */
	function checkout__classic_add_thumb( $html, $cart_item, $cart_item_key ){

		if( ! is_checkout() ){
			return $html;
		}

		if( ! get_theme_mod('checkout_add_thumbs', true) ){
			return $html;
		}

		if( $this->get_checkout_layout() !== self::DEFAULT_LAYOUT ){
			return $html;
		}

		return sprintf('<div class="rey-classic-reviewOrder-img">%s</div>%s',
			apply_filters( 'woocommerce_cart_item_thumbnail',
				$cart_item['data']->get_image( apply_filters('reycore/woocommerce/checkout/classic_thumbnail_size', 'thumbnail') ),
				$cart_item,
				$cart_item_key
			),
			$html
		);
	}

	/**
	 * Add buttons in the confirmation order
	 *
	 * @since 1.9.7
	 **/
	function checkout__add_buttons_order_confirmation($order_id) {

		if( ! apply_filters( 'reycore/woocommerce/checkout/order_confirmation/add_buttons', true) ){
			return;
		}

		echo '<div class="rey-ordRecPage-buttons" style="margin-bottom: 2em;">';
		echo apply_filters(
			'reycore/woocommerce/checkout/order_confirmation/link',
			'<a href="' . esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ) . '" class="btn btn-primary">' . esc_html__( 'Review your orders', 'woocommerce' ) . '</a>', $order_id
		);
		echo '<a href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '" class="btn btn-secondary">' . esc_html__( 'Continue shopping', 'woocommerce' ) . '</a>';
		echo '</div>';
	}

	/**
	 * More product info
	 * Link to product
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function cart_progress() {

		if( ! get_theme_mod('cart_checkout_bar_process', true) ){
			return;
		}

		$pid = get_the_ID();
		$active_cart = wc_get_page_id( 'cart' ) == $pid || wc_get_page_id( 'checkout' ) == $pid;
		$active_checkout = wc_get_page_id( 'checkout' ) == $pid;
		?>

		<div class="rey-checkoutBar-wrapper <?php echo get_theme_mod('cart_checkout_bar_icons', 'icon') === 'icon' ? '--icon' : '--numbers'; ?>">
			<ul class="rey-checkoutBar">
				<li class="<?php echo ($active_cart ? '--is-active' : '') ?>">
					<a href="<?php echo get_permalink( wc_get_page_id( 'cart' ) ); ?>">
						<h4>
							<?php echo ($active_cart ? reycore__get_svg_icon(['id' => 'check']) : ''); ?>
							<span><?php
								if( $title_1 = get_theme_mod('cart_checkout_bar_text1_t', '' ) ) {
									echo $title_1;
								}
								else {
									echo esc_html_x('Shopping Bag', 'Checkout bar shopping cart title', 'rey-core');
								}
							?></span>
						</h4>
						<p><?php
							if( $subtitle_1 = get_theme_mod('cart_checkout_bar_text1_s', '' ) ) {
								echo $subtitle_1;
							}
							else {
								echo esc_html_x('View your items', 'Checkout bar shopping cart subtitle', 'rey-core');
							}
						?></p>
					</a>
				</li>
				<li class="<?php echo ($active_checkout ? '--is-active' : '') ?>">
					<a href="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>">
						<h4>
							<?php echo ($active_checkout ? reycore__get_svg_icon(['id' => 'check']) : ''); ?>
							<span><?php
								if( $title_2 = get_theme_mod('cart_checkout_bar_text2_t', '' ) ) {
									echo $title_2;
								}
								else {
									echo esc_html_x('Shipping and Checkout', 'Checkout bar checkout title', 'rey-core');
								}
							?></span>
						</h4>
						<p><?php
							if( $subtitle_2 = get_theme_mod('cart_checkout_bar_text2_s', '' ) ) {
								echo $subtitle_2;
							}
							else {
								echo esc_html_x('Enter your details', 'Checkout bar checkout subtitle', 'rey-core');
							}
						?></p>
					</a>
				</li>
				<li>
					<div>
						<h4><?php
							if( $title_3 = get_theme_mod('cart_checkout_bar_text3_t', '' ) ) {
								echo $title_3;
							}
							else {
								echo esc_html_x('Confirmation', 'Checkout bar confirmation title', 'rey-core');
							}
						?></h4>
						<p><?php
							if( $subtitle_3 = get_theme_mod('cart_checkout_bar_text3_s', '' ) ) {
								echo $subtitle_3;
							}
							else {
								echo esc_html_x('Review your order!', 'Checkout bar confirmation subtitle', 'rey-core');
							}
						?></p>
					</div>
				</li>
			</ul>
		</div>
		<?php

	}

	/**
	 * Display Express layout block
	 *
	 * @since 1.8.1
	 **/
	function checkout_express()
	{

		ob_start();
		do_action('reycore/woocommerce/checkout/express_checkout');
		$content = ob_get_clean();

		if( empty($content) ){
			return;
		} ?>

		<div class="rey-checkoutExpress">

			<div class="rey-checkoutExpress-title">
				<?php echo esc_html_x('Express checkout', 'Title in checkout form.', 'rey-core') ?>
			</div>

			<div class="rey-checkoutExpress-content">
				<?php echo $content; ?>
			</div>

		</div>
		<?php
	}

	/**
	 * Replaces the login button
	 */
	function checkout_change_login_link( $html ){

		// it's Elementor WC. Checkout (Custom)
		if( $this->is_custom_layout() ){

			$custom = sprintf(' data-reymodal=\'%s\' ', wp_json_encode([
				'content' => '.rey-checkoutLogin-form',
				'width' => 700,
				'id' => 'rey-checkout-login-modal',
			]));

			add_filter( 'reycore/modals/always_load', '__return_true');


			$new_html = str_replace('class="showlogin"', $custom . 'class="showlogin"', $html);

			return $new_html;
		}

		return $html;
	}

	function checkout_redirect_empty_cart($status){

		if(
			$this->is_element() &&
			(reycore__elementor_edit_mode())
		){
			return false;
		}

		return $status;
	}

	/**
	 * Check if Billing is first in custom layout
	 *
	 * @since 1.9.0
	 **/
	function checkout_custom_billing_first() {

		if( ! $this->is_custom_layout() ){
			return false;
		}

		if( WC()->cart ){
			$shipping_needed = WC()->cart->needs_shipping();
		}

		if( wc_ship_to_billing_address_only() ){
			$shipping_needed = false;
		}

		$shipping_disabled = $this->checkout_custom_shipping_disabled();

		if( $shipping_disabled ){
			$shipping_needed = false;
		}

		// force true if no shipping available
		if( ! $shipping_needed ){
			return true;
		}

		return $this->get_element_settings('show_billing_first') === 'yes';
	}

	/**
	 * Check if Billing is first in custom layout
	 *
	 * @since 1.9.0
	 **/
	function checkout_custom_shipping_disabled() {
		return $this->is_custom_layout() && $this->get_element_settings('disable_shipping_step') === 'yes';
	}

	function customize_checkout_fields( $fields ){

		if( ! $this->is_custom_layout() ){
			return $fields;
		}

		if( $this->checkout_custom_billing_first() ){
			return $fields;
		}

		// Add phone to shipping
		if( ! isset($fields['shipping']['billing_phone']) &&
			isset($fields['billing']['billing_phone']) &&
			! empty( $fields['billing']['billing_phone'] )
			){
			$fields['billing']['billing_phone']['description'] = esc_html__('In case we need to contact you about your order.', 'rey-core');
			$fields['shipping']['billing_phone'] = $fields['billing']['billing_phone'];
		}

		return $fields;
	}

	/**
	 * Override checkout templates
	 * @since 1.8.0
	 */
	function add_templates( $templates ){

		if( ! is_checkout() ){
			return $templates;
		}

		$custom_checkout_templates = [
			[
				'template_name' => 'checkout/form-checkout.php',
				'template' => 'template-parts/woocommerce/checkout/custom-form-checkout.php'
			],
			[
				'template_name' => 'checkout/form-billing.php',
				'template' => 'template-parts/woocommerce/checkout/custom-form-billing.php'
			],
			[
				'template_name' => 'checkout/review-order.php',
				'template' => 'template-parts/woocommerce/checkout/custom-review-order.php'
			],
			[
				'template_name' => 'checkout/form-shipping.php',
				'template' => 'template-parts/woocommerce/checkout/custom-form-shipping.php'
			]
		];

		if( $this->is_custom_layout() ){
			return array_merge($templates, $custom_checkout_templates);
		}

		return $templates;

	}

	function load_styles(){
		reyCoreAssets()->add_styles(['rey-wc-cart', 'rey-wc-checkout']);
	}

	/**
	 * Generate form review block
	 *
	 * @since 1.8.0
	 **/
	function review_form_block($name, $fill, $target, $content = '')
	{
		?>
		<div class="rey-formReview-block">
			<div class="rey-formReview-title">
				<?php echo esc_html_x($name, 'Title in checkout steps form review.', 'rey-core') ?>
			</div>
			<div class="rey-formReview-content" data-fill="<?php echo esc_attr($fill) ?>">
				<?php echo $content; ?>
			</div>
			<div class="rey-formReview-action">
				<a href="#" data-target="<?php echo esc_attr($target) ?>"><?php echo esc_html_x('Change', 'Action to take in checkout steps form review', 'rey-core') ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Sets Distraction Free Checkout
	 *
	 * @return void
	 */
	public function distraction_free_checkout(){

		if( ! is_checkout() ){
			return;
		}

		if ( is_wc_endpoint_url('order-received') && apply_filters('reycore/checkout/distraction_free/order-received', false) ){
			return;
		}

		if ( ! get_theme_mod('checkout_distraction_free', false) ){
			return;
		}

		// disable header
		remove_all_actions('rey/header');

		// adds a logo only
		add_action('rey/content/title', 'reycore__tags_logo_block', 0);

		// adds class
		add_filter('rey/site_content_classes', function($classes){
			return $classes + ['--checkout-distraction-free'];
		});
	}

}
