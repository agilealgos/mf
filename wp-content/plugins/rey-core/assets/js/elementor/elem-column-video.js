!function(e){"use strict";var t=function(e,t){if(reyElementorFrontendParams.compatibilities.column_video&&e.hasClass("rey-colbg--video")){var n={containers:e,lazyLoad:!1},o=t("> .elementor-widget-wrap > .rey-background-video-container.--lazy-video",e);if(e.hasClass("rey-animate-el")&&rey.vars.is_desktop&&!rey.vars.elementor_edit_mode)t(document).on("rey/elementor_column/animation_complete",(function(o,i,r){t(r).attr("data-id")===e.attr("data-id")&&t.reyVideosHelper(n).init()}));else if(o.length&&"undefined"!=typeof ScrollOut){var i=!1;ScrollOut({targets:o[0],once:!0,threshold:.25,onShown:function(e,o,r){i||(i=!0,t.reyVideosHelper(n).init())}})}else t.reyVideosHelper(n).init()}};rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"column",cb:t})}))}(jQuery);