<?php
namespace ReyCore\Modules\MiniCartRecents;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Customizer
{
	public function __construct()
	{
		add_action('reycore/customizer/control=header_cart__close_extend', [$this, 'add_controls'], 10, 2);
	}

	function add_controls( $control_args, $section ){

		$section->add_control_before( $control_args, [
			'type'        => 'toggle',
			'settings'    => 'header_cart__recent',
			'label'       => esc_html__( 'Show "Recently viewed" products', 'rey-core' ),
			'help' => [
				esc_html__( 'This will show up a list of 10 of the most recently viewed products.', 'rey-core')
			],
			'default'     => false,
			'separator'   => 'before',
		] );

	}

}
