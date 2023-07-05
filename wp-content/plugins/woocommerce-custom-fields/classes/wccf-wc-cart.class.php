<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to WooCommerce Cart
 *
 * @class WCCF_WC_Cart
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_WC_Cart')) {

class WCCF_WC_Cart
{

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 20;

    private $custom_woocommerce_price_num_decimals;

    private $extra_cart_items = array();

    // Singleton instance
    protected static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Add field values to cart item meta data
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_product_field_values'), 10, 3);

        // Add extra cart items after splitting quantity-based field data
        add_action('woocommerce_add_to_cart', array($this, 'add_extra_cart_items'), 99, 6);
        add_filter('rightpress_product_price_test_simulate_add_to_cart_extra_items', array($this, 'add_extra_cart_items_for_pricing_test'), $this->rightpress_hook_position, 5);

        // Add to cart validation
        add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_cart_item_product_field_values'), 10, 6);
        add_action('wp_loaded', array($this, 'maybe_redirect_to_product_page_after_failed_validation'), 20);

        // Remove cart items with invalid configuration
        add_action('woocommerce_cart_loaded_from_session', array($this, 'remove_cart_items_with_invalid_configuration'), 1);

        // Get cart item from session
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session'), 10, 3);

        // Add prices array pointer
        add_filter('rightpress_product_price_breakdown_prices_array_pointers', array($this, 'add_prices_array_pointer'), $this->rightpress_hook_position);

        // Register cart item price changes first stage callback
        add_filter('rightpress_product_price_cart_item_price_changes_first_stage_callbacks', array($this, 'add_cart_item_price_changes_first_stage_callback'), $this->rightpress_hook_position);

        // Get values for display in cart
        add_filter('woocommerce_get_item_data', array($this, 'get_values_for_display'), 12, 2);

        // Add configuration query vars to product link
        add_filter('woocommerce_cart_item_permalink', array($this, 'add_query_vars_to_cart_item_link'), 99, 3);

        // Copy product field values from order item meta to cart item meta on Order Again
        add_filter('woocommerce_order_again_cart_item_data', array($this, 'move_product_field_values_on_order_again'), 10, 3);

        // Maybe print hidden modal for product field editing
        add_action('woocommerce_after_cart', array($this, 'maybe_print_product_field_editing_modal'));

        // Cart item product field editing field view
        add_action('wp_ajax_wccf_get_cart_item_product_field_editing_view', array($this, 'ajax_get_cart_item_product_field_editing_view'));
        add_action('wp_ajax_nopriv_wccf_get_cart_item_product_field_editing_view', array($this, 'ajax_get_cart_item_product_field_editing_view'));

        // Update cart item product field values
        add_action('wp_ajax_wccf_update_cart_item_product_field_values', array($this, 'ajax_update_cart_item_product_field_values'));
        add_action('wp_ajax_nopriv_wccf_update_cart_item_product_field_values', array($this, 'ajax_update_cart_item_product_field_values'));
    }

    /**
     * Add product field values to cart item meta
     *
     * Splits cart item into multiple cart items if cart item has different
     * configurations for different quantity units (quantity-based fields)
     *
     * Note: This runs for both real cart requests and cart simulation during price tests
     *
     * @access public
     * @param array $cart_item_data
     * @param int $product_id
     * @param int $variation_id
     * @return array
     */
    public function add_cart_item_product_field_values($cart_item_data, $product_id, $variation_id)
    {

        $custom_posted_data = null;

        // Check if this is price test call
        $is_test = RightPress_Product_Price_Test::is_running();

        // Cart item product field values already added
        if (isset($cart_item_data['wccf'])) {
            return $cart_item_data;
        }

        // Get quantity
        $quantity = empty($_REQUEST['quantity']) ? 1 : wc_stock_amount($_REQUEST['quantity']);

        // Allow developers to skip adding product field values to cart item
        if (!apply_filters('wccf_add_cart_item_product_field_values', true, $cart_item_data, $product_id, $variation_id)) {
            return $cart_item_data;
        }

        // Load final product
        $product = $variation_id ? wc_get_product($variation_id) : wc_get_product($product_id);

        // Maybe skip product fields for this product based on various conditions
        if (WCCF_WC_Product::skip_product_fields($product)) {
            return $cart_item_data;
        }

        // Get fields to save values for
        $fields = WCCF_Product_Field_Controller::get_filtered(null, array('item_id' => $product_id, 'child_id' => $variation_id));

        // Check if current request is product price live update request
        if (RightPress_Product_Price_Live_Update::is_processing_live_update_request()) {

            // Get request data
            $custom_posted_data = RightPress_Product_Price_Live_Update::get_request_data();

            // Set quantity from request data
            $quantity = $custom_posted_data['quantity'];
        }

        // Sanitize field values
        // Note - we will need to pass $variation_id here somehow if we ever implement variation-level conditions
        $values = WCCF_Field_Controller::sanitize_posted_field_values('product_field', array(
            'object_id'         => $product_id,
            'fields'            => $fields,
            'quantity'          => $quantity,
            'posted'            => $custom_posted_data,
            'skip_invalid'      => $is_test,
            'leave_no_trace'    => $is_test,
            'store_empty_value' => ($is_test && !RightPress_Product_Price_Shop::is_calculating()),
        ));

        // Check if any values were found
        if ($values) {

            // Group value sets by quantity index if quantity-based fields are used
            $value_sets = $this->group_value_sets_by_quantity_index($values, $fields, $quantity);

            // Itentifier by which we attribute extra cart items to the correct cart item
            $parent_hash = null;

            // Iterate over value sets
            foreach ($value_sets as $value_set) {

                $set_values = $value_set['values'];

                // First set of values are added to parent cart item
                if ($parent_hash === null) {

                    // Set values to cart item
                    if (!empty($set_values)) {
                        $cart_item_data['wccf'] = $set_values;
                        $cart_item_data['wccf_version'] = WCCF_VERSION;
                    }

                    // Generate parent data hash
                    $parent_hash = md5(json_encode(array(
                        (int) $product_id,
                        (int) $variation_id,
                        (array) $set_values,
                    )));

                    // Prepend parent hash during price tests so test and non-test entries are not mixed up
                    if ($is_test) {
                        $parent_hash = 'test_' . $parent_hash;
                    }
                }
                // Data for extra cart items is saved in memory to be used when woocommerce_add_to_cart is called
                else {

                    // Set values and quantities by parent hash
                    $this->extra_cart_items[$parent_hash][] = array(
                        'values'    => $set_values,
                        'quantity'  => count($value_set['quantity_indexes']),
                    );
                }
            }
        }

        return $cart_item_data;
    }

    /**
     * Add extra cart items after splitting quantity-based field data
     *
     * @access public
     * @param string $cart_item_key
     * @param int $product_id
     * @param int $quantity
     * @param int $variation_id
     * @param array $variation
     * @param array $cart_item_data
     * @return void
     */
    public function add_extra_cart_items($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {

        // Get parent item quantity
        $parent_quantity = WC()->cart->cart_contents[$cart_item_key]['quantity'];

        // Unable to determine parent quantity
        if (!$parent_quantity) {
            return;
        }

        // Get extra cart item data
        $data = WCCF_WC_Cart::get_extra_cart_item_data($product_id, $variation_id, $cart_item_data, $parent_quantity);

        // Check if any extra cart items need to be added
        if (!empty($data['extra_cart_items'])) {

            // Update parent quantity
            WC()->cart->set_quantity($cart_item_key, $data['parent_quantity'], false);

            // Iterate over extra cart items
            foreach ($data['extra_cart_items'] as $extra_cart_item_data) {

                // Prevent infinite loop
                remove_action('woocommerce_add_to_cart', array($this, 'add_extra_cart_items'));

                // Add to cart
                do_action('wccf_before_extra_item_add_to_cart', $product_id, $extra_cart_item_data['quantity'], $variation_id, $variation, $extra_cart_item_data['cart_item_data']);
                WC()->cart->add_to_cart($product_id, $extra_cart_item_data['quantity'], $variation_id, $variation, $extra_cart_item_data['cart_item_data']);
                do_action('wccf_after_extra_item_add_to_cart', $product_id, $extra_cart_item_data['quantity'], $variation_id, $variation, $extra_cart_item_data['cart_item_data']);

                // Prevent infinite loop
                add_action('woocommerce_add_to_cart', array($this, 'add_extra_cart_items'), 99, 6);
            }
        }
    }

    /**
     * Add extra cart items after splitting quantity-based field data for product price test
     *
     * @access public
     * @param array $extra_cart_items
     * @param int $product_id
     * @param int $variation_id
     * @param array $cart_item_data
     * @param int $parent_quantity
     * @return array
     */
    public function add_extra_cart_items_for_pricing_test($extra_cart_items, $product_id, $variation_id, $cart_item_data, $parent_quantity)
    {
        // Get extra cart item data
        $data = WCCF_WC_Cart::get_extra_cart_item_data($product_id, $variation_id, $cart_item_data, $parent_quantity, true);

        // Add extra cart items
        if (!empty($data['extra_cart_items'])) {
            $extra_cart_items = array_merge($extra_cart_items, $data['extra_cart_items']);
        }

        return $extra_cart_items;
    }

    /**
     * Get extra cart item data
     *
     * Note: Getting data resets any data stored in memory for requested cart item
     *
     * @access public
     * @param int $product_id
     * @param int $variation_id
     * @param array $cart_item_data
     * @param int $parent_quantity
     * @param bool $is_test
     * @return array
     */
    public static function get_extra_cart_item_data($product_id, $variation_id, $cart_item_data, $parent_quantity, $is_test = false)
    {
        $data = array(
            'parent_quantity'   => $parent_quantity,
            'extra_cart_items'  => array(),
        );

        // Get instance
        $instance = WCCF_WC_Cart::get_instance();

        // Get quantity limit for extra cart items
        $limit = $parent_quantity - 1;

        // Generate parent data hash
        $parent_hash = md5(json_encode(array(
            (int) $product_id,
            (int) $variation_id,
            (isset($cart_item_data['wccf']) && is_array($cart_item_data['wccf'])) ? $cart_item_data['wccf'] : array(),
        )));

        // Prepend parent hash during price tests so test and non-test entries are not mixed up
        if ($is_test) {
            $parent_hash = 'test_' . $parent_hash;
        }

        // Check if this item is parent to any extra items
        if (!empty($instance->extra_cart_items[$parent_hash])) {

            // Iterate over extra cart items
            foreach ($instance->extra_cart_items[$parent_hash] as $key => $extra_cart_item) {

                // Limit not sufficient
                if (!$limit) {
                    break;
                }

                // Get current quantity to use
                $current_quantity = $limit < $extra_cart_item['quantity'] ? $limit : $extra_cart_item['quantity'];

                // Reduce quantity of parent cart item
                $data['parent_quantity'] -= $current_quantity;

                // Note: not sure if we better reset cart item data array here so that 3rd party plugins can
                // add their custom data again or we better keep their values and just reset ours (resetting for now)
                $cart_item_data = array();

                // Add values if any
                // Note: we can't add empty values to cart item data because these units may need to be merged
                // with existing cart item which was added in a regular way (with no values submitted and no 'wccf' data present
                if (!empty($extra_cart_item['values'])) {
                    $cart_item_data['wccf'] = $extra_cart_item['values'];
                    $cart_item_data['wccf_version'] = WCCF_VERSION;
                }

                // Add item to array
                $data['extra_cart_items'][] = array(
                    'quantity'          => $current_quantity,
                    'cart_item_data'    => $cart_item_data,
                );

                // Update limit
                $limit -= $current_quantity;
            }

            // Unset extra items from memory
            unset($instance->extra_cart_items[$parent_hash]);
        }

        return $data;
    }

    /**
     * Group values by quantity indexes and identical sets of values (quantity-based fields)
     *
     * @access protected
     * @param array $values
     * @param array $fields
     * @param int $quantity
     * @return array
     */
    protected function group_value_sets_by_quantity_index($values, $fields, $quantity)
    {
        $value_sets = array();

        // Group values by quantity indexes
        $grouped = array();
        $shared  = array();

        // Fill array with all quantity indexes
        for ($i = 0; $i < $quantity; $i++) {
            $grouped[$i] = array();
        }

        // Iterate over all values
        foreach ($values as $field_id => $field_value) {

            // Get quantity index and clean field id
            $quantity_index = WCCF_Field_Controller::get_quantity_index_from_field_id($field_id, 0);
            $clean_field_id = WCCF_Field_Controller::clean_field_id($field_id);

            // Load field
            if ($field = WCCF_Field_Controller::cache($clean_field_id)) {

                // Field is quantity based
                if ($field->is_quantity_based()) {

                    // Add value to array
                    $grouped[$quantity_index][$clean_field_id] = $field_value;
                }
                // Field is not quantity based
                else {

                    // Add to shared values array
                    $shared[$clean_field_id] = $field_value;
                }
            }
        }

        // Iterate over groups
        foreach ($grouped as $quantity_index => $group) {

            // Add shared values to the end of the values list
            foreach ($shared as $field_id => $field_value) {
                $group[$field_id] = $field_value;
            }

            // Generate hash
            $hash = md5(json_encode($group));

            // Add current values if not yet added
            if (!isset($value_sets[$hash])) {
                $value_sets[$hash] = array(
                    'quantity_indexes'  => array(),
                    'values'            => $group,
                );
            }

            // Track quantity indexes
            $value_sets[$hash]['quantity_indexes'][] = $quantity_index;
        }

        return $value_sets;
    }

    /**
     * Validate product field values on add to cart
     *
     * @access public
     * @param bool $is_valid
     * @param int $product_id
     * @param int $quantity
     * @param int $variation_id
     * @param array $variation
     * @param array $cart_item_data
     * @return bool
     */
    public function validate_cart_item_product_field_values($is_valid, $product_id, $quantity, $variation_id = null, $variation = null, $cart_item_data = null)
    {

        // Load final product
        $product = $variation_id ? wc_get_product($variation_id) : wc_get_product($product_id);

        // Maybe skip product fields for this product based on various conditions
        if (WCCF_WC_Product::skip_product_fields($product)) {
            return $is_valid;
        }

        // Get fields for validation
        $fields = WCCF_Product_Field_Controller::get_filtered(null, array('item_id' => $product_id, 'child_id' => $variation_id, 'variation_attributes' => $variation));

        // Validate all fields
        // Note - we will need to pass $variation_id here somehow if we ever implement variation-level conditions
        $validation_result = WCCF_Field_Controller::validate_posted_field_values('product_field', array(
            'object_id' => $product_id,
            'fields'    => $fields,
            'quantity'  => $quantity,
            'values'    => (is_array($cart_item_data) && !empty($cart_item_data['wccf'])) ? $cart_item_data['wccf'] : null,
        ));

        if (!$validation_result) {
            define('WCCF_ADD_TO_CART_VALIDATION_FAILED', true);
            return false;
        }

        return $is_valid;
    }

    /**
     * Maybe redirect to product page if add to cart action was initiated via
     * URL and its validation failed and URL does not include product URL
     *
     * @access public
     * @return void
     */
    public function maybe_redirect_to_product_page_after_failed_validation()
    {
        // Our validation failed
        if (defined('WCCF_ADD_TO_CART_VALIDATION_FAILED') && WCCF_ADD_TO_CART_VALIDATION_FAILED) {

            // Add to cart was from link as opposed to regular add to cart when data is posted
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['add-to-cart'])) {

                // Get product
                $product = wc_get_product($_GET['add-to-cart']);

                // Product was not loaded
                if (!$product) {
                    return;
                }

                // Get urls to compare
                $request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $product_url = untrailingslashit(get_permalink($product->get_id()));

                // Current request url does not contain product url
                if (strpos($request_url, str_replace(array('http://', 'https://'), array('', ''), $product_url)) === false) {

                    // Add query string to product url
                    if (strpos($product_url, '?') === false) {
                        $redirect_url = $product_url . $_SERVER['REQUEST_URI'];
                    }
                    else {
                        $redirect_url = $product_url . str_replace('?', '&', $_SERVER['REQUEST_URI']);
                    }

                    // Unset notices since we will repeat the same exact process and all notices will be added again
                    wc_clear_notices();

                    // Redirect to product page
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }
    }

    /**
     * Get cart item from session
     *
     * @access public
     * @param array $cart_item
     * @param array $values
     * @param string $key
     * @return array
     */
    public function get_cart_item_from_session($cart_item, $values, $key)
    {
        // Check if we have any product field data stored in cart
        if (!empty($values['wccf'])) {

            // Migrate data if needed
            if (WCCF_Migration::support_for('1')) {
                foreach ($values['wccf'] as $key => $value) {
                    if (isset($value['key']) && !isset($value['data'])) {
                        $values['wccf'] = WCCF_Migration::product_fields_in_cart_from_1_to_2($values['wccf']);
                        break;
                    }
                }
            }

            // Set field values
            $cart_item['wccf'] = $values['wccf'];

            // Set plugin version
            if (!empty($values['wccf_version'])) {
                $cart_item['wccf_version'] = $values['wccf_version'];
            }
        }

        // Return item
        return $cart_item;
    }

    /**
     * Add prices array pointer
     *
     * @access public
     * @param array $pointers
     * @return array
     */
    public function add_prices_array_pointer($pointers)
    {
        $pointers['rp_wccf'] = 1;
        return $pointers;
    }

    /**
     * Register cart item price changes first stage callback
     *
     * @access public
     * @param array $callbacks
     * @return array
     */
    public function add_cart_item_price_changes_first_stage_callback($callbacks)
    {
        // Check if pricing is enabled and if this is first request or test request
        if (WCCF_WC_Product::prices_subject_to_adjustment()) {

            // Add callback
            $callbacks['rp_wccf'] = array($this, 'add_first_stage_price_changes_for_cart_items');

            // Set flag
            if (!RightPress_Product_Price_Test::is_running()) {
                define('WCCF_CART_ITEM_PRICE_CHANGES_APPLIED', true);
            }
        }

        // Return callbacks
        return $callbacks;
    }

    /**
     * Add first stage price changes for cart items
     *
     * This method can be called for both real cart ($cart is set) and simulated cart during price tests (in which case $test_cart_items is not empty)
     *
     * @access public
     * @param array $price_changes
     * @param array $cart_items
     * @param array $test_cart_items
     * @return array
     */
    public function add_first_stage_price_changes_for_cart_items($price_changes, $cart_items, $test_cart_items = array())
    {

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Allow developers to skip pricing adjustment
            if (apply_filters('wccf_skip_pricing_for_cart_item', false, $cart_item)) {
                continue;
            }

            // Get variation id
            $variation_id = !empty($cart_item['variation_id']) ? $cart_item['variation_id'] : null;

            // Get cart item data
            $cart_item_data = !empty($cart_item['wccf']) ? $cart_item['wccf'] : array();

            // Iterate over alternative price changes
            foreach ($price_changes[$cart_item_key]['alternatives'] as $base_price_candidate_key => $alternative_changes) {

                // Iterate over price ranges
                foreach ($alternative_changes['prices']['ranges'] as $price_range_index => $price_range) {

                    // Get base price candidate
                    $base_price_candidate = $price_range['price'];

                    // Adjust price
                    $adjusted_price = WCCF_Pricing::get_adjusted_price_for_product($base_price_candidate, $cart_item['data'], $cart_item_data, $cart_item['quantity'], false, false, true);

                    // Check if price was adjusted
                    if (RightPress_Product_Price::prices_differ($adjusted_price, $base_price_candidate)) {

                        // Set adjusted price to current range
                        $price_changes[$cart_item_key]['alternatives'][$base_price_candidate_key]['prices']['ranges'][$price_range_index]['price'] = $adjusted_price;

                        // Set extra data
                        $price_changes[$cart_item_key]['alternatives'][$base_price_candidate_key]['prices']['ranges'][$price_range_index]['new_changes']['rp_wccf'][RightPress_Help::get_hash()] = array();
                    }
                }
            }
        }

        // Return changes
        return $price_changes;
    }

    /**
     * Get product field values to display in cart
     *
     * @access public
     * @param array $data
     * @param array $cart_item
     * @return array
     */
    public function get_values_for_display($data, $cart_item)
    {

        if (!empty($cart_item['wccf'])) {
            foreach ($cart_item['wccf'] as $field_id => $field_value) {

                // Get field
                $field = WCCF_Field_Controller::get($field_id, 'wccf_product_field');

                // Make sure this field exists
                if (!$field || !$field->is_enabled()) {
                    continue;
                }

                // Check if pricing can be displayed for this product
                $display_pricing = !WCCF_WC_Product::skip_pricing($cart_item['data']);

                // Get value
                $value      = $field->format_display_value($field_value, $display_pricing, true, $cart_item['data']);
                $display    = $value;

                // Maybe allow value editing
                if (WCCF_Settings::get('allow_product_field_editing_in_cart') && is_cart()) {
                    $display = '<span class="wccf_cart_item_product_field_editing" data-wccf-cart-item-edit-field-id="' . $field->get_id() . '" data-wccf-cart-item-key="' . $cart_item['key'] . '">' . $display . '</span>';
                }

                // Add to data array
                $data[] = array(
                    'name'      => $field->get_label(),
                    'value'     => $value,
                    'display'   => $display,
                );
            }
        }

        return $data;
    }

    /**
     * Add configuration query vars to product link
     *
     * @access public
     * @param string $link
     * @param array $cart_item
     * @param string $cart_item_key
     * @return string
     */
    public function add_query_vars_to_cart_item_link($link, $cart_item, $cart_item_key)
    {
        // No link provided
        if (empty($link)) {
            return $link;
        }

        // Do not add query vars
        if (!apply_filters('wccf_preconfigured_cart_item_product_link', true, $link, $cart_item, $cart_item_key)) {
            return $link;
        }

        $new_link = $link;
        $quantity_based_field_found = false;

        // Add a flag to indicate that this link is cart item link to product
        $new_link = add_query_arg('wccf_qv_conf', 1, $new_link);

        // Cart item does not have custom fields
        if (empty($cart_item['wccf'])) {
            return $new_link;
        }

        // Iterate over field values
        foreach ($cart_item['wccf'] as $field_id => $field_value) {

            // Load field
            $field = WCCF_Field_Controller::cache(WCCF_Field_Controller::clean_field_id($field_id));

            // Unable to load field - if we can't get full configuration, don't add anything at all
            if (!$field) {
                return $link;
            }

            // Check if field is quantity based
            $quantity_based_field_found = $quantity_based_field_found ?: $field->is_quantity_based();

            // Get query var key
            $query_var_key = 'wccf_' . $field->get_context() . '_' . $field->get_id();

            // Handle array values
            if (is_array($field_value['value'])) {

                // Fix query var key
                $query_var_key .= '[]';

                $is_first = true;

                foreach ($field_value['value'] as $single_value) {

                    // Encode current value
                    $current_value = rawurlencode($single_value);

                    // Handle first value
                    if ($is_first) {

                        // Add query var
                        $new_link = add_query_arg($query_var_key, $current_value, $new_link);

                        // Check if query var was added
                        if (strpos($new_link, $query_var_key) !== false) {
                            $is_first = false;
                        }
                    }
                    // Handle subsequent values - add_query_arg does not allow duplicate query vars
                    else {

                        if ($frag = strstr($new_link, '#')) {
                            $new_link = substr($new_link, 0, -strlen($frag));
                        }

                        $new_link .= '&' . $query_var_key . '=' . $current_value;

                        if ($frag) {
                            $new_link .= $frag;
                        }
                    }

                }
            }
            else {
                $new_link = add_query_arg($query_var_key, rawurlencode($field_value['value']), $new_link);
            }
        }

        // Add quantity
        if ($quantity_based_field_found && strpos($new_link, 'wccf_') !== false && !empty($cart_item['quantity']) && $cart_item['quantity'] > 1) {
            $new_link .= '&wccf_quantity=' . $cart_item['quantity'];
        }

        // Bail if our URL is longer than URL length limit of 2000
        if (strlen($new_link) > 2000) {
            return $link;
        }

        // Return new link
        return $new_link;
    }

    /**
     * Copy product field values from order item meta to cart item meta on Order Again
     *
     * @access public
     * @param array $cart_item_data
     * @param object|array $order_item
     * @param object $order
     * @return array
     */
    public function move_product_field_values_on_order_again($cart_item_data, $order_item, $order)
    {
        // Get order item meta
        $order_item_meta = $order_item['item_meta'];

        // Iterate over order item meta
        foreach ($order_item_meta as $key => $value) {

            // Check if this is our field id entry
            if (RightPress_Help::string_begins_with_substring($key, '_wccf_pf_id_')) {

                // Attempt to load field
                if ($field = WCCF_Field_Controller::cache($value)) {

                    $current = array();

                    // Field is disabled
                    if (!$field->is_enabled()) {
                        continue;
                    }

                    // Get field key
                    $field_key = $field->get_key();

                    // Quantity index
                    $quantity_index = null;

                    // Attempt to get quantity index from meta entry
                    if ($key !== ('_wccf_pf_id_' . $field_key)) {

                        $quantity_index = str_replace(('_wccf_pf_id_' . $field_key . '_'), '', $key);
                        $extra_data_access_key = $field->get_extra_data_access_key($quantity_index);

                        // Result is not numeric
                        if (!is_numeric($quantity_index)) {
                            continue;
                        }

                        // Unable to validate quantity index
                        if (!isset($order_item_meta[$extra_data_access_key]['quantity_index']) || ((string) $order_item_meta[$extra_data_access_key]['quantity_index'] !== (string) $quantity_index)) {
                            continue;
                        }
                    }

                    // Cart/order items with quantity indexes are no longer supported
                    if ($quantity_index) {

                        // Unset any properties set earlier
                        unset($cart_item_data['wccf']);
                        unset($cart_item_data['wccf_version']);

                        // Do not check subsequent items
                        break;
                    }

                    // Get access keys
                    $value_access_key = $field->get_value_access_key();
                    $extra_data_access_key = $field->get_extra_data_access_key();

                    // Value or extra data entry is not present
                    if (!isset($order_item_meta[$value_access_key]) || !isset($order_item_meta[$extra_data_access_key])) {
                        continue;
                    }

                    // Reference value
                    $current_value = $order_item_meta[$value_access_key];

                    // Remove no longer existent options
                    if ($field->uses_options()) {

                        // Get options
                        $options = $field->get_options_list();

                        // Field can have multiple values
                        if ($field->accepts_multiple_values()) {

                            // Value is not array
                            if (!is_array($current_value)) {
                                continue;
                            }

                            // Value is not empty
                            if (!empty($current_value)) {

                                // Unset non existent options
                                foreach ($current_value as $index => $option_key) {
                                    if (!isset($options[(string) $option_key])) {
                                        unset($current_value[$index]);
                                    }
                                }

                                // No remaining values
                                if (empty($current_value)) {
                                    continue;
                                }
                            }
                        }
                        // Field always has one value
                        else {

                            // Option no longer exists
                            if (!isset($options[(string) $current_value])) {
                                continue;
                            }
                        }
                    }

                    // Remove no longer existent files and prepare file data array
                    if ($field->field_type_is('file')) {

                        $all_file_data = array();

                        // Value is not array
                        if (!is_array($current_value)) {
                            continue;
                        }

                        // Value is not empty
                        if (!empty($current_value)) {

                            // Unset non existent files
                            foreach ($current_value as $index => $access_key) {

                                $file_data_access_key = $field->get_file_data_access_key($access_key);

                                // File data not present in meta
                                if (!isset($order_item_meta[$file_data_access_key])) {
                                    unset($current_value[$index]);
                                    continue;
                                }

                                // Reference file data
                                $file_data = $order_item_meta[$file_data_access_key];

                                // File not available
                                if (!WCCF_Files::locate_file($file_data['subdirectory'], $file_data['storage_key'])) {
                                    unset($current_value[$index]);
                                    continue;
                                }

                                // Add to file data array
                                $all_file_data[$access_key] = $file_data;
                            }

                            // No remaining values
                            if (empty($current_value)) {
                                continue;
                            }
                        }
                    }

                    // Add value
                    $current['value'] = $current_value;

                    // Add extra data
                    $current['data'] = array();

                    // Add files
                    $current['files'] = $field->field_type_is('file') ? $all_file_data : array();

                    // Add to main array
                    $cart_item_data['wccf'][$field->get_id()] = $current;

                    // Add version number
                    $cart_item_data['wccf_version'] = WCCF_VERSION;
                }
            }
        }

        return $cart_item_data;
    }

    /**
     * Remove cart items with invalid configuration
     *
     * @access public
     * @param object $cart
     * @return void
     */
    public function remove_cart_items_with_invalid_configuration($cart)
    {
        // Iterate over cart items
        if (is_array($cart->cart_contents) && !empty($cart->cart_contents)) {
            foreach ($cart->cart_contents as $cart_item_key => $cart_item) {

                // Remove cart items added before version 2.2.4 that have quantity based fields
                // Note: Pre-2.2.4 items did not have version number set at all
                if (isset($cart_item['wccf']) && !isset($cart_item['wccf_version'])) {

                    $remove = false;

                    // Iterate over values
                    foreach ($cart_item['wccf'] as $field_id => $value) {

                        // Get quantity index and clean field id
                        $quantity_index = WCCF_Field_Controller::get_quantity_index_from_field_id($field_id);
                        $clean_field_id = WCCF_Field_Controller::clean_field_id($field_id);

                        // Load field
                        $field = WCCF_Field_Controller::cache($clean_field_id);

                        // Flag for removal
                        if ($quantity_index || !is_object($field) || $field->is_quantity_based()) {
                            $remove = true;
                        }
                    }

                    // Remove cart item
                    if ($remove) {
                        $cart->remove_cart_item($cart_item_key);
                    }
                    // Add version number so that we only run this once
                    else {
                        $cart->cart_contents[$cart_item_key]['wccf_version'] = WCCF_VERSION;
                    }
                }
            }
        }
    }

    /**
     * Maybe print field editing modal
     *
     * @access public
     * @return void
     */
    public function maybe_print_product_field_editing_modal()
    {

        // Must not be printed yet
        if (defined('WCCF_PRODUCT_FIELD_EDITING_MODAL_PRINTED')) {
            return;
        }

        // Check functionality enabled in settings
        if (WCCF_Settings::get('allow_product_field_editing_in_cart')) {

            $print = false;

            // Iterate over cart items
            foreach (RightPress_Help::get_wc_cart_items() as $cart_item) {
                if (!empty($cart_item['wccf'])) {
                    $print = true;
                    break;
                }
            }

            // Check if modal should be printed
            if ($print) {

                // Format modal
                $modal  = '<div class="wccf_modal_header"><div class="wccf_modal_close">&times;</div><div style="clear: both;"></div></div>';
                $modal .= '<div class="wccf_modal_content"></div>';

                // Print modal
                echo '<div id="wccf_cart_item_product_field_editing_modal" style="display: none;"><div class="wccf_modal_wrapper">' . $modal . '</div></div>';

                // Set flag
                define('WCCF_PRODUCT_FIELD_EDITING_MODAL_PRINTE', true);
            }
        }
    }

    /**
     * Cart item product field editing field view
     *
     * @access public
     * @return void
     */
    public function ajax_get_cart_item_product_field_editing_view()
    {

        try {

            $fields = array();
            $values = array();

            // Set flag
            define('WCCF_CART_ITEM_PRODUCT_FIELD_EDITING_VIEW_REQUEST', true);

            // Default result
            $result = array('result' => 'success');

            // No cart item key or field ids set
            if (empty($_REQUEST['cart_item_key']) || empty($_REQUEST['field_ids'])) {
                throw new Exception('Required properties not sent.');
            }

            // Get cart item key
            $cart_item_key = $_REQUEST['cart_item_key'];

            // Get cart item
            $cart_item = RightPress_Help::get_wc_cart_item_by_key($cart_item_key);

            // Cart item does not exist
            if (!$cart_item) {
                throw new Exception('Cart item does not exist.');
            }

            // Get field ids
            $field_ids = $_REQUEST['field_ids'];

            // Iterate over field ids
            foreach ($field_ids as $field_id) {

                // Cart item does not have value for such field
                if (!isset($cart_item['wccf'][$field_id])) {
                    throw new Exception('Cart item does not have value for such field.');
                }

                // Load field
                if ($field = WCCF_Product_Field_Controller::get($field_id)) {

                    // Add to fields
                    $fields[$field->get_id()] = $field;

                    // Add value
                    $values[$field->get_id()] = $cart_item['wccf'][$field->get_id()]['value'];
                }
                // Unable to load field
                else {

                    // Return error
                    throw new Exception('Unable to load field.');
                }
            }

            // Get fields html
            ob_start();
            WCCF_Field_Controller::print_fields($fields, null, null, null, null, $values);
            $html = ob_get_clean();

            // Add cart item key input
            $html .= '<input type="hidden" name="cart_item_key" value="' . $cart_item_key . '">';

            // Append submit button
            $html .= '<button type="submit" class="button" name="wccf_cart_item_product_field_editing_form_submit" value="' . __('Update', 'rp_wccf') . '">' . __('Update', 'rp_wccf') . '</button>';

            // Clear float
            $html .= '<div style="clear: both;"></div>';

            // Wrap html in our own form
            $html = '<form method="post" id="wccf_cart_item_product_field_editing_form" data-wccf-cart-item-key="' . $cart_item_key . '">' . $html . '</form>';

            // Set fields html to result
            $result['html'] = $html;

            // Send result
            echo json_encode($result);
        }
        catch (Exception $e) {

            echo json_encode(array(
                'result' => 'error',
            ));
        }

        exit;
    }

    /**
     * Update cart item product field values
     */
    public function ajax_update_cart_item_product_field_values()
    {

        try {

            // Default result
            $result = array('result' => 'success');

            // Store values to update
            $update = array();

            // Get parsed request data
            $data = RightPress_Help::get_parsed_ajax_request_data();

            // No cart item key or product field values set
            if (empty($data['cart_item_key']) || (empty($data['wccf']['product_field']) && empty($data['wccf_ignore']['product_field']))) {
                throw new Exception('Required properties not sent.');
            }

            // Get cart item key
            $cart_item_key = $data['cart_item_key'];

            // Cart item does not exist
            if (!isset(WC()->cart->cart_contents[$cart_item_key])) {
                throw new Exception('Cart item does not exist.');
            }

            // Get cart item by key
            $cart_item = RightPress_Help::get_wc_cart_item_by_key($cart_item_key);

            // Cart item does not have any existing values
            if (empty($cart_item['wccf'])) {
                throw new Exception('Cart item does not have any existing field values.');
            }

            // Reference raw field values from data
            $raw_values = !empty($data['wccf_ignore']['product_field']) ? $data['wccf_ignore']['product_field'] : $data['wccf']['product_field'];

            if (!empty($data['wccf_ignore']['product_field']) && !empty($data['wccf']['product_field'])) {
                foreach ($data['wccf']['product_field'] as $field_id => $value) {
                    $raw_values[$field_id] = $value;
                }
            }

            // Load all enabled product fields
            $fields = WCCF_Product_Field_Controller::get_all();

            // Filter out fields
            foreach ($fields as $field_id => $field) {

                // Field does not have existing value on cart item
                if (!isset($cart_item['wccf'][$field_id])) {
                    unset($fields[$field_id]);
                }

                // Updated value not passed
                if (!isset($raw_values[$field_id])) {
                    unset($fields[$field_id]);
                }
            }

            // Extract posted field values
            $values = WCCF_Field_Controller::extract_posted_values('product_field', array(
                'fields'    => $fields,
                'posted'    => $data,
            ));

            // Instantiate WP_Error object
            $wp_errors = new WP_Error();

            // Validate field values
            $validation_result = WCCF_Field_Controller::sanitize_field_values($values, array(
                'fields'                    => $fields,
                'skip_frontend_validation'  => true,
                'validate_only'             => true,
                'wp_errors'                 => $wp_errors,
            ));

            // Validation failed
            if (!$validation_result) {

                // Handle validation error
                throw new RightPress_Exception('cart_item_product_field_validation_failed', 'Cart item product field validation failed.', array(
                    'error_messages' => $wp_errors->get_error_messages(),
                ));
            }

            // Sanitize field values
            $sanitized_values = WCCF_Field_Controller::sanitize_field_values($values, array(
                'fields'                    => $fields,
                'skip_frontend_validation'  => true,
                'store_empty_value'         => true,
            ));

            // Cart updated flag
            $cart_updated = false;

            // Iterate over sanitized field values
            foreach ($sanitized_values as $field_id => $sanitized_value) {

                // New value is not empty
                if (!empty($sanitized_value['value']) || $sanitized_value['value'] === '0') {

                    // Make sure value does not match existing value
                    if (WC()->cart->cart_contents[$cart_item_key]['wccf'][$field_id] !== $sanitized_value) {

                        // Set updated value
                        WC()->cart->cart_contents[$cart_item_key]['wccf'][$field_id] = $sanitized_value;

                        // Set flag
                        $cart_updated = true;
                    }
                }
                // New value is empty
                else {

                    // Clear value
                    unset(WC()->cart->cart_contents[$cart_item_key]['wccf'][$field_id]);

                    // Set flag
                    $cart_updated = true;
                }
            }

            // Check if cart was updated
            if ($cart_updated) {

                // Maybe clear empty cart item data array
                if (isset(WC()->cart->cart_contents[$cart_item_key]['wccf']) && empty(WC()->cart->cart_contents[$cart_item_key]['wccf'])) {
                    unset(WC()->cart->cart_contents[$cart_item_key]['wccf']);
                    unset(WC()->cart->cart_contents[$cart_item_key]['wccf_version']);
                }

                // Update session
                WC()->cart->set_session();
            }

            // Send result
            echo json_encode($result);
        }
        catch (Exception $e) {

            // Get validation error messages
            if (is_a($e, 'RightPress_Exception') && $e->get_error_code() === 'cart_item_product_field_validation_failed') {

                // Get error data
                $error_data = $e->get_error_data();

                // Set validation error messages
                $validation_error_messages = $error_data['error_messages'];
            }
            else {
                $validation_error_messages = null;
            }

            echo json_encode(array(
                'result'                    => 'error',
                'validation_error_messages' => $validation_error_messages,
            ));
        }

        exit;
    }





}

WCCF_WC_Cart::get_instance();

}
