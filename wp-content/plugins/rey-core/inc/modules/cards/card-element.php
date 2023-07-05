<?php
namespace ReyCore\Modules\Cards;

use ReyCore\Modules\Cards\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( Base::instance() ):

class CardElement extends \ReyCore\Elementor\WidgetsBase {

	public $_settings = [];
	public $_items = [];

	public $selectors = [
		'wrapper'    => '{{WRAPPER}}',
		'card'       => '{{WRAPPER}} .rey-card',
		'card_hover' => '{{WRAPPER}} .rey-card:hover',
		'media_link' => '{{WRAPPER}} .__media-link',
		'media'      => '{{WRAPPER}} .__media',
		'title'      => '{{WRAPPER}} .__captionTitle',
		'title_a'    => '{{WRAPPER}} .__captionTitle, {{WRAPPER}} .__captionTitle a',
	];

	public $supports = [
		'shadow' => false
	];

	public function element_support( $s ){
		return isset($this->supports[$s]) && $this->supports[$s];
	}

	public function add_element_controls(){}

	public function register_controls() {
		$this->add_element_controls();
	}

	public function controls__content(){

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'rey-core' ),
			]
		);

			$sources = [
				'images'  => esc_html__( 'Images', 'rey-core' ),
				'custom'  => esc_html__( 'Custom content', 'rey-core' ),
				'posts'   => esc_html__( 'Posts', 'rey-core' ),
			];

			if( class_exists('\WooCommerce') ){
				$sources['product_cat'] = esc_html__( 'Product Categories', 'rey-core' );
			}

			$this->add_control(
				'source',
				[
					'label' => esc_html__( 'Data Source', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'images',
					'options' => $sources,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Image_Size::get_type(),
				[
					'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
					'default' => 'large',
				]
			);

		$this->end_controls_section();


	}

	public function controls__teaser(){

		$this->start_controls_section(
			'section_teaser',
			[
				'label' => __( 'Teaser', 'rey-core' ),
			]
		);

			$this->add_control(
				'teaser_gs',
				[
					'label_block' => true,
					'label'       => __( 'Select Global Section', 'rey-core' ),
					'description' => __( 'Include a global section inside the list, at a specific chosen location.', 'rey-core' ),
					'type'        => 'rey-query',
					'default'     => '',
					'placeholder' => esc_html__('- Select -', 'rey-core'),
					'query_args'  => [
						'type'      => 'posts',
						'post_type' => \ReyCore\Elementor\GlobalSections::POST_TYPE,
						'meta'      => [
							'meta_key'   => 'gs_type',
							'meta_value' => 'generic',
						]
					],
				]
			);

			$this->add_control(
				'teaser_position',
				[
					'label' => esc_html__( 'Position', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'last',
					'options' => [
						'first'  => esc_html__( 'First', 'rey-core' ),
						'last'  => esc_html__( 'Last', 'rey-core' ),
						'custom'  => esc_html__( 'Custom', 'rey-core' ),
					],
					'condition' => [
						'teaser_gs!' => '',
					],
				]
			);

			$this->add_control(
				'teaser_pos_custom',
				[
					'label' => esc_html__( 'Position', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 1,
					'min' => 1,
					'max' => 20,
					'step' => 1,
					'condition' => [
						'teaser_gs!' => '',
						'teaser_position' => 'custom',
					],
				]
			);

			$this->add_control(
				'teaser_pos_repeat',
				[
					'label' => esc_html__( 'Repeat every nth', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'min' => 2,
					'max' => 100,
					'step' => 1,
					'condition' => [
						'teaser_gs!' => '',
					],
				]
			);

		$this->end_controls_section();

	}

	public function controls__images(){

		$this->start_controls_section(
			'section_images',
			[
				'label' => __( 'Images', 'rey-core' ),
				'condition' => [
					'source' => 'images',
				],
			]
		);

			$this->add_control(
				'images',
				[
					'label' => esc_html__( 'Add Images', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::GALLERY,
					'default' => [],
					'show_label' => false,
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$this->add_control(
				'images_link',
				[
					'label' => esc_html__( 'Images links', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'media',
					'options' => [
						'media'  => esc_html__( 'Link to media', 'rey-core' ),
						'all'  => esc_html__( 'Link all', 'rey-core' ),
						''  => esc_html__( 'Disable link', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'images_link_all',
				[
					'label' => __( 'Link', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => __( 'https://your-link.com', 'rey-core' ),
					'default' => [
						'url' => '#',
					],
					'condition' => [
						'images_link' => 'all',
					],
				]
			);

			$this->add_control(
				'images_caption',
				[
					'label' => esc_html__( 'Display Caption', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
				]
			);

		$this->end_controls_section();

	}

	public function controls__product_cat_query(){

		if( ! class_exists('\WooCommerce') ){
			return;
		}

		$this->start_controls_section(
			'section_product_cat_query',
			[
				'label' => __( 'Product Categories', 'rey-core' ),
				'condition' => [
					'source' => 'product_cat',
				],
			]
		);

			$this->add_control(
				'product_cat_type',
				[
					'label' => esc_html__( 'Query Type', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'all',
					'options' => [
						'all'  => esc_html__( 'All', 'rey-core' ),
						'manual'  => esc_html__( 'Manual selection', 'rey-core' ),
						'top-parents'  => esc_html__( 'All parents', 'rey-core' ),
						'siblings'  => esc_html__( 'Sibling Categories (of current)', 'rey-core' ),
						'subcategories'  => esc_html__( 'Sub-Categories (of current)', 'rey-core' ),
						// 'parents'  => esc_html__( 'Parent & Siblings (of current)', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'product_cat_limit',
				[
					'label' => __( 'Limit', 'rey-core' ),
					'description' => __( 'Select the number of items to load from query.', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 6,
					'min' => 1,
					'max' => 100,
					'condition' => [
						'product_cat_type!' => 'manual',
					],
				]
			);

			$this->add_control(
				'product_cat_exclude',
				[
					'label'       => esc_html__( 'Exclude', 'rey-core' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'type' => 'rey-query',
					'multiple' => true,
					'query_args' => [
						'type' => 'terms',
						'taxonomy' => 'product_cat',
					],
					'condition' => [
						'product_cat_type!' => 'manual',
					],
				]
			);

			$this->add_control(
				'product_cat_orderby',
				[
					'label' => __( 'Order By', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'term_order',
					'options' => [
						'name' => __( 'Name', 'rey-core' ),
						'term_id' => __( 'Term ID', 'rey-core' ),
						'menu_order' => __( 'Menu Order', 'rey-core' ),
						'count' => __( 'Count', 'rey-core' ),
						'term_order' => __( 'Term Order (Needs Objects IDs)', 'rey-core' ),
					],
					'condition' => [
						'product_cat_type!' => 'manual',
					],
				]
			);

			$this->add_control(
				'product_cat_order',
					[
					'label' => __( 'Order', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'asc' => __( 'ASC', 'rey-core' ),
						'desc' => __( 'DESC', 'rey-core' ),
					],
					'condition' => [
						'product_cat_type!' => 'manual',
					],
				]
			);

			$product_cats = new \Elementor\Repeater();

			$product_cats->add_control(
				'cat',
				[
					'label' => esc_html__('Product Category', 'rey-core'),
					'placeholder' => esc_html__('- Select category -', 'rey-core'),
					'type' => 'rey-query',
					'query_args' => [
						'type' => 'terms',
						'taxonomy' => 'product_cat',
					],
					'label_block' => true,
					'default'     => '',
				]
			);

			$this->add_control(
				'product_cats',
				[
					'label' => __( 'Select product categories', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $product_cats->get_controls(),
					'default' => [],
					'condition' => [
						'product_cat_type' => 'manual',
					],
				]
			);

			$this->add_control(
				'product_cat_show_count',
				[
					'label' => esc_html__( 'Show counters', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
				]
			);

		$this->end_controls_section();
	}

	public function controls__post_query(){

		$this->start_controls_section(
			'section_post_query',
			[
				'label' => __( 'Posts query', 'rey-core' ),
				'condition' => [
					'source' => 'posts',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Limit', 'rey-core' ),
				'description' => __( 'Select the number of items to load from query.', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 8,
				'min' => 1,
				'max' => 100,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label' => esc_html__( 'Post Type', 'rey-core' ),
				'default' => 'post',
				'type' => 'rey-ajax-list',
				'query_args' => [
					'request' => [__CLASS__, 'get_post_types_list_except_product'],
				],
			]
		);

		$this->add_control(
			'query_type',
			[
				'label' => esc_html__('Query Type', 'rey-core'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => [
					'recent'           => esc_html__('Recent', 'rey-core'),
					'manual-selection' => esc_html__('Manual Selection', 'rey-core'),
					'current-query' => esc_html__('Current Query', 'rey-core'),
				],
				'condition' => [
					'post_type!' => 'page'
				],
			]
		);

		$this->add_control(
			'all_taxonomies',
			[
				'label' => esc_html__('Taxonomy Term', 'rey-core'),
				'placeholder' => esc_html__('- Select term -', 'rey-core'),
				'type' => 'rey-query',
				'query_args' => [
					'type' => 'terms',
					'taxonomy' => 'all_taxonomies',
				],
				'label_block' => true,
				'multiple' => true,
				'default'     => [],
				'condition' => [
					'query_type' => 'recent',
					'post_type!' => ['', 'page']
				],
			]
		);

		// Advanced settings
		$this->add_control(
			'include',
			[
				'label'       => esc_html__( 'Manual include', 'rey-core' ),
				'description' => __( 'Add posts IDs separated by comma.', 'rey-core' ),
				'label_block' => true,
				'type' => 'rey-query',
				'multiple' => true,
				'query_args' => [
					'type' => 'posts',
					'post_type' => '{post_type}',
				],
				'condition' => [
					'query_type' => 'manual-selection',
				],
			]
		);

		$this->add_control(
			'exclude',
			[
				'label'       => esc_html__( 'Exclude', 'rey-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'type' => 'rey-query',
				'multiple' => true,
				'query_args' => [
					'type' => 'posts',
					// 'post_type' => 'product',
				],
				'condition' => [
					'query_type!' => 'manual-selection',
				],
			]
		);

		/* wip
		$this->add_control(
			'posts_meta_query',
			[
				'label' => esc_html__( 'Use Meta Query', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			]
		); */

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date' => __( 'Date', 'rey-core' ),
					'post_title' => __( 'Title', 'rey-core' ),
					'menu_order' => __( 'Menu Order', 'rey-core' ),
					'rand' => __( 'Random', 'rey-core' ),
				],
				// 'condition' => [
				// 	'query_type!' => 'manual-selection',
				// ],
			]
		);

		$this->add_control(
			'order',
				[
				'label' => __( 'Order', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __( 'ASC', 'rey-core' ),
					'desc' => __( 'DESC', 'rey-core' ),
				],
				// 'condition' => [
				// 	'query_type!' => 'manual-selection',
				// ],
			]
		);

		$this->add_control(
			'exclude_duplicates',
			[
				'label' => __( 'Exclude Duplicates', 'rey-core' ),
				'description' => __( 'Exclude duplicates that were already loaded in this page', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'exclude_without_image',
			[
				'label' => __( 'Exclude posts without image', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'posts_map_label',
			[
				'label' => esc_html__( 'Use "Label" as', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( '- None -', 'rey-core' ),
					'date'  => esc_html__( 'Date', 'rey-core' ),
					'category'  => esc_html__( 'Category', 'rey-core' ),
				],
			]
		);

		$this->add_control(
			'query_id',
			[
				'label' => esc_html__( 'Custom Query ID', 'rey-core' ),
				'description' => esc_html__( 'Give your Query a custom unique id to allow server side modifications.', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'eg: my_custom_action', 'rey-core' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		/* wip

		$this->start_controls_section(
			'section_meta_query',
			[
				'label' => __( 'Meta Query', 'rey-core' ),
				'condition' => [
					'posts_meta_query! ' => '',
				],
			]
		);

			$meta_co = new \Elementor\Repeater();

			$this->add_control(
				'meta_key',
				[
					'label' => esc_html__( 'Meta Key', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'meta_value',
				[
					'label' => esc_html__( 'Meta Value', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				]
			);

			$this->add_control(
				'meta_compare',
				[
					'label' => esc_html__( 'Compare operator', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '=',
					'options' => [
						'true'  => 'True',
						'false'  => 'False',
						'null'  => 'Is Null',
						'not_null'  => 'Not Null',
						'==' => esc_html__('Is equal to', 'rey-core'),
						'!=' => esc_html__('Is not equal to', 'rey-core'),
						'>' => esc_html__('Is greater than', 'rey-core'),
						'<' => esc_html__('Is less than', 'rey-core'),
						'!=empty' => esc_html__('Is not empty', 'rey-core'),
						'==empty' => esc_html__('Is empty', 'rey-core'),
					],
				]
			);

			$this->add_control(
				'meta_conditions',
				[
					'label' => __( 'Meta Conditions', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $meta_co->get_controls(),
					'default' => [],
					'title_field' => '{{{ meta_key }}}',
				]
			);

		$this->end_controls_section();

		*/
	}

	public function controls__custom_content(){

		$this->start_controls_section(
			'section_custom_content',
			[
				'label' => __( 'Custom Items', 'rey-core' ),
				'condition' => [
					'source' => 'custom',
				],
			]
		);

		$items = new \Elementor\Repeater();

		$items->add_control(
			'image',
			[
			'label' => __( 'Image', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$items->add_control(
			'image_position',
			[
				'label' => _x( 'Image Position', 'Background Control', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => 'eg: 50% 50% (x / y)',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--bg-size-position: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$items->add_control(
			'video',
			[
				'label' => __( 'Video URL', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'description' => __( 'Link to video (YouTube, or self-hosted mp4 is recommended).', 'rey-core' ),
			]
		);

		/* TODO: Revisit based on requests

		$items->add_control(
			'overlay_color',
			[
				'label' => __( 'Overlay Background Color', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .__overlay' => 'background-color: {{VALUE}}',
				],
			]
		); */

		$items->add_control(
			'captions',
			[
				'label' => __( 'Enable Captions', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$items->add_control(
			'title',
			[
				'label'       => __( 'Title', 'rey-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'captions',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$items->add_control(
			'subtitle',
			[
				'label'       => __( 'Subtitle Text', 'rey-core' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'captions',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$items->add_control(
			'label',
			[
				'label'       => __( 'Label Text', 'rey-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'conditions' => [
					'terms' => [
						[
							'name' => 'captions',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		/* TODO: Revisit based on requests
		$items->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .__caption' => 'color: {{VALUE}}',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'captions',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
				'separator' => 'after'
			]
		); */

		$items->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Click here', 'rey-core' ),
				'placeholder' => __( 'eg: SHOP NOW', 'rey-core' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'captions',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$items->add_control(
			'button_url',
			[
				'label' => __( 'Link', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'rey-core' ),
				'default' => [
					'url' => '#',
				],
				'separator' => 'after'
			]
		);

		// No 2nd button because they would need too many options, style, color, hover color, per button

		$this->add_control(
			'carousel_items',
			[
				'label' => __( 'Items', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $items->get_controls(),
				'default' => [
					[
						'image' => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						],
						'captions' => 'yes',
						'title' => esc_html_x('Some title', 'Placeholder title', 'rey-core'),
						'subtitle' => esc_html_x('Phosfluorescently predominate pandemic applications for real-time customer service', 'Placeholder text', 'rey-core'),
						'button_text' => esc_html_x('Click here', 'Placeholder button text', 'rey-core'),
						'button_url' => [
							'url' => '#',
						],
					],
					[
						'image' => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						],
						'captions' => 'yes',
						'title' => esc_html_x('Some title', 'Placeholder title', 'rey-core'),
						'subtitle' => esc_html_x('Phosfluorescently predominate pandemic applications for real-time customer service', 'Placeholder text', 'rey-core'),
						'button_text' => esc_html_x('Click here', 'Placeholder button text', 'rey-core'),
						'button_url' => [
							'url' => '#',
						],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Get CTP list except products
	 *
	 * @since 2.4.5
	 **/
	public static function get_post_types_list_except_product()
	{
		return reycore__get_post_types_list([
			'exclude' => [
				'product'
			]
		]);
	}

	public function controls__content_styles(){

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Layout', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$cards_list = Base::instance()->get_cards_list();
			$cards_list_keys = array_keys($cards_list);

			$this->add_control(
				Base::CARD_KEY,
				[
					'label' => esc_html__( 'Select Layout', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'basic',
					'options' => $cards_list,
				]
			);

			$this->add_control(
				'card_align',
				[
					'label' => esc_html__( 'Alignment', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'left'  => esc_html__( 'Left', 'rey-core' ),
						'center'  => esc_html__( 'Center', 'rey-core' ),
						'right'  => esc_html__( 'Right', 'rey-core' ),
						'justify'  => esc_html__( 'Justified', 'rey-core' ),
						''  => esc_html__( '- Inherit -', 'rey-core' ),
					],
					'selectors' => [
						$this->selectors['card'] => 'text-align: {{VALUE}};',
					],
					'condition' => [
						Base::CARD_KEY => $cards_list_keys,
					],
				]
			);

			$this->add_control(
				'card_valign',
				[
					'label' => esc_html__( 'Vertical Alignment', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'' => esc_html__( '- Inherit -', 'rey-core' ),
						'flex-start' => esc_html__( 'Start', 'rey-core' ),
						'center' => esc_html__( 'Center', 'rey-core' ),
						'flex-end' => esc_html__( 'End', 'rey-core' ),
					],
					'selectors' => [
						$this->selectors['card'] => 'align-items: {{VALUE}}; -v-align-items: {{VALUE}};',
					],
					'condition' => [
						Base::CARD_KEY => $cards_list_keys,
					],
				]
			);

			$this->add_control(
				'card_radius',
				[
					'label' => esc_html__( 'Corner Radius', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'selectors' => [
						$this->selectors['card'] => '--card-radius: {{SIZE}}px; overflow: hidden;',
					],
					'condition' => [
						Base::CARD_KEY => $cards_list_keys,
					],
				]
			);

			$this->add_responsive_control(
				'card_padding',
				[
					'label' => __( 'Padding', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'selectors' => [
						$this->selectors['card'] => '--spacing-top:{{TOP}}px; --spacing-right:{{RIGHT}}px; --spacing-bottom:{{BOTTOM}}px; --spacing-left:{{LEFT}}px;',
					],
					'condition' => [
						Base::CARD_KEY => $cards_list_keys,
					],
				]
			);

			$this->add_responsive_control(
				'card_height',
				[
					'label' => esc_html__( 'Height', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1200,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 490,
					],
					'selectors' => [
						$this->selectors['card'] => '--item-height: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						Base::CARD_KEY => Base::instance()->get_card_supports('height')
					],
					// 'condition' => [
					// 	Base::CARD_KEY => $cards_list_keys,
					// ],
				]
			);

			$this->start_controls_tabs( 'card_styles_tabs', [
				'condition' => [
					Base::CARD_KEY => $cards_list_keys,
				],
			] );

				$this->start_controls_tab(
					'card_styles_tab',
					[
						'label' => esc_html__( 'Normal', 'rey-core' ),
					]
				);

					$this->add_control(
						'card_color',
						[
							'label' => esc_html__( 'Color', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								$this->selectors['card'] => 'color: {{VALUE}}; --color: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Background::get_type(),
						[
							'name' => 'card_bg_color',
							'types' => [ 'classic', 'gradient' ],
							'selector' => $this->selectors['card'],
							'condition' => [
								Base::CARD_KEY => Base::instance()->get_card_supports('background')
							],
							'fields_options' => [
								'color' => [
									'selectors' => [
										'{{SELECTOR}}' => 'background-color:{{VALUE}}; --bg-color: {{VALUE}};',
									],
								]
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'card_border',
							'selector' => $this->selectors['card'],
							'fields_options' => [
								'color' => [
									'selectors' => [
										'{{SELECTOR}}' => 'border-color: {{VALUE}}; --border-color: {{VALUE}};',
									],
								]
							],
						]
					);

					if( $this->element_support('shadow') ){

						$this->add_control(
							'card_shadow',
							[
								'label' => esc_html__( 'Shadow', 'rey-core' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => '',
								'options' => [
									''  => esc_html__( 'None', 'rey-core' ),
									'var(--b-shadow-1)'  => esc_html__( 'Preset #1', 'rey-core' ),
									'var(--b-shadow-2)'  => esc_html__( 'Preset #2', 'rey-core' ),
									'var(--b-shadow-3)'  => esc_html__( 'Preset #3', 'rey-core' ),
									'var(--b-shadow-4)'  => esc_html__( 'Preset #4', 'rey-core' ),
									'var(--b-shadow-5)'  => esc_html__( 'Preset #5', 'rey-core' ),
									'custom'  => esc_html__( 'Custom Box Shadow', 'rey-core' ),
								],
								'selectors' => [
									$this->selectors['card'] => 'box-shadow: {{VALUE}};',
								],
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Box_Shadow::get_type(),
							[
								'name' => 'card_custom_shadow',
								'selector' => $this->selectors['card'],
								'condition' => [
									'card_shadow' => 'custom',
								],
							]
						);
					}

				$this->end_controls_tab();

				$this->start_controls_tab(
					'card_styles_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'rey-core' ),
					]
				);

					$this->add_control(
						'card_color_hover',
						[
							'label' => esc_html__( 'Color', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								$this->selectors['card_hover'] => 'color: {{VALUE}}; --color: {{VALUE}}; --color-hover: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Background::get_type(),
						[
							'name' => 'card_bg_color_hover',
							'selector' => $this->selectors['card_hover'],
							'condition' => [
								Base::CARD_KEY => Base::instance()->get_card_supports('background')
							],
							'fields_options' => [
								'color' => [
									'selectors' => [
										'{{SELECTOR}}' => 'background-color:{{VALUE}}; --bg-color: {{VALUE}}; --bg-color-hover: {{VALUE}};',
									],
								]
							],
						]
					);

					$this->add_control(
						'card_border_color_hover',
						[
							'label' => esc_html__( 'Border Color', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								$this->selectors['card_hover'] => 'border-color: {{VALUE}}; --border-color: {{VALUE}}; --border-color-hover: {{VALUE}};',
							],
						]
					);

					if( $this->element_support('shadow') ){

						$this->add_control(
							'card_shadow_hover',
							[
								'label' => esc_html__( 'Shadow', 'rey-core' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => '',
								'options' => [
									''  => esc_html__( 'None', 'rey-core' ),
									'var(--b-shadow-1)'  => esc_html__( 'Preset #1', 'rey-core' ),
									'var(--b-shadow-2)'  => esc_html__( 'Preset #2', 'rey-core' ),
									'var(--b-shadow-3)'  => esc_html__( 'Preset #3', 'rey-core' ),
									'var(--b-shadow-4)'  => esc_html__( 'Preset #4', 'rey-core' ),
									'var(--b-shadow-5)'  => esc_html__( 'Preset #5', 'rey-core' ),
									'custom'  => esc_html__( 'Custom Box Shadow', 'rey-core' ),
								],
								'selectors' => [
									$this->selectors['card_hover'] => 'box-shadow: {{VALUE}};',
								],
							]
						);

						$this->add_group_control(
							\Elementor\Group_Control_Box_Shadow::get_type(),
							[
								'name' => 'card_custom_shadow_hover',
								'selector' => $this->selectors['card'],
								'condition' => [
									'card_shadow' => 'custom',
								],
							]
						);

					}

					$this->add_control(
						'card_hover_transition',
						[
							'label' => esc_html__( 'Transition Duration', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [],
							'range' => [
								'px' => [
									'max' => 3000,
									'step' => 50,
								],
							],
							'render_type' => 'ui',
							'separator' => 'before',
							'selectors' => [
								$this->selectors['card'] => '--transition-duration: {{SIZE}}ms;',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();


			// effect
			// box snhadow
			// border
			//

			/*
			$this->add_control(
				'clip_effect',
				[
					'label' => esc_html__( 'Clip Effect', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'condition' => [
						Base::CARD_KEY => $this->get_card_supports('clip')
					],
				]
			);
			*/

			// Individual card settings
			Base::instance()->add_cards_controls( $this );

		$this->end_controls_section();
	}

	public function controls__media_styles(){

		$this->start_controls_section(
			'section_media_style',
			[
				'label' => __( 'Media Styles', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					Base::CARD_KEY => array_keys(Base::instance()->get_cards_list()),
				],
			]
		);

			$this->add_control(
				'image_show',
				[
					'label' => esc_html__( 'Display Image', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => esc_html__( '- Inherit -', 'rey-core' ),
						'yes'  => esc_html__( 'Yes', 'rey-core' ),
						'no'  => esc_html__( 'No', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'media_fit',
				[
					'label' => esc_html__( 'Media Fit', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => esc_html__( 'Natural', 'rey-core' ),
						'cover'  => esc_html__( 'Cover', 'rey-core' ),
						'contain'  => esc_html__( 'Contain', 'rey-core' ),
					],
					'selectors' => [
						$this->selectors['media'] => 'object-fit: {{VALUE}};',
					],
					'condition' => [
						'image_show!' => 'no',
						// supports_stretch! = no
					],
				]
			);

			$this->add_responsive_control(
				'media_width',
				[
					'label' => esc_html__( 'Media Width', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 30,
							'max' => 1200,
							'step' => 1,
						],
					],
					'default' => [],
					'selectors' => [
						$this->selectors['media_link'] => '--media-max-width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						Base::CARD_KEY => Base::instance()->get_card_supports('media-width'),
						'grid_type' => 'vlist'
					],
				]
			);

			$this->add_responsive_control(
				'media_height',
				[
					'label' => esc_html__( 'Media Height', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1200,
							'step' => 1,
						],
					],
					'default' => [],
					'selectors' => [
						$this->selectors['media'] => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'media_fit!' => '',
						'image_show!' => 'no',
					],
				]
			);

			$this->add_control(
				'media_radius',
				[
					'label' => esc_html__( 'Corner Radius', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'selectors' => [
						$this->selectors['card'] => '--media-radius: {{SIZE}}px; overflow: hidden;',
					],
					'condition' => [
						'image_show!' => 'no',
					],
				]
			);

			$this->add_control(
				'overlay_heading',
				[
				   'label' => esc_html__( 'Overlay', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				[
					'name' => 'overlay_color',
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .__overlay',
					'exclude' => [ 'image' ],
					'fields_options' => [
						'background' => [
							'label' => esc_html__('Color Type', 'rey-core'),
						],
						'color' => [
							'selectors' => [
								'{{SELECTOR}}' => 'background: {{VALUE}};',
							],
						]
					],
				]
			);

			$this->add_control(
				'overlay_opacity',
				[
				   'label' => esc_html__( 'Opacity', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'max' => 1,
					'step' => 0.05,
					'selectors' => [
						'{{WRAPPER}} .__overlay' => 'opacity:{{VALUE}};',
					]
				]
			);

			$this->add_control(
				'overlay_hover_opacity',
				[
				   'label' => esc_html__( 'Opacity (Hover)', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'max' => 1,
					'step' => 0.05,
					'selectors' => [
						$this->selectors['card_hover'] . ' .__overlay' => 'opacity:{{VALUE}};',
					]
				]
			);


		$this->end_controls_section();

	}

	public function controls__title_styles(){

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title Styles', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					Base::CARD_KEY => array_keys(Base::instance()->get_cards_list()),
				],
			]
		);

			$this->add_control(
				'title_show',
				[
					'label' => esc_html__( 'Display Title', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => esc_html__( '- Inherit -', 'rey-core' ),
						'yes'  => esc_html__( 'Yes', 'rey-core' ),
						'no'  => esc_html__( 'No', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' => esc_html__( 'Title Color', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						$this->selectors['title_a'] => 'color: {{VALUE}}',
					],
					'condition' => [
						'title_show!' => 'no',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typo',
					'label' => esc_html__('Title Typography', 'rey-core'),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => $this->selectors['title'],
					'condition' => [
						'title_show!' => 'no',
					],
				]
			);

			$this->add_control(
				'title_link',
				[
					'label' => esc_html__( 'Wrap in link', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'condition' => [
						'title_show!' => 'no',
					],
				]
			);

			$this->add_responsive_control(
				'title_min_height',
				[
					'label' => __( 'Title Min. Height', 'rey-core' ) . ' (px)',
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'min' => 0,
					'max' => 300,
					'selectors' => [
						$this->selectors['title'] => 'min-height: {{VALUE}}px;',
					],
				]
			);

		$this->end_controls_section();

	}

	public function controls__subtitle_styles(){

		$this->start_controls_section(
			'section_subtitle_style',
			[
				'label' => __( 'Subtitle Styles', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					Base::CARD_KEY => array_keys(Base::instance()->get_cards_list()),
				],
			]
		);

			$this->add_control(
				'subtitle_show',
				[
					'label' => esc_html__( 'Show', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => esc_html__( '- Inherit -', 'rey-core' ),
						'yes'  => esc_html__( 'Yes', 'rey-core' ),
						'no'  => esc_html__( 'No', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'subtitle_color',
				[
					'label' => esc_html__( 'Sub-Title Color', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .__captionSubtitle' => 'color: {{VALUE}}',
					],
					'condition' => [
						'subtitle_show!' => 'no',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'subtitle_typo',
					'label' => esc_html__('Sub-Title Typography', 'rey-core'),
					'selector' => '{{WRAPPER}} .__captionSubtitle',
					'condition' => [
						'subtitle_show!' => 'no',
					],
				]
			);

			$this->add_control(
				'subtitle_length',
				[
					'label' => __( 'Subtitle Length (Words Count)', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => 20,
					'min' => 0,
					'max' => 200,
					'step' => 0,
					'condition' => [
						'subtitle_show!' => 'no',
					],
				]
			);


		$this->end_controls_section();

	}

	public function controls__label_styles(){


		$this->start_controls_section(
			'section_label_style',
			[
				'label' => __( 'Label Styles', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'source' => ['custom', 'posts'],
					Base::CARD_KEY => array_keys(Base::instance()->get_cards_list()),
				],
			]
		);

			$this->add_control(
				'label_show',
				[
					'label' => esc_html__( 'Show', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => esc_html__( '- Inherit -', 'rey-core' ),
						'yes'  => esc_html__( 'Yes', 'rey-core' ),
						'no'  => esc_html__( 'No', 'rey-core' ),
					],
				]
			);

			$this->add_control(
				'label_color',
				[
					'label' => esc_html__( 'Label Color', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .__captionLabel' => 'color: {{VALUE}}',
					],
					'condition' => [
						'label_show!' => 'no',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'label_typo',
					'label' => esc_html__('Label Typography', 'rey-core'),
					'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .__captionLabel',
					'condition' => [
						'label_show!' => 'no',
					],
				]
			);

			$this->add_responsive_control(
				'label_distance',
				[
					'label' => __( 'Label Distance', 'rey-core' ) . ' (px)',
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .__captionLabel' => '--distance: {{SIZE}}px;',
					],
					'condition' => [
						'label_show!' => 'no',
					],
				]
			);

		$this->end_controls_section();

	}

	public function controls__button_styles(){

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Button Styles', 'rey-core' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'source' => ['custom', 'posts', 'product_cat'],
					Base::CARD_KEY => array_keys(Base::instance()->get_cards_list()),
				],
			]
		);

		$this->add_control(
			'button_show',
			[
				'label' => esc_html__( 'Show', 'rey-core' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( '- Inherit -', 'rey-core' ),
					'yes'  => esc_html__( 'Yes', 'rey-core' ),
					'no'  => esc_html__( 'No', 'rey-core' ),
				],
			]
		);

			$this->add_control(
				'button_text',
				[
					'label' => __( 'Button Text', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => __( 'Click here', 'rey-core' ),
					'placeholder' => __( 'eg: SEE MORE', 'rey-core' ),
					'condition' => [
						'source' => ['posts', 'product_cat'],
						'button_show!' => 'no',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'button_typo',
					'label' => esc_html__('Button Typography', 'rey-core'),
					'selector' => '{{WRAPPER}} .__captionBtn a',
					'condition' => [
						'button_show!' => 'no',
					],
				]
			);

			$this->add_control(
				'button_style',
				[
					'label' => __( 'Button Style', 'rey-core' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						''  => __( '- Inherit -', 'rey-core' ),
						'btn-simple'  => __( 'Link', 'rey-core' ),
						'btn-primary'  => __( 'Primary', 'rey-core' ),
						'btn-secondary'  => __( 'Secondary', 'rey-core' ),
						'btn-primary-outline'  => __( 'Primary Outlined', 'rey-core' ),
						'btn-secondary-outline'  => __( 'Secondary Outlined', 'rey-core' ),
						'btn-line-active'  => __( 'Underlined', 'rey-core' ),
						'btn-line'  => __( 'Hover Underlined', 'rey-core' ),
						'btn-primary-outline btn-dash'  => __( 'Primary Outlined & Dash', 'rey-core' ),
						'btn-primary-outline btn-dash btn-rounded'  => __( 'Primary Outlined & Dash & Rounded', 'rey-core' ),
						'btn-dash-line'  => __( 'Dash', 'rey-core' ),
					],
					'condition' => [
						'button_show!' => 'no',
					],
				]
			);

			$this->start_controls_tabs( 'btn_tabs_styles', [
				'condition' => [
					'button_show!' => 'no',
				],
			]);

				$this->start_controls_tab(
					'btn_tab_default',
					[
						'label' => __( 'Default', 'rey-core' ),
					]
				);

					$this->add_control(
						'button_color',
						[
							'label' => esc_html__( 'Primary Color (text)', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .__captionBtn .btn' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_color_bg',
						[
							'label' => esc_html__( 'Primary Color (background)', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .__captionBtn .btn' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'btn_tab_hover',
					[
						'label' => __( 'Hover', 'rey-core' ),
					]
				);

					$this->add_control(
						'button_color_hover',
						[
							'label' => esc_html__( 'Primary Color (text)', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .__captionBtn .btn:hover' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_control(
						'button_color_bg_hover',
						[
							'label' => esc_html__( 'Primary Color (background)', 'rey-core' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .__captionBtn .btn:hover' => 'background-color: {{VALUE}}',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function pre_get_posts_query_filter( $query ){

		$query_id = $this->_settings[ 'query_id' ];

		do_action( "reycore/elementor/query/{$query_id}", $query, $this );

	}

	public function query_posts() {

		$query_args = [
			'posts_per_page' => $this->_settings['posts_per_page'] ? $this->_settings['posts_per_page'] : get_option('posts_per_page'),
			'post_type' => $this->_settings['post_type'],
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'update_post_term_cache' => false, //useful when taxonomy terms will not be utilized
			'orderby' => isset($this->_settings['orderby']) ? $this->_settings['orderby'] : 'date',
			'order' => isset($this->_settings['order']) ? $this->_settings['order'] : 'DESC',
			'fields' => 'ids',
		];

		if( $this->_settings['query_type'] == 'current-query' ){
			$current_query_args = array_filter($GLOBALS['wp_query']->query_vars);
			$query_args = array_merge($current_query_args, $query_args);
		}
		else if( $this->_settings['query_type'] == 'manual-selection' && !empty($this->_settings['include']) ) {
			$query_args['post__in'] = array_map( 'absint', $this->_settings['include'] );
			// $query_args['orderby'] = 'post__in';
		}
		else {

			if(
				// 'post' !== $this->_settings['post_type'] &&
				isset($this->_settings['all_taxonomies']) &&
				$all_taxonomies = $this->_settings['all_taxonomies']
			){

				unset($query_args['update_post_term_cache']);

				foreach ( $all_taxonomies as $term_id ) {

					$term = get_term( $term_id );

					if( isset($term->taxonomy) ){
						$query_args['tax_query'][] = [
							'taxonomy' => $term->taxonomy,
							'field' => 'term_id',
							'terms' => absint($term_id),
						];
					}
				}

			}

			if( !empty($this->_settings['exclude']) ) {
				$query_args['post__not_in'] = array_map( 'absint', $this->_settings['exclude'] );
			}
		}

		// Exclude duplicates
		if( $this->_settings['exclude_duplicates'] !== '' ){
			if(
				isset($GLOBALS["rey_exclude_posts"])
				&& ($to_exclude = $GLOBALS["rey_exclude_posts"]) ) {
				$query_args['post__not_in'] = isset($query_args['post__not_in']) ? array_merge( $query_args['post__not_in'], $to_exclude ) : $to_exclude;
			}
		}

		if( $this->_settings['exclude_without_image'] !== '' ){
			$query_args['meta_query'] = [
				[
					'key' => '_thumbnail_id'
				]
			];
		}

		// Deprecated
		$query_args = apply_filters_deprecated( 'reycore/elementor/carousel/query_args', [$query_args, $this], '2.4.4', 'reycore/elementor/card/query_args' );

		$query_args = apply_filters( 'reycore/elementor/card/query_args', $query_args, $this );

		if ( isset($this->_settings['query_id']) && !empty($this->_settings['query_id']) ) {
			add_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );
		}

		$query = \ReyCore\Helper::get_query( $query_args );

		remove_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );

		do_action( 'reycore/elementor/query/query_results', $query, $this );

		$post_ids = $query->get_posts();

		// create the global exclusion array
		$GLOBALS["rey_exclude_posts"] = isset($GLOBALS["rey_exclude_posts"]) ? array_merge($GLOBALS["rey_exclude_posts"], $post_ids) : $post_ids;

		return $post_ids;
	}

	public function query_product_cat(){

		if( ! class_exists('\WooCommerce') ){
			return [];
		}

		$product_cat_type = $this->_settings['product_cat_type'];

		$terms_args = [
			'query_source' => 'card_element_product_cat',
			'hide_empty' => true,
			'orderby'    => $this->_settings['product_cat_orderby'],
			'order'      => $this->_settings['product_cat_order'],
		];

		if( 'manual' === $product_cat_type && ( $cats = $this->_settings['product_cats'] ) ){
			$terms_args['orderby'] = 'include';
			$terms_args['order'] = 'ASC';
			$terms_args['include'] = array_column($cats, 'cat');
		}

		else {

			if( $this->_settings['product_cat_limit'] ){
				$terms_args['number'] = $this->_settings['product_cat_limit'];
			}

			$excludes = [];

			if( $uncategorized = get_option( 'default_product_cat' ) ){
				$excludes = (array) $uncategorized;
			}

			if( $custom_excludes = $this->_settings['product_cat_exclude'] ){
				$excludes = array_merge($excludes, $custom_excludes);
			}

			if( ! empty($excludes) ){
				$terms_args['exclude'] = $excludes;
			}

			if( 'top-parents' === $product_cat_type ){
				$terms_args['parent'] = 0;
			}

			elseif( 'siblings' === $product_cat_type ){

				if( is_tax('product_cat') ){
					$current_cat = get_queried_object();
					$terms_args['parent'] = $current_cat->parent;
					$terms_args['exclude'] = $current_cat->term_id;
				}

				// show parents on Shop and Attributes
				elseif ( is_shop() || is_product_taxonomy() ) {
					$terms_args['parent'] = 0;
				}

			}

			elseif( 'subcategories' === $product_cat_type ){

				if( is_tax('product_cat') ){
					$current_cat = get_queried_object();
					$terms_args['parent'] = $current_cat->term_id;
				}

				// show parents on Shop and Attributes
				elseif ( is_shop() || is_product_taxonomy() ) {
					$terms_args['parent'] = 0;
				}

			}
		}

		// may be overridden
		$terms_args['taxonomy'] = 'product_cat';

		$terms_args = apply_filters('reycore/elementor/card_element/product_cat_args', $terms_args, $this);

		$terms_args['fields'] = 'ids';

		return get_terms( $terms_args );
	}

	public function parse_item(){

		if( ! (isset($this->_items[$this->item_key]) && ($item = $this->_items[$this->item_key])) ){
			return;
		}

		$args = [];

		switch( $this->_settings['source'] ){

			case "images":

				$args = [
					'image' => $item,
				];

				if( '' !== $this->_settings['images_link'] ){

					$args['button_url'] = [];

					if( 'media' === $this->_settings['images_link'] ){
						$args['button_url']['url'] = $item['url'];
						$args['button_url']['custom_attributes'] = [
							'data-elementor-open-lightbox' => 'yes'
						];
					}
					else if( 'all' === $this->_settings['images_link'] ){

						$image_url_control = $this->_settings['images_link_all'];

						$args['button_url']['url'] = esc_url($image_url_control['url']);

						$image_link_attributes = [];

						if ( ! empty( $image_url_control['is_external'] ) ) {
							$image_link_attributes['target'] = '_blank';
						}

						if ( ! empty( $image_url_control['nofollow'] ) ) {
							$image_link_attributes['rel'] = 'nofollow';
						}

						if ( ! empty( $image_url_control['custom_attributes'] ) ) {
							// Custom URL attributes should come as a string of comma-delimited key|value pairs
							$image_link_attributes = array_merge( $image_link_attributes, \Elementor\Utils::parse_custom_attributes( $image_url_control['custom_attributes'] ) );
						}

						$args['button_url']['custom_attributes'] = $image_link_attributes;
					}
				}

				if( '' !== $this->_settings['images_caption'] ){

					$attachment_post = get_post( $item['id'] );
					$args['captions'] = 'yes';
					$args['title'] = $attachment_post->post_excerpt ? $attachment_post->post_excerpt : $attachment_post->post_title;
					$args['subtitle'] = $attachment_post->post_content;
				}

				break;

			case "posts":

				$args = [
					'image'        => [],
					'_id'          => 'posts-' . $item,
					'post_id'      => $item,
					// 'item_classes' => get_post_class('', $item),
					'item_classes' => [
						'post-' . $item,
						'type-' . esc_attr($this->_settings['post_type'])
					],
				];

				if( in_array($this->_settings[Base::CARD_KEY], array_keys(Base::instance()->get_cards_list()), true) ):

					if( 'no' !== $this->_settings['image_show'] ){
						$args['image'] = [
							'id' => get_post_thumbnail_id($item),
						];
					}

					$args['button_url'] = [
						'url' => get_permalink($item)
					];
					$args['button_text'] = $this->_settings['button_text'];
					$args['captions'] = 'yes';

					$args['title'] = get_the_title($item);
					$args['subtitle'] = get_the_excerpt( $item );

					if( $map_label = $this->_settings['posts_map_label'] ){
						if( 'date' === $map_label ){
							$args['label'] = get_the_date( '', $item );
						}
						else if( 'category' === $map_label ){
							$post_cats = array_column(get_the_category( $item ), 'name');
							$args['label'] = implode(', ', $post_cats);
						}
					}

				endif;

				break;

			case "product_cat":

				if( class_exists('\WooCommerce') && ($term = get_term( $item )) && isset($term->name) ):

					$thumbnail_id = get_term_meta( $item, 'thumbnail_id', true );

					$args = [
						'image' => [
							'id' => $thumbnail_id,
							// 'url' => wp_get_attachment_url( $thumbnail_id ),
						],
						'button_url' => [
							'url' => get_term_link($item, 'product_cat')
						],
						'captions' => 'yes',
						'button_text' => $this->_settings['button_text'],
						'_id' => 'prod-cat-' . $item,
					];

					$args['title'] = $term->name;
					$args['subtitle'] = $term->description;

					if( '' !== $this->_settings['product_cat_show_count'] ){
						$args['title'] .= sprintf(' <span class="u-count">%d</span>', $term->count);
					}

				endif;

				break;

			case "custom":
				$args = $item;
				break;

		}

		if( $button_style = $this->_settings['button_style'] ){
			$args['button_style'] = $button_style;
		}

		$args['button_show'] = $this->_settings['button_show'];

		if( $this->_settings['title_show'] === 'no' ){
			$args['title'] = '';
		}

		$args['subtitle_show'] = $this->_settings['subtitle_show'];

		if( $this->_settings['subtitle_show'] === 'no' ){
			$args['subtitle'] = '';
		}

		if( $this->_settings['label_show'] === 'no' ){
			$args['label'] = '';
		}

		if( $this->_settings['image_show'] === 'no' ){
			$args['image'] = [];
		}

		$args['uid'] = sprintf('%s-%d', $this->get_id(), $this->item_key);

		$this->_items[$this->item_key] = $args;

	}

	public function get_items_data(){

		$items = [];

		switch( $this->_settings['source'] ){

			case "images":
				$items = $this->_settings['images'];
				break;

			case "posts":
				$items = $this->query_posts();
				break;

			case "product_cat":
				$items = $this->query_product_cat();
				break;

			case "custom":
				$items = $this->_settings['carousel_items'];
				break;

		}

		return $items;

	}

	public function render_item(){
		Base::instance()->render_card($this);
	}

	public function render() {}

	public function content_template() {}
}
endif;
