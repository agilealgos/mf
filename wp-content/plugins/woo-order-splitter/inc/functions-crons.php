<?php
	if(!function_exists('wc_os_cron_jobs')){
		function wc_os_cron_jobs(){
			if(isset($_GET['wc_os_cron_jobs'])){
				$order_id = isset($_GET['order_id'])?sanitize_wc_os_data($_GET['order_id']):0;
				wc_os_pre('CRON - '.$order_id, 'cron');
				wc_os_crons($order_id);exit;
			
			}
			
		}
			
	}	
	function wc_os_crons_light($order_id=0, $force=false){
		
		$user_id = is_user_logged_in()?get_current_user_id():0;
				
		global $wc_os_settings, $wc_os_debug, $pagenow, $wc_os_general_settings, $wc_os_order_splitter, $wc_os_pro;
		
		$split_proceed = ($order_id || (!is_admin() || wc_os_is_orders_list()));
		
		if(!$force){
			if(wc_os_is_orders_list() && is_array($wc_os_general_settings) && (empty($wc_os_general_settings) || (!empty($wc_os_general_settings) && !array_key_exists('wc_os_cron_shop_order_page', $wc_os_general_settings)))){
				return;		
			}
		}
		if($split_proceed){
		}else{
			return;
		}
	
		wc_os_settings_refresh();
		
		
		$wc_os_order_splitter = new wc_os_order_splitter();
		
		$wc_os_order_splitter->cron_light_in_progress = true;
		
		$data = array();
		
		if(wc_os_order_split($order_id)){
			$data = $wc_os_order_splitter->split_order_logic($order_id);
		}
		$data = (is_array($data)?$data:array());
		
		$wc_os_ie_selected = (array_key_exists('wc_os_ie_selected', $data)?$data['wc_os_ie_selected']:'');
		
		$split_status_value = '';
		
		switch($wc_os_ie_selected){
			case 'io':
				$items_io = (array_key_exists('items_io', $data)?$data['items_io']:array());
				$in_stock_exists = (isset($items_io['in_stock']) && !empty($items_io['in_stock']) && isset($items_io['in_stock']['items']) && !empty($items_io['in_stock']['items']));
				$backorder_exists = (isset($items_io['backorder']) && !empty($items_io['backorder']) && isset($items_io['backorder']['items']) && !empty($items_io['backorder']['items']));
				
				$io_options = get_option('wc_os_io_options', array());
				$io_options = is_array($io_options)?$io_options:array('default_stock_status'=>'','in_stock_status'=>'','out_stock_status'=>'');
		
				if($in_stock_exists && $backorder_exists){

					
					$split_status_value = $io_options['default_stock_status'];
					
				}else{
					if($in_stock_exists){
						
						$split_status_value = $io_options['in_stock_status'];
					}
					if($backorder_exists){
						
						$split_status_value = $io_options['out_stock_status'];
					}
				}
			break;
		}
		
		
		if($split_status_value){

			wc_os_set_order_status($order_id, '', false, 81);//$split_status_value
			update_post_meta($order_id, 'split_ignore', true);
		}
		
		//exit;
	}
	function wc_os_crons($order_id=0, $split_action=true, $force_proceed = false){
		
		$user_id = is_user_logged_in()?get_current_user_id():0;
		
		
		$last_split_id = 0;
		
		global $wpdb, $wc_os_settings, $wc_os_debug, $pagenow, $wc_os_general_settings, $wc_os_order_splitter, $wc_os_pro, $wc_os_cron_settings;

		
		$split_proceed = ($order_id || (!is_admin() || wc_os_is_orders_list()));
		
		
		$wc_os_cron_shop_order_page = (is_array($wc_os_general_settings) && ((!empty($wc_os_general_settings) && array_key_exists('wc_os_cron_shop_order_page', $wc_os_general_settings))));
		


		if(wc_os_is_orders_list()){
			
			if(!$wc_os_cron_shop_order_page || (date('i')%2==0)){// && !array_key_exists('debug')
				return;
			}
			
		}
		
		if($order_id){
			
			$order_meta_keys = array_keys(get_post_meta($order_id));
			if(!empty($order_meta_keys)){
				$halt_keys = array('_shipstation_exported');
				$halt_keys_confirmed = array_intersect($halt_keys, $order_meta_keys);
				if(count($halt_keys_confirmed)>0){
					update_post_meta($order_id, 'split_status', true);
					return;
				}
			}
			
			//wc_os_pree($order_id);wc_os_pree($order_meta_keys);exit;

		}
		
		if($split_proceed){
			$last_split_id = $wpdb->get_row("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='split_status' ORDER BY post_id DESC LIMIT 1");
			if(is_object($last_split_id)){
				$last_split_id = $last_split_id->post_id;
			}		

			$last_split_id = ((array_key_exists('wc-os-last-split-id', $wc_os_cron_settings) && $wc_os_cron_settings['wc-os-last-split-id']>0)?$wc_os_cron_settings['wc-os-last-split-id']:$last_split_id);

			if($order_id && $order_id<$last_split_id){
				update_post_meta($order_id, 'split_status', true);
				return;
			}

		}else{
			return;
		}

		
		wc_os_settings_refresh();
		
		
		
		
		
		
		
		$proceed_partially = (($order_id && wc_os_order_split($order_id)) || !$order_id);//exit;
		
		if($proceed_partially){
			
			$wc_os_ie = $wc_os_settings['wc_os_ie'];
		
			
			
			//$split_action = $split_action?(get_post_meta($order_id, 'wc_os_customer_permitted', true)== 'yes'):$split_action;
				
						
			$wc_os_general_settings = get_option('wc_os_general_settings', array());
			
			
			
			$split_action = $split_action?array_key_exists('wc_os_auto_forced', $wc_os_general_settings):$split_action;
			
			
			
			$customer_permission_required = array_key_exists('wc_os_customer_permitted', $wc_os_general_settings);
			
			if($customer_permission_required){
			
				if($order_id){
					$split_action = $split_action?(get_post_meta($order_id, 'wc_os_customer_permitted', true) == 'yes'):$split_action;
				}else{
					$split_action = false;
				}
			
			}			
			
			
			
			$wc_os_order_splitter_cron_clear = get_option('wc_os_order_splitter_cron_clear', array());
			$wc_os_order_splitter_cron_clear = (is_array($wc_os_order_splitter_cron_clear)?$wc_os_order_splitter_cron_clear:array());
				
			$wc_os_products = $wc_os_settings['wc_os_products'];		
			
			$wc_os_order_splitter_cron = (!$order_id?get_option('wc_os_order_splitter_cron', array()):array());
			$wc_os_order_splitter_cron = (is_array($wc_os_order_splitter_cron)?$wc_os_order_splitter_cron:array());
			
			
			

			
			if(!$order_id){
				

				if($wc_os_pro && function_exists('wc_os_get_io_setting') && wc_os_get_io_setting('out_stock_automation')=='yes'){
			
					$cron_query = "SELECT p.ID, p.post_status FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN('trash') AND mt.meta_key IN ('_wos_out_of_stock') AND p.ID>=$last_split_id GROUP BY p.ID ORDER BY p.ID DESC";
					
					

					$wc_os_order_key_cron = $wpdb->get_results($cron_query);			

					if(!empty($wc_os_order_key_cron)){
						
						$io_options = get_option('wc_os_io_options', array());
						$in_stock_status = (array_key_exists('in_stock_status', $io_options) ? $io_options['in_stock_status'] : '');
						$out_stock_status = (array_key_exists('out_stock_status', $io_options) ? $io_options['out_stock_status'] : '');
						
						$products_with_qty = array();
						$product_item_updated_stock = array();
						
						
						
						foreach($wc_os_order_key_cron as $all_crons_items){		
						
								
								$updated_status = '';
								if($all_crons_items->post_status!=$out_stock_status){
									$updated_status = wc_os_set_order_status($all_crons_items->ID, '', false, 203);
								}
								
								

								
							//if(isset($_GET['update-status'])){
								
								$products_with_qty[$all_crons_items->ID] = array();
								
								$order_data = wc_get_order($all_crons_items->ID);
								


								$products_with_qty[$all_crons_items->ID]['total-items'] = count($order_data->get_items());
								$products_with_qty[$all_crons_items->ID]['in-items'] = array();
								$products_with_qty[$all_crons_items->ID]['out-items'] = array();	
											

															
								foreach($order_data->get_items() as $item_id => $item_data){	
									
									if(!is_array($products_with_qty[$all_crons_items->ID])){ continue; }else{ }
									
									$pid = $item_data->get_product_id();
									$vid = $item_data->get_variation_id();
									$product_item = $vid?$vid:$pid;	
									$product = wc_get_product($product_item);
									
									if(!is_array($product) && !is_object($product)){ continue; }
									
									$product_parent = (!$product->get_parent_id()?$product:wc_get_product($product->get_parent_id()));
									
									if(array_key_exists($product_item, $product_item_updated_stock)){
										$qty_stock_status = $product_item_updated_stock[$product_item];
									}else{
										
										$qty_stock_status = $product->managing_stock()?$product->get_stock_quantity():$product_parent->get_stock_quantity();
										
										$_wc_os_stock_quantity = get_post_meta($product_item, '_wc_os_stock_quantity', true);										

										$qty_stock_status = (is_numeric($_wc_os_stock_quantity)?$_wc_os_stock_quantity:$qty_stock_status);
										
										//update_post_meta($product_item, '_wc_os_stock_quantity', 0);
									}
									

									
									
									if($qty_stock_status>=1 && $item_data->get_quantity()>=1){
										
										//$updated_stock_value = ($qty_stock_status-$item_data->get_quantity());
										
										//$updated_stock = ($updated_stock_value<0?0:$updated_stock_value);
										
										$updated_stock = ($qty_stock_status>=$item_data->get_quantity()?$item_data->get_quantity():$qty_stock_status);
										
										$product_item_updated_stock[$product_item] = $updated_stock;
										
										if($updated_stock>=0){
											$products_with_qty[$all_crons_items->ID]['in-items'][] = array('item'=>$product_item, 'updated_stock'=>$updated_stock);
											
											if($item_data->get_quantity()-$updated_stock>0){												
												$products_with_qty[$all_crons_items->ID]['out-items'][] = $product_item;
											}
										}
										
										
									}else{
										$products_with_qty[$all_crons_items->ID]['out-items'][] = $product_item;
									}
									
									
									
								
								}
								
								
								
							//}
							
						}
						
						//pre('$products_with_qty');pre($products_with_qty);exit;
						
						if(!empty($products_with_qty)){
							foreach($products_with_qty as $out_order_id => $out_order_data){

								if(
										($out_order_data['total-items']==count($out_order_data['out-items']))
									||	
										empty($out_order_data['in-items'])
								){
									//unset($products_with_qty[$out_order_id]);
								}
							}
						}
						
						
						if(!empty($products_with_qty)){
							
							foreach($products_with_qty as $out_order_id => $out_order_data){
								

								if($out_order_data['total-items']>0){// && wc_os_order_split($out_order_id)){
									
									
									
									

	
									
									if(!empty($out_order_data['out-items'])){// && !empty($out_order_data['in-items']) && $out_order_data['total-items']!=count($out_order_data['out-items'])
										
										
										
										if(wc_os_order_split($out_order_id, $out_stock_status)){
											wc_os_pre('REPEATING SPLIT FOR OUTSTOCK ITEMS #'.$out_order_id, 'cron');
											delete_post_meta($out_order_id, 'split_status');	

											delete_post_meta($out_order_id, '_wos_out_of_stock');											
											delete_post_meta($out_order_id, '_wc_os_total_items');											
											delete_post_meta($out_order_id, '_wos_in_stock', true);
											
											$wc_os_order_splitter->auto_split = true;
											$wc_os_order_splitter->lock_released = true;
											$wc_os_order_splitter->re_split = true;
											$wc_os_order_splitter->split_order_logic($out_order_id);
											$wc_os_order_splitter->re_split = false;		
											
											
										}else{
											wc_os_pre('REPEATING SPLIT FOR OUTSTOCK ITEMS #'.$out_order_id.' WAITING FOR SPLIT LOCK RELEASE', 'cron');
										}
										
																
									}
									
									if(!empty($out_order_data['in-items']) && empty($out_order_data['out-items'])){// && $out_order_data['total-items']==count($out_order_data['in-items'])

										

										delete_post_meta($out_order_id, '_wos_out_of_stock');
										update_post_meta($out_order_id, '_wos_in_stock', true);
										wc_os_set_order_status($out_order_id, '', false, 303);//$in_stock_status

										foreach($out_order_data['in-items'] as $in_items_data){

											//update_post_meta($in_items_data['item'], '_stock', $in_items_data['updated_stock']);
										}
										
									}
									
								}
								
								
								
								
							}
							$wc_os_order_splitter->auto_split = false;
						}
						if(is_admin() && isset($_GET['products_with_qty'])){
							pree($products_with_qty);
							exit;
						}
					}else{
						
					}
				}		
				
			
			
			
				$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key IN ('_wos_consider_split_email') AND p.ID>=$last_split_id";

				$wc_os_order_key_cron = $wpdb->get_results($cron_query);			
			
				if(!empty($wc_os_order_key_cron)){
					foreach($wc_os_order_key_cron as $all_crons_items){
					
                        $mail_success = wos_email_notification(array('new'=>array($all_crons_items->ID), 'original'=>$all_crons_items->ID));
						//exit;
						if($mail_success){
							delete_post_meta($all_crons_items->ID, '_wos_consider_split_email');
						}
						
					}
				}
				
				switch($wc_os_ie){
					case 'group_by_vendors':
					case 'group_by_woo_vendors':					
						$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key IN ('_vendor_term') AND mt.meta_key NOT IN ('_vendor_term_assigned') AND p.ID>=$last_split_id";
		
						$wc_os_order_key_cron = $wpdb->get_results($cron_query);			
					
						if(!empty($wc_os_order_key_cron)){
							foreach($wc_os_order_key_cron as $all_crons_items){
							
								$_vendor_term = get_post_meta( $all_crons_items->ID, '_vendor_term', true );
								$vendor_id = get_post_meta( $all_crons_items->ID, '_vendor_id', true );
								if($_vendor_term && $vendor_id && defined('WC_PRODUCT_VENDORS_TAXONOMY')){
									wp_set_object_terms( $all_crons_items->ID, intval($vendor_id), WC_PRODUCT_VENDORS_TAXONOMY );
									update_post_meta($all_crons_items->ID, '_vendor_term_assigned', true);
								}
								
							}
						}
					
					break;
				}
					
					
				
				$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key IN ('_wos_update_rounds') AND mt.meta_value>=1 AND p.ID>=$last_split_id";

				$wc_os_order_key_cron = $wpdb->get_results($cron_query);			
			
				if(!empty($wc_os_order_key_cron)){
					foreach($wc_os_order_key_cron as $all_crons_items){
						wc_os_update_order_item_admin($all_crons_items->ID);
					}
				}
				
				
				
				
				
				$cron_query = "SELECT p.ID, mt.meta_value FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key IN ('_wc_os_set_status') AND p.ID>=$last_split_id";

				$wc_os_order_key_cron = ($wc_os_pro?$wpdb->get_results($cron_query):array());			

				if(!empty($wc_os_order_key_cron)){

					foreach($wc_os_order_key_cron as $all_crons_items){
						
						//$parent_id = get_post_meta($all_crons_items->ID, 'splitted_from', true);
						//$parent_id = ($parent_id?$parent_id:get_post_meta($all_crons_items->ID, 'cloned_from', true));
						
						//if($parent_id)

						
						$wc_child_order = wc_get_order($all_crons_items->ID);
						
						//$wc_child_order_status = $wc_child_order->get_status();
						
						$order_status = wc_os_add_prefix($all_crons_items->meta_value, 'wc-');
						
						//$wc_child_order->set_status($order_status);
						wc_os_update_order_status($all_crons_items->ID, $order_status);
						
						//$wc_child_order = wc_get_order($all_crons_items->ID);

						
						delete_post_meta($all_crons_items->ID, '_wc_os_set_status');
						
						
						
						
						
					}
					//exit;
				}
			
				
				$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key='items_status_check' AND p.ID>=$last_split_id";

				$wc_os_order_key_cron = ($wc_os_pro?$wpdb->get_results($cron_query):array());	
				
				if(!empty($wc_os_order_key_cron)){
					foreach($wc_os_order_key_cron as $all_crons_items){				
						$order_data = wc_get_order( $all_crons_items->ID );
						if(count($order_data->get_items())<1 || wc_os_order_removal()){
							wc_os_trash_post($all_crons_items->ID);
						}
					}
				}
				
			
				wc_os_crons_wos_calculate_totals();
				
				
			
				$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key='__wos_calculate_totals_for_tax' AND p.ID>=$last_split_id";

				$wc_os_order_key_cron = $wpdb->get_results($cron_query);

				if(!empty($wc_os_order_key_cron)){
					foreach($wc_os_order_key_cron as $all_crons_items){									
						$order_data = new WC_Order( $all_crons_items->ID );
						$order_data->calculate_taxes();
						$order_data->calculate_totals();
						delete_post_meta($all_crons_items->ID, '__wos_calculate_totals_for_tax');
					}
				}
			
			
				$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type='shop_order' WHERE p.post_status NOT IN ('trash') AND mt.meta_key='wos_remove_taxes' AND p.ID>=$last_split_id GROUP BY mt.post_id ORDER BY p.ID DESC";

				$wc_os_order_key_cron = $wpdb->get_results($cron_query);

				if(!empty($wc_os_order_key_cron) && false){
					add_action( 'woocommerce_calculate_totals', 'wc_os_action_woocommerce_calculate_totals', 10, 1 );
					add_filter('woocommerce_order_is_vat_exempt', function(){ return true; });
					
					foreach($wc_os_order_key_cron as $all_crons_items){
						$_shipping_postcode = get_post_meta($all_crons_items->ID, '_shipping_postcode', true);
						delete_post_meta($all_crons_items->ID, '_shipping_postcode');
						$order_data = new WC_Order( $all_crons_items->ID );
						
						
						//$woocommerce->customer->set_is_vat_exempt( true );
						delete_post_meta($all_crons_items->ID, 'wos_remove_taxes');
						delete_post_meta($all_crons_items->ID, '_order_tax');
						delete_post_meta($all_crons_items->ID, '_tax_status');
						delete_post_meta($all_crons_items->ID, '_tax_class');
						foreach($order_data->get_items() as $item_id => $item_data){						
							update_metadata( 'order_item', $item_id, '_line_subtotal_tax', 0);
							update_metadata( 'order_item', $item_id, '_line_tax_data', 0);
							update_metadata( 'order_item', $item_id, '_line_tax_data', array());						
						}
						$order_data->calculate_totals();
						update_post_meta($all_crons_items->ID, '_shipping_postcode', $_shipping_postcode);
					
					}

				}
				
				
				
			}
				
			
			$cron_query = "SELECT p.ID FROM $wpdb->postmeta mt RIGHT JOIN $wpdb->posts p ON p.ID=mt.post_id AND p.post_type IN ('shop_order') ".($order_id?'AND p.ID IN ('.$order_id.') ':'')."WHERE p.post_status NOT IN ('trash') AND mt.meta_key='wc_os_order_splitter_cron' AND p.ID>=$last_split_id GROUP BY mt.post_id ORDER BY p.ID DESC";// AND mt.meta_key!='splitted_from'
			

			
			$wc_os_order_key_cron = $wpdb->get_results($cron_query);

			
			
			if($wc_os_debug)
			wc_os_pree($wc_os_order_key_cron);
			

			
			if(!empty($wc_os_order_key_cron)){
				
				if($order_id){
					$wc_os_order_splitter_cron[$order_id] = true;

					
					//$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID = $order_id"); //02/07/2020
				
				}else{
	

					foreach($wc_os_order_key_cron as $all_crons_items){
						
						if(!array_key_exists($all_crons_items->ID, $wc_os_order_splitter_cron)){
							if($customer_permission_required){
								$wc_os_order_splitter_cron[$all_crons_items->ID] =  (get_post_meta($all_crons_items->ID, 'wc_os_customer_permitted', true) == 'yes');
							}else{
								$wc_os_order_splitter_cron[$all_crons_items->ID] = (is_admin()?true:$split_action); //23/10/2019 //13/10/2019							
							}
							
						}

						
					}
					//exit;
				}
			}else{
				
				$wc_os_order_splitter_cron = array();

				update_option('wc_os_order_splitter_cron', $wc_os_order_splitter_cron);
				
			}

			
			
			
						
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'wos_quick_split'){
				$split_action = true;
				if(empty($wc_os_order_splitter_cron)){					
					$wc_os_order_splitter_cron[$order_id] = true;
				}
			}			

			


			if(!empty($wc_os_order_splitter_cron)){
				
				
				
				foreach($wc_os_order_splitter_cron as $order_id_split => $auto_split){

					
					if ( get_post_type( $order_id_split ) != 'shop_order') {
						wc_os_update_split_status($order_id_split,559);
						continue;
					}
					if(!wc_os_order_split($order_id_split)){
						
						
						
						continue;
					}else{

					}
					
					
					
					$valid_order = wc_get_order($order_id_split); 
					
					$wc_os_log_order = $valid_order;
					
					if(empty($valid_order)){ 
						unset($wc_os_order_splitter_cron[$order_id_split]); 
						delete_post_meta($order_id_split, 'wc_os_order_splitter_cron'); 

						continue; 
					}
					
					
					$wc_os_order_splitter->cron_in_progress = true;
					
					$wc_os_order_splitter->auto_split = ($auto_split?true:false);
				
					$wc_os_prcess_status = true;
					
					
					
					
					
					if($auto_split){	

							
							
							$wos_forced_ie = get_post_meta($order_id_split, 'wos_forced_ie', true);
							$wos_delete_order = get_post_meta($order_id_split, 'wos_delete_order', true);
							if($wos_forced_ie && $wos_delete_order){
								$wc_os_ie = $wos_forced_ie;
							}
							
							
							update_post_meta($order_id_split, '_wc_os_skip_default_coupon', true);
							do_action('wc_os_before_order_split', $order_id_split);
							
					
						

							
							
							
							
							switch($wc_os_ie){
								default:
									


									
									if($customer_permission_required){
										$split_action = (get_post_meta($order_id_split, 'wc_os_customer_permitted', true) == 'yes');
									}

									
									if($split_action){
										
								
										$get_post_meta = get_post_meta($order_id_split);

										$split_status_exists = array_key_exists('split_status', $get_post_meta);
										$order_id_split_status = ($split_status_exists && $get_post_meta['split_status']==1);
										
										$wc_os_logger_str = 'split_order_logic > '.date('d M, Y H:i:s A').' ~ '.$order_id_split.' SPLITTED '.($order_id_split_status?'YES':'NO');

										
										if(!$order_id_split_status){
											
											$wc_os_prcess_status = $wc_os_order_splitter->split_order_logic($order_id_split);//, $wc_os_products);


											
										}
									}else{

									}
									
									//$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID = $order_id_split"); //02/07/2020
								break;
								case 'default':

									
									
									if($split_action){
										$wc_os_order_splitter->split_order($order_id_split);
									}

									
									
								break;								
							}
                            
							
							
                            $get_post_meta = get_post_meta($order_id_split);
                            if($wc_os_log_order && wc_os_order_removal() && array_key_exists('split_status', $get_post_meta)){

                                wc_os_save_order_log($wc_os_log_order);

                            }							
							//$a++;
							
						//}
						//exit;
						

						
					}else{
						
					}
					
					//if(wc_os_order_removal()){ //WE ARE CLONING SO WE HAVE TO REMOVE IT, NO NEED TO CHECK REMOVAL OPTION
					if($wc_os_prcess_status){ //20/05/2019
						$wc_os_order_splitter_cron_clear[$order_id_split] = time(); //24/01/2019
						unset($wc_os_order_splitter_cron[$order_id_split]);
					}
					//}

					
					
					$wc_os_order_splitter->cron_in_progress = false;
					
				}
				
				
			} 
			
			if(!$order_id){
				
				

				
				if(!empty($wc_os_order_splitter_cron_clear)){
					
				
					foreach($wc_os_order_splitter_cron_clear as $order_id_cron_clear => $timestamp){ //continue;
						
							$mins = ((time()-$timestamp)/60);
					
							$get_post_meta = get_post_meta($order_id_cron_clear);
							
							if(!$wc_os_debug && !array_key_exists('split_ignore', $get_post_meta)){
								
								if(!wc_os_order_removal()){
									
									$wos_delete_order = array_key_exists('wos_delete_order', $get_post_meta);
									if($wos_delete_order){
										wc_os_trash_post($order_id_cron_clear); //14/05/2019
									}else{										
									
										$removal_arr[$order_id_cron_clear] = 'wc_os_order_splitter_cron';

										update_option('wos_update_status', $removal_arr);
									
									}
									
								}elseif(
											array_key_exists('split_status', $get_post_meta) 
										&& 
											(
												!array_key_exists('splitted_from', $get_post_meta) 
											&& 
												!array_key_exists('cloned_from', $get_post_meta)
											) 
										&& 
											wc_os_order_removal() 
										&& 
											!array_key_exists('wos_untrashed', $get_post_meta)
										){
									
									wc_os_trash_post($order_id_cron_clear); //24/01/2019
								}
							}
							
							unset($wc_os_order_splitter_cron_clear[$order_id_cron_clear]);
						//}									
					}
					
					
					
					
					
				}else{

					if(wc_os_order_removal()){ //24/03/2020
						$get_post_meta = get_post_meta($order_id);
						$get_post_meta = is_array($get_post_meta)?$get_post_meta:array();
								
						if(is_array($get_post_meta) && array_key_exists('split_status', $get_post_meta) && (!array_key_exists('splitted_from', $get_post_meta) && !array_key_exists('cloned_from', $get_post_meta))){				
							wc_os_trash_post($order_id);
						}
					}
				}
				
				//exit;
				
				
				
				
				
				switch($wc_os_ie){
					default:
					
				
						$removal_arr = get_option('wos_update_status', array());
						//$removal_arr = array(229=>'');

						if(!empty($removal_arr)){

							foreach($removal_arr as $order_id=>$status){
								
								switch($status){
									case 'wc_os_order_splitter_cron':
										delete_post_meta($order_id, $status);
										$status = '';
									break;
								}
						
								//$order_data = wc_get_order( $order_id );
								$status = ($status?'wc-'.str_replace('wc-', '', $status):'');
								$update_post = array('ID' => $order_id, 'post_status' => $status);
							
								//wp_update_post($update_post);
								$is_custom_status = get_post_meta($order_id, '_wos_custom_status_update', true);
								
								//if($is_custom_status!=$status){					
									//wp_update_post($update_post);
									
									$updated_status = wc_os_set_order_status($order_id, $status, false, 793);

									
									//update_wcfm_order_status($order_id, $status);
							
								//}
								
								
							
								unset($removal_arr[$order_id]);
								update_option('wos_update_status', $removal_arr);
								
								
								
								
							}
							//exit;
						}
					
					break;
					
					
				}
			}
			
			update_option('wc_os_order_splitter_cron_clear', $wc_os_order_splitter_cron_clear);
			
			
		}
		
		
		
		
		if($order_id){

            return $order_id;
		}
		
		//if($wc_os_debug)
		//exit;
							
	}