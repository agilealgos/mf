!function(e){"use strict";var t=function(t){var s=this;this.init=function(){wc_cart_fragments_params&&(this.$cartPanel=t.$cartPanel,this.$cartPanel.length&&wp&&(this._html=wp.template("reyCrossSellsCarousel"),this._html&&this.events()))},this.events=function(){e(document.body).on("wc_fragments_refreshed",(function(e){s.makeMarkupAndStart()})),e(document.body).on("wc_fragments_loaded",(function(e){t.isOpen&&s.makeMarkupAndStart()})),rey.hooks.addAction("minicart/opened",(function(e){e.willRefreshFragments&&!e.hasBeenOpened||e.somethingChanged&&s.makeMarkupAndStart()}))},this.makeMarkupAndStart=function(){this.createMarkup()&&this.makeSlider()},this.createMarkup=function(){if(this.wcFragments=JSON.parse(sessionStorage.getItem(wc_cart_fragments_params.fragment_name)),e(".rey-crossSells-carousel",this.$cartPanel).remove(),this.wcFragments&&this.wcFragments._crosssells_){var s=this._html({items:this.wcFragments._crosssells_}),a=e(s);if(!rey.vars.is_desktop||!a.hasClass("--dnone-desktop"))return t.log("| CS. CAROUSEL MARKUP"),this.$slider=a.insertAfter(e(".woocommerce-mini-cart",this.$cartPanel)),this.$slider.length}},this.makeSlider=function(){var t=JSON.parse(this.$slider.attr("data-slider-config")||"{}"),s={type:"slide",rewind:!0,perPage:1,autoplay:t.autoplay,interval:t.autoplaySpeed,gap:0,arrows:!1,pagination:!0,classes:{pagination:"splide__pagination --circles"}};if(void 0===e.reySplide)return console.log("reySplide not loaded.");e.reySplide({element:this.$slider[0],config:s,delay:300}),this.$slider.removeClass("--loading")},this.init()},s=function(e){new t(e)};rey.hooks.addAction("minicart/init",s),rey.hooks.addAction("minicart/assets_ready",s)}(jQuery);