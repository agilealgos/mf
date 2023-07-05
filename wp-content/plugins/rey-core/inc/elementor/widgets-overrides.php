<?php
namespace ReyCore\Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WidgetsOverrides
{

	public function __construct(){
		$this->load_elements_overrides();
	}

	/**
	 * Load custom Elementor elements overrides
	 *
	 * @since 1.0.0
	 */
	public function load_elements_overrides()
	{

		$elements = [
			'Accordion',
			'Button',
			'Column',
			'Common',
			'Container',
			'Document',
			'GlobalSettings',
			'Heading',
			'Icon',
			'IconBox',
			'ImageCarousel',
			'ImageGallery',
			'Image',
			'Kit',
			'Section',
			'Sidebar',
			'Text',
			'Video',
		];

		foreach ($elements as $element) {
			$class_name = \ReyCore\Helper::fix_class_name($element, 'Elementor\Custom');
			new $class_name();
		}
	}


	/**
	 * Render Custom CSS control in Section & Container
	 *
	 * @param object $element
	 * @return void
	 */
	public static function custom_css_controls( $element ){

		$element->start_controls_section(
			'section_rey_custom_CSS',
			[
				'label' => sprintf( '<span>%s</span><span class="rey-hasStylesNotice">%s</span>', __( 'Custom CSS', 'rey-core' ) , __( 'Has Styles!', 'rey-core' ) ) . \ReyCore\Elementor\Helper::rey_badge(),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				'hide_in_inner' => true,
			]
		);

		$uid = 'SELECTOR';
		$uid_inner = 'SELECTOR-INNER';

		$css_desc = sprintf(__('<p class="rey-addMargin">Click to insert selector: <span class="rey-selectorCss js-insertToEditor" data-uid="%1$s" title="Click to insert">%1$s {}</span> or <span class="rey-selectorCss js-insertToEditor" data-uid="%2$s" title="Click to insert">%2$s {}</span></p>', 'rey-core') , $uid, $uid_inner );

		$inner_mq = "\n  $uid {\n    \n  }\n";

		$css_desc .= sprintf("<p class='rey-addMargin'><span></span><select class='js-insertSnippetToEditor'>
			<option value=''>%s</option>
			<option value='@media (max-width:767px) {" . $inner_mq . "}'>< 767px (Mobile only)</option>
			<option value='@media (max-width:1024px) {" . $inner_mq . "}'>< 1024px (Mobiles & Tablet)</option>
			<option value='@media (min-width:768px) and (max-width:1024px) {" . $inner_mq . "}'>768px to 1024px (Tablet only)</option>
			<option value='@media (min-width:768px) {" . $inner_mq . "}'>> 768px (Tablet & Desktop)</option>
			<option value='@media (min-width:1025px) {" . $inner_mq . "}'>> 1025px (Desktop only)</option>
			<option value='@media (min-width:1025px) and (max-width:1440px) {" . $inner_mq . "}'>1025px to 1440px (Desktop, until 1440px)</option>
			<option value='@media (min-width:1441px) {" . $inner_mq . "}'>> 1441px (Desktop, from 1441px)</option>
		</select></p>", esc_html__('Insert media query snippet:', 'rey-core') );

		$element->add_control(
			'rey_custom_css',
			[
				'type' => \Elementor\Controls_Manager::CODE,
				'label' => esc_html__('Custom CSS', 'rey-core'),
				'language' => 'css',
				'render_type' => 'ui',
				'show_label' => false,
				'separator' => 'none',
				'description' =>  $css_desc,
			]
		);

		$element->end_controls_section();

	}

	/**
	* Render Custom CSS control in Section & Container
	*
	* @param object $element
	* @return void
	*/
   public static function hide_element_on( $element ){

		$element->add_control(
			'rey_hide_on',
			[
				'label' => esc_html__( 'Hide for:', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Don\'t hide', 'rey-core' ),
					'logged-in'  => esc_html__( 'Logged IN users', 'rey-core' ),
					'logged-out'  => esc_html__( 'Logged OUT users', 'rey-core' ),
				],
			]
		);

   }

	/**
	* Render Custom CSS control in Section & Container
	*
	* @param object $element
	* @return void
	*/
   public static function horizontal_offset_for_mobile( $element, $extra_desc = '' ){

	$element->add_control(
		'rey_mobile_offset',
		[
			'label' => __( 'Mobile Horizontal Scroll', 'rey-core' ) . \ReyCore\Elementor\Helper::rey_badge(),
			'description' => __( 'You can force this element\'s container to stretch on mobiles and display a horizontal scrollbar. ', 'rey-core' ) . $extra_desc,
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'rey-mobiOffset',
			'default' => '',
			'prefix_class' => '',
			'separator' => 'before'
		]
	);

		$element->add_control(
			'rey_mobile_offset_width',
			[
				'label' => esc_html__( 'Stretch width', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'max' => 3000,
				'step' => 1,
				'selectors' => [
					'{{WRAPPER}}' => '--mobi-offset: {{SIZE}}px;',
				],
				'condition' => [
					'rey_mobile_offset!' => '',
				],
			]
		);

		$element->add_control(
			'rey_mobile_offset_gutter',
			[
				'label' => esc_html__( 'Include Side Gap', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'rey-mobiOffset--gap',
				'default' => '',
				'prefix_class' => '',
				'condition' => [
					'rey_mobile_offset!' => '',
				],
			]
		);

	}

	public static function should_render_element_or_widget( $should_render, $element ){

		if( reycore__elementor_edit_mode() ) {
			return $should_render;
		}

		if( $hide_on = $element->get_settings('rey_hide_on') ){

			$is_logged_in = is_user_logged_in();

			if( $hide_on === 'logged-in' && $is_logged_in ){
				return false;
			}
			else if( $hide_on === 'logged-out' && ! $is_logged_in ){
				return false;
			}

		}

		return $should_render;
	}

}
