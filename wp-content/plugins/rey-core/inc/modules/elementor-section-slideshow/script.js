!function(e){"use strict";var t=function(e,t){if(e.is("[data-settings*='rey_slideshow']")){var s,a,i;s=t(".rey-section-slideshow",e),a=JSON.parse(s.attr("data-rey-slideshow-settings")||"{}"),i={type:a.type,perPage:1,rewind:!0,autoplay:a.autoplay,interval:parseInt(a.interval),speed:void 0!==a.speed?parseInt(a.speed):700,easing:"var(--easeoutexpo)",gap:0,arrows:!1,pagination:!1,swipeThreshold:!1,dragThreshold:!1,waitForTransition:!1,autoWidth:!0,autoHeight:!0},(rey.vars.is_desktop||a.mobile)&&(a.class&&s.addClass(a.class),rey.vars.is_desktop&&!rey.vars.elementor_edit_mode&&e.hasClass("rey-animate-el")?t(document).on("rey/elementor_section/animation_complete",(function(e,s,a){var n=t(".rey-section-slideshow",t(a));n.length&&t.reySplide({element:n[0],config:i,delay:1e3})})):t.reySplide({element:s[0],config:i,delay:1e3}))}};rey.hooks.addAction("elementor/init",(function(e){e.registerElement({name:"section",cb:t}),e.registerElement({name:"container",cb:t})}))}(jQuery);