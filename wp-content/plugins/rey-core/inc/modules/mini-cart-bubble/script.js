!function(t){"use strict";var e=function(e){var s=this;this.init=function(){rey.vars.is_desktop&&wc_cart_fragments_params&&(this.$cartPanel=e.$cartPanel,this.$cartPanel.length&&wp&&(this._html=wp.template("reyCrossSellsBubble"),this._html&&(this.events(),this.instantRun())))},this.events=function(){t(document).on("click",".rey-crossSells-bubble-close",(function(t){t.preventDefault(),e.closePanel()})),rey.hooks.addAction("minicart/close",(function(t){setTimeout((()=>{s.remove()}),500)})),t(document).on("added_to_cart",(function(t,e){s.wcFragments=e,s.makeMarkupAndStart()}))},this.instantRun=function(){-1!==["added","adding"].indexOf(e.status)&&(this.wcFragments=JSON.parse(sessionStorage.getItem(wc_cart_fragments_params.fragment_name)),this.makeMarkupAndStart())},this.makeMarkupAndStart=function(){this.createMarkup()&&this.$bubble.removeClass("--loading")},this.remove=function(){t(".rey-crossSells-bubble",this.$cartPanel).remove()},this.createMarkup=function(){if(s.remove(),this.wcFragments&&this.wcFragments._crosssells_bubble_){e.log("| BUBBLE MARKUP");var n=this._html({items:this.wcFragments._crosssells_bubble_});return this.$bubble=t(n).appendTo(this.$cartPanel),this.$bubble.length}},this.init()},s=function(t){new e(t)};rey.hooks.addAction("minicart/init",s),rey.hooks.addAction("minicart/assets_ready",s)}(jQuery);