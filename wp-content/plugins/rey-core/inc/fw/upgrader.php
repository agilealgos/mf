<?php
namespace Rey;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Upgrader
{

	const THEME_SLUG = 'rey';

	const WP_THEME_SLUG = 'theme';

	private $args = [];

	private $item = [];

	public function __construct( $args = [] )
	{

		$this->args = wp_parse_args($args, [
			'slug'     => '',
			'version'  => '',
			'basename' => '',
			'hook'     => '',
		]);

		$wp_slug = self::THEME_SLUG === $this->args['slug'] ? self::WP_THEME_SLUG : $this->args['slug'];

		if( ! class_exists('\ReyTheme_API') ){
			return ['error' => 'API Missing'];
		}

		$url = \ReyTheme_API::getInstance()->get_download_url( $wp_slug, $this->args['version'] );

		if( ! self::valid_url( $url ) ){
			return ['error' => 'Invalid url.'];
		}

		$this->item = [
			'type'     => 'plugin',
			'url'      => $url,
			'version'  => $this->args['version'],
			'slug'     => $this->args['slug'],
			'basename' => $this->args['basename'],
		];

		if( self::WP_THEME_SLUG === $wp_slug ){
			$this->item['type'] = self::WP_THEME_SLUG;
		}

		$this->apply_package();

		return $this->upgrade();
	}

	/**
	 * Refresh the update transient.
	 *
	 * @since 2.0.0
	 */
	private function apply_package() {

		$update_transient = get_site_transient( "update_{$this->item['type']}s" );

		if ( ! is_object( $update_transient ) ) {
			$update_transient = new \stdClass();
		}

		if ( 'theme' === $this->item['type'] ) {
			$item_info                = [];
			$item_info['new_version'] = $this->item['version'];
			$item_info['plugin']      = $this->item['basename'];
			$item_info['slug']        = $this->item['slug'];
			$item_info['package']     = $this->item['url'];
		} else {
			$item_info              = new \stdClass();
			$item_info->new_version = $this->item['version'];
			$item_info->plugin      = $this->item['basename'];
			$item_info->slug        = $this->item['slug'];
			$item_info->package     = $this->item['url'];
		}

		$update_transient->response[ $this->item['basename'] ] = $item_info;

		remove_all_filters( "pre_set_site_transient_update_{$this->item['type']}s" );
		set_site_transient( "update_{$this->item['type']}s", $update_transient );
	}

	/**
	 * Upgrade
	 *
	 * @since 2.0.0
	 */
	private function upgrade() {

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = ucfirst( $this->item['type'] ) . '_Upgrader';
		$upgrader = new $upgrader( $skin );
		$result   = $upgrader->bulk_upgrade( [ $this->item['basename'] ] );

		if ( is_array( reset( $result ) ) ) {

			if( $this->args['hook'] ){
				do_action($this->args['hook'], $this->item);
			}

			return $result;
		}

		return ['error' => $skin->get_upgrade_messages()];
	}

	public static function valid_url($url)
	{
		$response = wp_safe_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return 200 === wp_remote_retrieve_response_code( $response );
	}
}
