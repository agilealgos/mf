<?php
namespace ReyCore\ACF;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use ReyCore\Plugin;

class Helper {

	public static $default_supported_singular_post_types = [];

	public function __construct(){
		add_filter( 'reycore/query-control/autocomplete', [$this, 'query_control_autocomplete'], 10, 2);
		add_filter( 'reycore/query-control/values', [$this, 'query_control_values'], 10, 2);
	}

	public static function get_acf_fields( $types = [] ){

		if ( function_exists( 'acf_get_field_groups' ) ) {
			$acf_groups = acf_get_field_groups();
		}

		$default_types = [
			'text',
			'textarea',
			'number',
			'email',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'true_false',
			'oembed',
			'google_map',
			'date_picker',
			'time_picker',
			'date_time_picker',
			'color_picker',
			'image',
		];

		$options = [];

		$options_page_groups_ids = [];

		if ( function_exists( 'acf_options_page' ) ) {

			$pages = acf_options_page()->get_pages();

			foreach ( $pages as $slug => $page ) {

				$options_page_groups = acf_get_field_groups( [
					'options_page' => $slug,
				] );

				foreach ( $options_page_groups as $options_page_group ) {
					$options_page_groups_ids[ $options_page_group['key'] ] = $page['post_id'];
				}

			}
		}

		foreach ( $acf_groups as $acf_group ) {

			if ( function_exists( 'acf_get_fields' ) ) {
				if ( isset( $acf_group['ID'] ) && ! empty( $acf_group['ID'] ) ) {
					$fields = acf_get_fields( $acf_group['ID'] );
				} else {
					$fields = acf_get_fields( $acf_group );
				}
			}


			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field ) {

				if ( ! in_array( $field['type'], $types, true ) ) {
					continue;
				}

				if( array_key_exists($field['parent'], $options_page_groups_ids) ){
					$key = $field['key'] . ':' . $field['name'] . ':' . $options_page_groups_ids[$field['parent']];
				}
				else {
					$key = $field['key'] . ':' . $field['name'];
				}

				$options[ $key ] = sprintf('%s > %s', $acf_group['title'], $field['label']);

			}

			if ( empty( $options ) ) {
				continue;
			}

		}

		return $options;

	}

	function query_control_values($results, $data){

		if( ! isset($data['query_args']['type']) ){
			return $results;
		}

		if( $data['query_args']['type'] !== 'acf' ){
			return $results;
		}

		$field_types = isset($data['query_args']['field_types']) ? $data['query_args']['field_types'] : [];
		$fields = self::get_acf_fields( $field_types );

		foreach ((array) $data['values'] as $id) {
			if( isset($fields[$id]) ){
				$results[ $id ] = $fields[$id];
			}
		}

		return $results;
	}

	function query_control_autocomplete($results, $data){

		if( ! isset($data['query_args']['type']) ){
			return $results;
		}

		if( $data['query_args']['type'] !== 'acf' ){
			return $results;
		}

		$field_types = isset($data['query_args']['field_types']) ? $data['query_args']['field_types'] : [];
		$fields = self::get_acf_fields( $field_types );

		foreach( $fields as $id => $text ){
			if( strpos($id, $data['q']) !== false || strpos(strtolower($text), strtolower($data['q'])) !== false ){
				$results[] = [
					'id' 	=> $id,
					'text' 	=> $text,
				];
			}
		}

		return $results;
	}

	/**
	 * Check if is exporting
	 *
	 * @since 1.6.10
	 **/
	public static function is_exporting()
	{
		if( isset($_REQUEST['page']) && $_REQUEST['page'] === 'acf-tools' && isset($_REQUEST['tool']) && $_REQUEST['tool'] === 'export' ){
			return true;
		}
		if( isset($_REQUEST['page']) && $_REQUEST['page'] === 'acf-tools' && isset($_REQUEST['action']) && $_REQUEST['action'] === 'download' ){
			return true;
		}
		return false;
	}

	/**
	 * Check if editing group
	 *
	 * @since 1.6.10
	 **/
	public static function is_editing_group()
	{
		return (get_post_type() === 'acf-field-group' && isset($_REQUEST['action']) && $_REQUEST['action'] === 'edit') ||
			(isset($_REQUEST['page']) && $_REQUEST['page'] === 'acf-tools');
	}

	/**
	 * Check if editing group
	 *
	 * @since 1.7.3
	 **/
	public static function prevent_export_dynamic_field() {
		return self::is_exporting() || self::is_editing_group();
	}

	/**
	 * ACF Image ID to ELementor Media control ID & URL
	 *
	 * @since 1.0.0
	 **/
	public static function image_to_elementor_image( $image_id )
	{
		$url = '';

		if( $image_id ){
			$url_array = wp_get_attachment_image_src( absint( $image_id ), 'full' );
			if( isset($url_array[0]) ){
				$url = $url_array[0];
			}
		}

		return [
			'url' => $url,
			'id' => $image_id ? absint( $image_id ) : ''
		];
	}

	/**
	 * Populate ACF's supported post types for Page Settings
	 *
	 * @since 1.6.6
	 */
	public static function default_supported_singular_post_types( $t = '' ){

		if( empty( self::$default_supported_singular_post_types ) ){

			$types = [
				'post_type' => [ 'post', 'page', 'product', 'rey-templates' ],
				'taxonomy' => [ 'category', 'product_cat', 'product_tag', 'pa_brand' ],
			];

			// automatically add product attributes which are public
			if( function_exists('wc_get_attribute_taxonomies') ):
				foreach ( wc_get_attribute_taxonomies() as $attribute ) {
					if( (bool) $attribute->attribute_public ){
						if( ($attr_name = wc_attribute_taxonomy_name($attribute->attribute_name)) && ! in_array($attr_name, $types['taxonomy'], true) ){
							$types['taxonomy'][] = $attr_name;
						}
					}
				}
			endif;

			self::$default_supported_singular_post_types = $types;

		}
		else {
			$types = self::$default_supported_singular_post_types;
		}

		if( isset($types[$t]) ){
			return $types[$t];
		}

		return $types;
	}

	public static function get_container_padding_placeholders( $edge = 'top' ){

		$prop = 'padding-';

		$defaults = [
			'top' => '50',
			'bottom' => '90',
		];

		if( ($cp = get_theme_mod('content_padding')) ){
			if( isset( $cp[ $prop . $edge ] ) && ($cp_edge = $cp[ $prop . $edge]) ){
				return $cp_edge;
			}
		}

		return $defaults[ $edge ];
	}

	public static function get_field_from_elementor( $key ){

		$parts = explode(':', $key);
		$field = $parts[0];
		$post_id = get_queried_object();

		// error_log(var_export( [
		// 	get_the_ID(),
		// 	get_queried_object_id(),
		// 	$key
		// ], true));

		// has option page
		if( count($parts) > 2 ){
			$post_id = $parts[2];
		}

		if( is_tax() ){
			if( isset($post_id->term_id) && ($tax = $post_id) ){
				$post_id = $tax->term_id;
				// if( reycore__is_multilanguage() ){
				// 	$post_id = apply_filters('reycore/translate_ids', $post_id, $tax->taxonomy );
				// }
			}
			$post_id = get_term_by('term_taxonomy_id', $post_id);
		}
		else {
			// if( reycore__is_multilanguage() ){
			// 	$post_id = apply_filters('reycore/translate_ids', $post_id );
			// }
		}

		return get_field($field, $post_id );
	}
}
