<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Price Override In Shop
 *
 * @class WCCF_WC_Product_Price_Shop
 * @package WooCommerce Custom Fields
 * @author RightPress
 */
if (!class_exists('WCCF_WC_Product_Price_Shop')) {

class WCCF_WC_Product_Price_Shop
{

    // TODO: Need to optimize/rewrite everything related to pricing, field value retrieval, validation and sanitization - too many exceptions (is test, is live update, is shop price calculation etc), need to standardize

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 20;

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

        // Set up on init so that we have access to settings
        add_action('init', array($this, 'init'));
    }

    /**
     * Set up on init
     *
     * @access public
     * @return void
     */
    public function init()
    {

        // There are no active fields that adjust product prices
        if (!WCCF_WC_Product::prices_subject_to_adjustment()) {
            return;
        }

        // Add product shop price calculation callback
        add_filter('rightpress_product_price_shop_calculation_callbacks', array($this, 'add_calculation_callback'), $this->rightpress_hook_position);

        // Add cache hash data
        add_filter('rightpress_product_price_shop_cache_hash_data', array($this, 'add_cache_hash_data'), $this->rightpress_hook_position, 4);

        // Add settings hash data
        add_filter('rightpress_product_price_shop_settings_hash_data', array($this, 'add_settings_hash_data'), $this->rightpress_hook_position, 2);

        // Maybe skip cache for this request
        add_filter('rightpress_product_price_shop_skip_cache', array($this, 'maybe_skip_cache'), $this->rightpress_hook_position, 2);
    }

    /**
     * Add product shop price calculation callback
     *
     * @access public
     * @param array $callbacks
     * @return array
     */
    public function add_calculation_callback($callbacks)
    {

        // Add callback
        $callbacks['rp_wccf'] = array($this, 'calculate_price');

        // Return list of callbacks
        return $callbacks;
    }

    /**
     * Calculate price
     *
     * @access public
     * @param array $calculation_data
     * @param string $price_type
     * @param object $product
     * @return array
     */
    public function calculate_price($calculation_data, $price_type, $product)
    {

        // Maybe skip plugin-specific calculation
        if ($this->skip_calculation($product)) {
            return $calculation_data;
        }

        // Iterate over base price alternatives
        foreach ($calculation_data['alternatives'] as $base_price_candidate_key => $alternative_data) {

            // Price must not be empty
            if ($alternative_data['price'] === '') {
                continue;
            }

            // Get adjusted price
            $adjusted_price = WCCF_Pricing::get_adjusted_price_for_product($alternative_data['price'], $product);

            // Check if price was adjusted
            if (RightPress_Product_Price::prices_differ($adjusted_price, $alternative_data['price'])) {

                // Set new price
                $calculation_data['alternatives'][$base_price_candidate_key]['price'] = $adjusted_price;

                // Add change data
                $calculation_data['changes']['rp_wccf'][RightPress_Help::get_hash()] = array();
            }
        }

        // Return calculation data
        return $calculation_data;
    }

    /**
     * Maybe skip price calculation
     *
     * @access public
     * @param object $product
     * @return bool
     */
    public function skip_calculation($product)
    {

        // Skip pricing adjustment for product based on other conditions
        if (WCCF_WC_Product::skip_pricing($product)) {
            return true;
        }

        // Do not skip
        return false;
    }

    /**
     * Reset cached price for product
     *
     * @access public
     * @param mixed $product
     * @return void
     */
    public static function clear_cached_price($product)
    {

        // Load product
        if (!is_object($product)) {
            $product = wc_get_product($product);
        }

        // Switch to parent product for variations
        if ($product->is_type('variation')) {
            $product = wc_get_product($product->get_parent_id());
        }

        // Clear price cache for product
        RightPress_Product_Price_Shop::clear_cache_for_product($product);
    }

    /**
     * Add cache hash data
     *
     * @access public
     * @param array $hash_data
     * @param float $price
     * @param string $price_type
     * @param object $product
     * @return array
     */
    public function add_cache_hash_data($hash_data, $price, $price_type, $product)
    {

        // Add data
        $hash_data['rp_wccf'] = array(
            WCCF_WC_Product::skip_product_fields($product),
        );

        // Return hash data
        return $hash_data;
    }

    /**
     * Add settings hash data
     *
     * @access public
     * @param array $hash_data
     * @param null $deprecated_1
     * @return array
     */
    public function add_settings_hash_data($hash_data, $deprecated_1)
    {

        // Add data
        $hash_data['rp_wccf'] = array(
            WCCF_Settings::get_objects_revision(),
            WCCF_Settings::get('_all'),
        );

        // Return hash data
        return $hash_data;
    }

    /**
     * Maybe skip cache
     *
     * @access public
     * @param bool $skip
     * @param object $product
     * @return bool
     */
    public function maybe_skip_cache($skip, $product)
    {

        // Skip for this call only
        if ($this->skip_calculation($product)) {
            return true;
        }

        return $skip;
    }





}

WCCF_WC_Product_Price_Shop::get_instance();

}
