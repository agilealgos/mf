!function(l){"use strict";function t(){var e=l(".rey-siteHeader.header-pos--side");if(e.length)return l(".elementor .elementor-section-wrap > .elementor-section",e).addClass("sideHeader-section"),!0}l(document).ready(function(){if(t()){var e=function(e){return l.reyHelpers.is_desktop&&void 0!==l.reyHelpers.sprintf?l.reyHelpers.sprintf("calc( %s - var(--side-header--width) )",e):e},r=function(e){return l.reyHelpers.is_desktop?"0px":e};void 0!==l.reyHelpers.addFilter&&(l.reyHelpers.addFilter("rey/siteWidth",e),l.reyHelpers.addFilter("rey/headerHeight",r),l.reyHelpers.addFilter("rey/main_menu/mobile/disable_scroll",function(e,r){return!r.closest(".rey-siteHeader.header-pos--side").length&&e})),void 0!==wp.hooks&&(wp.hooks.addFilter("rey_siteWidth","reymodule/side_header",e),wp.hooks.addFilter("rey_headerHeight","reymodule/side_header",r))}}),l(window).on("elementor/frontend/init",function(){t()}),l(document).on("rey/account_panel/open",function(e,r,t){var d=r,i=l(".rey-siteHeader.header-pos--side"),o=d.$buttonInUse;if(i.length){var n=function(){if(!1!==o){var e=o[0].getBoundingClientRect(),r=l(window).width(),t=e.top+o.height(),i=e.left+o.width()+20,n=r-(e.left+o.width()+20),s=t;l.reyHelpers.is_desktop&&(s=t-d.$panel.height()),d.$panel.css("top",s+"px"),l.reyHelpers.is_mobile?d.$panel.css({left:"",right:""}):i<r/2?d.$panel.css({left:i+"px",right:""}):d.$panel.css({right:n+"px",left:""})}};n(),l(window).on("resize",l.reyHelpers.debounce(n,500))}})}(jQuery);