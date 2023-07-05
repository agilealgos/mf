<?php
namespace ReyCore;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

final class CriticalCSS
{
	public function __construct()
	{
		add_action( 'init', [$this, 'init']);
	}

	function init(){
		add_action( 'wp_head', [$this, 'add_css_in_head'], 5);
		add_action( 'wp_footer', [$this, 'remove_critical_css']);
	}

	function can_add(){

		$is_wc = class_exists('\WooCommerce') && (is_cart() || is_checkout());

		if( apply_filters('reycore/critical_css/disable', false) && ! $is_wc ){
			return;
		}

		return get_theme_mod('perf__critical_css', true);
	}

	function add_css_in_head(){

		if( ! $this->can_add() ){
			return;
		}

		$inlined_css = str_replace(
			[': ', ';  ', '; ', '  '],
			[':', ';', ';', ' '],
			preg_replace( "/[\t\n\r]+/", '', implode('', $this->css() ) )
		);

		printf('<style type="text/css" id="reycore-critical-css" data-noptimize data-no-optimize="1">%s</style>', $inlined_css );
	}

	function css(){

		$css[] = 'body{overflow-y: scroll}';

		$css[] = '.--lz-invisible{visibility: hidden;}';

		$css[] = '.btn, button, button[type=button], button[type=submit], input[type=button], input[type=reset], input[type=submit] {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			text-align: center;
			vertical-align: middle;
			background-color: transparent;
			border: 1px solid transparent;
			padding: 0;
			font-size: 1rem;
			line-height: 1.5;
		}';

		$css[] = 'mark {
			background-color: transparent;
		}';

		$css[] = '.rey-arrowSvg i, .rey-arrowSvg svg, .rey-icon {
			font-size: 1rem;
			display: inline-block;
			fill: currentColor;
			height: 1em;
			position: relative;
			vertical-align: middle;
			width: 1em;
		}';

		if( 'default' === get_theme_mod('header_layout_type', 'default') ):
			$css[] = '.rey-siteHeader.rey-siteHeader--default .rey-siteHeader-container {
				padding-right: var(--half-gutter-size);
				padding-left: var(--half-gutter-size);
				width: 100%;
				max-width: var(--container-max-width);
				margin-right: auto;
				margin-left: auto
			}';
			$css[] = '@media (min-width: 1025px) {
				.rey-siteHeader.rey-siteHeader--default .rey-siteHeader-container {
					max-width:var(--header-default--max-width)
				}
			}';
			$css[] = '.rey-siteHeader.rey-siteHeader--default {
				--v-spacing: 15px
			}';
			$css[] = '@media (min-width: 1025px) {
				.rey-siteHeader.rey-siteHeader--default {
					--v-spacing:20px
				}
			}';
			$css[] = '.rey-siteHeader.rey-siteHeader--default .rey-siteHeader-row {
				display: flex;
				padding-top: var(--v-spacing);
				padding-bottom: var(--v-spacing);
				align-items: center;
			}';
		endif;

		// desktop menu
		$css[] = '.rey-mainMenu {
			list-style: none;
			margin: 0;
			padding: 0;
		}';

		$css[] = '.rey-mainNavigation.rey-mainNavigation--desktop {
			display: var(--nav-breakpoint-desktop);
		}';

		$css[] = '.rey-mainMenu--desktop {
			display: inline-flex;
		}';

		// mobile menu
		$css[] = '.rey-mainNavigation.rey-mainNavigation--mobile {
			display: none;
		}';

		$css[] = '.rey-mainNavigation-mobileBtn {
			position: relative;
			display: none;
		}';

		$css[] = '.btn.rey-headerSearch-toggle .__icon svg.icon-close {
			--size: 0.875rem;
			position: absolute;
			font-size: var(--size);
			top: calc(50% - var(--size)/2);
			left: calc(50% - var(--size)/2);
			transform: rotate( 40deg );
			opacity: 0;
		}';

		$css[] = '.reyEl-menu-nav {
			list-style: none;
			margin: 0;
			padding: 0;
			display: flex;
			flex-wrap: wrap;
		}';

		$css[] = '.reyEl-menu--horizontal {
			--distance: 0.5em;
		}';

		$css[] = '.reyEl-menu--horizontal .reyEl-menu-nav {
			flex-direction: row;
		}';

		// Search
		$css[] = '.rey-headerSearch--inline .rey-searchPanel__qlinks,
		.rey-headerSearch--inline .rey-searchPanel__suggestions,
		.rey-searchAjax .rey-lineLoader {
			display:none;
		}';

		$css[] = '.rey-headerSearch--inline form {
			display: flex;
		}';

		$css[] = '@media (min-width: 1025px){
			.rey-headerSearch--inline .rey-headerSearch-toggle,
			.rey-headerSearch--inline .rey-inlineSearch-mobileClose {
				display: none;
			}
		}';

		$css[] = '.rey-headerSearch--inline input[type="search"] {
			border: 0;
			height: 100%;
			font-size: 16px;
			z-index: 1;
			position: relative;
			background: none;
			box-shadow: none;
			position: relative;
		}';

		$css[] = '.reyajfilter-updater {
			position: absolute;
			opacity: 0;
			visibility: hidden;
			left: -350vw;
		}';

		// Tabs
		$css[] = '.elementor-element.rey-tabs-section>.elementor-container>.elementor-row>.elementor-column:not(:first-child),.elementor-element.rey-tabs-section.--tabs-loaded>.elementor-container>.elementor-row>.elementor-column:not(.--active-tab),.elementor-element.rey-tabs-section>.elementor-container>.elementor-column:not(:first-child),.elementor-element.rey-tabs-section.--tabs-loaded>.elementor-container>.elementor-column:not(.--active-tab) ,.elementor-element.rey-tabs-section>.elementor-element:not(:first-child),.elementor-element.rey-tabs-section.--tabs-loaded>.elementor-element:not(.--active-tab) {visibility:hidden;opacity:0;position:absolute;left:0;top:0;pointer-events:none;}';

		// Sticky Social Icons
		$css[] = '.rey-stickySocial.--position-left{left:-150vw}.rey-stickySocial.--position-right{right:150vw;}';

		// Header panels
		$css[] = '.rey-compareNotice-wrapper,.rey-scrollTop,.rey-wishlist-notice-wrapper{left:-150vw;opacity:0;visibility:hidden;pointer-events:none;}';
		$css[] = '.rey-accountPanel-wrapper[data-layout="drop"]{display:none;}';

		// Mega menus
		$css[] = '.rey-mega-gs,.depth--0>.sub-menu{display:none;}';

		// Separator in menu
		// $css[] = '.rey-mainMenu--desktop.rey-mainMenu--desktop .menu-item.depth--0.--separated{position:relative;padding-left:0.625rem;margin-left:1.25rem;}@media(min-width:1025px){.rey-mainMenu--desktop.rey-mainMenu--desktop .menu-item.depth--0.--separated{padding-left:var(--header-nav-x-spacing);margin-left:calc(var(--header-nav-x-spacing) * 2);}}';

		// product thumbs
		$css[] = '.woocommerce ul.products li.product .rey-productThumbnail .rey-thumbImg, .woocommerce ul.products li.product .rey-productThumbnail .rey-productThumbnail__second, .woocommerce ul.products li.product .rey-productThumbnail img {backface-visibility: visible;}';

		// dashed button
		$css[] = '.elementor-element.elementor-button-dashed.--large .elementor-button .elementor-button-text {padding-right:50px;}.elementor-element.elementor-button-dashed.--large .elementor-button .elementor-button-text:after {width: 35px;}';

		// svg's
		$css[] = '.elementor-icon svg {max-width: 1rem; max-height: 1rem;}';

		// Cookie notice
		$css[] = '.rey-cookieNotice.--visible{left: var(--cookie-distance);opacity: 1;transform: translateY(0);}';

		// Helper classes
		$css[] = '.--hidden{display:none!important;}@media(max-width:767px){.--dnone-sm,.--dnone-mobile{display:none!important;}}@media(min-width:768px) and (max-width:1025px){.--dnone-md,.--dnone-tablet{display:none!important;}} @media(min-width:1025px){.--dnone-lg,.--dnone-desktop{display:none!important;}}';

		// Nest cover
		$css[] = '.rey-coverNest .cNest-slide{opacity: 0;}';

		$css[] = '@media (min-width: 768px) {
			.el-reycore-cover-nest .rey-siteHeader > .elementor {
				opacity: 0;
			}
		}';

		// Section slideshow
		$css[] = '.elementor-element > .rey-section-slideshow { position: absolute; width: 100%; height: 100%; top: 0; left: 0; background-size: cover; background-position: center center; }';

		$css[] = '.rey-textScroller-item ~ .rey-textScroller-item {display:none;}';

		$css[] = '.--direction--h .rey-toggleBoxes {
			flex-direction: row;
		}
		.rey-toggleBoxes {
			display: flex;
			flex-wrap: wrap;
		}';

		$css[] = '.rey-loopHeader {display: none;}';

		$css[] = '.is-animated-entry {opacity: 0;}';

		$css[] = '@media (min-width: 768px) {
			.el-reycore-cover-sideslide .rey-siteHeader > .elementor,
			.el-reycore-cover-sideslide .rey-siteHeader > .rey-siteHeader-container {
				opacity: 0;
			}
		}';

		$css[] = '.rey-stickyContent { display:none; }';

		// $css[] = '.rey-cartPanel-wrapper.rey-sidePanel {
		// 	display:none;
		// }';

		$css[] = '.woocommerce div.product .woocommerce-product-gallery { opacity: 0; }';

		$css[] = '@media (min-width: 1025px) {
			.woocommerce div.product .woocommerce-product-gallery.--is-loading .woocommerce-product-gallery__wrapper {
				opacity:0;
			}
		}';

		$css[] = '.rey-postSocialShare {
			list-style: none;
			margin: 0;
			padding: 0;
			display: flex;
		}';

		$css[] = '.rey-buyNowBtn .rey-lineLoader {
			opacity:0;
		}';

		$css[] = '.rey-coverSplit.--mainSlide .cSplit-slide--mainBg {
			opacity:0;
		}';

		return apply_filters('reycore/critical_css/css', $css);
	}

	function remove_critical_css(){

		if( ! $this->can_add() ){
			return;
		} ?>

		<script type="text/javascript" id="reycore-critical-css-js" data-noptimize data-no-optimize="1">
			document.addEventListener("DOMContentLoaded", function() {
				var CCSS = document.getElementById('reycore-critical-css');
				CCSS && CCSS.remove();
			});
		</script><?php
	}

}
