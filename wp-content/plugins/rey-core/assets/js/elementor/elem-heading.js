!function(e){"use strict";var n=function(e,n){if(e.hasClass("el-parent-animation--show")||e.hasClass("el-parent-animation--hide")){var t=n(".elementor-heading-title",e),o=function(){t[0].style.setProperty("--el-height",t.outerHeight()+"px")};o(),n(window).on("resize",rey.util.debounce(o,200))}n.each({column:".elementor-column, .e-container, .e-con",section:".elementor-top-section, .e-container--row, .e-con"},(function(e,t){n(".el-parent-trigger--"+e).closest(t).addClass(e+"-parent-trigger")}))};rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"heading.default",cb:n})}))}(jQuery);