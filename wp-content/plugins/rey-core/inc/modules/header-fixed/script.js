!function(e){"use strict";var i=function(){this.transitionDuration=350,this.shrinked,this.init=function(){var e=this,i=parseInt(rey.params.fixed_header_lazy);if(!i)return this.lazyInit();setTimeout((function(){e.lazyInit()}),i||3e3)},this.lazyInit=function(){this.$header=e(".rey-siteHeader.header-pos--fixed"),this.$header.length&&(this.$header.removeClass("--loading-fixed-desktop --loading-fixed-tablet --loading-fixed-mobile"),this.fixedHeaderActivationPoint=parseFloat(rey.params.fixed_header_activation_point),0===this.fixedHeaderActivationPoint&&(this.fixedHeaderActivationPoint=rey.vars.adminBar),this.$sections=e(":not(.rey-mega-gs) .elementor-section.elementor-top-section",this.$header),this.$hiddenSections=this.$sections.filter(".hide-on-scroll"),this.$dropPanelsInHiddenSections=e(".rey-header-dropPanel-btn",this.$hiddenSections),this.$headerHelper=this.$header.nextAll(".rey-siteHeader-helper"),this.events(),this.checkFixedHeader(),this.checkShrinkingHeader(),this.removeHiddenClassOnMobiles(),this.mobilesColumnsHideOnScroll())},this.events=function(){var i=this;e(window).on("scroll",rey.util.debounce((function(e){i.checkFixedHeader(),i.checkShrinkingHeader(),i.hiddenOnScrollFixes()}),100)),e(window).on("resize",rey.util.debounce((function(e){i.removeHiddenClassOnMobiles(),i.mobilesColumnsHideOnScroll()}),500));var t=e(":not(.rey-mega-gs) .elementor-section.--show-hover-yes.hide-on-scroll",this.$header);t.length&&this.$header.on("mouseenter",(function(){t.removeClass("hide-on-scroll")})).on("mouseleave",(function(){t.addClass("hide-on-scroll")}))},this.checkShrinkingHeader=function(){var e=this;if(this.$header.hasClass("--fixed-shrinking")&&(!rey.vars.is_mobile||!this.$header.hasClass("--not-mobile"))){var i="--shrank";(window.pageYOffset||document.documentElement.scrollTop)>this.fixedHeaderActivationPoint?this.shrinked||(this.$header.addClass(i),e.$header.trigger("reycore/header_shrink/immediate/on"),this.transitionDuration&&setTimeout((function(){e.$header.trigger("reycore/header_shrink/on",[e.$header]),e.shrinkVar||(rey.dom.setProperty("--shrank--header-height",e.$header.height()+"px"),e.shrinkVar=!0)}),this.transitionDuration),this.shrinked=!0):this.shrinked&&(this.$header.removeClass(i),this.shrinked=!1,this.$header.trigger("reycore/header_shrink/immediate/off",[e.$header]),this.transitionDuration&&setTimeout((function(){e.$header.trigger("reycore/header_shrink/off",[e.$header])}),this.transitionDuration))}},this.checkFixedHeader=function(){var e="--scrolled";(window.pageYOffset||document.documentElement.scrollTop)>this.fixedHeaderActivationPoint?this.$header.hasClass(e)||(this.$header.trigger("reycore/header_fixed/scrolled",[this.$header,e,!0]),this.$header.addClass(e)):this.$header.hasClass(e)&&(this.$header.trigger("reycore/header_fixed/unscrolled",[this.$header,e,!1]),this.$header.removeClass(e))},this.hiddenOnScrollFixes=function(){if(this.$dropPanelsInHiddenSections.length){var e=this.$dropPanelsInHiddenSections.closest(".rey-header-dropPanel.--active");e.length&&(rey.frontend.overlay.close(),e.removeClass("--active"))}},this.removeHiddenClassOnMobiles=function(){rey.vars.is_desktop||this.$header.hasClass("--not-mobile")&&this.$hiddenSections.removeClass("hide-on-scroll")},this.mobilesColumnsHideOnScroll=function(){rey.vars.is_desktop||this.$header.hasClass("--not-mobile")||e(".elementor-column.elementor-top-column[data-hide-on-scroll-mobile]",this.$header).each((function(i,t){var r=e(t);r.toggleClass("hide-on-scroll","yes"===r.attr("data-hide-on-scroll-mobile"))}))},this.init()};e(document).ready((function(){new i})),rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"reycore-header-logo.default",cb:function(e,i){var t=i(".rey-siteHeader.header-pos--fixed");if(t.length){var r={},s=i(".custom-logo",e),n=s.attr("src"),o=e.attr("data-sticky-logo")||"";o&&(r.desktop={initialSrc:n,stickySrc:o,$img:s});var a=i(".rey-mobileLogo",e);if(a.length){var h=a.attr("src"),d=e.attr("data-sticky-mobile-logo")||"";d&&(r.mobile={initialSrc:h,stickySrc:d,$img:a})}i.each(r,(function(e,r){i("<img />",{src:r.stickySrc,srcset:"",sizes:""}).one("load",(function(){!function(e){t.on("reycore/header_fixed/scrolled",(function(){e.$img.attr({src:e.stickySrc,srcset:"",sizes:""})})).on("reycore/header_fixed/unscrolled",(function(){e.$img.attr({src:e.initialSrc,srcset:"",sizes:""})}))}(r)}))}))}}})}))}(jQuery);