!function(e,i,n){"use strict";var t,s=function(e,i){var n=i(".rey-coverSideSlide",e),t=i(".cSslide-sliderWrapper",e),s=i(".splide",e),o=i(".cSslide-slider",e),a=i(".cSslide-slide",o),r=i(".cSslide-caption",n),l=i(".cSlide-logoInner",n);if(!s.hasClass("--init")){var d=JSON.parse(n.attr("data-slider-settings")||"{}"),c={type:d.type,rewind:!0,perPage:1,autoplay:d.autoplay,interval:parseInt(d.interval),gap:0,speed:700,arrows:!1,pagination:!1},m=d.intro,u=n.children(".cSslide-effectBg--1"),f=n.children(".cSslide-effectBg--2"),v=(s.children(".cSslide-effectBg-slide--1"),s.children(".cSslide-effectBg-slide--2"));s.on("rey/splide",(function(e,t){var o=i.reyVideosHelper({containers:a});o.init(),u.remove(),f.remove(),n.removeClass("--animate-intro"),t.on("mounted",(function(){n.addClass("--init"),o.changeState(0,"play")}));var l=function(e){r.length>1&&r.eq(e).addClass("--active")};l(0),t.on("move",(function(e){r.removeClass("--active"),"curtains"==d.effect&&s.addClass("--animate-curtain"),o.changeState(e,"pause")})).on("moved",(function(e){"curtains"==d.effect&&v.on("animationend webkitAnimationEnd",(function(){s.removeClass("--animate-curtain")})),setTimeout((function(){l(e)}),rey.vars.is_desktop?400:0),o.changeState(e,"play")}))})),r.each((function(){i(".cSslide-captionEl",i(this)).each((function(e,n){var t=i(n),s=t.css("margin-bottom");t.is(":visible")&&t.css({"transition-delay":.1*e+"s","margin-bottom":0}).wrap('<div class="cSslide-captionWrapper" style="margin-bottom:'+s+'"/>')}))}));var h=function(){i("body").addClass("--cSslide-active")};rey.util.imagesLoaded(n,(function(){if(n.removeClass("--loading"),m&&n.addClass("--animate-intro"),l.addClass("--shown"),rey.vars.is_desktop&&n.closest(".elementor-hidden-desktop").length)h();else if(rey.vars.is_tablet&&n.closest(".elementor-hidden-tablet").length)h();else if(rey.vars.is_mobile&&n.closest(".elementor-hidden-mobile").length)h();else{var e=function(){h(),d.vertical&&a.height(o.outerHeight());var e=!1,n=function(n){e||(e=!0,i.reySplide({element:s[0],config:c,customArrows:d.customArrows}),setTimeout((function(){l.removeClass("--shown")}),1e3))};rey.vars.is_mobile?n():m?t.one("animationend",n):n()};rey.vars.is_mobile?e():m?f.on("transitionend",e):e()}})),i(window).on("resize",rey.util.debounce((function(){d.vertical&&a.height(o.outerHeight())}),50))}};rey.hooks.addAction("site_loaded",(function(){!t&&rey.elements.sitePreloader&&e(".elementor-widget-reycore-cover-sideslide").each((function(i,n){new s(e(n),e)}))})),rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"reycore-cover-sideslide.default",cb:function(e,i){new s(e,i),t=!0}})}))}(jQuery,window.elementorFrontend,window.elementorModules);