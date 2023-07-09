<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_import_main">
	<p><?php echo $this->step_description;?></p>
	<div class="wt_iew_warn wt_iew_post_type_wrn" style="display:none;">
		<?php _e('Please select a post type');?>
	</div>
	<table class="form-table wt-iew-form-table">
		<tr>
			<th><label><?php _e( 'Select a post type to import', 'users-customers-import-export-for-wp-woocommerce' ); ?></label></th>
			<td>
				<select name="wt_iew_import_post_type">
					<option value="">-- <?php _e( 'Select post type', 'users-customers-import-export-for-wp-woocommerce' ); ?> --</option>
					<?php
					$item_type = isset($item_type) ? $item_type : 'user';
					foreach($post_types as $key=>$value)
					{
						?>
						<option value="<?php echo $key;?>" <?php echo ($item_type==$key ? 'selected' : '');?>><?php echo $value;?></option>
						<?php
					}
					?>
				</select>
			</td>
			<td></td>
		</tr>
	</table>
		<br/>
	<?php 
	$wt_iew_post_types = array(
		'product' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Products.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_review' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product reviews.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_categories' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product categories.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'product_tags' => array(
			'message' => __('The <b>Product Import Export for WooCommerce Add-On</b> is required to export WooCommerce Product tags.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=product-import-export-for-woo')
		),
		'order' => array(
			'message' => __('The <b>Order Export & Order Import for WooCommerce Add-On</b> is required to export WooCommerce Orders.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=order-import-export-for-woocommerce')
		),
		'coupon' => array(
			'message' => __('The <b>Order Export & Order Import for WooCommerce Add-On</b> is required to export WooCommerce Coupons.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => admin_url('plugin-install.php?tab=plugin-information&plugin=order-import-export-for-woocommerce')
		),
		'subscription' => array(
			'message' => __('The <b>Order, Coupon, Subscription Export Import for WooCommerce</b> premium is required to import WooCommerce Subscriptions.', 'users-customers-import-export-for-wp-woocommerce' ),
			'link' => esc_url( 'https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp_post_type&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=' . WT_U_IEW_VERSION )
		)
	);
	foreach ($wt_iew_post_types as $wt_iew_post_type => $wt_iew_post_type_detail) { ?>
			
	<div class="wt_iew_free_addon wt_iew_free_addon_warn <?php echo 'wt_iew_type_'.$wt_iew_post_type; ?>" style="display:none">
		<p><?php echo $wt_iew_post_type_detail['message']; ?></p>
		<?php 
		$install_now = esc_html( 'Install now for free', 'users-customers-import-export-for-wp-woocommerce' ); 
		$is_pro = false;
		if( 'subscription' === $wt_iew_post_type ){
			$install_now = esc_html( 'Get the plugin', 'users-customers-import-export-for-wp-woocommerce' ); 
			$is_pro = true;
		}
		?>		
		<a target="_blank" href="<?php echo $wt_iew_post_type_detail['link']; ?>"><?php esc_attr_e( $install_now ); ?></a>
	</div>
	
	<?php
	}
	?>
</div>