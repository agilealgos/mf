!function(t){"use strict";var e={init:function(){this.$selector=t(".rey-pppSelector"),this.$selector.length&&(this.type="inline",this.$selectorItems=t("li[data-count]",this.$selector),this.$selector.hasClass("rey-loopSelectList")&&(this.type="drop",this.$selectorItems=t("option",this.$selector)),this.$selectorItems.length&&this.events())},events:function(){"inline"!==this.type?this.$selector.on("change",(function(t){var s=t.target.value;s&&(e.$selector.addClass("--loading"),e.setCount(s))})):this.$selectorItems.on("click",(function(s){s.preventDefault();var n=t(this).attr("data-count");n&&(e.$selector.addClass("--loading"),e.setCount(n))}))},setCount:function(t){rey.ajax.request("set_ppp",{params:{cache:!1},data:{ppp:t},cb:function(t){(t||t&&!t.success)&&setTimeout((function(){window.location.reload()}),1e3)}})}};t(document).ready((function(){e.init()}))}(jQuery);