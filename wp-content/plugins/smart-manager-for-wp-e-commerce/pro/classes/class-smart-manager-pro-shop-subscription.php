<?php

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Smart_Manager_Pro_Shop_Subscription' ) ) {
	class Smart_Manager_Pro_Shop_Subscription extends Smart_Manager_Pro_Base {
		public $dashboard_key = '',
				$req_params = array(),
				$plugin_path = '';

		protected static $_instance = null;

		public static function instance($dashboard_key) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self($dashboard_key);
			}
			return self::$_instance;
		}

		function __construct($dashboard_key) {

			parent::__construct($dashboard_key);

			$this->dashboard_key = $dashboard_key;
			$this->post_type = $dashboard_key;
			$this->req_params  	= (!empty($_REQUEST)) ? $_REQUEST : array();

			if ( file_exists(SM_PLUGIN_DIR_PATH . '/classes/class-smart-manager-shop-order.php') ) {
				include_once SM_PLUGIN_DIR_PATH . '/classes/class-smart-manager-shop-order.php';
			}

			add_filter( 'sm_dashboard_model',array( &$this,'subscriptions_dashboard_model' ), 10, 2 );
			add_filter( 'sm_data_model', array( &$this, 'subscriptions_data_model' ), 10, 2 );
			add_filter( 'posts_where',array( &$this,'sm_query_sub_where_cond' ), 100, 2 );
			add_filter( 'sm_batch_update_copy_from_ids_select',array( &$this,'sm_batch_update_copy_from_ids_select' ), 10, 2 );

			add_filter( 'found_posts',array( 'Smart_Manager_Shop_Order' ,'kpi_data_query'), 100, 2 );
		}

		public static function actions() {

		}

		public function __call( $function_name, $arguments = array() ) {

			if ( ! is_callable( array( 'Smart_Manager_Shop_Order', $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( 'Smart_Manager_Shop_Order', $function_name ), $arguments );
			} else {
				return call_user_func( array( 'Smart_Manager_Shop_Order', $function_name ) );
			}
		}

		//Function to generate the col model for dropdown datatype
		public function generate_dropdown_col_model( $colObj, $dropdownValues = array() ) {

			$dropdownKeys = ( !empty( $dropdownValues ) ) ? array_keys( $dropdownValues ) : array();
			$colObj['defaultValue'] = ( !empty( $dropdownKeys[0] ) ) ? $dropdownKeys[0] : '';
			$colObj['save_state'] = true;
			
			$colObj['values'] = $dropdownValues;
			$colObj['selectOptions'] = $dropdownValues; //for inline editing

			$colObj['search_values'] = array();
			foreach( $dropdownValues as $key => $value) {
				$colObj['search_values'][] = array('key' => $key, 'value' => $value);
			}

			$colObj['type'] = 'dropdown';
			$colObj['strict'] = true;
			$colObj['allowInvalid'] = false;
			$colObj['editor'] = 'select';
			$colObj['renderer'] = 'selectValueRenderer';

			return $colObj;
		}

		//Fucntion for overriding the select clause for fetching the ids for batch update 'copy from' functionality
		public function sm_batch_update_copy_from_ids_select( $select, $args ) {

			$select = " SELECT ID AS id, CONCAT('Subscription #', ID) AS title";
			return $select;
		}

		public function subscriptions_dashboard_model ($dashboard_model, $dashboard_model_saved) {
			global $wpdb, $current_user;

			$dashboard_model['tables']['posts']['where']['post_type'] = 'shop_subscription';

			$visible_columns = array('ID', 'post_date', 'post_status', '_billing_email', '_billing_first_name', '_billing_last_name', '_order_total', '_billing_interval', '_billing_period', '_payment_method_title', '_schedule_next_payment', '_schedule_end');

			$numeric_columns = array('_billing_phone', '_cart_discount', '_cart_discount_tax', '_customer_user', '_billing_interval');

			$string_columns = array('_billing_postcode', '_shipping_postcode');

			$post_status_col_index = sm_multidimesional_array_search('posts_post_status', 'data', $dashboard_model['columns']);
			
			$sub_statuses = array();

			if( function_exists('wcs_get_subscription_statuses') ) {
				$sub_statuses = wcs_get_subscription_statuses();
			}

			$sub_statuses_keys = ( !empty( $sub_statuses ) ) ? array_keys($sub_statuses) : array();
			$dashboard_model['columns'][$post_status_col_index]['defaultValue'] = ( !empty( $sub_statuses_keys[0] ) ) ? $sub_statuses_keys[0] : 'wc-pending';

			$dashboard_model['columns'][$post_status_col_index]['save_state'] = true;
			
			$dashboard_model['columns'][$post_status_col_index]['values'] = $sub_statuses;
			$dashboard_model['columns'][$post_status_col_index]['selectOptions'] = $sub_statuses; //for inline editing

			$dashboard_model['columns'][$post_status_col_index]['search_values'] = array();
			foreach ($sub_statuses as $key => $value) {
				$dashboard_model['columns'][$post_status_col_index]['search_values'][] = array('key' => $key, 'value' => $value);
			}

			$color_codes = array( 'green' => array( 'wc-active' ),
									'red' => array( 'cancelled' ),
									'orange' => array( 'wc-on-hold', 'wc-pending' ),
									'blue' => array( 'wc-switched', 'wc-pending-cancel' ) );

			$dashboard_model['columns'][$post_status_col_index]['colorCodes'] = apply_filters( 'sm_'.$this->dashboard_key.'_status_color_codes', $color_codes );

			//Code to get the custom column model
			if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_custom_column_model' ) ) ) {
				$dashboard_model['columns'] = self::generate_orders_custom_column_model( $dashboard_model['columns'] );
			}

			$column_model = &$dashboard_model['columns'];

			//Code for unsetting the position for hidden columns

			foreach( $column_model as &$column ) {
				
				if (empty($column['src'])) continue;

				$src_exploded = explode("/",$column['src']);

				if (empty($src_exploded)) {
					$src = $column['src'];
				}

				if ( sizeof($src_exploded) > 2) {
					$col_table = $src_exploded[0];
					$cond = explode("=",$src_exploded[1]);

					if (sizeof($cond) == 2) {
						$src = $cond[1];
					}
				} else {
					$src = $src_exploded[1];
					$col_table = $src_exploded[0];
				}


				if( empty($dashboard_model_saved) ) {
					if (!empty($column['position'])) {
						unset($column['position']);
					}

					$position = array_search($src, $visible_columns);

					if ($position !== false) {
						$column['position'] = $position + 1;
						$column['hidden'] = false;
					} else {
						$column['hidden'] = true;
					}
				}

				if ($src == 'post_date') {
					$column ['name'] = $column ['key'] = __('Date', 'smart-manager-for-wp-e-commerce');
				} else if ($src == 'post_status') {
					$column ['name'] = $column ['key'] = __('Status', 'smart-manager-for-wp-e-commerce');
				} else if ($src == '_billing_interval') {
					$subscription_period_interval = ( function_exists('wcs_get_subscription_period_interval_strings') ) ? wcs_get_subscription_period_interval_strings() : array();
					$column = $this->generate_dropdown_col_model( $column, $subscription_period_interval );					
				} else if ($src == '_billing_period') {
					$subscription_period = ( function_exists('wcs_get_subscription_period_strings') ) ? wcs_get_subscription_period_strings() : array();
					$column = $this->generate_dropdown_col_model( $column, $subscription_period );				
				} else if( !empty( $numeric_columns ) && in_array( $src, $numeric_columns ) ) {
					$column ['type'] = $column ['editor'] = 'numeric';
				} else if( !empty( $string_columns ) && in_array( $src, $string_columns ) ) {
					$column ['type'] = $column ['editor'] = 'text';
				}
			}

			if (!empty( $dashboard_model_saved )) {
				$col_model_diff = sm_array_recursive_diff($dashboard_model_saved,$dashboard_model);	
			}

			//clearing the transients before return
			if (!empty($col_model_diff)) {
				delete_transient( 'sa_sm_'.$this->dashboard_key );	
			}

			return $dashboard_model;

		}

		//Function to process subscriptions search for custom columns
		public function sm_query_sub_where_cond ($where, $wp_query_obj) {

			if( is_callable( array( 'Smart_Manager_Shop_Order', 'process_custom_search' ) ) ) {
				$where = self::process_custom_search( $where, $this->req_params );
			}

			return $where;
		}

		//function to modify the data_model only for Export CSV & fetch data for custom columns
		public function subscriptions_data_model( $data_model, $data_col_params ) {

			if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_custom_column_data' ) ) ) {
				$data_model = self::generate_orders_custom_column_data( $data_model, $this->req_params );
			}

			if( !empty( $this->req_params['sm_page'] ) && $this->req_params['sm_page'] == 1 ) {
				if( is_callable( array( 'Smart_Manager_Shop_Order', 'generate_orders_kpi_data' ) ) ) {

					$sub_statuses = array();

					if( function_exists('wcs_get_subscription_statuses') ) {
						$sub_statuses = wcs_get_subscription_statuses();
					}

					$data_model['kpi_data'] = self::generate_orders_kpi_data( $this->req_params, $sub_statuses );
				}
			}

			return $data_model;
			
		}

	}

}
