!function(t,e,i){"use strict";var n={map:{"elementor-section":{display:"flex",selector:".elementor-container"},"elementor-column":{display:"flex",selector:".elementor-widget-wrap"},"elementor-element":{display:"block",selector:".elementor-widget-container"}},init:function(t,e){if(this.$btn=e(".js-triggerBtn",t),this.$btn.length){var i=this.$btn.attr("data-selector");i&&(0===i.indexOf("#")||0===i.indexOf(".")?(this.$targets=e(i),this.$targets.length&&(this.trigger=this.$btn.attr("data-trigger"),this.is_animated=this.$btn.hasClass("--toggle-animated"),this.events())):console.log("Invalid selector"))}},events:function(){var e=this,i=this.is_animated?350:0;this.$btn.hasClass("--is-active");this.$btn.on(this.trigger,(function(n){n.preventDefault(),t(this).toggleClass("--is-active"),e.$targets.each((function(){var n=t(this);t.each(e.map,(function(e,s){n.hasClass(e)&&n.children(s.selector).slideToggle({duration:i,start:function(){t(this).css({display:s.display})}})}))}))}))}};rey.hooks.addAction("elementor/init",(function(t){t.registerElement({name:"reycore-trigger-v2.default",cb:function(t,e){var i=e(".js-triggerBtn",t).attr("data-action");""!==i&&("toggle"===i&&Object.create(n).init(t,e))}})}))}(jQuery,window.elementorFrontend,window.elementorModules);