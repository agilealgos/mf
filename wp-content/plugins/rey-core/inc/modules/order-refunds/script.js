!function(e){"use strict";var r=function(){this.init=function(){this.$wrapper=e(".rey-refundsPage-orders"),this.$wrapper.length&&(this.$orderList=e("#rey_refund__orders",this.$wrapper),this.$productsField=e("#rey_refund__order_items",this.$wrapper),this.$form=e(".woocommerce-form",this.$wrapper),this.$formBtn=e("button",this.$form),this.$response=e(".rey-refundsPage-response",this.$wrapper),this.events())},this.events=function(){var r=this;this.$orderList.on("change",(function(t){var s=e(this),n=s.val();s.parent().css({opacity:.5,"pointer-events":"none"}),r.$productsField.html(""),n&&e.ajax({method:"get",url:rey.params.ajaxurl,cache:!1,data:{action:"rey_refund_request_order_products",order:n,security:rey.params.ajax_nonce},success:function(t){t.data&&(r.$productsField.parent().removeClass("--hidden"),s.parent().css({opacity:"","pointer-events":""}),t&&e.each(t.data,(function(t,s){e('<option value="'+t+'">'+s+"</option>").appendTo(r.$productsField)})))}})})),this.$form.on("submit",(function(t){t.preventDefault();var s=e(this);r.$response.addClass("--empty").removeClass("--error --success").empty(),r.$formBtn.css({opacity:.5,"pointer-events":"none"});var n=s.serialize()||"";n+="&action=rey_refund_request_submit",n+="&security="+rey.params.ajax_nonce,e.ajax({method:"post",url:rey.params.ajaxurl,cache:!1,data:n,success:function(t){if(r.$formBtn.css({opacity:"","pointer-events":""}),t.data){if(t.data.errors){var s="";return e.each(t.data.errors,(function(e,r){s+=r})),void(""!==s&&r.make_notice(s,"error"))}r.make_notice(t.data,"success"),r.resetForm()}},error:function(e){r.make_notice(e,"error"),r.$formBtn.css({opacity:"","pointer-events":""})}})})),this.resetForm=function(){this.$productsField.parent().addClass("--hidden"),this.$form.trigger("reset")},this.make_notice=function(e,r){this.$response.removeClass("--empty --error --success").addClass("--"+r).html(e)}},this.init()};e(document).ready((function(){new r}))}(jQuery);