!function(e){"use strict";var t=function(e){var t=this;this.init=function(){if(this.$element=e,this.finish=this.$element.getAttribute("data-finish"),this.finish){if(this.endtime=new Date(this.finish),this.endTimeDate=Date.parse(this.endtime),this.endTimeDate-this.nowDate()<=0)return this.$element.closest(".rey-schedSale").remove();this.daysSpan=this.$element.querySelector(".--d .__item"),this.hoursSpan=this.$element.querySelector(".--h .__item"),this.minutesSpan=this.$element.querySelector(".--i .__item"),this.secondsSpan=this.$element.querySelector(".--s .__item"),this.updateClock(),this.timeinterval=setInterval(this.updateClock,1e3)}},this.nowDate=function(){return Date.parse(new Date)},this.getTimeRemaining=function(){var e=this.endTimeDate-this.nowDate(),t=Math.floor(e/1e3%60),i=Math.floor(e/1e3/60%60),n=Math.floor(e/36e5%24);return{total:e,days:Math.floor(e/864e5),hours:n,minutes:i,seconds:t}},this.updateClock=function(){var e=t.getTimeRemaining();t.daysSpan.innerHTML=e.days,t.hoursSpan.innerHTML=("0"+e.hours).slice(-2),t.minutesSpan.innerHTML=("0"+e.minutes).slice(-2),t.secondsSpan.innerHTML=("0"+e.seconds).slice(-2),e.total<=0&&clearInterval(t.timeinterval)},this.init()},i=function(i){e(".rey-countDown",i).each((function(e,i){new t(i)}))};e(document).ready((function(){i()})),e(document).on("reycore/after_quickview",(function(){i()})),rey.hooks.addAction("ajaxfilters/finished",(function(e){i(e)})),rey.hooks.addAction("product/loaded",(function(e){i(e)}))}(jQuery);