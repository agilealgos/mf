<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['order_id'] ) ) {
	$order_id = $_POST['order_id'];
} elseif ( isset( $_GET['order_id'] ) ) {
	$order_id = $_GET['order_id'];
}
$flag = false;
$get_nonce = isset( $_REQUEST['mwb-order-msg-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mwb-order-msg-nonce'] ) ) : '';
if ( isset( $_POST['mwb_order_msg_submit'] ) && ! empty( $_POST['mwb_order_msg_submit'] ) ) {
	if ( wp_verify_nonce( $get_nonce, 'mwb-order-msg-nonce' ) ) {
		$msg = isset( $_POST['mwb_order_new_msg'] ) ? filter_input( INPUT_POST, 'mwb_order_new_msg' ) : '';
		$to     = get_option( 'admin_email' );
		$sender = 'Customer';
		$flag = ced_rnx_send_order_msg_callback( $order_id, $msg, $sender, $to, $order_id );
	}
}

get_header( 'shop' );

/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
*/
do_action( 'woocommerce_before_main_content' );
$upload_attach = get_option( 'mwb_wrma_order_message_attachment', 'no' );
if ( isset( $order_id ) && ! empty( $order_id ) ) {
	?>

<div class="mwb_order_add_msg_form">
	<div class="mwb_notice_main_container">
		<?php if ( $flag ) { ?>	
		<div class="mwb_order_msg_notice_wrapper">
			<p class="mwb_order_msg_sent_notice"><strong><?php esc_html_e( 'Message has been sent.', 'woocommerce-refund-and-exchange' ); ?></strong></p>
			<a href="#" class="mwb_order_send_msg_dismiss">X</a>
		</div>
		<?php } ?>
	</div>
	<div class="mwb_order_msg_container">
		<form id="mwb_order_new_msg_form" method="post" enctype="multipart/form-data">
			<div class="mwb_order_msg_title"><h4 class="mwb-order-heading"><?php esc_html_e( 'Add a message', 'woocommerce-refund-and-exchange' ); ?></h4></div>
			<textarea id="mwb_order_new_msg" name="mwb_order_new_msg" placeholder="<?php esc_html_e( 'Write a message you want to sent to the Shop Manager.', 'woocommerce-refund-and-exchange' ); ?>" rows="5" required ></textarea>
			<?php if ( isset( $upload_attach ) && 'yes' == $upload_attach ) { ?>
				<div>
					<label for="mwb_order_msg_attachment"> <?php esc_html_e( 'Attach file', 'woocommerce-refund-and-exchange' ); ?></label>
				</div>
				<?php } ?>
				<div class="mwb-order-msg-attachment-wrapper">
					<?php if ( isset( $upload_attach ) && 'yes' == $upload_attach ) { ?>
						<input type="file" id="mwb_order_msg_attachment" name="mwb_order_msg_attachment[]" multiple >
					<?php } ?>
					<div class="mwb-order-msg-btn">
						<input type="submit" class="button button-primary" id="mwb_order_msg_submit" name="mwb_order_msg_submit" value="<?php esc_html_e( 'Send', 'woocommerce-refund-and-exchange' ); ?>">
						<input 	type="hidden" name="mwb-order-msg-nonce" value="<?php echo esc_attr( wp_create_nonce( 'mwb-order-msg-nonce' ) ); ?>"> 
					</div>
				</div>
		</form>
	</div>
	<div class="mwb_order_msg_reload_notice_wrapper">
		<p class="mwb_order_msg_sent_notice"><strong><?php esc_html_e( 'Messages Refreshed Successfully.', 'woocommerce-refund-and-exchange' ); ?></strong></p>
	</div>
	<div class="mwb_order_msg_history_container">
		<div class="mwb_order_msg_history_title"><h4 class="mwb-order-heading"><?php esc_html_e( 'Message History', 'woocommerce-refund-and-exchange' ); ?>
			<a href="" class="mwb_reload_messages"><img src="<?php echo esc_url( CED_REFUND_N_EXCHANGE_URL ) . 'assets/images/reload-icon.png'; ?>" class="reload-icon"></a>
		</h4>
		</div>
		<div class="mwb_order_msg_sub_container">
			<?php
			$mwb_order_messages = get_option( $order_id . '-mwb_cutomer_order_msg', array() );
			if ( isset( $mwb_order_messages ) && is_array( $mwb_order_messages ) && ! empty( $mwb_order_messages ) ) {
				foreach ( array_reverse( $mwb_order_messages ) as $o_key => $o_val ) {
					foreach ( $o_val as $om_key => $om_val ) {
						?>
						<div class="mwb_order_msg_main_container mwb_order_messages">
							<div>
								<div class="mwb_order_msg_sender"><?php echo esc_html__( $om_val['sender'], 'woocommerce-refund-and-exchange' ); ?></div>
								<span class="mwb_order_msg_date"><?php echo get_date_from_gmt( date( 'Y-m-d h:i a', $om_key ), 'Y-m-d h:i a' ); ?></span>
							</div>
							<div class="mwb_order_msg_detail_container">
								<span><?php echo esc_html__( $om_val['msg'], 'woocommerce-refund-and-exchange' ); ?></span>
							</div>
							<?php if ( isset( $om_val['files'] ) && ! empty( $om_val['files'] ) ) { ?>
								<hr>
								<div class="mwb_order_msg_attach_container">
									<div class="mwb_order_msg_attachments_title"><?php esc_html_e( 'Message attachments:', 'woocommerce-refund-and-exchange' ); ?></div>
									<?php
									foreach ( $om_val['files'] as $fkey => $fval ) {
										if ( ! empty( $fval['name'] ) ) {
											$is_image = $fval['img'];
											?>
											<div class="mwb_order_msg_single_attachment">
												<a target="_blank" href="<?php echo esc_html( get_home_url() ) . '/wp-content/attachment/' . $order_id . '-' . $fval['name']; ?>">
													<img class="mwb_order_msg_attachment_thumbnail" src="<?php echo $is_image ? esc_html( get_home_url() ) . '/wp-content/attachment/' . $order_id . '-' . $fval['name'] : esc_url( CED_REFUND_N_EXCHANGE_URL ) . 'assets/images/attachment.png'; ?>">
													<span class="mwb_order_msg_attachment_file_name"><?php echo esc_html( $fval['name'] ); ?></span>
												</a>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
	</div>
</div>
	<?php
}
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
?>
