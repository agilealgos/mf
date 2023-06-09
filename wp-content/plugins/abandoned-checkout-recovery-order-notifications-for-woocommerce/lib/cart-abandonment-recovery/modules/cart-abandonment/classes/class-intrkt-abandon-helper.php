<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cart abandonment tracking class.
 */
class INTRKT_ABANDON_Helper {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor function that initializes required actions and hooks.
	 */
	public function __construct() {
	}

		/**
		 * Get checkout url.
		 *
		 * @param  integer $post_id    post id.
		 * @param  string  $token_data token data.
		 * @return string
		 */
	public function intrkt_get_checkout_url( $post_id, $token_data ) {

		$token        = $this->intrkt_generate_token( (array) $token_data );
		$global_param = get_option( 'intrkt_global_param', false );
		$checkout_url = get_permalink( $post_id );

		$token_param  = array(
			'intrkt_ac_token' => $token,
		);
		$checkout_url = add_query_arg( $token_param, $checkout_url );

		if ( ! empty( $global_param ) ) {

			$query_param  = array();
			$global_param = preg_split( "/[\f\r\n]+/", $global_param );

			foreach ( $global_param as $key => $param ) {

				$param_parts                            = explode( '=', $param );
				$query_param[ trim( $param_parts[0] ) ] = trim( $param_parts[1] );
			}
			$checkout_url = add_query_arg( $query_param, $checkout_url );
		}

		return esc_url( $checkout_url );
	}

		/**
		 *  Geberate the token for the given data.
		 *
		 * @param array $data data.
		 */
	public function intrkt_generate_token( $data ) {
		return urlencode( base64_encode( http_build_query( $data ) ) );
	}

	/**
	 * Get the acceptable order statuses.
	 */
	public function intrkt_get_acceptable_order_statuses() {

		$acceptable_order_statuses = get_option( 'intrkt_excludes_orders' );
		$acceptable_order_statuses = array_map( 'strtolower', $acceptable_order_statuses );

		return $acceptable_order_statuses;
	}

	/**
	 * Generate comma separated products.
	 *
	 * @param string $cart_contents user cart details.
	 */
	public function intrkt_get_comma_separated_products( $cart_contents ) {
		$cart_comma_string = '';
		if ( ! $cart_contents ) {
			return $cart_comma_string;
		}
		$cart_data   = unserialize( $cart_contents );
		$cart_length = count( $cart_data );
		$index       = 0;
		foreach ( $cart_data as $key => $product ) {

			if ( ! isset( $product['product_id'] ) ) {
				continue;
			}

			$cart_product = wc_get_product( $product['product_id'] );

			if ( $cart_product ) {
				$cart_comma_string = $cart_comma_string . $cart_product->get_title();
				if ( ( $cart_length - 2 ) === $index ) {
					$cart_comma_string = $cart_comma_string . ' & ';
				} elseif ( ( $cart_length - 1 ) !== $index ) {
					$cart_comma_string = $cart_comma_string . ', ';
				}
				$index++;
			}
		}
		return $cart_comma_string;

	}

		/**
		 * Count abandoned carts
		 *
		 * @since 1.1.5
		 */
	public function intrkt_abandoned_cart_count() {
		global $wpdb;
		$cart_abandonment_table_name = $wpdb->prefix . INTRKT_ABANDON_CART_ABANDONMENT_TABLE;

        $query       = $wpdb->prepare( "SELECT   COUNT(`id`) FROM {$cart_abandonment_table_name}  WHERE `order_status` = %s", INTRKT_CART_ABANDONED_ORDER ); // phpcs:ignore
        $total_items = $wpdb->get_var( $query ); // phpcs:ignore
		return $total_items;
	}

		/**
		 * Get start and end date for given interval.
		 *
		 * @param  string $interval interval .
		 * @return array
		 */
	public function intrkt_get_start_end_by_interval( $interval ) {

		if ( 'today' === $interval ) {
			$start_date = gmdate( 'Y-m-d' );
			$end_date   = gmdate( 'Y-m-d' );
		} else {

			$days = $interval;

			$start_date = gmdate( 'Y-m-d', strtotime( '-' . $days . ' days' ) );
			$end_date   = gmdate( 'Y-m-d' );
		}

		return array(
			'start' => $start_date,
			'end'   => $end_date,
		);
	}

		/**
		 * Get the checkout details for the user.
		 *
		 * @param string $intrkt_session_id checkout page session id.
		 * @since 1.0.0
		 */
	public function intrkt_get_checkout_details( $intrkt_session_id ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . INTRKT_ABANDON_CART_ABANDONMENT_TABLE;
		$result                 = $wpdb->get_row(
            $wpdb->prepare('SELECT * FROM `' . $cart_abandonment_table . '` WHERE session_id = %s', $intrkt_session_id ) // phpcs:ignore
		);
		return $result;
	}

}

INTRKT_ABANDON_Helper::get_instance();
