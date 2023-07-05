<?php
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ced_refund_and_exchange_order_meta' ) ) {

	/**
	 * This class for managing admin interfaces for woocommerce order.
	 *
	 * @name    Ced_refund_and_exchange_order_meta
	 * @category Class
	 * @author   makewebbetter <webmaster@makewebbetter.com>
	 */

	class Ced_refund_and_exchange_order_meta {

		/**
		 * This function is construct of class
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function __construct() {
			add_filter( 'admin_enqueue_scripts', array( $this, 'ced_rnx_admin_scripts' ) );
			add_action( 'wp_ajax_ced_rnx_register_license', array( $this, 'ced_rnx_register_license' ) );

			$ced_rnx_license_hash = get_option( 'ced_rnx_license_hash' );
			$ced_rnx_license_key = get_option( 'ced_rnx_license_key' );
			$ced_rnx_license_plugin = get_option( 'ced_rnx_plugin_name' );
			$ced_rnx_hash = md5( $_SERVER['HTTP_HOST'] . $ced_rnx_license_plugin . $ced_rnx_license_key );
			$ced_rnx_activation_date = get_option( 'ced_rnx_activation_date', false );
			$ced_rnx_after_month = strtotime( '+30 days', $ced_rnx_activation_date );
			$ced_rnx_currenttime = current_time( 'timestamp' );
			$ced_rnx_time_difference = $ced_rnx_after_month - $ced_rnx_currenttime;
			$ced_rnx_days_left = floor( $ced_rnx_time_difference / ( 60 * 60 * 24 ) );
			if ( $ced_rnx_license_hash == $ced_rnx_hash || $ced_rnx_days_left >= 0 ) {
				add_action( 'admin_menu', array( $this, 'ced_rnx_product_return_meta_box' ) );

				// Return Request Hooks and filter
				add_action( 'wp_ajax_ced_return_fee_add', array( $this, 'ced_rnx_return_fee_add_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_fee_add', array( $this, 'ced_rnx_return_fee_add_callback' ) );
				add_action( 'wp_ajax_ced_return_req_approve', array( $this, 'ced_rnx_return_req_approve_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_req_approve', array( $this, 'ced_rnx_return_req_approve_callback' ) );
				add_action( 'wp_ajax_ced_return_req_cancel', array( $this, 'ced_rnx_return_req_cancel_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_return_req_cancel', array( $this, 'ced_rnx_return_req_cancel_callback' ) );

				// Exchange Request Hooks and filter
				add_action( 'wp_ajax_ced_exchange_fee_add', array( $this, 'ced_rnx_exchange_fee_add_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_fee_add', array( $this, 'ced_rnx_exchange_fee_add_callback' ) );
				add_action( 'woocommerce_refund_created', array( $this, 'ced_rnx_action_woocommerce_order_refunded' ), 10, 2 );
				add_action( 'wp_ajax_ced_exchange_req_approve_refund', array( $this, 'ced_exchange_req_approve_refund' ) );
				add_action( 'wp_ajax_ced_exchange_req_approve', array( $this, 'ced_exchange_req_approve_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_req_approve', array( $this, 'ced_exchange_req_approve_callback' ) );
				add_action( 'wp_ajax_ced_exchange_req_cancel', array( $this, 'ced_rnx_exchange_req_cancel_callback' ) );
				add_action( 'wp_ajax_nopriv_ced_exchange_req_cancel', array( $this, 'ced_rnx_exchange_req_cancel_callback' ) );

				add_action( 'woocommerce_admin_order_items_after_fees', array( $this, 'ced_rnx_show_order_exchange_product' ) );
				add_filter( 'woocommerce_order_number', array( $this, 'ced_rnx_update_order_number_callback' ) );
				add_filter( 'woocommerce_valid_order_statuses_for_payment', array( $this, 'ced_rnx_order_need_payment' ) );
				add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( $this, 'ced_rnx_order_can_cancel' ) );

				add_action( 'woocommerce_order_status_changed', array( $this, 'ced_rnx_woocommerce_order_status_changed' ), 10, 3 );
				add_action( 'wp_ajax_ced_rnx_coupon_regenertor', array( $this, 'ced_rnx_coupon_regenertor' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_coupon_regenertor', array( $this, 'ced_rnx_coupon_regenertor' ), 10 );
				add_action( 'wp_ajax_ced_rnx_generate_user_wallet_code', array( $this, 'ced_rnx_generate_user_wallet_code' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_generate_user_wallet_code', array( $this, 'ced_rnx_generate_user_wallet_code' ), 10 );
				add_action( 'wp_ajax_ced_rnx_change_customer_wallet_amount', array( $this, 'ced_rnx_change_customer_wallet_amount' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_change_customer_wallet_amount', array( $this, 'ced_rnx_change_customer_wallet_amount' ), 10 );
				add_action( 'wp_ajax_ced_rnx_catalog_count', array( $this, 'ced_rnx_catalog_count' ), 10 );
				add_action( 'wp_ajax_ced_rnx_catalog_delete', array( $this, 'ced_rnx_catalog_delete' ), 10 );
				add_action( 'wp_ajax_ced_rnx_cancel_customer_order', array( $this, 'ced_rnx_cancel_customer_order' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_cancel_customer_order', array( $this, 'ced_rnx_cancel_customer_order' ), 10 );
				add_action( 'wp_ajax_ced_rnx_cancel_customer_order_products', array( $this, 'ced_rnx_cancel_customer_order_products' ), 10 );
				add_action( 'wp_ajax_nopriv_ced_rnx_cancel_customer_order_products', array( $this, 'ced_rnx_cancel_customer_order_products' ), 10 );
				add_action( 'wp_ajax_ced_rnx_manage_stock', array( $this, 'ced_rnx_manage_stock' ) );
				add_action( 'wp_ajax_ced_rnx_refund_price', array( $this, 'ced_rnx_refund_price' ) );
				add_action( 'wp_ajax_ced_rnx_order_messages_save', array( $this, 'ced_rnx_order_messages_save' ) );
				add_action( 'wp_ajax_nopriv_ced_rnx_order_messages_save', array( $this, 'ced_rnx_order_messages_save' ) );
			}
		}

		public function ced_rnx_register_license() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$ced_rnx_license_key = sanitize_text_field( $_POST['license_key'] );
				$ced_rnx_admin_name = '';
				$ced_rnx_admin_email = get_option( 'admin_email', null );
				$ced_rnx_admin_details = get_user_by( 'email', $admin_email );
				if ( isset( $ced_rnx_admin_details->data ) ) {
					if ( isset( $ced_rnx_admin_details->data->display_name ) ) {
						$ced_rnx_admin_name = $ced_rnx_admin_details->data->display_name;
					}
				}
				$ced_rnx_license_arr = array(
					'license_key' => $ced_rnx_license_key,
					'domain_name' => $_SERVER['HTTP_HOST'],
					'admin_name' => $ced_rnx_admin_name,
					'admin_email' => $ced_rnx_admin_email,
					'plugin_name' => 'Woocommerce Refund & Exchange With RMA',
				);
				$args ['body'] = $ced_rnx_license_arr;

				$response = wp_remote_post( 'https://makewebbetter.com/codecanyon/validate_license.php', $args );

				if ( is_wp_error( $response ) ) {
					echo 'Unexpected Error! The query returned with an error.';
				}
				$ced_rnx_res = json_decode( wp_remote_retrieve_body( $response ) );

				if ( $ced_rnx_res->status == true ) {
					update_option( 'ced_rnx_license_hash', $ced_rnx_res->hash );
					update_option( 'ced_rnx_plugin_name', 'Woocommerce Refund & Exchange With RMA' );
					update_option( 'ced_rnx_license_key', $ced_rnx_res->mwb_key );
					echo json_encode(
						array(
							'status' => true,
							'msg' => __( 'Successfully Verified', 'woocommerce-refund-and-exchange' ),
							'url' => admin_url( '/' ) . 'admin.php?page=ced-rnx-notification',
						)
					);
				} else if ( $ced_rnx_res->status == false ) {
					echo json_encode(
						array(
							'status' => false,
							'msg' => $ced_rnx_res->msg,
						)
					);
				}
				wp_die();
			}
		}

		public function ced_rnx_refund_price() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				// Wallet for customer.
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 0;
				$order = wc_get_order( $order_id );
				$refund_amount = isset( $_POST['refund_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ) : 0;
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
				$ced_rnx_select_refund_method_enable = get_option( 'ced_rnx_select_refund_method_enable', 'no' );
				$ced_rnx_refund_method = '';
				$ced_rnx_refund_method = get_post_meta( $order_id, 'ced_rnx_refund_method', true );
				$response['refund_method'] = '';
				if ( $ced_rnx_refund_method != '' ) {
					$response['refund_method'] = $ced_rnx_refund_method;
				}
				if ( $wallet_enable == 'yes' && $ced_rnx_select_refund_method_enable == 'yes' && $ced_rnx_refund_method == 'manual_method' ) {
					echo json_encode( $response );
					wp_die();
				} elseif ( $wallet_enable == 'yes' ) {

					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
						if ( empty( $walletcoupon ) ) {
							$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
							$amount = $refund_amount; // Amount

							$discount_type = 'fixed_cart';
							$coupon_description = "REFUND ACCEPTED - ORDER #$order_id";

							$coupon = array(
								'post_title' => $coupon_code,
								'post_content' => $coupon_description,
								'post_excerpt' => $coupon_description,
								'post_status' => 'publish',
								'post_author' => get_current_user_id(),
								'post_type'     => 'shop_coupon',
							);

							$new_coupon_id = wp_insert_post( $coupon );
							$discount_type = 'fixed_cart';
							update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
							update_post_meta( $new_coupon_id, 'rnxwallet', true );
							update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
							update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
						} else {
							$the_coupon = new WC_Coupon( $walletcoupon );
							$coupon_id = $the_coupon->get_id();
							if ( isset( $coupon_id ) ) {
								$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
								$remaining_amount = $amount + $refund_amount;
								update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
								update_post_meta( $coupon_id, 'rnxwallet', true );
							}
						}
					}
					// Add the note
					$order           = wc_get_order( $order_id );
					$today           = date( 'F j, Y' );
					$timezone_format = 'h:i a';
					$time            = date_i18n( $timezone_format );
					$mess            = '#' . $order_id . ' Refund  - ' . get_woocommerce_currency_symbol() . $refund_amount . ' ' . $today . ', ' . $time . ' by admin in wallet';
					$refund = wc_create_refund(
						array(
							'amount' => $refund_amount,
							'reason' => $mess,
							'order_id' => $order_id,
							'refund_payment' => false,
						)
					);
					$order->save();
					$order->calculate_totals();
					update_post_meta( $order_id, 'refundable_amount', '0' );
				}
				update_post_meta( $order_id, 'ced_rnx_refund_approve_refunded', 'yes' );
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * Manage stock when product is actually back in stock.
		 *
		 * @name ced_rnx_manage_stock
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_manage_stock() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : '';
				;
				if ( $order_id > 0 ) {
					$ced_rnx_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
					;

					if ( $ced_rnx_type != '' ) {
						if ( $ced_rnx_type == 'ced_rnx_return' ) {
							$manage_stock = get_option( 'ced_rnx_return_request_manage_stock' );
							if ( $manage_stock == 'yes' ) {
								$ced_rnx_return_data = get_post_meta( $order_id, 'ced_rnx_return_product', true );
								if ( is_array( $ced_rnx_return_data ) && ! empty( $ced_rnx_return_data ) ) {
									foreach ( $ced_rnx_return_data as $date => $requested_data ) {
										$ced_rnx_returned_products = $requested_data['products'];
										if ( is_array( $ced_rnx_returned_products ) && ! empty( $ced_rnx_returned_products ) ) {
											foreach ( $ced_rnx_returned_products as $key => $product_data ) {
												if ( $product_data['variation_id'] > 0 ) {
													$product = wc_get_product( $product_data['variation_id'] );
												} else {
													$product = wc_get_product( $product_data['product_id'] );
												}
												if ( $product->managing_stock() ) {
													$avaliable_qty = $product_data['qty'];
													if ( WC()->version < '3.0.0' ) {
														$product->set_stock( $avaliable_qty, 'add' );
													} else {
														if ( $product_data['variation_id'] > 0 ) {
															$total_stock = get_post_meta( $product_data['variation_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['variation_id'], $total_stock, 'set' );
														} else {
															$total_stock = get_post_meta( $product_data['product_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['product_id'], $total_stock, 'set' );
														}
													}
													update_post_meta( $order_id, 'ced_rnx_manage_stock_for_return', 'no' );
													$response['result'] = 'success';
													$response['msg'] = __( 'Product Stock is updated Successfully.', 'woocommerce-refund-and-exchange' );
												} else {
													$response['result'] = false;
													$response['msg'] = __( 'Product Stock is not updated as manage stock setting of product is disable.', 'woocommerce-refund-and-exchange' );
												}
											}
										}
									}
								}
							}
						} else {
							$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
							if ( $manage_stock == 'yes' ) {
								$ced_rnx_exchange_deta = get_post_meta( $order_id, 'ced_rnx_exchange_product', true );
								if ( is_array( $ced_rnx_exchange_deta ) && ! empty( $ced_rnx_exchange_deta ) ) {
									foreach ( $ced_rnx_exchange_deta as $date => $requested_data ) {
										$ced_rnx_exchanged_products = $requested_data['from'];
										if ( is_array( $ced_rnx_exchanged_products ) && ! empty( $ced_rnx_exchanged_products ) ) {
											foreach ( $ced_rnx_exchanged_products as $key => $product_data ) {
												if ( $product_data['variation_id'] > 0 ) {
													$product = wc_get_product( $product_data['variation_id'] );
												} else {
													$product = wc_get_product( $product_data['product_id'] );
												}
												if ( $product->managing_stock() ) {
													$avaliable_qty = $product_data['qty'];
													if ( WC()->version < '3.0.0' ) {
														$product->set_stock( $avaliable_qty, 'add' );
													} else {
														if ( $product_data['variation_id'] > 0 ) {
															$total_stock = get_post_meta( $product_data['variation_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['variation_id'], $total_stock, 'set' );
														} else {
															$total_stock = get_post_meta( $product_data['product_id'], '_stock', true );
															$total_stock = $total_stock + $avaliable_qty;
															wc_update_product_stock( $product_data['product_id'], $total_stock, 'set' );
														}
													}
													update_post_meta( $order_id, 'ced_rnx_manage_stock_for_exchange', 'no' );
													$response['result'] = true;
													$response['msg'] = __( 'Product Stock is updated Successfully.', 'woocommerce-refund-and-exchange' );
												} else {
													$response['result'] = false;
													$response['msg'] = __( 'Product Stock is not updated as manage stock setting of product is disable.', 'woocommerce-refund-and-exchange' );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * update left amount becuse amount is refunded.
		 *
		 * @name ced_rnx_action_woocommerce_order_refunded
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_action_woocommerce_order_refunded( $order_get_id, $refund_get_id ) {
			update_post_meta( $refund_get_id['order_id'], 'ced_rnx_left_amount', '0' );
			update_post_meta( $refund_get_id['order_id'], 'refundable_amount', '0' );
		}

		/**
		 * Cancel order and manage stock of cancelled product.
		 *
		 * @name ced_rnx_cancel_customer_order
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_cancel_customer_order() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = $_POST['order_id'];

				$the_order = wc_get_order( $order_id );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$subject = '#' . $order_id . __( ' order canceled by customer', 'woocommerce-refund-and-exchange' );

				$message = __( 'Order is canceled by customer and current order status goes in canceled.', 'woocommerce-refund-and-exchange' );
				$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; margin-bottom: 10px; color: #99B1D8; font-size: 12px;">
														' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
														' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';

				$to = get_option( 'ced_rnx_notification_from_mail' );
				wc_mail( $to, $subject, $html_content, $headers );

				$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

				$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$url .= "$endpoints";
				$success    = __( 'Your order is cancelled', 'woocommerce-refund-and-exchange' );
				$the_order->cancel_order( __( 'Order canceled by customer.', 'woocommerce' ) );

				$notice     = wc_add_notice( $success );
				echo $url;
				wp_die();
			}
		}

		/**
		 * Cancel order's profucts and manage stock of cancelled product.
		 *
		 * @name ced_rnx_cancel_customer_order
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_cancel_customer_order_products() {
			 $check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = intval( $_POST['order_id'] );
				$the_order = wc_get_order( $order_id );
				$items = $the_order->get_items();
				$item_ids = $_POST['item_ids'];
				if ( ! is_array( $item_ids ) || empty( $item_ids ) ) {
					$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );
					$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
					$url .= "$endpoints";
					$success    = __( 'Please select order\'s product to cancel.', 'woocommerce-refund-and-exchange' );
					$notice     = wc_add_notice( $success, 'error' );
					echo $url;
					wp_die();
				}
				$message = '';
				$message .= '<div class="order">
								<h4>Order #' . $order_id . '</h4>
								<table>
									<thead>
										<tr>
											<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
											<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
											<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
										</tr>
									</thead>
									<tbody>';
				$total_amount = 0;
				foreach ( $items as $item_id => $item ) {
					foreach ( $item_ids as $item_detail ) {
						if ( $item_id == $item_detail[0] ) {
							$product_name = $item['name'];
							$product_id = $item['product_id'];
							$product_variation_id = $item['variation_id'];
							if ( $product_variation_id > 0 ) {
								$product = wc_get_product( $product_variation_id );
							} else {
								$product = wc_get_product( $product_id );
							}
							if ( WC()->version < '3.1.0' ) {
								$item_meta      = new WC_Order_Item_Meta( $item, $_product );
								$item_meta_html = $item_meta->display( true, true );
							} else {
								$item_meta      = new WC_Order_Item_Product( $item, $_product );
								$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
							}
							$total_amount += $item_detail[1] * wc_get_price_to_display( $product );

							$message .= '<tr>
													<td>' . $item['name'] . '<br>';
										$message .= '<small>' . $item_meta_html . '</small>
													<td>' . $item_detail[1] . '</td>
													<td>' . wc_price( $item_detail[1] * wc_get_price_to_display( $product ) ) . '</td>
												</tr>';
							if ( WC()->version < '3.0.0' ) {
								$product_qty_left = $item['qty'] - $item_detail[1];
								$product_quantity = $item_detail[1];
							} else {
								$product_qty_left = $item['qty'] - $item_detail[1];
								$product_quantity = $item_detail[1];
							}

							if ( $product_qty_left < 0 ) {
								$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

								$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );
								$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
								$url .= "$endpoints";
								$success    = __( 'Please select correct quantity of order\'s product.', 'woocommerce-refund-and-exchange' );
								$notice     = wc_add_notice( $success, 'error' );
								echo $url;
								wp_die();
							} else if ( $product_qty_left >= 0 ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

								$item['qty'] = $item['qty'] - $item_detail[1];
								$args['qty'] = $item['qty'];
								if ( WC()->version < '3.0.0' ) {
									$the_order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									$product = wc_get_product( $product->get_id() );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}

									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $order_id );
									$item->set_props( $args );
									$item->save();
								}
							}
							$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
							if ( $product->managing_stock() ) {
								if ( WC()->version < '3.0.0' ) {
									$product->set_stock( $product_quantity, 'add' );

								} else {
									if ( $product_variation_id > 0 ) {
										$total_stock = get_post_meta( $product_variation_id, '_stock', true );
										$total_stock = $total_stock + $product_quantity;
										wc_update_product_stock( $product_variation_id, $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $product_id, '_stock', true );
										$total_stock = $total_stock + $product_quantity;
										wc_update_product_stock( $product_id, $total_stock, 'set' );
									}
								}
							}
						}
					}
				}

				$message .= '</tbody></table></div>';
				$the_order->calculate_totals();
				$wallet_flag = true;
				if ( 'processing' === $the_order->get_status() && 'cod' === $the_order->get_payment_method() ) {
					$wallet_flag = false;
				}
				if ( ced_rnx_wallet_feature_enable() ) {
					$cancelstatusenable = get_option( 'ced_rnx_return_wallet_cancelled', 'no' );
					if ( $cancelstatusenable == 'yes' && $wallet_flag ) {
						$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
						if ( $customer_id > 0 ) {
							$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
							if ( empty( $walletcoupon ) ) {
								$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
								$discount_type = 'fixed_cart';
								$order_id = $the_order->get_id();
								$coupon_description = "CANCELLED - ORDER #$order_id";

								$coupon = array(
									'post_title' => $coupon_code,
									'post_content' => $coupon_description,
									'post_excerpt' => $coupon_description,
									'post_status' => 'publish',
									'post_author' => get_current_user_id(),
									'post_type'     => 'shop_coupon',
								);

								$new_coupon_id = wp_insert_post( $coupon );
								$discount_type = 'fixed_cart';
								update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
								update_post_meta( $new_coupon_id, 'rnxwallet', true );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
								update_post_meta( $new_coupon_id, 'coupon_amount', $total_amount );
							} else {
								$the_coupon = new WC_Coupon( $walletcoupon );
								$coupon_id = $the_coupon->get_id();
								if ( isset( $coupon_id ) ) {

									$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
									$remaining_amount = $amount + $total_amount;
									update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
									update_post_meta( $coupon_id, 'rnxwallet', true );
									update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								}
							}
						}
					} else {
						wc_add_notice( 'your order is unpaid' );
					}
				}
				$the_order->update_status( 'wc-partial-cancel', __( 'User has canceled some product of order.', 'woocommerce-refund-and-exchange' ) );
				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$subject = __( 'Product(s) canceled by a customer of order #', 'woocommerce-refund-and-exchange' ) . $order_id;
				$html_content = '<html>
								<body>
								<style>
								body {
								    box-shadow: 2px 2px 10px #ccc;
								    color: #767676;
								    font-family: Arial,sans-serif;
								    margin: 80px auto;
								    max-width: 700px;
								    padding-bottom: 30px;
								    width: 100%;
								}
								
								h2 {
									font-size: 30px;
									margin-top: 0;
									color: #fff;
									padding: 40px;
									background-color: #557da1;
								}
								
								h4 {
									color: #557da1;
									font-size: 20px;
									margin-bottom: 10px;
								}
								
								.content {
									padding: 0 40px;
								}

								.Customer-detail {
									padding: 0 40px;
								}

								.details {
									padding: 0 40px;
								}

								.Customer-detail ul li p {
									margin: 0;
								}
								
								.details .Shipping-detail {
									width: 40%;
									float: right;
								}
								
								.details .Billing-detail {
									width: 60%;
									float: left;
								}
								
								.details .Shipping-detail ul li,.details .Billing-detail ul li {
									list-style-type: none;
									margin: 0;
								}
								
								.details .Billing-detail ul,.details .Shipping-detail ul {
									margin: 0;
									padding: 0;
								}
								
								.clear {
									clear: both;
								}
								
								table,td,th {
									border: 2px solid #ccc;
									padding: 15px;
									text-align: left;
								}
								
								table {
									border-collapse: collapse;
									width: 100%;
								}
								
								.info {
									display: inline-block;
								}
								
								.bold {
									font-weight: bold;
								}
								
								.footer {
									margin-top: 30px;
									text-align: center;
									color: #99B1D8;
									font-size: 12px;
								}
								dl.variation dd {
								    font-size: 12px;
								    margin: 0;
									}
								</style>
								<div class="header" style="text-align: center; padding: 10px;">
								' . $mail_header . '
								</div>
							
								<div class="header">
									<h2>' . $subject . '</h2>
								</div>
											
								<div class="content">
									' . $message . '</div>';

											$html_content .= ' <div class="Customer-detail">
															<h4>' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</h4>
															<ul>
																<li><p class="info">
																		<span class="bold">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_email', true ) . '
																	</p></li>
																<li><p class="info">
																		<span class="bold">' . __( 'Tel', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_phone', true ) . '
																	</p></li>
															</ul>
														</div>
														<div class="details">
															<div class="Shipping-detail">
																<h4>' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $the_order->get_formatted_shipping_address() . '
															</div>
															<div class="Billing-detail">
																<h4>' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $the_order->get_formatted_billing_address() . '
															</div>
															<div class="clear"></div>
														</div>
													</div>
													<div style="text-align: center; padding: 10px;" class="footer">
													' . $mail_footer . '
													</div>
												</body>
												</html>';
				$headers = array();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_option( 'ced_rnx_notification_from_mail' );
				wc_mail( $to, $subject, $html_content, $headers );

				$endpoints = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );

				$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$url .= "$endpoints";
				$success    = __( 'Your selected product(s) removed from order.', 'woocommerce-refund-and-exchange' );
				$notice     = wc_add_notice( $success );
				echo $url;
				wp_die();
			}
		}

		/**
		 * Change coupon amount for customers from user listing panel.
		 *
		 * @name ced_rnx_change_customer_wallet_amount
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_change_customer_wallet_amount() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = $_POST['coupon_code'];
				$amount = $_POST['amount'];
				if ( ! isset( $amount ) || $amount == '' ) {
					$amount = 0;
				}
				$the_coupon = new WC_Coupon( $coupon_code );
				$customer_coupon_id = $the_coupon->get_id();
				if ( isset( $the_coupon ) && $the_coupon != '' ) {
					update_post_meta( $customer_coupon_id, 'coupon_amount', $amount );
				}
			}
			wp_die();
		}

		/**
		 * Generate User Wallet Coupon Code with no wallet.
		 *
		 * @name ced_rnx_generate_user_wallet_code
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_generate_user_wallet_code() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = ced_rnx_coupon_generator( 5 );
				$coupon = array(
					'post_title' => $coupon_code,
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_type'     => 'shop_coupon',
				);
				$new_coupon_id = wp_insert_post( $coupon );
				$discount_type = 'fixed_cart';
				update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
				update_post_meta( $new_coupon_id, 'coupon_amount', 0 );
				update_post_meta( $new_coupon_id, 'rnxwallet', true );
				update_post_meta( $_POST['id'], 'ced_rnx_refund_wallet_coupon', $coupon_code );
				echo esc_html( $coupon_code );
				wp_die();
			}
		}

		/**
		 * Regenerate Customer Wallet Coupon Code.
		 *
		 * @name ced_rnx_coupon_regenertor
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_coupon_regenertor() {

			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$coupon_code = ced_rnx_coupon_generator( 5 );
				$coupon = array(
					'ID' => $_POST['id'],
					'post_title' => $coupon_code,
				);
				$customer_id = get_current_user_id();
				wp_update_post( $coupon );
				update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
				$coupon_price = get_post_meta( $_POST['id'], 'coupon_amount', true );
				$response = array(
					'coupon_code' => $coupon_code,
					'currency_symbol' => get_woocommerce_currency_symbol(),
					'coupon_price' => ced_rnx_currency_seprator( $coupon_price ),
					'coupon_code_text' => __( 'Coupon Code', 'woocommerce-refund-and-exchange' ),
					'wallet_amount_text' => __( 'Wallet Amount', 'woocommerce-refund-and-exchange' ),
				);
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * Manage Customer Wallet on Order cancelled
		 *
		 * @name ced_rnx_woocommerce_order_status_changed
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 */
		function ced_rnx_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$order = wc_get_order( $order_id );
			if ( ced_rnx_wallet_feature_enable() ) {
				$cancelstatusenable = get_option( 'ced_rnx_return_wallet_cancelled', 'no' );

				if ( $cancelstatusenable == 'yes' ) {
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$statuses = array( 'processing', 'completed' );
						$wallet_flag = true;
						if ( 'processing' === $old_status && 'cod' === $order->get_payment_method() ) {
							$wallet_flag = false;
						}
						if ( in_array( $old_status, $statuses ) && $wallet_flag ) {
							if ( $new_status == 'cancelled' ) {
								$order_total = $order->get_total();
								$order_discount = $order->get_total_discount();
								$total_amount = $order_total + $order_discount;

								$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
								if ( empty( $walletcoupon ) ) {
									$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
									$discount_type = 'fixed_cart';
									$order_id = $order->get_id();
									$coupon_description = "CANCELLED - ORDER #$order_id";

									$coupon = array(
										'post_title' => $coupon_code,
										'post_content' => $coupon_description,
										'post_excerpt' => $coupon_description,
										'post_status' => 'publish',
										'post_author' => get_current_user_id(),
										'post_type'     => 'shop_coupon',
									);

									$new_coupon_id = wp_insert_post( $coupon );
									$discount_type = 'fixed_cart';
									update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
									update_post_meta( $new_coupon_id, 'coupon_amount', $total_amount );
									update_post_meta( $new_coupon_id, 'rnxwallet', true );
									update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
								} else {
									$the_coupon = new WC_Coupon( $walletcoupon );
									$coupon_id = $the_coupon->get_id();
									if ( isset( $coupon_id ) ) {

										$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
										$remaining_amount = $amount + $total_amount;
										update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
										update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
										update_post_meta( $coupon_id, 'rnxwallet', true );
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Exchange cancel callback.
		 *
		 * @name ced_rnx_coupon_regenertor
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_exchange_req_cancel_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$date = $_POST['date'];

				$products = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$products[ $date ]['status'] = 'cancel';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['cancel_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $products );

				$order = new WC_Order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_post_meta( $orderid, '_billing_email', true );
				$subject = get_option( 'ced_rnx_notification_exchange_cancel_subject', false );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_cancel', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$order_id = $orderid;
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$template = get_option( 'ced_rnx_notification_exchange_cancel_template', 'no' );

				if ( isset( $template ) && $template == 'on' ) {

					$html_content = $message;
				} else {
					$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; margin-bottom: 10px; color: #99B1D8; font-size: 12px;">
														' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
														' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					$status = wc_mail( $to, $subject, $html_content, $headers );
				}
				$order->update_status( 'wc-exchange-cancel', 'User Request of Exchange Product is Cancelled.' );

				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}

		}


		/**
		 * This function is enable cancel for exchange approved order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		function ced_rnx_order_can_cancel( $status ) {
			$status[] = 'exchange-approve';
			return $status;
		}

		/**
		 * This function is enable Payment for exchange approved order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		function ced_rnx_order_need_payment( $status ) {
			$status[] = 'exchange-approve';
			return $status;
		}

		/**
		 * This function is update order number listing for exhanged Order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_update_order_number_callback( $order_id ) {
			$orderid = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
			if ( isset( $orderid ) && ! empty( $orderid ) ) {
				$order_id = $order_id . ' → ' . $orderid;
			}
			return $order_id;
		}

		/**
		 * This function is add Meta box for Return Product on order detail at admin
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_product_return_meta_box() {
			 add_meta_box( 'ced_rnx_order_return', __( 'Refund Requested Products', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_return' ), 'shop_order' );
			add_meta_box( 'ced_rnx_order_exchange', __( 'Exchange Products Requested', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_exchange' ), 'shop_order' );
			add_meta_box( 'ced_rnx_order_msg_history', __( 'Order Message History', 'woocommerce-refund-and-exchange' ), array( $this, 'ced_rnx_order_msg_history' ), 'shop_order' );
		}

		/**
		 * This function is add Meta box for Exchange Product on order detail at admin
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_order_exchange() {
			 global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-exchange-product-meta.php';
		}

		/**
		 * This function is metabox template for Refund order product
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @param unknown $order
		 */
		public function ced_rnx_order_return() {
			global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-return-product-meta.php';
		}

		/**
		 * This function is add cs and js to order meta
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 * @param unknown $order
		 */
		public function ced_rnx_admin_scripts() {
			$wallet_enable = false;
			$screen = get_current_screen();
			if ( isset( $screen->id ) ) {
				if ( $screen->id == 'shop_order' ) {
					$customer_id = ( $value = get_post_meta( $_GET['post'], '_customer_user', true ) ) ? absint( $value ) : 0;
					$wallet_enabled = get_option( 'ced_rnx_return_wallet_enable', false );
					if ( $wallet_enabled == 'yes' && $customer_id > 0 ) {
						$wallet_enable = true;
					}
				}
			}
			$url = plugins_url();
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'ced-rnx-notification' ) {

				wp_enqueue_style( 'ced-rnx-style-jqueru-ui', CED_REFUND_N_EXCHANGE_URL . 'assets/css/jquery-ui.css' );
				wp_enqueue_style( 'ced-rnx-style-timepicker', CED_REFUND_N_EXCHANGE_URL . 'assets/css/jquery.ui.timepicker.css' );
				wp_enqueue_script( 'ced-rnx-script-timepicker', CED_REFUND_N_EXCHANGE_URL . 'assets/js/jquery.ui.timepicker.js', array( 'jquery' ), CED_REFUND_N_EXCHANGE_VERSION, true );
			}
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
			wp_dequeue_script( 'select2' );
			wp_deregister_script( 'select2' );

			wp_register_script( 'ced-rnx-script-admin', CED_REFUND_N_EXCHANGE_URL . 'assets/js/ced-rnx-admin-script.js', array( 'jquery' ), CED_REFUND_N_EXCHANGE_VERSION, true );
			$ajax_nonce = wp_create_nonce( 'ced-rnx-ajax-seurity-string' );
			$translation_array = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wallet'  => $wallet_enable,
				'defuat_catalog_name' => __( 'Default Catalog', 'woocommerce-refund-and-exchange' ),
				'catalog_name' => __( 'Catalog Name:', 'woocommerce-refund-and-exchange' ),
				'select_catalog_product' => __( 'Select Catalog Products:', 'woocommerce-refund-and-exchange' ),
				'maximum_catalog_refund_days' => __( 'Maximum Refund Days:', 'woocommerce-refund-and-exchange' ),
				'maximum_catalog_exchange_days' => __( 'Maximum Exchange Days:', 'woocommerce-refund-and-exchange' ),
				'placeholder_exchange' => __( 'Enter Exchange Days', 'woocommerce-refund-and-exchange' ),
				'placeholder_refund' => __( 'Enter Refund Days', 'woocommerce-refund-and-exchange' ),
				'placeholder_catalog_name' => __( 'Enter Catalog Name', 'woocommerce-refund-and-exchange' ),
				'catalog_disable' => __( 'If value is 0 then catalog will not work.', 'woocommerce-refund-and-exchange' ),
				'ced_rnx_nonce' => $ajax_nonce,
				'ced_rnx_currency_symbol' => get_woocommerce_currency_symbol(),
				'remove'                => __( 'Remove', 'woocommerce-refund-and-exchange' ),
				'message_sent' => __( 'Message has been sent successfully', 'woocommerce-refund-and-exchange' ),
				'message_empty' => __( 'Please enter a Message.', 'woocommerce-refund-and-exchange' ),
			);
			wp_localize_script( 'ced-rnx-script-admin', 'global_rnx', $translation_array );

			if ( $screen->id == 'woocommerce_page_ced-rnx-notification' || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'ced_rnx_setting' ) || $screen->id == 'edit-shop_order' || $screen->id == 'shop_order' || $screen->id == 'users' || $screen->id == 'profile' || 'user-edit' == $screen->id || isset( $_GET['page'] ) && $_GET['page'] == 'ced-rnx-notification' ) {
				wp_enqueue_style( 'ced-rnx-style-admin', CED_REFUND_N_EXCHANGE_URL . 'assets/css/ced-rnx-admin.css' );
				wp_enqueue_script( 'ced-rnx-script-admin' );
				$ced_rnx_side = array(
					'ced_rnx_URL' => CED_REFUND_N_EXCHANGE_URL,
					'Hide_sidebar' => __( 'Hide Sidebar', 'woocommerce-refund-and-exchange' ),
					'Show_sidebar' => __( 'Show Sidebar', 'woocommerce-refund-and-exchange' ),
					'button_text' => __( 'View More Features', 'woocommerce-refund-and-exchange' ),
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				);
				wp_register_script( 'ced_rnx_sidebar_script', CED_REFUND_N_EXCHANGE_URL . 'assets/js/mwb_get_sidebar.js', array( 'jquery', 'wp-color-picker' ), CED_REFUND_N_EXCHANGE_VERSION, false );
				wp_localize_script( 'ced_rnx_sidebar_script', 'ced_rnx_side', $ced_rnx_side );
				wp_enqueue_script( 'ced_rnx_sidebar_script' );
			}
		}

		/**
		 * This function is add extra fee to Refund product
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_return_fee_add_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$pending_date = $_POST['date'];
				$fees = $_POST['fees'];

				if ( isset( $fees ) ) {
					foreach ( $fees as $k => $fee ) {
						if ( $fee['text'] == '' || $fee['val'] == '' ) {
							unset( $fees[ $k ] );
						}
					}
				}
				$added_fees = get_post_meta( $orderid, 'ced_rnx_return_added_fee', array() );
				$exist = true;
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $date => $added_fee ) {
						if ( $date == $pending_date ) {
							$added_fees[ $pending_date ] = $fees;
							$exist = false;
							break;
						}
					}
				}

				if ( $exist ) {
					$added_fees[ $pending_date ] = $fees;
				}

				update_post_meta( $orderid, 'ced_rnx_return_added_fee', $added_fees );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is approve return request and decrease product quantity from order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_return_req_approve_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {

				$orderid = $_POST['orderid'];
				$date = $_POST['date'];
				$products = get_post_meta( $orderid, 'ced_rnx_return_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$product_datas = $product['products'];
							$products[ $date ]['status'] = 'complete';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['approve_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_product', $products );

				$request_files = get_post_meta( $orderid, 'ced_rnx_return_attachment', true );

				if ( isset( $request_files ) && ! empty( $request_files ) ) {
					foreach ( $request_files as $date => $request_file ) {
						if ( $request_file['status'] == 'pending' ) {
							$request_files[ $date ]['status'] = 'complete';
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_attachment', $request_files );

				$order = wc_get_order( $orderid );

				// Return the ordered product qty
				$return_pro = '';
				foreach ( $order->get_items() as $item_id => $item ) {
					foreach ( $product_datas as $k => $product_data ) {
						if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
							$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

							$item['qty'] = $item['qty'] - $product_data['qty'];
							$args['qty'] = $item['qty'];
							$return_pro = $return_pro . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
							if ( WC()->version < '3.0.0' ) {
								$order->update_product( $item_id, $product, $args );
							} else {
								wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

								$product = wc_get_product( $product->get_id() );

								if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
									$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
								}

								$item_data = $item->get_data();

								$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
								$price_tax_excluded = $item_data['total'] / $item_data['quantity'];

								$args['subtotal'] = $price_excluded_tax * $args['qty'];
								$args['total']  = $price_tax_excluded * $args['qty'];

								$item->set_order_id( $orderid );
								$item->set_props( $args );
								$item->save();
							}
							break;
						}
					}
				}
				$refund_note = __( 'Product Refund Request for', 'woocommerce-refund-and-exchange' ) . ' { ' . trim( $return_pro, '| ' ) . ' } ' . __( ' has been approved.', 'woocommerce-refund-and-exchange' );

				$order = new WC_Order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = get_option( 'ced_rnx_notification_return_approve_subject', false );
				$approve = get_option( 'ced_rnx_notification_return_approve', false );
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );

				if ( $wallet_enable == 'yes' ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$approve = get_option( 'ced_rnx_notification_return_approve_wallet', false );
					}
				}

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );

				$fullname = $fname . ' ' . $lname;

				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$message = '<html>
				<body>
				<style>
				body {
				    box-shadow: 2px 2px 10px #ccc;
				    color: #767676;
				    font-family: Arial,sans-serif;
				    margin: 80px auto;
				    max-width: 700px;
				    padding-bottom: 30px;
				    width: 100%;
				}
				
				h2 {
					font-size: 30px;
					margin-top: 0;
					color: #fff;
					padding: 40px;
					background-color: #557da1;
				}
				
				h4 {
					color: #557da1;
					font-size: 20px;
					margin-bottom: 10px;
				}
				
				.content {
					padding: 0 40px;
				}
				
				.Customer-detail ul li p {
					margin: 0;
				}
				
				.details .Shipping-detail {
					width: 40%;
					float: right;
				}
				
				.details .Billing-detail {
					width: 60%;
					float: left;
				}
				
				.details .Shipping-detail ul li,.details .Billing-detail ul li {
					list-style-type: none;
					margin: 0;
				}
				
				.details .Billing-detail ul,.details .Shipping-detail ul {
					margin: 0;
					padding: 0;
				}
				
				.clear {
					clear: both;
				}
				
				table,td,th {
					border: 2px solid #ccc;
					padding: 15px;
					text-align: left;
				}
				
				table {
					border-collapse: collapse;
					width: 100%;
				}
				
				.info {
					display: inline-block;
				}
				
				.bold {
					font-weight: bold;
				}
				
				.footer {
					margin-top: 30px;
					text-align: center;
					color: #99B1D8;
					font-size: 12px;
				}
							dl.variation dd {
							    font-size: 12px;
							    margin: 0;
								}
				</style>
						
				<div style="text-align: center; padding: 10px;" class="header">
					' . $mail_header . '
				</div>		
				
				<div class="header">
				<h2>' . __( 'Your Refund Request is Approved', 'woocommerce-refund-and-exchange' ) . '</h2>
				</div>
				<div class="content">
					<div class="reason">
						<p>' . $approve . '</p>
					</div>
				<div class="Order">
				<h4>Order #' . $orderid . '</h4>
				<table>
				<tbody>
				<tr>
				<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
				<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
				<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
				</tr>';
				$order = wc_get_order( $orderid );
				$requested_products = $products[ $date ]['products'];

				if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
					$total = 0;
					$mwb_get_refnd = get_post_meta( $orderid, 'ced_rnx_return_product', true );
					if ( ! empty( $mwb_get_refnd ) ) {
						foreach ( $mwb_get_refnd as $key => $value ) {
							if ( isset( $value['amount'] ) ) {
								$total_price = $value['amount'];
								break;
							}
						}
					}
					foreach ( $order->get_items() as $item_id => $item ) {
						$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
						foreach ( $requested_products as $requested_product ) {
							if ( $item_id == $requested_product['item_id'] ) {

								if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
									$prod = wc_get_product( $requested_product['variation_id'] );

								} else {
									$prod = wc_get_product( $requested_product['product_id'] );
								}

								$prod_price = wc_get_price_excluding_tax( $prod, array( 'qty' => 1 ) );
								$subtotal = $prod_price * $requested_product['qty'];
								$total += $subtotal;
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									$item_meta_html = $item_meta->display( true, true );
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
								}
								$message .= '<tr>
											<td>' . $item['name'] . '<br>';
								$message .= '<small>' . $item_meta_html . '</small>
											<td>' . $requested_product['qty'] . '</td>
											<td>' . ced_rnx_format_price( $requested_product['price'] * $requested_product['qty'] ) . '</td>
										</tr>';

							}
						}
					}
					$message .= '<tr>
									<th colspan="2">Total:</th>
									<td>' . ced_rnx_format_price( $total_price ) . '</td>
								</tr>
								<tr>
									<th colspan="3">Extra:</th>
								</tr>';
				}
				if ( WC()->version < '3.0.0' ) {
					$order_id = $order->id;
				} else {
					$order_id = $order->get_id();
				}
				$added_fees = get_post_meta( $order_id, 'ced_rnx_return_added_fee', true );
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $da => $added_fee ) {
						if ( $date == $da ) {
							foreach ( $added_fee as $fee ) {
								$total -= $fee['val'];
								$total_price -= $fee['val'];
								$message .= ' <tr>
												<th colspan="2">' . $fee['text'] . ':</th>
												<td>' . ced_rnx_format_price( $fee['val'] ) . '</td>
											</tr>';
							}
						}
					}
				}
				if ( WC()->version < '3.0.0' ) {
					$order_id = $order->id;
				} else {
					$order_id = $order->get_id();
				}
				$message .= ' <tr>
								<th colspan="2">' . __( 'Refund Total', 'woocommerce-refund-and-exchange' ) . ':</th>
									<td>' . ced_rnx_format_price( $total_price ) . '</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="Customer-detail">
						<h4>' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</h4>
							<ul>
								<li>
									<p class="info">
										<span class="bold">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_email', true ) . '
									</p>
								</li>
								<li>
									<p class="info">
										<span class="bold">' . __( 'Tel', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $order_id, '_billing_phone', true ) . '
									</p>
								</li>
							</ul>
						</div>
						<div class="details">
							<div class="Shipping-detail">
								<h4>' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</h4>
								' . $order->get_formatted_shipping_address() . '
								</div>
								<div class="Billing-detail">
									<h4>' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</h4>
									' . $order->get_formatted_billing_address() . '
								</div>
								<div class="clear"></div>
							</div>
						</div>
					<div style="text-align: center; padding: 10px;" class="footer">
						' . $mail_footer . '
					</div>
					</body>
				</html>';

				$template = stripslashes( get_option( 'ced_rnx_notification_return_approve_template', 'no' ) );

				if ( isset( $template ) && $template == 'on' ) {
					$refund_approve_template = stripslashes( get_option( 'ced_rnx_notification_return_approve', false ) );
					$refund_approve_template = apply_filters( 'mwb_rnx_meta_content', $refund_approve_template );
					$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
					if ( $wallet_enable == 'yes' ) {
						$wallet_template = stripslashes( get_option( 'ced_rnx_notification_return_approve_wallet_template', 'no' ) );
						if ( isset( $wallet_template ) && $wallet_template == 'on' ) {
							$refund_approve_template = stripslashes( get_option( 'ced_rnx_notification_return_approve_wallet', false ) );
							$refund_approve_template = apply_filters( 'mwb_rnx_meta_content', $refund_approve_template );
						}
					}
				}
				// shortcode replace variable start//////////////////////

				$fname = get_post_meta( $order_id, '_billing_first_name', true );
				$lname = get_post_meta( $order_id, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				// shortcode replace variable end///////////////////

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $order_id, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $order_id, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				if ( isset( $refund_approve_template ) && $refund_approve_template != '' ) {
					$template = $refund_approve_template;
					$template = str_replace( '[username]', $fullname, $template );
					$template = str_replace( '[order]', '#' . $order_id, $template );
					$template = str_replace( '[siteurl]', home_url(), $template );
					$template = str_replace( '[_billing_company]', $billing_company, $template );
					$template = str_replace( '[_billing_email]', $billing_email, $template );
					$template = str_replace( '[_billing_phone]', $billing_phone, $template );
					$template = str_replace( '[_billing_country]', $billing_country, $template );
					$template = str_replace( '[_billing_address_1]', $billing_address_1, $template );
					$template = str_replace( '[_billing_address_2]', $billing_address_2, $template );
					$template = str_replace( '[_billing_state]', $billing_state, $template );
					$template = str_replace( '[_billing_postcode]', $billing_postcode, $template );
					$template = str_replace( '[_shipping_first_name]', $shipping_first_name, $template );
					$template = str_replace( '[_shipping_last_name]', $shipping_last_name, $template );
					$template = str_replace( '[_shipping_company]', $shipping_company, $template );
					$template = str_replace( '[_shipping_country]', $shipping_country, $template );
					$template = str_replace( '[_shipping_address_1]', $shipping_address_1, $template );
					$template = str_replace( '[_shipping_address_2]', $shipping_address_2, $template );
					$template = str_replace( '[_shipping_city]', $shipping_city, $template );
					$template = str_replace( '[_shipping_state]', $shipping_state, $template );
					$template = str_replace( '[_shipping_postcode]', $shipping_postcode, $template );
					$template = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $template );
					$template = str_replace( '[_order_shipping]', $order_shipping, $template );
					$template = str_replace( '[_order_total]', $order_total, $template );
					$template = str_replace( '[_refundable_amount]', $refundable_amount, $template );
					$template = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $template );
					$template = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $template );
					$html_content = $template;
				} else {
					$html_content = $message;
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}

				update_post_meta( $orderid, 'refundable_amount', $total_price );
				if ( $total_price > 0 ) {
					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $total_price );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );
				}

				$final_stotal = 0;
				$lastElement = end( $order->get_items() );
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( $item != $lastElement ) {
						$final_stotal += $item['subtotal'];
					}
				}

				update_post_meta( $orderid, 'discount', 0 );

				if ( $final_stotal > 0 ) {
					$mwb_rnx_obj = wc_get_order( $orderid );
					$tax_rate = 0;
					$tax = new WC_Tax();
					$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
					$rates = $tax->find_rates( array( 'country' => $country_code ) );
					foreach ( $rates as $rate ) {
						$tax_rate = $rate['rate'];
					}

					$total_ptax = $final_stotal * $tax_rate / 100;
					$orderval = $final_stotal + $total_ptax;
					$orderval = round( $orderval, 2 );

					// Coupons used in the order LOOP (as they can be multiple)
					if ( WC()->version < '3.7.0' ) {
						$coupon_used = $mwb_rnx_obj->get_used_coupons();
					} else {
						$coupon_used = $mwb_rnx_obj->get_coupon_codes();
					}
					foreach ( $coupon_used as $coupon_name ) {
						$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
						$coupon_id = $coupon_post_obj->ID;
						$coupons_obj = new WC_Coupon( $coupon_id );

						 $coupons_amount = $coupons_obj->get_amount();
						 $coupons_type = $coupons_obj->get_discount_type();
						if ( $coupons_type == 'percent' ) {
							$finaldiscount = $orderval * $coupons_amount / 100;
						}
					}

					$discount = $finaldiscount * 100 / ( 100 + $tax_rate );

					if ( $discount > 0 ) {
						update_post_meta( $orderid, 'discount', $discount );
					} else {
						update_post_meta( $orderid, '_cart_discount_tax', 0.00 );
						update_post_meta( $orderid, 'discount', 0.00 );
					}
				}

				// Auto accept return request
				if ( isset(
					$_POST['autoaccept
					']
				) ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_option( 'ced_rnx_notification_from_mail' );
					$subject = get_option( 'ced_rnx_notification_auto_accept_return_subject' );
					$subject = str_replace( '[order]', '#' . $order_id, $subject );

					$message = get_option( 'ced_rnx_notification_auto_accept_return_rcv' );
					$message = str_replace( '[username]', $fullname, $message );
					$message = str_replace( '[order]', '#' . $order_id, $message );
					$message = str_replace( '[siteurl]', home_url(), $message );
					$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
					$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[order]', '#' . $order_id, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );

					$mail_header = str_replace( '[order]', '#' . $order_id, $mail_header );
					$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

					$html_content = '<html>
									<head>
										<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
										<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
									</head>
									<body>
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
													' . $mail_header . '
												</td>
											</tr>	
											<tr>
												<td>
													<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
														<tr>
															<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
														</tr>
														<tr>
															<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;"> 
													' . $mail_footer . '
												</td>
											</tr>
										</table>
															
									</body>
								</html>';

					wc_mail( $to, $subject, $html_content, $headers );

				}

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );
				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					if ( WC()->version < '3.0.0' ) {
						$order_id = $order->id;
					} else {
						$order_id = $order->get_id();
					}
					$ced_rnx_shiping_address = $order->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $order->get_formatted_billing_address();
					}

					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $order_id, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $order_id, $subject );

					$message = get_option( 'ced_rnx_return_ship_template' );
					$message = str_replace( '[username]', $fullname, $message );
					$message = str_replace( '[order]', '#' . $order_id, $message );
					$message = str_replace( '[siteurl]', home_url(), $message );
					$message = str_replace( '[Tracking_Id]', 'ID#' . $order_id, $message );
					$message = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message );
					$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
					$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					if ( $message == '' ) {

					}
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message, $headers );
					}
				}

				$order->update_status( 'wc-return-approved', __( 'User Request of Refund Product is approved', 'woocommerce-refund-and-exchange' ) );
				$order->calculate_totals();
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is process cancel Refund request
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		function ced_rnx_return_req_cancel_callback() {
			 $check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$date = $_POST['date'];

				$products = get_post_meta( $orderid, 'ced_rnx_return_product', true );

				// Fetch the return request product
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( $product['status'] == 'pending' ) {
							$product_datas = $product['products'];
							$products[ $date ]['status'] = 'cancel';
							$approvdate = date( 'd-m-Y' );
							$products[ $date ]['cancel_date'] = $approvdate;
							break;
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_product', $products );

				$request_files = get_post_meta( $orderid, 'ced_rnx_return_attachment', true );
				if ( isset( $request_files ) && ! empty( $request_files ) ) {
					foreach ( $request_files as $date => $request_file ) {
						if ( $request_file['status'] == 'pending' ) {
							$request_files[ $date ]['status'] = 'cancel';
						}
					}
				}

				// Update the status
				update_post_meta( $orderid, 'ced_rnx_return_attachment', $request_files );

				$order = wc_get_order( $orderid );
				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$to = get_post_meta( $orderid, '_billing_email', true );
				$subject = get_option( 'ced_rnx_notification_return_cancel_subject', false );
				$message = stripslashes( get_option( 'ced_rnx_notification_return_cancel', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );
				$order_id = $orderid;
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $order_id, '_billing_company', true );
				$billing_email = get_post_meta( $order_id, '_billing_email', true );
				$billing_phone = get_post_meta( $order_id, '_billing_phone', true );
				$billing_country = get_post_meta( $order_id, '_billing_country', true );
				$billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
				$billing_state = get_post_meta( $order_id, '_billing_state', true );
				$billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $order_id, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $order_id, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $order_id, '_shipping_company', true );
				$shipping_country = get_post_meta( $order_id, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $order_id, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $order_id, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $order_id, '_shipping_city', true );
				$shipping_state = get_post_meta( $order_id, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $order_id, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $order_id, '_payment_method_title', true );
				$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
				$order_total = get_post_meta( $order_id, '_order_total', true );
				$refundable_amount = get_post_meta( $order_id, 'refundable_amount', true );

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$message = str_replace( '[formatted_shipping_address]', $order->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $order->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$mail_header = str_replace( '[username]', $fullname, $mail_header );
				$mail_header = str_replace( '[order]', '#' . $orderid, $mail_header );
				$mail_header = str_replace( '[siteurl]', home_url(), $mail_header );

				$template = get_option( 'ced_rnx_notification_return_cancel_template', 'no' );

				if ( isset( $template ) && $template == 'on' ) {

					$html_content = $message;
				} else {
					$html_content = '<html>
										<head>
											<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
											<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
										</head>
										<body>
											<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td style="text-align: center; margin-top: 30px; padding: 10px; color: #99B1D8; font-size: 12px;">
													' . $mail_header . '
													</td>
												</tr>
												<tr>
													<td>
														<table align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Open Sans; max-width: 600px; width: 100%;">
															<tr>
																<td style="padding: 36px 48px; width: 100%; background-color:#557DA1;color: #fff; font-size: 30px; font-weight: 300; font-family:helvetica;">' . $subject . '</td>
															</tr>
															<tr>
																<td style="width:100%; padding: 36px 48px 10px; background-color:#fdfdfd; font-size: 14px; color: #737373;">' . $message . '</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td style="text-align: center; margin-top: 30px; color: #99B1D8; font-size: 12px;">
													' . $mail_footer . '
													</td>
												</tr>
											</table>
										</body>
									</html>';
				}

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_return_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}

				$order->update_status( 'wc-return-cancelled', __( 'User Request of Refund Product is Cancelled', 'woocommerce-refund-and-exchange' ) );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}

		/**
		 * This function is add extra fee to exchange product
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		function ced_rnx_exchange_fee_add_callback() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$pending_date = $_POST['date'];
				$fees = array();
				if ( isset( $_POST['fees'] ) ) {
					$fees = $_POST['fees'];
					if ( isset( $fees ) ) {
						foreach ( $fees as $k => $fee ) {
							if ( $fee['text'] == '' || $fee['val'] == '' ) {
								unset( $fees[ $k ] );
							}
						}
					}
				}
				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );
				if ( isset( $exchange_details[ $pending_date ] ) ) {
					if ( isset( $exchange_details[ $pending_date ]['fee'] ) ) {
						$added_fees = $exchange_details[ $pending_date ]['fee'];
					} else {
						$added_fees = array();
					}
				}
				$exist = true;
				if ( isset( $added_fees ) && ! empty( $added_fees ) ) {
					foreach ( $added_fees as $date => $added_fee ) {
						if ( $date == $pending_date ) {
							$exchange_details[ $pending_date ]['fee'] = $fees;
							$exist = false;
							break;
						}
					}
				}

				if ( $exist ) {
					$exchange_details[ $pending_date ]['fee'] = $fees;
				}

				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );
				$response['response'] = 'success';
				echo json_encode( $response );
				wp_die();
			}
		}
		public function ced_exchange_req_approve_refund() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$order_id = isset( $_POST['orderid'] ) ? $_POST['orderid'] : 0;
				$ced_rnx_amount_for_refund = isset( $_POST['amount'] ) ? sanitize_text_field( wp_unslash( $_POST['amount'] ) ) : 0;
				$request_type = isset( $_POST['request_type'] ) ? sanitize_text_field( wp_unslash( $_POST['request_type'] ) ) : '';
				$wallet_enable = get_option( 'ced_rnx_return_wallet_enable', 'no' );
				update_post_meta( $order_id, 'ced_rnx_exchange_approve_refunded', 'yes' );
				if ( $wallet_enable == 'yes' && $order_id > 0 ) {
					$customer_id = ( $value = get_post_meta( $order_id, '_customer_user', true ) ) ? absint( $value ) : '';
					if ( $customer_id > 0 ) {
						$walletcoupon = get_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', true );
						if ( empty( $walletcoupon ) ) {
							$coupon_code = ced_rnx_coupon_generator( 5 ); // Code
							$amount = $total_price; // Amount
							$discount_type = 'fixed_cart';
							$coupon_description = "REFUND ACCEPTED - ORDER #$order_id";

							$coupon = array(
								'post_title' => $coupon_code,
								'post_content' => $coupon_description,
								'post_excerpt' => $coupon_description,
								'post_status' => 'publish',
								'post_author' => get_current_user_id(),
								'post_type'     => 'shop_coupon',
							);

							$new_coupon_id = wp_insert_post( $coupon );
							$discount_type = 'fixed_cart';
							update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
							update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
							update_post_meta( $new_coupon_id, 'rnxwallet', true );
							update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $coupon_code );
						} else {
							$the_coupon = new WC_Coupon( $walletcoupon );
							$coupon_id = $the_coupon->get_id();
							if ( isset( $coupon_id ) ) {
								$amount = get_post_meta( $coupon_id, 'coupon_amount', true );
								$remaining_amount = $amount + $ced_rnx_amount_for_refund;
								update_post_meta( $coupon_id, 'coupon_amount', $remaining_amount );
								update_post_meta( $customer_id, 'ced_rnx_refund_wallet_coupon', $walletcoupon );
								update_post_meta( $coupon_id, 'rnxwallet', true );
							}
						}
						if ( $request_type == 'refund' ) {
							update_post_meta( $order_id, 'refundable_amount', 0 );
						} else {
							update_post_meta( $order_id, 'ced_rnx_left_amount', 0 );
						}
						$order = wc_get_order( $order_id );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Amount Refunded in wallet' ) );
						$new_fee->set_total( - $ced_rnx_amount_for_refund );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->save();
						$item_id = $order->add_item( $new_fee );

						$order->calculate_totals();
						$response['result'] = true;
						$response['msg'] = __( 'Amount is added in customer wallet.', 'woocommerce-refund-and-exchange' );
						echo json_encode( $response );
						wp_die();
					}
				} else {
					$response['result'] = false;
					$response['msg'] = __( 'Wallet is not Enable, Please Enable wallet to add the amount in customer wallet.', 'woocommerce-refund-and-exchange' );

					echo json_encode( $response );
					wp_die();
				}
			}
		}

		/**
		 * This function is approve exchange request and Create new order for exchnage product and decrease product quantity from order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */

		public function ced_exchange_req_approve_callback() {
			
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$orderid = $_POST['orderid'];
				$checkdate = $_POST['date'];

				$exchange_details = get_post_meta( $orderid, 'ced_rnx_exchange_product', true );

				if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
					foreach ( $exchange_details as $date => $exchange_detail ) {
						if ( $exchange_detail['status'] == 'pending' ) {
							$exchanged_products = $exchange_detail['to'];
							$exchanged_from_products = $exchange_detail['from'];
							if ( isset( $exchange_detail['fee'] ) ) {
								$added_fee = $exchange_detail['fee'];
							}
							$exchange_details[ $date ]['status'] = 'complete';
							$exchange_details[ $date ]['approve'] = date( 'd-m-Y' );
							break;
						}
					}
				}
				$order_detail = wc_get_order( $orderid );

				$includeTax = isset( $order_detail->prices_include_tax ) ? $order_detail->prices_include_tax : false;
				$user_id = $order_detail->user_id;

				$order_data = array(
					'post_name'     => 'order-' . date( 'M-d-Y-hi-a' ), // 'order-jun-19-2014-0648-pm'
					'post_type'     => 'shop_order',
					'post_title'    => 'Order &ndash; ' . date( 'F d, Y @ h:i A' ), // 'June 19, 2014 @ 07:19 PM'
					'post_status'   => 'wc-exchange-approve',
					'ping_status'   => 'closed',
					'post_excerpt'  => 'requested',
					'post_author'   => $user_id,
					'post_password' => uniqid( 'order_' ),
					'post_date'     => date( 'Y-m-d H:i:s e' ),
					'comment_status' => 'open',
				);

				$order_id = wp_insert_post( $order_data, true );

				$approve = get_option( 'ced_rnx_notification_exchange_approve' );
				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );

				$fullname = $fname . ' ' . $lname;

				$approve = str_replace( '[username]', $fullname, $approve );
				$approve = str_replace( '[order]', '#' . $orderid, $approve );
				$approve = str_replace( '[siteurl]', home_url(), $approve );
				$message = stripslashes( get_option( 'ced_rnx_notification_exchange_approve', false ) );
				$message = apply_filters( 'mwb_rnx_meta_content', $message );

				$fname = get_post_meta( $orderid, '_billing_first_name', true );
				$lname = get_post_meta( $orderid, '_billing_last_name', true );
				$billing_company = get_post_meta( $orderid, '_billing_company', true );
				$billing_email = get_post_meta( $orderid, '_billing_email', true );
				$billing_phone = get_post_meta( $orderid, '_billing_phone', true );
				$billing_country = get_post_meta( $orderid, '_billing_country', true );
				$billing_address_1 = get_post_meta( $orderid, '_billing_address_1', true );
				$billing_address_2 = get_post_meta( $orderid, '_billing_address_2', true );
				$billing_state = get_post_meta( $orderid, '_billing_state', true );
				$billing_postcode = get_post_meta( $orderid, '_billing_postcode', true );
				$shipping_first_name = get_post_meta( $orderid, '_shipping_first_name', true );
				$shipping_last_name = get_post_meta( $orderid, '_shipping_last_name', true );
				$shipping_company = get_post_meta( $orderid, '_shipping_company', true );
				$shipping_country = get_post_meta( $orderid, '_shipping_country', true );
				$shipping_address_1 = get_post_meta( $orderid, '_shipping_address_1', true );
				$shipping_address_2 = get_post_meta( $orderid, '_shipping_address_2', true );
				$shipping_city = get_post_meta( $orderid, '_shipping_city', true );
				$shipping_state = get_post_meta( $orderid, '_shipping_state', true );
				$shipping_postcode = get_post_meta( $orderid, '_shipping_postcode', true );
				$payment_method_tittle = get_post_meta( $orderid, '_payment_method_title', true );
				$order_shipping = get_post_meta( $orderid, '_order_shipping', true );
				$order_total = get_post_meta( $orderid, '_order_total', true );
				$refundable_amount = get_post_meta( $orderid, 'refundable_amount', true );

				$fullname = $fname . ' ' . $lname;

				$message = str_replace( '[username]', $fullname, $message );
				$message = str_replace( '[order]', '#' . $orderid, $message );
				$message = str_replace( '[siteurl]', home_url(), $message );
				$message = str_replace( '[_billing_company]', $billing_company, $message );
				$message = str_replace( '[_billing_email]', $billing_email, $message );
				$message = str_replace( '[_billing_phone]', $billing_phone, $message );
				$message = str_replace( '[_billing_country]', $billing_country, $message );
				$message = str_replace( '[_billing_address_1]', $billing_address_1, $message );
				$message = str_replace( '[_billing_address_2]', $billing_address_2, $message );
				$message = str_replace( '[_billing_state]', $billing_state, $message );
				$message = str_replace( '[_billing_postcode]', $billing_postcode, $message );
				$message = str_replace( '[_shipping_first_name]', $shipping_first_name, $message );
				$message = str_replace( '[_shipping_last_name]', $shipping_last_name, $message );
				$message = str_replace( '[_shipping_company]', $shipping_company, $message );
				$message = str_replace( '[_shipping_country]', $shipping_country, $message );
				$message = str_replace( '[_shipping_address_1]', $shipping_address_1, $message );
				$message = str_replace( '[_shipping_address_2]', $shipping_address_2, $message );
				$message = str_replace( '[_shipping_city]', $shipping_city, $message );
				$message = str_replace( '[_shipping_state]', $shipping_state, $message );
				$message = str_replace( '[_shipping_postcode]', $shipping_postcode, $message );
				$message = str_replace( '[_payment_method_tittle]', $payment_method_tittle, $message );
				$message = str_replace( '[_order_shipping]', $order_shipping, $message );
				$message = str_replace( '[_order_total]', $order_total, $message );
				$message = str_replace( '[_refundable_amount]', $refundable_amount, $message );
				$ced_rnx_odr = wc_get_order( $orderid );
				$message = str_replace( '[formatted_shipping_address]', $ced_rnx_odr->get_formatted_shipping_address(), $message );
				$message = str_replace( '[formatted_billing_address]', $ced_rnx_odr->get_formatted_billing_address(), $message );

				$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
				$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
				$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
				$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );
				$ced_rnx_notification_exchange_approve_template = get_option( 'ced_rnx_notification_exchange_approve_template', 'no' );
				$mwb_dis_tot = 0;
				$ced_flag = false;

				$fname = get_option( 'ced_rnx_notification_from_name' );
				$fmail = get_option( 'ced_rnx_notification_from_mail' );

				$ced_rnx_enable_return_ship_label = get_option( 'ced_rnx_enable_return_ship_label', 'no' );
				if ( $ced_rnx_enable_return_ship_label == 'on' ) {
					$headers = array();
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$to = get_post_meta( $orderid, '_billing_email', true );
					$subject = get_option( 'ced_rnx_return_slip_mail_subject' );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$ced_rnx_order_for_label = wc_get_order( $orderid );
					$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_shipping_address();
					if ( $ced_rnx_shiping_address == '' ) {
						$ced_rnx_shiping_address = $ced_rnx_order_for_label->get_formatted_billing_address();
					}
					$message1 = get_option( 'ced_rnx_return_ship_template' );
					$message1 = str_replace( '[username]', $fullname, $message1 );
					$message1 = str_replace( '[order]', '#' . $orderid, $message1 );
					$message1 = str_replace( '[siteurl]', home_url(), $message1 );
					$message1 = str_replace( '[Tracking_Id]', 'ID#' . $orderid, $message1 );
					$message1 = str_replace( '[Order_shipping_address]', $ced_rnx_shiping_address, $message1 );
					$message1 = str_replace( '[formatted_shipping_address]', $ced_rnx_order_for_label->get_formatted_shipping_address(), $message1 );
					$message1 = str_replace( '[formatted_billing_address]', $ced_rnx_order_for_label->get_formatted_billing_address(), $message1 );

					$mail_header = stripslashes( get_option( 'ced_rnx_notification_mail_header', false ) );
					$mail_header = apply_filters( 'mwb_rnx_meta_content', $mail_header );
					$mail_footer = stripslashes( get_option( 'ced_rnx_notification_mail_footer', false ) );
					$mail_footer = apply_filters( 'mwb_rnx_meta_content', $mail_footer );

					$subject = str_replace( '[username]', $fullname, $subject );
					$subject = str_replace( '[order]', '#' . $orderid, $subject );
					$subject = str_replace( '[siteurl]', home_url(), $subject );
					$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
					if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
						wc_mail( $to, $subject, $message1, $headers );
					}
				}

				update_post_meta( $order_id, 'ced_rnx_exchange_order', $orderid );
				update_post_meta( $orderid, "date-$date", $order_id );
				update_post_meta( $orderid, 'mwb_rnx_status_exchanged', $mwb_dis_tot );

				$ex_fr = '';
				foreach ( $order_detail->get_items() as $item_id => $item ) {
					if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
						foreach ( $exchanged_from_products as $k => $product_data ) {
							if ( $item['product_id'] == $product_data['product_id'] && $item['variation_id'] == $product_data['variation_id'] ) {
								$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
								$item['qty'] = $item['qty'] - $product_data['qty'];
								$args['qty'] = $item['qty'];
								$ex_fr = $ex_fr . $item['name'] . '(SKU : ' . $product->get_sku() . ') x ' . $product_data['qty'] . ' | ';
								if ( WC()->version < '3.0.0' ) {
									$order->update_product( $item_id, $product, $args );
								} else {
									wc_update_order_item_meta( $item_id, '_qty', $item['qty'] );

									if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
										$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ) ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
									}
									$item_data = $item->get_data();

									$price_excluded_tax = wc_get_price_excluding_tax( $product, array( 'qty' => 1 ) );
									$price_tax_excluded = $item_data['total'] / ($item_data['quantity'] > 0 ? $item_data['quantity']:1);

									$args['subtotal'] = $price_excluded_tax * $args['qty'];
									$args['total']  = $price_tax_excluded * $args['qty'];

									$item->set_order_id( $orderid );
									$item->set_props( $args );
									$item->save();
								}

								break;
							}
						}
					}
				}

				$order_detail->calculate_totals();
				$order_detail->update_status( 'wc-completed' );
				$order = (object) $order_detail->get_address( 'shipping' );

				// Shipping info

				update_post_meta( $order_id, '_customer_user', $user_id );
				update_post_meta( $order_id, '_shipping_address_1', $order->address_1 );
				update_post_meta( $order_id, '_shipping_address_2', $order->address_2 );
				update_post_meta( $order_id, '_shipping_city', $order->city );
				update_post_meta( $order_id, '_shipping_state', $order->state );
				update_post_meta( $order_id, '_shipping_postcode', $order->postcode );
				update_post_meta( $order_id, '_shipping_country', $order->country );
				update_post_meta( $order_id, '_shipping_company', $order->company );
				update_post_meta( $order_id, '_shipping_first_name', $order->first_name );
				update_post_meta( $order_id, '_shipping_last_name', $order->last_name );

				// billing info

				$order_detail = wc_get_order( $orderid );
				$order_detail->calculate_totals();
				$order = (object) $order_detail->get_address( 'billing' );

				add_post_meta( $order_id, '_billing_first_name', $order->first_name, true );
				add_post_meta( $order_id, '_billing_last_name', $order->last_name, true );
				add_post_meta( $order_id, '_billing_company', $order->company, true );
				add_post_meta( $order_id, '_billing_address_1', $order->address_1, true );
				add_post_meta( $order_id, '_billing_address_2', $order->address_2, true );
				add_post_meta( $order_id, '_billing_city', $order->city, true );
				add_post_meta( $order_id, '_billing_state', $order->state, true );
				add_post_meta( $order_id, '_billing_postcode', $order->postcode, true );
				add_post_meta( $order_id, '_billing_country', $order->country, true );
				add_post_meta( $order_id, '_billing_email', $order->email, true );
				add_post_meta( $order_id, '_billing_phone', $order->phone, true );

				// $exchanged_products

				$order = wc_get_order( $order_id );
				if ( WC()->version >= '3.0.0' ) {
					if ( ! $order->get_order_key() ) {
						update_post_meta( $order_id, '_order_key', 'wc-' . uniqid( 'order_' ) );
					}
				}
				$orders = wc_get_order( $order_id );
				$new_url = $orders->get_checkout_order_received_url();
				$message = str_replace( '[new_order_id_created]', '#' . $order_id, $message );
				$message = str_replace( '[new_order_typ_url]', $new_url, $message );

				if ( isset( $ced_rnx_notification_exchange_approve_template ) && $ced_rnx_notification_exchange_approve_template == 'on' ) {
					$html_content = $message;
				} else {
					$ced_flag = true;

				}
				if ( $ced_flag ) {
					$html_content = $this->create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer );
				}
				$left_amount = get_post_meta( $orderid, 'ced_rnx_left_amount', true );
				if ( $left_amount > 0 ) {

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Refundable Amount' ) );
					$new_fee->set_total( $left_amount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->save();
					$item_id = $order_detail->add_item( $new_fee );
					$order_detail->calculate_totals();
				}
				$headers = array();
				$headers[] = "From: $fname <$fmail>";
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$subject = get_option( 'ced_rnx_notification_exchange_approve_subject' );

				$to = get_post_meta( $orderid, '_billing_email', true );

				$subject = str_replace( '[username]', $fullname, $subject );
				$subject = str_replace( '[order]', '#' . $orderid, $subject );
				$subject = str_replace( '[siteurl]', home_url(), $subject );

				$ced_rnx_restrict_mails = get_option( 'ced_rnx_exchange_restrict_customer_mails', true );
				if ( ! empty( $ced_rnx_restrict_mails ) && 'yes' != $ced_rnx_restrict_mails ) {
					wc_mail( $to, $subject, $html_content, $headers );
				}
				$ex_to = '';
				if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
					foreach ( $exchanged_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) ) {
							$product = wc_get_product( $exchanged_product['variation_id'] );
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
							$variation_product_price = wc_get_price_excluding_tax( $variation_product, array( 'qty' => 1 ) );

							$variation_att['variation'] = $variation_attributes;

							$variation_att['totals']['subtotal'] = $exchanged_product['qty'] * $variation_product_price;
							$variation_att['totals']['total'] = $exchanged_product['qty'] * $variation_product_price;

							$item_id = $order->add_product( $variation_product, $exchanged_product['qty'], $variation_att );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} elseif ( isset( $exchanged_product['id'] ) ) {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );

							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						} else {
							$product = wc_get_product( $exchanged_product['id'] );
							$item_id = $order->add_product( $product, $exchanged_product['qty'] );
							if ( $product->managing_stock() ) {
								$qty       = $exchanged_product['qty'];
								$new_stock = $product->reduce_stock( $qty );
							}
							$ex_to = $ex_to . $product->get_name() . '(SKU : ' . $product->get_sku() . ') x ' . $exchanged_product['qty'] . ' | ';
						}
					}
				}
				$ex_fr = trim( $ex_fr, '| ' );
				$ex_to = trim( $ex_to, '| ' );
				$exchange_note = __( 'Product Exchange Request from', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_fr . ' } ' . __( 'to', 'woocommerce-refund-and-exchange' ) . ' { ' . $ex_to . ' } ' . __( ' has been approved.', 'woocommerce-refund-and-exchange' );
				wc_get_order( $orderid )->add_order_note( $exchange_note );

				if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
					if ( is_array( $added_fee ) ) {
						foreach ( $added_fee as $fee ) {

							$new_fee  = new WC_Order_Item_Fee();
							$new_fee->set_name( esc_attr( $fee['text'] ) );
							$new_fee->set_total( $fee['val'] );
							$new_fee->set_tax_class( '' );
							$new_fee->set_tax_status( 'none' );
							$new_fee->set_total_tax( $totalProducttax );
							$new_fee->save();
							$item_id = $order->add_item( $new_fee );
						}
					}
				}
				$discount = 0;
				if ( isset( $exchanged_from_products ) && ! empty( $exchanged_from_products ) ) {
					$totalProducttax = '';
					$exchanged_from_products_count = count( $exchanged_from_products );
					$l_amount = $left_amount / $exchanged_from_products_count;
					foreach ( $exchanged_from_products as $exchanged_product ) {
						if ( isset( $exchanged_product['variation_id'] ) && $exchanged_product['variation_id'] > 0 ) {
							$p = wc_get_product( $exchanged_product['variation_id'] );
						} else {
							$p = wc_get_product( $exchanged_product['product_id'] );
						}
						if ( true ) {
							$_tax = new WC_Tax();

							$prePrice = $p->get_price_excluding_tax();
							$pTax = $exchanged_product['qty'] * ( $p->get_price() - $prePrice );
							
							$totalProducttax = (float)$totalProducttax;

							$totalProducttax += $pTax;
							if (!isset($rates)) {
								$rates = [];
							}
							
							$item_rate = round( array_shift( $rates ) );
							$price = $exchanged_product['qty'] * $prePrice;
							$discount += $price;
							$tax_rates = WC_Tax::get_rates( $p->get_tax_class() );
							if ( ! empty( $tax_rates ) ) {
								$tax_rate = reset( $tax_rates );

								$dis_tax = $tax_rate['rate'];
							}
						} else {
							$price = $exchanged_product['qty'] * $exchanged_product['price'];
							$discount += $price;
						}
					}
				}
				$dis_tax_amu = 0;
				if ( $left_amount > 0 ) {
					$mwb_rnx_obj = $order;
					$amount_discount = $mwb_rnx_obj->calculate_totals();
					$total_ptax = $mwb_rnx_obj->get_total_tax();
					$amount_discount = $amount_discount - $total_ptax;

					$new_fee  = new WC_Order_Item_Fee();
					$new_fee->set_name( esc_attr( 'Discount' ) );
					$new_fee->set_total( -$amount_discount );
					$new_fee->set_tax_class( '' );
					$new_fee->set_tax_status( 'none' );
					$new_fee->set_total_tax( '' );
					$new_fee->save();
					$item_id = $order->add_item( $new_fee );

				} else {
					if ( $discount > 0 ) {

						$mwb_rnx_obj = wc_get_order( $orderid );
						$tax_rate = 0;
						$tax = new WC_Tax();
						$country_code = WC()->countries->countries[ $mwb_rnx_obj->billing_country ]; // or populate from order to get applicable rates
						$rates = $tax->find_rates( array( 'country' => $country_code ) );
						foreach ( $rates as $rate ) {
							$tax_rate = $rate['rate'];
						}

						$total_ptax = $discount * $tax_rate / 100;
						$orderval = $discount + $total_ptax;
						$orderval = round( $orderval, 2 );

						// Coupons used in the order LOOP (as they can be multiple)
						if ( WC()->version < '3.7.0' ) {
							$coupon_used = $mwb_rnx_obj->get_used_coupons();
						} else {
							$coupon_used = $mwb_rnx_obj->get_coupon_codes();
						}
						foreach ( $coupon_used as $coupon_name ) {
							$coupon_post_obj = get_page_by_title( $coupon_name, OBJECT, 'shop_coupon' );
							$coupon_id = $coupon_post_obj->ID;
							$coupons_obj = new WC_Coupon( $coupon_id );

							 $coupons_amount = $coupons_obj->get_amount();
							 $coupons_type = $coupons_obj->get_discount_type();
							if ( $coupons_type == 'percent' ) {
								$finaldiscount = $orderval * $coupons_amount / 100;
							}
						}

						$discount = $orderval - $finaldiscount;
						$discount = $discount * 100 / ( 100 + $tax_rate );

						$new_fee  = new WC_Order_Item_Fee();
						$new_fee->set_name( esc_attr( 'Discount' ) );
						$new_fee->set_total( -$discount );
						$new_fee->set_tax_class( '' );
						$new_fee->set_tax_status( 'none' );
						$new_fee->set_total_tax( '' );
						$new_fee->save();
						$order->add_item( $new_fee );
						$items_key = $new_fee->get_id();
						$dis_tax_amu = ( $discount * $dis_tax ) / 100;
					}
				}

				$order_total = $order->calculate_totals();
				$order_total = $dis_tax_amu + $order_total;
				$order->set_total( $order_total, 'total' );

				if ( $order_total == 0 ) {
					$order->update_status( 'wc-processing' );
				} else {
					$manage_stock = get_option( 'ced_rnx_exchange_request_manage_stock' );
					if ( $manage_stock == 'yes' ) {
						if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
							foreach ( $exchanged_products as $key => $prod_data ) {
								if ( $prod_data['variation_id'] > 0 ) {
									$product = wc_get_product( $prod_data['variation_id'] );
								} else {
									$product = wc_get_product( $prod_data['id'] );
								}
								if ( $product->managing_stock() ) {
									$avaliable_qty = $prod_data['qty'];
									if ( $prod_data['variation_id'] > 0 ) {
										$total_stock = get_post_meta( $prod_data['variation_id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['variation_id'], $total_stock, 'set' );
									} else {
										$total_stock = get_post_meta( $prod_data['id'], '_stock', true );
										$total_stock = $total_stock - $avaliable_qty;
										wc_update_product_stock( $prod_data['id'], $total_stock, 'set' );
									}
								}
							}
						}
					}
				}
				if ( $includeTax ) {
					$order_total = $order_total - $totalProducttax;
				}
				update_post_meta( $orderid, 'ced_rnx_exchange_product', $exchange_details );

			}

		}
		/**
		 * This function is used for catalog count
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_catalog_count() {
			$catalog_count = $_POST['catalog_count'];
			update_option( 'catalog_count', $catalog_count, 'yes' );
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * This function is used for catalog deletion
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_catalog_delete() {
			$catalog_db_index = $_POST['catalog_db_index'];
			$ced_rnx_catalog = get_option( 'catalog' );
			foreach ( $ced_rnx_catalog as $key => $value ) {
				if ( $key == 'Catalog' . $catalog_db_index ) {
					array_splice( $ced_rnx_catalog, ( $catalog_db_index - 1 ), 1 );
					update_option( 'catalog', $ced_rnx_catalog, 'yes' );
				}
			}
			wp_die();
		}

		/**
		 * This function is show exchange request product on new exchange order
		 *
		 * @author makewebbetter<webmaster@makewebbetter.com>
		 * @link http://www.makewebbetter.com/
		 */
		public function ced_rnx_show_order_exchange_product( $order_id ) {
			$exchanged_order = get_post_meta( $order_id, 'ced_rnx_exchange_order', true );
			$exchange_details = get_post_meta( $exchanged_order, 'ced_rnx_exchange_product', true );
			$order = new WC_Order( $exchanged_order );
			$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );

			if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
				foreach ( $exchange_details as $date => $exchange_detail ) {
					if ( $exchange_detail['status'] == 'complete' ) {
						$exchanged_products = $exchange_detail['from'];
						break;
					}
				}
			}

			if ( isset( $exchanged_products ) && ! empty( $exchanged_products ) ) {
				?>
				<thead>
				<tr>
					<th colspan="6"><b><?php _e( 'Exchange Products', 'woocommerce-refund-and-exchange' ); ?></b></th>
					<th></th>
				</tr>		
					<tr>
						<th><?php _e( 'Item', 'woocommerce-refund-and-exchange' ); ?></th>
						<th colspan="2"><?php _e( 'Name', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Cost', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Qty', 'woocommerce-refund-and-exchange' ); ?></th>
						<th><?php _e( 'Total', 'woocommerce-refund-and-exchange' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<?php
				foreach ( $line_items as $item_id => $item ) {

					foreach ( $exchanged_products as $key => $exchanged_product ) {
						if ( $item_id == $exchanged_product['item_id'] ) {
							$_product  = $item->get_product();
							$item_meta = wc_get_order_item_meta( $item_id, $key );
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							?>
							<tr>
								<td class="thumb">
								<?php
									echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>';
								?>
								</td>
								<td class="name" colspan="2">
								<?php
									echo esc_html( $item['name'] );
								if ( $_product && $_product->get_sku() ) {
									echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'woocommerce-refund-and-exchange' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
								}
								if ( ! empty( $item['variation_id'] ) ) {
									echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'woocommerce-refund-and-exchange' ) . '</strong> ';
									if ( ! empty( $item['variation_id'] ) && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] );
									} elseif ( ! empty( $item['variation_id'] ) ) {
										echo esc_html( $item['variation_id'] ) . ' (' . __( 'No longer exists', 'woocommerce-refund-and-exchange' ) . ')';
									}
									echo '</div>';
								}
								if ( WC()->version < '3.1.0' ) {
									$item_meta      = new WC_Order_Item_Meta( $item, $_product );
									$item_meta->display();
								} else {
									$item_meta      = new WC_Order_Item_Product( $item, $_product );
									wc_display_item_meta( $item_meta );
								}
								?>
								</td>
								<td><?php echo ced_rnx_format_price( $exchanged_product['price'] ); ?></td>
								<td><?php echo $exchanged_product['qty']; ?></td>
								<td><?php echo ced_rnx_format_price( $exchanged_product['price'] * $exchanged_product['qty'] ); ?></td>
								<td></td>
							</tr>
							<?php
						}
					}
				}
			}
		}

		function create_exchange_approve_mail_html( $mail_header, $message, $orderid, $order_id, $exchange_details, $mail_footer ) {

			if ( isset( $exchange_details ) && ! empty( $exchange_details ) ) {
				foreach ( $exchange_details as $date => $exchange_detail ) {
					$requested_products = $exchange_details[ $date ]['from'];
					$exchanged_to_products = $exchange_details[ $date ]['to'];
				}
			}

			$html_content = '<html>
								<body>
								<style>
								body {
								    box-shadow: 2px 2px 10px #ccc;
								    color: #767676;
								    font-family: Arial,sans-serif;
								    margin: 80px auto;
								    max-width: 700px;
								    padding-bottom: 30px;
								    width: 100%;
								}
								
								h2 {
									font-size: 30px;
									margin-top: 0;
									color: #fff;
									padding: 40px;
									background-color: #557da1;
								}
								
								h4 {
									color: #557da1;
									font-size: 20px;
									margin-bottom: 10px;
								}
								
								.content {
									padding: 0 40px;
								}
								
								.Customer-detail ul li p {
									margin: 0;
								}
								
								.details .Shipping-detail {
									width: 40%;
									float: right;
								}
								
								.details .Billing-detail {
									width: 60%;
									float: left;
								}
								
								.details .Shipping-detail ul li,.details .Billing-detail ul li {
									list-style-type: none;
									margin: 0;
								}
								
								.details .Billing-detail ul,.details .Shipping-detail ul {
									margin: 0;
									padding: 0;
								}
								
								.clear {
									clear: both;
								}
								
								table,td,th {
									border: 2px solid #ccc;
									padding: 15px;
									text-align: left;
								}
								
								table {
									border-collapse: collapse;
									width: 100%;
								}
								
								.info {
									display: inline-block;
								}
								
								.bold {
									font-weight: bold;
								}
								
								.footer {
									margin-top: 30px;
									text-align: center;
									color: #99B1D8;
									font-size: 12px;
								}
								dl.variation dd {
								    font-size: 12px;
								    margin: 0;
									}
								</style>
								<div class="header" style="text-align: center; padding: 10px;">
								' . $mail_header . '
								</div>
							
								<div class="header">
									<h2>' . __( 'Your Exchange Request is Accepted.', 'woocommerce-refund-and-exchange' ) . '</h2>
								</div>
											
								<div class="content">
									<div class="reason">
										<p>' . $message . '</p>
									</div>
									<div class="Order">
										<h4>Order #' . $orderid . '</h4>
										<h4>' . __( 'Exchanged From', 'woocommerce-refund-and-exchange' ) . '</h4>
												<table>
												<tbody>
													<tr>
														<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
														<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
														<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
													</tr>';
									$order = wc_get_order( $orderid );

			if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
				$total = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
					foreach ( $requested_products as $requested_product ) {
						if ( $item_id == $requested_product['item_id'] ) {
							if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
								$requested_product_obj = wc_get_product( $requested_product['variation_id'] );
							} else {
								$requested_product_obj = wc_get_product( $requested_product['product_id'] );
							}
							$subtotal = $requested_product['price'] * $requested_product['qty'];
							$total += $subtotal;
							if ( WC()->version < '3.1.0' ) {
								$item_meta      = new WC_Order_Item_Meta( $item, $_product );
								$item_meta_html = $item_meta->display( true, true );
							} else {
								$item_meta      = new WC_Order_Item_Product( $item, $_product );
								$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
							}

							$html_content .= '<tr><td>' . $item['name'] . '<br>';
							$html_content .= '<small>' . $item_meta_html . '</small><td>' . $requested_product['qty'] . '</td><td>' . ced_rnx_format_price( $requested_product['price'] * $requested_product['qty'] ) . '</td></tr>';
						}
					}
				}
			}
									$html_content .= '
												<tr>
													<th colspan="2">' . __( 'Total', 'woocommerce-refund-and-exchange' ) . ':</th>
													<td>' . ced_rnx_format_price( $total ) . '</td>
												</tr>
											</tbody>
										</table>
										<h4>' . __( 'Exchanged To', 'woocommerce-refund-and-exchange' ) . '</h4>
										<table>
											<tbody>
												<tr>
													<th>' . __( 'Product', 'woocommerce-refund-and-exchange' ) . '</th>
													<th>' . __( 'Quantity', 'woocommerce-refund-and-exchange' ) . '</th>
													<th>' . __( 'Price', 'woocommerce-refund-and-exchange' ) . '</th>
												</tr>';

											$total_price = 0;
			if ( isset( $exchanged_to_products ) && ! empty( $exchanged_to_products ) ) {
				foreach ( $exchanged_to_products as $key => $exchanged_product ) {
					$variation_attributes = array();
					if ( isset( $exchanged_product['variation_id'] ) ) {
						if ( $exchanged_product['variation_id'] ) {
							$variation_product = new WC_Product_Variation( $exchanged_product['variation_id'] );
							$variation_attributes = $variation_product->get_variation_attributes();
							$variation_labels = array();
							foreach ( $variation_attributes as $label => $value ) {
								if ( is_null( $value ) || $value == '' ) {
									$variation_labels[] = $label;
								}
							}

							if ( isset( $exchanged_product['variations'] ) && ! empty( $exchanged_product['variations'] ) ) {
								$variation_attributes = $exchanged_product['variations'];
							}
						}
					}

					if ( isset( $exchanged_product['p_id'] ) ) {
						if ( $exchanged_product['p_id'] ) {
							$grouped_product = new WC_Product_Grouped( $exchanged_product['p_id'] );
							$grouped_product_title = $grouped_product->get_title();
						}
					}

					if ( isset( $exchanged_product['variation_id'] ) ) {

						$product = wc_get_product( $exchanged_product['variation_id'] );
					} else {
						$product = wc_get_product( $exchanged_product['id'] );
					}
					$pro_price = $exchanged_product['qty'] * $exchanged_product['price'];
					$total_price += $pro_price;
					$title = '';
					if ( isset( $exchanged_product['p_id'] ) ) {
						$title .= $grouped_product_title . ' -> ';
					}
					$title .= $product->get_title();

					if ( isset( $variation_attributes ) && ! empty( $variation_attributes ) ) {
						$title .= wc_get_formatted_variation( $variation_attributes );
					}

					$html_content .= '<tr>
																	<td>' . $title . '</td>
																	<td>' . $exchanged_product['qty'] . '</td>
																	<td>' . ced_rnx_format_price( $pro_price ) . '</td>
																</tr>';

				}
			}
											$html_content .= '<tr>
															<th colspan="2">' . __( 'Sub-Total', 'woocommerce-refund-and-exchange' ) . ':</th>
															<td>' . ced_rnx_format_price( $total_price ) . '</td>
														</tr>';
			if ( isset( $added_fee ) && ! empty( $added_fee ) ) {
				if ( is_array( $added_fee ) ) {
					foreach ( $added_fee as $fee ) {
						$total_price += $fee['val'];
						$html_content .= '<tr>
																		<th colspan="2">' . $fee['text'] . '</th>
																		<td>' . ced_rnx_format_price( $fee['val'] ) . '</td>
																	</tr>';
					}
				}
			}
											$html_content .= '<tr>
															<th colspan="2">' . __( 'Grand Total', 'woocommerce-refund-and-exchange' ) . '</th>
																<td>' . ced_rnx_format_price( $total_price ) . '</td>
															</tr>';

													$html_content .= '</tbody>
												</table>
											</div>';
											$mwb_cpn_dis = $order->get_discount_total();
											$mwb_cpn_tax = $order->get_discount_tax();

											$mwb_dis_tot = 0;
			if ( $total_price - ( $total + $mwb_dis_tot ) > 0 ) {
				$extra_amount = $total_price - ( $total + $mwb_dis_tot );
				$html_content .= '<h2>Extra Amount : ' . ced_rnx_format_price( $extra_amount ) . '</h2>';
			} else {
				if ( $mwb_dis_tot > $total_price ) {
					$total_price = 0;
				} else {
					$total_price = $total_price - $mwb_dis_tot;
				}
				$left_amount = $total - $total_price;
				update_post_meta( $orderid, 'ced_rnx_left_amount', $left_amount );

				$html_content .= '<h2><i>Left Amount After Exchange:</i> ' . ced_rnx_format_price( $left_amount ) . '</h2>';
			}

											$orders = wc_get_order( $order_id );

											$new_url = $orders->get_checkout_order_received_url();

											$html_content .= '<div><b>' . __( 'Your new order id is: #', 'woocommerce-refund-and-exchange' ) . $order_id . '</b></div>';
											$html_content .= '<a href=' . $new_url . '>' . __( 'Click here', 'woocommerce-refund-and-exchange' ) . '</a>';

											$html_content .= ' <div class="Customer-detail">
															<h4>' . __( 'Customer details', 'woocommerce-refund-and-exchange' ) . '</h4>
															<ul>
																<li><p class="info">
																		<span class="bold">' . __( 'Email', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $orderid, '_billing_email', true ) . '
																	</p></li>
																<li><p class="info">
																		<span class="bold">' . __( 'Tel', 'woocommerce-refund-and-exchange' ) . ': </span>' . get_post_meta( $orderid, '_billing_phone', true ) . '
																	</p></li>
															</ul>
														</div>
														<div class="details">
															<div class="Shipping-detail">
																<h4>' . __( 'Shipping Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $order->get_formatted_shipping_address() . '
															</div>
															<div class="Billing-detail">
																<h4>' . __( 'Billing Address', 'woocommerce-refund-and-exchange' ) . '</h4>
																' . $order->get_formatted_billing_address() . '
															</div>
															<div class="clear"></div>
														</div>
													</div>
													<div style="text-align: center; padding: 10px;" class="footer">
													' . $mail_footer . '
													</div>
												</body>
												</html>';

												return $html_content;
		}

		/**
		 * This function is metabox template for order msg history.
		 *
		 * @name ced_rnx_order_msg_history.
		 */
		public function ced_rnx_order_msg_history() {
			global $post, $thepostid, $theorder;
			include_once CED_REFUND_N_EXCHANGE_DIRPATH . 'admin/ced-rnx-admin-order-msg-history-meta.php';
		}

		public function ced_rnx_order_messages_save() {
			$check_ajax = check_ajax_referer( 'ced-rnx-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$msg = isset( $_POST['msg'] ) ? sanitize_text_field( wp_unslash( $_POST['msg'] ) ) : '';
				$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 0;
				$order = new WC_Order( $order_id );
				$to = $order->billing_email;
				$sender = 'Shop Manager';
				$flag = ced_rnx_send_order_msg_callback( $order_id, $msg, $sender, $to );
				echo $flag;
				wp_die();
			}
		}
	}
	new Ced_refund_and_exchange_order_meta();
}
?>
