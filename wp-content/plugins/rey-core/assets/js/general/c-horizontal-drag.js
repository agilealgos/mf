!function(e){"use strict";var o=function(o){if(!rey.vars.elementor_edit_mode&&rey.vars.is_desktop){var t,n,s=e(o),a=!1;s.on("mousedown",(function(e){a=!0,s.addClass("--active"),t=e.pageX-s.offset().left,n=o.scrollLeft})),s.on("mouseleave",(function(){a=!1,s.removeClass("--active")})),s.on("mouseup",(function(){a=!1,s.removeClass("--active")})),s.on("mousemove",(function(e){if(a){e.preventDefault();var o=3*(e.pageX-s.offset().left-t);s.scrollLeft(n-o)}}))}};rey.hooks.addAction("reytheme/init",(function(){e(".js-horizontal-drag").each((function(e,t){new o(t)}))}))}(jQuery);