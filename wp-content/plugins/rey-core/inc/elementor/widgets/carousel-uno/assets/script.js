!function(e,n,t){"use strict";var a=function(e,n){if(rey.vars.is_edit_mode||!e.hasClass("--init")){e.addClass("--init");var t=n(".rey-carouselUno",e),a=n(".splide",e),o=n(".cUno-slide",a),i=n(".cUno-caption",t),s=n(".cUno-nav",t),r=JSON.parse(t.attr("data-slider-settings")||"{}");if(!r.sync){var c={type:r.type,autoplay:r.autoplay,interval:parseInt(r.interval),speed:parseInt(r.speed),pauseOnHover:r.pauseOnHover,rewind:!0,perPage:1,gap:0,arrows:!1,pagination:!1},d=n.reyVideosHelper({containers:o});d.init();var l=function(e,t){var a=n("video",o.eq(e));a.length&&a[0][t]()};a.on("rey/splide",(function(e,a){if(r.syncId){var o=n("."+r.syncId);if(o.length){var c=JSON.parse(o.closest(".rey-carouselUno").attr("data-slider-settings")||"{}"),u=n.reySplide({element:o[0],config:{type:c.type,speed:parseInt(c.speed),rewind:!0,perPage:1,gap:0,arrows:!1,pagination:!1}});a.sync(u.slider).mount()}}a.on("mounted",(function(){t.removeClass("--loading").addClass("--init"),"outside"===r.contentPosition&&i.eq(0).addClass("--active"),l(0,"play"),d.changeState(0,"play")})),a.on("move",(function(e,t){"outside"===r.contentPosition&&(i.removeClass("--active"),i.eq(e).addClass("--active")),s.length&&s.each((function(t,a){n(a).children("button").removeClass("--active"),n(a).children("button").eq(e).addClass("--active")})),l(t,"pause"),d.changeState(t,"")})),a.on("moved",(function(e){l(e,"play"),d.changeState(e,"play")}))})),n.reySplide({element:a[0],config:c,customArrows:r.customArrows,customPagination:r.customPagination,animateHeight:r.animateHeight,mount:!r.syncId})}}};rey.hooks.addAction("site_loaded",(function(){rey.elements.sitePreloader&&e(".elementor-widget-reycore-carousel-uno").each((function(n,t){new a(e(t),e)}))})),rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"reycore-carousel-uno.default",cb:function(e,n){new a(e,n)}})}))}(jQuery,window.elementorFrontend,window.elementorModules);