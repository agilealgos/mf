<?php
/**
 * Plugin Name: custom code function
 * Plugin URI: https://pishago.com/
 * Description: Add Custom Code functions.
 * Version: 1.1
 * Author: Pishago
 * Author URI: https://pishago.com/
 * License: GPL2
*/

add_filter('wf_pklist_alter_product_table_head','wt_pklist_add_short_desc_column_in_invoice',10,3);
function wt_pklist_add_short_desc_column_in_invoice($columns_list_arr, $template_type, $order){
	if($template_type=='invoice' || $template_type=='packinglist'){
		unset($columns_list_arr['serial_no']);
		unset($columns_list_arr['image']);
		unset($columns_list_arr['sku']);
		unset($columns_list_arr['total_price']);
		
		$th_html = '<th class="wfte_product_table_head_hsn_code" col-type="hsn_code" style="text-align:center;">'.__('HSN CODE').'</th>';
		$th_html_sku = '<th class="wfte_product_table_head_sku" col-type="sku" style="text-align:center;">'.__('SKU').'</th>';
		$th_html_unit_price = '<th class="wfte_product_table_head_price" col-type="price" style="text-align:center;">'.__('UNIT PRICE').'</th>';
		$th_html_taxable_value = '<th class="wfte_product_table_head_price" col-type="price" style="text-align:center;">'.__('TAXABLE VALUE').'</th>';
		$th_html_total = '<th class="wfte_product_table_head_price" col-type="price" style="text-align:center;">'.__('TOTAL').'</th>';
		$out=array();
		foreach($columns_list_arr as $key=>$vl){
			$out[$key]=$vl;
			if($key=='product'){
				$out['sku'] = $th_html_sku;
				$out['hsn_code'] = $th_html;
			}else if($key=='quantity'){
				$out['unit_price'] = $th_html_unit_price;
			}else if($key=='price'){
				$out['price'] = $th_html_taxable_value;
			}else if($key=='tax'){
				$out['total'] = $th_html_total;
			}
			
		}
		if(!isset($out['hsn_code'])){
			$out['hsn_code']=$th_html;
		}
		
		$columns_list_arr=$out;
	}
	return $columns_list_arr;
}
// add_filter('wf_pklist_package_product_table_additional_column_val','wt_pklist_add_short_desc_column_val',10,6);
add_filter('wf_pklist_product_table_additional_column_val','wt_pklist_add_short_desc_column_val',10,60);
function wt_pklist_add_short_desc_column_val($column_data, $template_type, $columns_key, $_product, $item, $order){
	if(($template_type=='invoice' || $template_type=='packinglist') && $columns_key=='hsn_code'){
		$column_data = '<span style="text-align:center;display: block;">'.@get_post_meta($_product->parent_id,'hsn_code')[0].'</span>';
	}else if(($template_type=='invoice' || $template_type=='packinglist') && $columns_key=='unit_price'){
		$column_data = '<span style="text-align:center;display: block;">'.wc_price($item->get_total() + $item->get_total_tax()).'</span>';
	}else if(($template_type=='invoice' || $template_type=='packinglist') && $columns_key=='total'){
		$column_data = '<span style="text-align:center;display: block;">'.wc_price($item->get_total() + $item->get_total_tax()).'</span>';
	}
	return $column_data;
}





add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta_remove_category', 10 );
function woocommerce_template_single_meta_remove_category(){
    global $post, $product;
    $cat_count = ( !empty( $cats ) && ! is_wp_error( $cats ) ) ? 
	count($cats) : 0;
    $tag_count = ( !empty( $tags ) && ! is_wp_error( $tags ) ) ? count($tags) : 0;
?>
<div class="product_meta_new_custom_code_function" style="margin-bottom: 20px;">
  <?php do_action( 'woocommerce_product_meta_start' ); ?>
  <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
    <span class="sku_wrapper"><?php _e( 'STYLE :', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span>.</span>
  <?php endif; ?>
  <?php echo $product->get_categories( ', ', '<span class="posted_in">' . _n( '', '', $cat_count, 'woocommerce' ) . ' ', '.</span>' ); ?>
  <?php echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce' ) . ' ', '.</span>' ); ?>
  <?php do_action( 'woocommerce_product_meta_end' ); ?>
</div>
<style>
	.woocommerce div.product .product_meta {
		display: none;
	}
	.woocommerce div.product .product_meta_new_custom_code_function, .woocommerce div.product .product_meta_new_custom_code_function a {
		display: inline-grid;
		font-weight: var(--font-weight-bold);
		color: var(--link-color);
		background-color: transparent;
		font-size: var(--btn-simple-font-size,13px);
		line-height: 1.5;
		transition: color .2s var(--easeoutcubic);
		position: relative;
		text-decoration: none;
		padding: 0 0 5px;
	}
</style>
<?php
}





add_filter( 'woocommerce_order_number', 'add_prefix_woocommerce_order_number', 1, 2);
function add_prefix_woocommerce_order_number( $order_id, $order ) {
	$split_from = $order->get_meta('_vibe_split_orders_split_from');
	$split_index = $order->get_meta('_vibe_split_orders_split_index');
	if ($split_from > 0) {
		$alphachar = range('A', 'Z');
		return $split_from.$alphachar[$split_index-1];
	}
    return $order_id;
}








add_action('parse_request', 'my_product_api_update_data_handler');

function my_product_api_update_data_handler() {
   	if($_SERVER["REQUEST_URI"] == '/product-api-stock-upload-data') {
		$json_response = [];
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$headers = apache_request_headers();
			if (isset($headers['Username']) && $headers['Username'] == 'qbItemstockqty' && isset($headers['Key']) && $headers['Key'] == 'XF0rEG66PlwasExqLlmtyh1pheKeF2B6mOEMpvT9iA8cqGQ45S') {
				global $wpdb;
				$json = file_get_contents('php://input');

				$data = json_decode($json,true);
				
				$api_json_log_dir = WP_PLUGIN_DIR . '/custom-code-function/api-json-log/';
				file_put_contents($api_json_log_dir."api-call-total-".count($data['Items'])."(".time().").json", $json);

				$numberofupdate = 0;
				$numberofinsert = 0;
				foreach ($data['Items'] as $key => $value) {
					$model_number = trim($value['model_number']);
					$sku_number = trim($value['sku_number']);
					$mrp = trim($value['mrp']);
					$selling_price = trim($value['selling_price']);
					$qty = trim($value['qty']);
					
					$existsdata = $wpdb->get_row("SELECT * FROM wp_api_stock_upload_data WHERE sku_number = '".$sku_number."'");
					if (isset($existsdata->id) && $existsdata->id > 0) {
						if ($sku_number != '' && (int)$mrp > 0) {

							$wpdb->query("UPDATE wp_api_stock_upload_data SET model_number = '".$model_number."',mrp = '".$mrp."', selling_price = '".$selling_price."', qty = '".$qty."', status = 0, date_modified = NOW() where id = '".$existsdata->id."'");

							$numberofupdate++;
						}
					}else{
						if ($sku_number != '' && (int)$mrp > 0) {
							$wpdb->query("INSERT INTO wp_api_stock_upload_data VALUES ( NULL, '".$model_number."','".$sku_number."', '".$mrp."', '".$selling_price."', '".$qty."', '0', NOW())");

							$numberofinsert++;
						}
						
					}
				}
				$json_response = [
					'success' => 1,
					'error' => '',
					'data' => ['Total Records : '.($numberofupdate+$numberofinsert).' , Records Insert : '.$numberofinsert.' , Records Update : '.$numberofupdate],
				];
			}else{
				$json_response = [
					'success' => 0,
					'error' => 'Header data Username or Key does not matched!',
					'data' => [],
				];
			}
	   	}else{
			$json_response = [
				'success' => 0,
				'data' => [],
			];
		}
		echo json_encode($json_response);
		exit();
   	}else if ($_SERVER["REQUEST_URI"] == '/product-stock-update-data') {
		
		global $wpdb;
		$updatedata = $wpdb->get_results("SELECT * FROM `wp_api_stock_upload_data` where status = 0");
		
		$totalproducts = 0;
		$totalnotfoundproducts = 0;

		foreach ($updatedata as $product) {

			$args = array(
				'post_status' => array('publish', 'private'),
				'post_type'  => 'product_variation',
				'meta_query' => array(
					array(
						'key'   => '_sku',
						'value' => $product->sku_number,
					)
				)
			);
			$posts = get_posts($args);
			
			foreach ($posts as $vproduct) {

				update_post_meta( $vproduct->ID, '_stock', $product->qty );
				update_post_meta( $vproduct->ID, '_regular_price', $product->mrp );
				update_post_meta( $vproduct->ID, '_price', $product->mrp );
				if ((int)$product->selling_price > 0) {
					update_post_meta( $vproduct->ID, '_price', $product->selling_price );
					update_post_meta( $vproduct->ID, '_sale_price', $product->selling_price );
				}else{
					update_post_meta( $vproduct->ID, '_sale_price', '' );
				}

				/* $mainproductid = $vproduct->post_parent;

				$variation = wc_get_product($vproduct->ID);
				
				$psize = $variation->get_attribute('size');
				if ($psize != '') {
					$termdata = get_term_by('name', esc_attr($psize), 'pa_size');
					if ($termdata) {
						if($product->qty > 0){
							wp_set_object_terms( $mainproductid, $termdata->name, 'pa_size', true);
						}else{
							// wp_remove_object_terms( $mainproductid, $termdata->term_id, 'pa_size' );
							wp_set_object_terms( $mainproductid, $termdata->name, 'pa_size', true);
						}
					}
				}
				
				$variation->set_stock_quantity($product->qty);
				$variation->set_regular_price($product->mrp);
				if ((int)$product->selling_price > 0) {
					$variation->set_sale_price($product->selling_price);
				}else{
					$variation->set_sale_price('');
				}
				$variation->save(); */

				
				/* $update_post = array(
					'ID' => $vproduct->ID,
					'post_status' => 'publish'
				);
				wp_update_post($update_post); */
				
				$wpdb->query("UPDATE wp_api_stock_upload_data SET status = 1 where sku_number = '".$product->sku_number."'");

				$totalproducts++;
			}
			if (count($posts) == 0) {
				$wpdb->query("UPDATE wp_api_stock_upload_data SET status = 2 where sku_number = '".$product->sku_number."'");
				$totalnotfoundproducts++;
			}
		}

		$json_response = [
			'success' => 1,
			'error' => '',
			'data' => ['Records Update : '.$totalproducts . ', Records Not Found : '.$totalnotfoundproducts],
		];
		echo json_encode($json_response);
		exit();
	}else if ($_SERVER["REQUEST_URI"] == '/product-stock-store-get-by-proceger-data') {


	
		/* global $wpdb;


		$args = array(
			'post_status' => array('publish', 'private'),
			'post_type'  => 'product_variation',
			'meta_query' => array(
				array(
					'key'   => '_sku',
					'value' => 'test-product-by-developer',
				)
			)
		);
		$posts = get_posts($args);
		
		foreach ($posts as $vproduct) {
			update_post_meta( $vproduct->ID, '_stock', 5051 );
			update_post_meta( $vproduct->ID, '_regular_price', 10000 );
			update_post_meta( $vproduct->ID, '_price', 10000 );
			if (0 > 0) {
				update_post_meta( $vproduct->ID, '_price', 5000 );
				update_post_meta( $vproduct->ID, '_sale_price', 5000 );
			}else{
				update_post_meta( $vproduct->ID, '_sale_price', '' );
			}


			// $vproductchild = wc_get_product($vproduct->ID);
			// $mainproduct = wc_get_product($vproductchild->parent_id);
			// $product_attributes = $mainproduct->get_attributes();
			// $product_attributes = $mainproduct->get_attribute('size');
			
			// $mainproductid = $vproduct->post_parent;
			
			// $variation = wc_get_product($vproduct->ID);
			
			// $variation->update_meta_data( '_rey_related_ids', 'dsdfdff' );
			
			// $product_data = $vproduct->get_data();
			/* $variation->set_stock_quantity(50);
			$variation->set_regular_price(10000);
			if (0 > 0) {
				$variation->set_sale_price(5000);
			}else{
				$variation->set_sale_price('');
			}
			
			$relatedproduct = get_post_meta( $vproduct->post_parent, '_rey_related_ids' );

			$variation->save();

			if ( ! empty( $relatedproduct ) ) {
				// delete_post_meta( $vproduct->post_parent, '_rey_related_ids');
				// $escaped_json = '{"key":"value with \\"escaped quotes\\""}';
				// update_post_meta( $vproduct->post_parent, '_rey_related_ids', $escaped_json );
				
				/* $mainproduct = wc_get_product($vproduct->post_parent);
				$mainproduct->update_meta_data( '_rey_related_ids', $escaped_json );
				$mainproduct->save();
				echo '<pre>';
				print_r('ms');
				exit;

			} */
			
			
			
			
			/* $update_post = array(
				'ID' => $vproduct->ID,
				'post_status' => 'publish'
			);
			wp_update_post($update_post); 
		}
			*/
			
		echo '<pre>';
		print_r('s');
		exit;
		/* 
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		$servername = "45.248.122.143";
		$username = "web_access";
		$password = "Web_Success365";
		$database = "QbAppdb2";
		$port = "6161";

			$conn = new PDO("sqlsrv:server=$servername,$port;Database=$database;ConnectionPooling=0", $username, $password,
				array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				)
			);
		
		
			$sql = "SELECT * FROM Table";
		
			foreach ($conn->query($sql) as $row) {
				print_r($row);
			} 
		
		

		echo '<pre>';
		// print_r('product-stock-store-get-by-proceger-data');
		exit; */
	}else if ($_SERVER["REQUEST_URI"] == '/product-stock-wise-product-status-data') {
		
		global $wpdb;
		$updatedata = $wpdb->get_results("SELECT model_number,sum(qty) as totalqty FROM `wp_api_stock_upload_data` group by model_number order by totalqty,model_number");

		$totalproductsdraft = 0;
		$totalproductpublish = 0;
		
		foreach ($updatedata as $product) {
			
			if ($product->totalqty > 0) {
				$findstatus = ['draft'];
			}else{
				$findstatus = ['publish'];
			}
			
			$products = get_posts(
				array(
					'post_status' => $findstatus,
					'post_type'  => array( 'product', 'product_variation' ),
					'post_parent'  => 0,
					'meta_query' => array(
						array(
							'key'     => '_sku',
							'value'   => $product->model_number,
						)
					),
				)
			);

			
			foreach ($products as $vproduct) {
				
				if ($product->totalqty > 0) {
					$changestatus = 'publish';
					$totalproductpublish++;
				}else{
					$changestatus = 'draft';
					$totalproductsdraft++;
				}

				$my_post = array(
					'ID'            => $vproduct->ID,
					'post_status'   => $changestatus,
				);
				wp_update_post( $my_post );

			}
		}

		$json_response = [
			'success' => 1,
			'error' => '',
			'data' => ['Product draft : '.$totalproductsdraft . ', Product publish : '.$totalproductpublish],
		];
		echo json_encode($json_response);
		exit();
	}else if ($_SERVER["REQUEST_URI"] == '/product-id-to-sku-update') {
		
		/* global $wpdb;
		$api_json_dir = WP_PLUGIN_DIR . '/custom-code-function/';
		$json = file_get_contents($api_json_dir.'sku-mf-update.json');
		
		$json_data = json_decode($json,true);
		$count = 1;
		foreach ($json_data as $product) {

			update_post_meta($product['ID'],'_sku',$product['SKU']);
			$count++;
		}
			
		$json_response = [
			'success' => $count,
			'error' => '',
		];
		echo json_encode($json_response);
		exit(); */
	}else if ($_SERVER["REQUEST_URI"] == '/product-json-to-price-status-update') {
		/* ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);
		error_reporting(-1);

		global $wpdb;
		$api_json_dir = WP_PLUGIN_DIR . '/custom-code-function/';
		$json = file_get_contents($api_json_dir.'price-sku-status-update.json');
		
		$json_data = json_decode($json,true);
		$count = 0;
		foreach ($json_data as $pro) {
			
			$args = array(
				'post_status' => array('publish', 'private'),
				'post_type'  => 'product_variation',
				'meta_query' => array(
					array(
						'key'   => '_sku',
						'value' => $pro['stock_no'],
					)
				)
			);
			$posts = get_posts($args);
			$found = 1;
			foreach ($posts as $vproduct) {
				$variation = wc_get_product($vproduct->ID);
				$variation->set_regular_price($pro['mrp']);
				$variation->set_sale_price($pro['selling_price']);
				$variation->save();

				if ($vproduct->post_parent > 0) {
					$update_post = array(
						'ID' => $vproduct->post_parent,
						'post_status' => 'publish'
					);
					
					wp_update_post($update_post);
				}
				

				$count++;
				$found = 0;
			}
			if ($found) {
				echo "<br />";
				echo $pro['stock_no'];
			}
		}
		
		$json_response = [
			'success' => 1,
			'product_update' => $count,
			'error' => '',
		];
		echo json_encode($json_response);
		exit(); */
	}else if ($_SERVER["REQUEST_URI"] == '/product-json-sku-to-update-price') {
		/* global $wpdb;
		$api_json_dir = WP_PLUGIN_DIR . '/custom-code-function/';
		$json = file_get_contents($api_json_dir.'all-sku-mrp.json');
		$json_data = json_decode($json,true);
		
		$count = 1;
		
		$json_data = array_slice($json_data, 18000);
		
		// array_slice
		foreach ($json_data as $key => $product) {
			if ($key > 2000) {
				break;
			}
			
			$args = array(
				'post_status' => array('publish', 'private'),
				'post_type'  => 'product_variation',
				'meta_query' => array(
					array(
						'key'   => '_sku',
						'value' => $product['SKU'],
					)
				)
			);
			$posts = get_posts($args);
			
			foreach ($posts as $vproduct) {
				$variation = wc_get_product( $vproduct->ID );
				$variation->set_regular_price($product['MRP']);
				$variation->save();
				$count++;
			}
			
		}
			
		$json_response = [
			'success' => $count,
			'error' => '',
		];
		echo json_encode($json_response);
		exit(); */
	}else if ($_SERVER["REQUEST_URI"] == '/product-json-model-to-update-price-sku') {
		/* global $wpdb;
		$api_json_dir = WP_PLUGIN_DIR . '/custom-code-function/';
		$json = file_get_contents($api_json_dir.'product-json-model-to-update-price-sku.json');
		$json_data = json_decode($json,true);
		
		

		$count = 1;
		$noupdate = [];
		// array_slice
		foreach ($json_data as $key => $product) {
			$update = true;
			$products = get_posts(
				array(
					'post_status' => array('publish', 'private', 'draft'),
					'post_type'  => array( 'product', 'product_variation' ),
					'post_parent'  => 0,
					'meta_query' => array(
						array(
							'key'     => '_sku',
							'value'   => $product['Model'],
						)
					),
				)
			);

			foreach ($products as $key => $productvalue) {

				
				$handle = new WC_Product_Variable($productvalue->ID);
				$variationData = $handle->get_children();
				
				foreach ($variationData as $value) {
					$single_variation = new WC_Product_Variation($value);
					$single_variation->set_regular_price($product['mrp']); 
                    $single_variation->save();
					update_post_meta($value,'_sku',$product['SKU']);
					$count++;
					$update = false;
				}
				
			}
			
			if ($update) {
				
				$noupdate[] = $product;
			}
		}
			
		$json_response = [
			'success' => $count,
			'noupdate' => $noupdate,
			'error' => '',
		];
		echo json_encode($json_response);
		exit(); */
	}else if ($_SERVER["REQUEST_URI"] == '/product-special-to-category-update') {
		
		global $wpdb;
		
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'paged' => 1,
			'post_status'    => 'any'
		);
		
		$loop = new WP_Query( $args );
		
		foreach ($loop->posts as $product) {
			$handle = new WC_Product_Variable($product->ID);
			$variationData = $handle->get_children();
			foreach ($variationData as $value) {
				$single_variation = new WC_Product_Variation($value);
				$regprice = $single_variation->regular_price;
				$sale_price = (int)$single_variation->sale_price;
				$categorys = $handle->category_ids;

				
				$handle->set_category_ids( array( 3180,3184,3182,3183 ) );
				$handle->save();

				echo '<pre>';
				print_r($handle);
				exit;
			
			}
		}
	
		wp_reset_query();
			
		$json_response = [
			'success' => 1,
			'product_update' => count($loop->posts),
			'error' => '',
		];
		echo json_encode($json_response);
		exit();
	}else if ($_SERVER["REQUEST_URI"] == '/product-api-table-to-remove-or-add-attributes') {
		
		global $wpdb;
		
		$vproductchild = wc_get_product(24561);
		$product_attributes = $vproductchild->get_attributes();

		/* $tempoption = $product_attributes['pa_size']->get_options();
		unset($tempoption[0]);
		$tempoption = array_values($tempoption);
		$product_attributes['pa_size']->set_options($tempoption);
		
		$temppa = $product_attributes['pa_size'];
		// unset($product_attributes['pa_size']);
		$product_attributes['size'] = $temppa;
		
		$vproductchild->set_attributes($product_attributes);
		$vproductchild->save(); */

		wp_remove_object_terms( 24561, 909, 'pa_size' );

		echo '<pre>';
		print_r($product_attributes);
		exit;
		
		
		/* $args = array(
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'paged' => 1,
			'post_status'    => 'any'
		);
		
		$loop = new WP_Query( $args );
		
		foreach ($loop->posts as $product) {
			
			$handle = new WC_Product_Variable($product->ID);
			$variationData = $handle->get_children();
			foreach ($variationData as $value) {
				$single_variation = new WC_Product_Variation($value);
				$regprice = $single_variation->regular_price;
				$sale_price = (int)$single_variation->sale_price;
				$categorys = $handle->category_ids;

				
				$handle->set_category_ids( array( 3180,3184,3182,3183 ) );
				$handle->save();

				echo '<pre>';
				print_r($handle);
				exit;
			
			}
		}
	
		wp_reset_query(); */
			
		$json_response = [
			'success' => 1,
			'product_update' => count($loop->posts),
			'error' => '',
		];
		echo json_encode($json_response);
		exit();
	}else if ($_SERVER["REQUEST_URI"] == '/product-api-category-mrp-special') {

		global $wpdb;
		$api_json_dir = WP_PLUGIN_DIR . '/custom-code-function/';
		$json = file_get_contents($api_json_dir.'category-mrp-special.json');
		$json_data = json_decode($json,true);
		
		$count = 1;
		foreach ($json_data as $product) {
			$product['style_desc'] = trim($product['style_desc']);
			$product['name'] = trim($product['name']);
			$product['mrp'] = trim($product['mrp']);
			$product['after_disc'] = trim($product['after_disc']);
			$product['category'] = trim($product['category']);
			$product['product_mapping'] = trim($product['product_mapping']);
			
			
			$get_products = get_posts(
				array(
					'post_status' => array('publish', 'private', 'draft'),
					'post_type'  => array( 'product', 'product_variation' ),
					'post_parent'  => 0,
					'meta_query' => array(
						array(
							'key'     => '_sku',
							'value'   => $product['style_desc'],
						)
					),
				)
			);
			foreach ($get_products as $key => $get_product) {
				$categorys = explode(',',$product['product_mapping']);
				$cate_ids = [];
				foreach ($categorys as $cat) {
					$cat = trim($cat);
					$category = get_term_by( 'name', $cat, 'product_cat' );
					if ($category) {
						$cate_ids[] = $category->term_id;						
					}else{
						echo '<pre>';
						print_r($cat);
						exit;
					}
				}
				
				/* $handle = new WC_Product_Variable($get_product->ID);
				$handle->set_category_ids( $cate_ids );
				$handle->save();


				$variationData = $handle->get_children();
				
				foreach ($variationData as $value) {

					update_post_meta( $value, '_regular_price', $product['mrp'] );
					update_post_meta( $value, '_price', $product['mrp'] );

					if ((int)$product['after_disc'] > 0) {
						update_post_meta( $value, '_price', $product['after_disc'] );
						update_post_meta( $value, '_sale_price', $product['after_disc'] );
					}else{
						update_post_meta( $value, '_sale_price', '' );
					}
					
				} */
			
				/* echo '<pre>';
				print_r($value);
				exit;	 */
			}
			
			$count++;
		}
			
		$json_response = [
			'success' => $count,
			'error' => '',
		];
		echo json_encode($json_response);
		exit(); 
	}
}



add_action('woocommerce_order_status_changed', 'woocommerce_payment_complete_order_status',10,3);
function woocommerce_payment_complete_order_status($order_id)
{
    if ( ! $order_id ) {
        return;
    }
    $order = wc_get_order( $order_id );
    if ($order->data['status'] == 'processing') {
        $payment_method=$order->get_payment_method();
        if ($payment_method == "cod"){
            $order->update_status( 'wc-wc-cod-order' );
        }else{
            $order->update_status( 'wc-razorpay' );
		}
    }
}




add_filter( 'woocommerce_package_rates', 'bbloomer_unset_shipping_when_free_is_available_all_zones', 9999, 2 );
   
function bbloomer_unset_shipping_when_free_is_available_all_zones( $rates, $package ) {
	global  $woocommerce;

   	$all_free_rates = array();
   	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$all_free_rates[ $rate_id ] = $rate;
			$all_free_rates[ $rate_id ]->label = get_woocommerce_currency_symbol().'0';
			break;
		}
   	}
   	if ( empty( $all_free_rates )) {
      return $rates;
   	} else {
      return $all_free_rates;
   	} 
}

/* add_filter( 'woocommerce_countries_inc_tax_or_vat', function () {
	return __( 'GST', 'woocommerce' );
}); */

add_filter( 'woocommerce_countries_ex_tax_or_vat', function () {
	return __( '(ex. GST)', 'woocommerce' );
}); 

 



add_action('wp_footer', 'size_chart_popup');
function size_chart_popup() {
    $custom_items = '
	<style>
	/* The Modal (background) */
	.modal {
	  display: none; /* Hidden by default */
	  position: fixed; /* Stay in place */
	  z-index: 99999; /* Sit on top */
	  padding-top: 100px; /* Location of the box */
	  left: 0;
	  top: 0;
	  width: 100%; /* Full width */
	  height: 100%; /* Full height */
	  overflow: auto; /* Enable scroll if needed */
	  background-color: rgb(0,0,0); /* Fallback color */
	  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}
	
	/* Modal Content */
	.modal-content {
	  background-color: #fefefe;
	  margin: auto;
	  padding: 20px;
	  border: 1px solid #888;
	  width: 30%;
	}
	
	/* The Close Button */
	.close {
	  color: #aaaaaa;
	  float: right;
	  font-size: 28px;
	  font-weight: bold;
	}
	
	.close:hover,
	.close:focus {
	  color: #000;
	  text-decoration: none;
	  cursor: pointer;
	}
	</style>

	<!-- Trigger/Open The Modal -->
	
	
	<!-- The Modal -->
	<div id="myModal" class="modal">
	
	  <!-- Modal content -->
	  <div class="modal-content">
		<span class="close">&times;</span>
		<img class="aligncenter wp-image-37746 size-full" src="/wp-content/uploads/2022/12/Size-Chart-scaled.jpg" alt="" />
	  </div>
	
	</div>
	
	<script>
	// Get the modal
	var modal = document.getElementById("myModal");
	
	// Get the button that opens the modal
	var btn = document.getElementById("myBtn");
	
	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];
	
	// When the user clicks the button, open the modal 
	btn.onclick = function() {
	  modal.style.display = "block";
	}
	
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
	  modal.style.display = "none";
	}
	
	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	  if (event.target == modal) {
		modal.style.display = "none";
	  }
	}
	</script>
	';
	$custom_items .= '';
    echo $custom_items;
}





// order edit in admin products column add storetable
add_action( 'woocommerce_admin_order_item_headers', 'pishago_admin_order_item_headers' );
function pishago_admin_order_item_headers( $order ) {
    $column_name = __( 'Store Qty Data', 'woocommerce' );
    echo '<th class="line_packing_weight">' . $column_name . '</th>';
}
 
add_action( 'woocommerce_admin_order_item_values', 'pishago_admin_order_item_values', 9999, 3 );
function pishago_admin_order_item_values( $product, $item, $item_id ) {
	global $wpdb;
    if ( $product ) {

		$dbstores = $wpdb->get_results("SELECT * FROM wp_stores where status = '1'");
		$stores = [];

		foreach ($dbstores as $key => $store) {
			$stores[$store->email] = $store->name;
		}

		$emailstring = implode("','",array_keys($stores));
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://45.248.122.143:8080/api/Quickbill/QuickbillApi',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{
			"StrQuery": "Select I.SyncId,A.ItemGuid,B.STOCKNO,B.ItemDescription,B.UomGuid,C.NAME CLASS1,D.NAME CLASS2,E.NAME CLASS3,F.NAME CLASS4,G.SIZENAME CLASS5,H.FieldValue,Sum(A.ClosingBalQty) As ClQty From VQbStockBalance As A With (noexpand,nolock) LEFT OUTER JOIN QBLEDGER AS I ON A.SOURCEGUID = I.QBGUID,  QbItemMaster As B LEFT OUTER JOIN QBITEMCLASS C ON B.CLASS1GUID = C.QBGUID  LEFT OUTER JOIN QBITEMCLASS D ON B.CLASS2GUID = D.QBGUID LEFT OUTER JOIN QBITEMCLASS E ON B.CLASS3GUID = E.QBGUID  LEFT OUTER JOIN QBITEMCLASS F ON B.CLASS4GUID = F.QBGUID LEFT OUTER JOIN QbItemSize G ON B.CLASS5GUID = G.QBGUID, QbPriceMaster As H  Where A.ItemGuid = B.QbGuid And B.IsServiceItem <> 1 And B.MaintainInventory = 1 And A.PriceGuid = H.QbGuid And A.ItemGuid = H.ItemGuid And H.FieldName = \'ItemPrice1\' And A.ClosingBalQty <> 0 And A.LocationCode In (Select QbGuid From QbLocation Where IsSaleable = 1 Union Select \'PrimaryLocation\')  And B.StockNo Like @Parameter1 And I.SyncId In (\''.$emailstring.'\') Group By I.SyncId,A.ItemGuid,B.STOCKNO,B.ItemDescription,B.UomGuid,C.NAME,D.NAME,E.NAME,F.NAME,G.SIZENAME,H.FieldValue",
			"StrParam1": "'.$product->sku.'",
			"StrParam2": "",
			"StrParam3": "",
			"StrParam4": "",
			"StrParam5": "",
			"StrParam6": "",
			"StrParam7": ""
			}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		$response = json_decode($response,true);
		echo '<td class="line_packing_weight">';
			echo '<select style="width: 100%;">';
				foreach ($response as $key => $value) {
					echo '<option>'.$value['ClQty'].' -- '.$stores[$value['SyncId']].'</option>';
				}
			echo '</select>';
		echo '</td>';


		/* $existsdata = $wpdb->get_row("SELECT * FROM wp_api_stock_upload_data where sku_number = '".$product->sku."'");
        echo '<td class="line_packing_weight">';
			if ($existsdata) {
				echo 'Total Available: <b>'.$existsdata->qty.'</b>';
			}
		echo '</td>'; */
    }else{
        echo '<td class="line_packing_weight"> </td>';
	}
}
// order edit in admin products column add storetable end 



// checkout phone number validation
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');
  
function my_custom_checkout_field_process() {
    global $woocommerce;
  	if ( ! (preg_match('/^[0-9]{10}$/D', $_POST['billing_phone'] ))){
        wc_add_notice( "<b>Billing Phone</b> Please enter valid 10 digits phone number."  ,'error' );
    }
}
// checkout phone number validation end