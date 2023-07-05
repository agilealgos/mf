<?php
namespace ReyCore\Modules\EstimatedDelivery;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Base extends \ReyCore\Modules\ModuleBase {

	const ASSET_HANDLE = 'reycore-module-estimated-delivery';

	const VARIATIONS_KEY = '_rey_estimated_delivery_variation';

	public $args = [];

	public function __construct()
	{
		add_action( 'reycore/woocommerce/init', [$this, 'init']);
	}

	public function init() {

		new AcfFields();
		new Customizer();

		if( ! $this->is_enabled() ){
			return;
		}

		add_action( 'wp', [$this, 'wp']);
		add_action( 'woocommerce_product_after_variable_attributes', [$this, 'variation_settings_fields'], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [$this, 'save_variation_settings_fields'], 10, 2 );
		add_action( 'reycore/module/quickview/product', [$this, 'wp']);
		add_action( 'reycore/templates/register_widgets', [$this, 'register_widgets']);
		add_action( 'woocommerce_cart_totals_before_order_total', [$this, 'cart_checkout_table_row'] );
		add_action( 'woocommerce_review_order_before_order_total', [$this, 'cart_checkout_table_row'] );

	}

	public static function allow_variations(){
		return get_theme_mod('estimated_delivery__variations', false);
	}


	function set_settings(){

		$position = [
			'default' => [
				'tag' => 'reycore/woocommerce_product_meta/before',
				'priority' => 20
			],
			'custom' => []
		];

		$position_option = get_theme_mod('estimated_delivery__position', 'default');

		$this->settings = apply_filters('reycore/woocommerce/estimated_delivery', [
			'days_text' => esc_html_x('days', 'Estimated delivery string', 'rey-core'),
			'date_format' => "l, M dS",
			'exclude_dates' => [], // array (YYYY-mm-dd) eg. array("2012-05-02","2015-08-01")
			'margin_excludes' => [], // ["Saturday", "Sunday"]
			'position' => isset($position[ $position_option ]) ? $position[ $position_option ] : $position[ 'default' ],
			'use_locale' => get_theme_mod('estimated_delivery__locale', false),
			'locale' => get_locale(),
			'locale_format' => get_theme_mod('estimated_delivery__locale_format', "%A, %b %d"),
			'variations' => self::allow_variations(),
			'limit_days' => 180, // 180 days maximum
		]);

	}

	function wp(){

		if( ! $this->is_enabled() ){
			return;
		}

		if( ! reycore_wc__is_product() ){
			return;
		}

		$this->set_settings();
		$this->set_args();

		if( wp_doing_ajax() && isset($_REQUEST['id']) && $product_id = absint($_REQUEST['id']) ){
			$this->args['product'] = wc_get_product($product_id);
		}

		if( isset($this->settings['position']['tag']) ){
			add_action($this->settings['position']['tag'], [$this, 'display'], $this->settings['position']['priority']);
		}

		add_shortcode('rey_estimated_delivery', [$this, 'display']);

		add_filter( 'woocommerce_available_variation', [$this, 'load_variation_settings_fields'] );
		add_action( 'woocommerce_single_product_summary', [$this, 'display_shipping_class'], 39);

	}

	function set_args(){
		$this->args = [
			'product' => wc_get_product(),
			'days' => reycore__get_option('estimated_delivery__days', 3),
			'days_individual' => reycore__acf_get_field( 'estimated_delivery__days' ),
			'margin' => reycore__get_option('estimated_delivery__days_margin', ''),
			'excludes' => get_theme_mod('estimated_delivery__exclude', ["Saturday", "Sunday"]),
			'inventory' => get_theme_mod('estimated_delivery__inventory', ['instock']),
		];
	}

	public function register_widgets($widgets_manager){
		$widgets_manager->register_widget_type( new PdpElement );
	}

	public function display( $atts = [] ){

		if( !isset($this->settings) ){
			$this->set_settings();
		}

		if( empty($this->args) ){
			$this->set_args();
		}

		if( isset($atts['id']) && $product_id = absint($atts['id']) ){
			$this->args['product'] = wc_get_product($product_id);
		}

		$this->output($this->args);
	}

	protected function output($args) {

		$args = wp_parse_args($args, [
			'custom_days' => '',
			'product' => false,
			'product_id' => 0
		]);

		if( $product_id = $args['product_id'] ){
			$args['product'] = wc_get_product($product_id);
		}

		if( ! $args['product'] ){
			return;
		}

		if( $custom_days = $args['custom_days'] ){
			$args['days'] = $custom_days;
		}

		$args['stock_status'] = $args['product']->get_stock_status();

		$args['date'] = $this->calculate_date([
			'days'        => absint($args['days']),
			'skipdays'    => $args['excludes'],
		]);

		// It's out of stock && has fallback text
		if( $args['stock_status'] === 'outofstock' && ! in_array( $args['stock_status'], $args['inventory'], true) &&
			($text = get_theme_mod('estimated_delivery__text_outofstock', '')) ){
			$this->print_wrapper( $text );
		}

		// It's on backorder && has fallback text
		else if( $args['stock_status'] === 'onbackorder' && ! in_array( $args['stock_status'], $args['inventory'], true) &&
			($text = get_theme_mod('estimated_delivery__text_onbackorder', '')) ){
			$this->print_wrapper( $text );
		}

		if( ! in_array( $args['stock_status'], $args['inventory'], true) ){
			return;
		}

		$display_type = get_theme_mod('estimated_delivery__display_type', 'number');

		if( ! $custom_days && $args['days_individual'] == -1 ){
			return;
		}

		$html = sprintf('<span class="rey-estimatedDelivery-title">%s</span>&nbsp;',
			get_theme_mod('estimated_delivery__prefix',
			esc_html__('Estimated delivery:', 'rey-core'))
		);

		if( ! $custom_days && $args['days_individual'] == '0' ){
			$html .= sprintf('<span class="rey-estimatedDelivery-date">%s</span>', esc_html__('Today', 'rey-core') );
		}

		else {

			$margin_date = '';

			if( $display_type === 'date' ){

				if( $args['margin'] ){
					$margin_excludes = $this->settings['margin_excludes'] ? $this->settings['margin_excludes'] : $args['excludes'];
					$margin_date = ' - ' . $this->calculate_date( [
						'days'        => absint($args['days']) + absint($args['margin']),
						'skipdays'    => $margin_excludes,
					]);
				}

				$html .= sprintf('<span class="rey-estimatedDelivery-date">%s%s</span>',
					$args['date'],
					$margin_date
				);
			}
			else {

				if( $args['margin'] ){
					$margin_date = ' - ' . absint($args['margin']);
				}

				$html .= sprintf('<span class="rey-estimatedDelivery-date">%1$s %2$s</span>',
					$args['days'] . $margin_date,
					$this->settings['days_text']
				);
			}
		}

		$this->print_wrapper( $html );
	}

	function print_wrapper($html){

		if( reycore__acf_get_field('estimated_delivery__hide') ){
			return;
		}

		if( ! $html ){
			return;
		}

		if( $custom_text = reycore__acf_get_field('estimated_delivery__custom_text') ){
			$html = $custom_text;
		}

		echo apply_filters( 'reycore/woocommerce/estimated_delivery/output', sprintf('<div class="rey-estimatedDelivery">%s</div>', $html), $this );
	}

	function calculate_date($args = []) {

		$args = wp_parse_args($args, [
			'timestamp'   => strtotime('today'),
			'days'        => 0,
			'skipdays'    => [],
			'date_format' => $this->settings['date_format'],
			'locale_format' => $this->settings['locale_format'],
		]);

		// limit to n days
		if( $args['days'] > $this->settings['limit_days'] ){
			$args['days'] = $this->settings['limit_days'];
		}

		$i = 1;

		while ($args['days'] >= $i) {
			$args['timestamp'] = strtotime("+1 day", $args['timestamp']);
			if ( (in_array(date("l", $args['timestamp']), $args['skipdays'])) || (in_array(date("Y-m-d", $args['timestamp']), $this->settings['exclude_dates'])) )
			{
				$args['days']++;
			}
			$i++;
		}

		if( $this->settings['use_locale'] ){
			setlocale(LC_TIME, $this->settings['locale']);
			return strftime($args['locale_format'], $args['timestamp']);
		}

		return date($args['date_format'], $args['timestamp']);
	}

	public function display_shipping_class(){

		if( ! get_theme_mod('single_extras__shipping_class', false) ){
			return;
		}

		global $product;

		if( $shipping_class = $product->get_shipping_class() ) {
			$term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
			if( is_a($term, '\WP_Term') ){
				echo apply_filters('reycore/woocommerce/product_page/shipping_class', '<p class="rey-shippingClass">' . $term->name . '</p>', $term);
			}
		}
	}

	function variation_settings_fields( $loop, $variation_data, $variation ) {

		if( ! $this->is_enabled() ){
			return;
		}

		if( ! self::allow_variations() ){
			return;
		}

		woocommerce_wp_text_input([
			'id'            => self::VARIATIONS_KEY. $loop,
			'name'          => self::VARIATIONS_KEY. '[' . $loop . ']',
			'value'         => get_post_meta( $variation->ID, self::VARIATIONS_KEY, true ),
			'label'         => __( 'Estimated days delivery', 'rey-core' ),
			'desc_tip'      => true,
			'description'   => __( 'Add an estimation delivery date for this variation.', 'rey-core' ),
			'wrapper_class' => 'form-row form-row-full',
			'class' => 'input-text',
		]);
	}

	function save_variation_settings_fields( $variation_id, $loop ) {

		if( ! $this->is_enabled() ){
			return;
		}

		if( ! self::allow_variations() ){
			return;
		}

		if ( isset( $_POST[self::VARIATIONS_KEY][ $loop ] ) ) {
			update_post_meta( $variation_id, self::VARIATIONS_KEY, reycore__clean( $_POST[self::VARIATIONS_KEY][ $loop ] ));
		}

	}

	function load_variation_settings_fields( $variation ) {

		if( ! reycore_wc__is_product() ){
			return $variation;
		}

		if( ! $this->settings['variations'] ){
			return $variation;
		}

		if( ! ( $variation_estimation = get_post_meta( $variation[ 'variation_id' ], self::VARIATIONS_KEY, true ) ) ){
			return $variation;
		}

		ob_start();

		$args = $this->args;
		$args['custom_days'] = $variation_estimation;
		$args['product_id'] = $variation[ 'variation_id' ];

		$this->output($args);

		$variation['estimated_delivery'] = ob_get_clean();

		return $variation;
	}

	public function cart_checkout_table_row() {

		$this->set_settings();

		$estimation = '';
		$global_estimation = reycore__get_option('estimated_delivery__days', 3);
		$products_estimations = [];

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$days = get_field( 'estimated_delivery__days', $product_id );

			if( isset( $cart_item['variation_id'] ) && ($variation_id = $cart_item['variation_id']) ){

				if( $var_est = get_field( 'estimated_delivery__days', $variation_id ) ){
					$product_id = $variation_id;
					$days = absint($var_est);
				}
			}

			$products_estimations[$product_id] = '' === $days ? $global_estimation : $days;

		}

		$min = absint(min($products_estimations));
		$max = absint(max($products_estimations));
		$is_range = $min !== $max;

		$excludes = get_theme_mod('estimated_delivery__exclude', ["Saturday", "Sunday"]);

		// date format
		if( get_theme_mod('estimated_delivery__display_type', 'number') === 'date' ){
			// get starting point
			$estimation = $this->calculate_date([
				'days'          => $min,
				'skipdays'      => $excludes,
				'date_format'   => 'M dS',
				'locale_format' => "%B %d",
			]);
			// get end point if range
			if( $is_range ){
				$estimation .= ' - ' . $this->calculate_date([
					'days'        => $max,
					'skipdays'    => $excludes,
					'date_format' => 'dS',
					'locale_format' => "%d",
				]);
			}
		}

		// number of days
		else {
			// get starting point
			$estimation = $min;
			// get end point if range
			if( $is_range ){
				$estimation .= ' - ' . $max;
			}
			// days text
			$estimation .= ' ' . $this->settings['days_text'];
		}

		if( '' === $estimation  ){
			return;
		} ?>

		<tr class="estimated-delivery">
			<th><?php esc_html_e( 'Estimated Delivery', 'rey-core' ); ?></th>
			<td data-title="<?php esc_html_e( 'Estimated Delivery', 'rey-core' ); ?>"><?php echo $estimation; ?></td>
		</tr><?php
	}


	public function is_enabled() {
		return get_theme_mod('single_extras__estimated_delivery', false);
	}

	public static function __config(){
		return [
			'id' => basename(__DIR__),
			'title' => esc_html_x('Estimated Delivery', 'Module name', 'rey-core'),
			'description' => esc_html_x('This tool is useful to display a specific date or timeframe until a product will be delivered.', 'Module description', 'rey-core'),
			'icon'        => '',
			'categories'  => ['woocommerce'],
			'keywords'    => ['product page'],
			'help'        => reycore__support_url('kb/estimated-delivery-text-issues-with-other-languages/'),
			'video' => true
		];
	}

	public function module_in_use(){
		return $this->is_enabled();
	}
}
