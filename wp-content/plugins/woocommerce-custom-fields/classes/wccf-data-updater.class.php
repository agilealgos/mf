<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RightPress_Data_Updater')) {
    RightPress_Loader::load_class_collection('data-updater');
}

/**
 * Data Updater
 *
 * @class WCCF_Data_Updater
 * @package WooCommerce Custom Fields
 * @author RightPress
 */

class WCCF_Data_Updater extends RightPress_Data_Updater implements RightPress_Data_Updater_Interface
{

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

        // Construct parent
        parent::__construct();
    }

    /**
     * Get plugin version
     *
     * @access public
     * @return string
     */
    public function get_plugin_version()
    {

        return WCCF_VERSION;
    }

    /**
     * Get plugin private prefix
     *
     * @access public
     * @return string
     */
    public function get_plugin_private_prefix()
    {

        return WCCF_PLUGIN_PRIVATE_PREFIX;
    }

    /**
     * Get custom terms
     *
     * @access protected
     * @return array
     */
    public function get_custom_terms()
    {

        $terms = array();

        // Get post object controller classes
        $controller_classes = WCCF_Post_Object_Controller::get_post_object_controller_classes();

        // Iterate over post object controller classes
        foreach ($controller_classes as $controller_class) {

            // Get controller instance
            $controller = $controller_class::get_instance();

            // Iterate over taxonomies
            foreach ($controller->get_taxonomies() as $key => $labels) {

                // Format full taxonomy key
                $taxonomy_key = $controller->get_post_type() . '_' . $key;

                // Get terms for current taxonomy
                $method = 'get_' . $key . '_list';
                $current_terms = $controller->$method();

                // Set to main array
                $terms[$taxonomy_key] = $current_terms;
            }
        }

        return $terms;
    }

    /**
     * Get custom capabilities
     *
     * @access public
     * @return string
     */
    public function get_custom_capabilities()
    {

        $capabilities = array();

        // Core capabilities
        $capabilities['core'] = array(
            WCCF::get_admin_capability()
        );

        // Define capability types
        $capability_types = array(
            'wccf_product_field',
            'wccf_product_prop',
            'wccf_checkout_field',
            'wccf_order_field',
            'wccf_user_field',
        );

        // Iterate over capability types
        foreach ($capability_types as $capability_type) {

            // Generate list of capabilities for current capability type
            $capabilities[$capability_type] = array(

                // Post type capabilities
                "edit_{$capability_type}",
                "read_{$capability_type}",
                "delete_{$capability_type}",
                "edit_{$capability_type}s",
                "edit_others_{$capability_type}s",
                "publish_{$capability_type}s",
                "read_private_{$capability_type}s",
                "delete_{$capability_type}s",
                "delete_private_{$capability_type}s",
                "delete_published_{$capability_type}s",
                "delete_others_{$capability_type}s",
                "edit_private_{$capability_type}s",
                "edit_published_{$capability_type}s",

                // Post term capabilities
                "manage_{$capability_type}_terms",
                "edit_{$capability_type}_terms",
                "delete_{$capability_type}_terms",
                "assign_{$capability_type}_terms",
            );
        }

        return $capabilities;
    }

    /**
     * Get custom tables sql
     *
     * @access public
     * @param string $table_prefix
     * @param string $collate
     * @return string
     */
    public function get_custom_tables_sql($table_prefix, $collate)
    {

        return "";
    }

    /**
     * Execute custom update procedure
     *
     * @access public
     * @return string
     */
    public function execute_custom()
    {

    }

    /**
     * Migrate settings
     *
     * @access public
     * @param array $stored
     * @param string $to_settings_version
     * @return array
     */
    public static function migrate_settings($stored, $to_settings_version)
    {

        return $stored;
    }





}

WCCF_Data_Updater::get_instance();
