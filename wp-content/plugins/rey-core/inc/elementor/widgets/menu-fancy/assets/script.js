!function(e,t,n){"use strict";var a=function(e,t,n){this.$backBtn=!1,this.init=function(){this.$scope=e,this.$nav=t(".reyEl-fancyMenu",this.$scope),this.$parentNav=t(".reyEl-fancyMenu-nav",this.$scope),this.depth=this.$nav.attr("data-depth")||20,this.makeHeight(),this.addBacks(),this.a11y(),this.createSubmenuIndicators(),this.events()},this.events=function(){var e=this;t(".menu-item.menu-item-has-children > a",this.$nav).on("click",(function(n){var a=t(this);a.siblings().length&&(n.preventDefault(),t("ul.--start",e.$nav).removeClass("--start"),a.closest("ul").addClass("--back"),a.next("ul").addClass("--start"),e.$nav.css("height",a.nextAll("ul").outerHeight()+"px"))})),t(".reyEl-fancyMenu-back",this.$parentNav).on("click",(function(n){n.preventDefault();var a=t(this);a.closest(".--start").removeClass("--start"),a.closest(".--back").removeClass("--back").addClass("--start"),e.$nav.css("height",t("ul.--start",e.$nav).outerHeight())})),t(document).on("reycore/offcanvas_panel/open",(function(){e.makeHeight()}))},this.makeHeight=function(){this.$nav.css("height",t("ul.--start",this.$nav).outerHeight())},this.a11y=function(){var e=this;this.$popupItems=t(".menu-item-has-children",this.$nav),this.$popupItems.attr({"aria-haspopup":"true","aria-expanded":"false"}),t(".sub-menu a, .reyEl-fancyMenu-back",this.$popupItems).attr("tabindex","-1"),t(document).on("keydown",(function(a){if(9!==a.keyCode){if(-1!==[13,32].indexOf(a.keyCode)){var i=t('.menu-item[aria-haspopup="true"] > a:focus',e.$nav).parent("li");i.length&&(a.preventDefault(),i.each((function(e,n){t(n).attr("aria-expanded","true").children("a").trigger("click"),t("> .sub-menu > li > a, > .sub-menu > .reyEl-fancyMenu-back",n).removeAttr("tabindex")})));var s=t(".reyEl-fancyMenu-back:focus",e.$nav);s.length&&n(s)}27==a.keyCode&&n()}}));var n=function(n){if(n)var a=n.closest('.menu-item[aria-haspopup="true"][aria-expanded="true"]');else a=t('.menu-item[aria-haspopup="true"][aria-expanded="true"]',e.$nav);a.length&&(t("> .sub-menu > .reyEl-fancyMenu-back",a).trigger("click"),a.attr("aria-expanded","false"),t(".sub-menu a, .sub-menu .reyEl-fancyMenu-back",a).attr("tabindex","-1"),a.children("a").trigger("focus"))}},this.addBacks=function(){var e=this.$nav.children(".reyEl-fancyMenu-back");e.length&&t(".sub-menu",this.$parentNav).each((function(n,a){t(a).children(".reyEl-fancyMenu-back").length||e.clone().appendTo(a)}))},this.createSubmenuIndicators=function(){var e,n=this,a=this.$nav.attr("data-indicator");if(a){rey&&("yes"===a&&(e=rey.frontend.svgIcon.get("play")),"chevron"===a&&(e=rey.frontend.svgIcon.get("arrow")),t.each(t(".menu-item-has-children > a",n.$nav),(function(n,a){var i=t(a);i.siblings().length&&t('<i class="--submenu-indicator">'+e+"</i>").appendTo(i)})))}},this.init()};rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"reycore-menu-fancy.default",cb:function(e,t){new a(e,t,"default")}})})),e(document).on("reymodule/fullscreen_nav/loaded",(function(t,n){"menu"===n&&new a(e(document),e,"default")}))}(jQuery,window.elementorFrontend,window.elementorModules);