!function(e){"use strict";var a=e(".rey-dashboard-main");void 0!==e.fn.masonry&&a.masonry({itemSelector:".rey-dashBox",originLeft:!e("body.rtl").length});var s=function(e,a,s){if(e&&a&&s){var t={value:a,expires_at:(new Date).getTime()+s/1};localStorage.setItem(e.toString(),JSON.stringify(t))}},t=function(e){if(!e)return null;var a=JSON.parse(localStorage.getItem(e.toString()));if(null!==a){if(!(null!==a.expires_at&&a.expires_at<(new Date).getTime()))return a.value;localStorage.removeItem(e.toString())}return null};e(".js-storeNotice").each((function(a,r){var n=e(r),i="rey-notice-"+n.attr("data-from");t(i)&&n.hide(),e(".notice-dismiss",n).on("click",(function(e){n.hide(),s(i,!0,432e5)}))})),e('<li class="rey-refresh-plugins"><a href="'+reyAdminParams.refresh_plugins_url+'">'+reyAdminParams.refresh_plugins_text+"</a></li>").appendTo(e(".tgmpa.wrap .subsubsub")),e("form#tgmpa-plugins .wp-list-table tbody tr").each((function(a,s){var t=e(s),r=t.find("td.type.column-type"),n=t.find(".rey-tgmpaNeedReg");r.length&&"Required"===r.text()&&t.addClass("--required"),r.length&&"Essential"===r.text()&&t.addClass("--essential"),n.length&&t.find('input[type="checkbox"]').prop("disabled",!0)}));var r=function(){void 0!==e.fn.masonry&&a.masonry("layout")},n=e('body[class*="_page_rey-settings"]'),i=e("#postbox-container-2",n);e("#publishing-action",n).detach().prependTo(i);e(".js-dashDeregister").on("click",(function(a){a.preventDefault();var s=e(this);s.addClass("rey-adminBtn--disabled"),e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_deregister",security:reyAdminParams.ajax_dashboard_nonce},success:function(e){e&&(e.success&&1==e.success?(s.text(reyAdminParams.dashboard_strings.deregister_success),setTimeout((function(){location.reload()}),2e3)):s.removeClass("rey-adminBtn--disabled"))},error:function(e,a,t){s.removeClass("rey-adminBtn--disabled"),console.error(e),console.error(a),console.error(t)}})})),e(".js-dashBox-registerForm").on("submit",(function(a){a.preventDefault();var s=e(this),t=s.find(".rey-adminBtn"),r=e('input[name="rey_purchase_code"]',s).val();e(".rWizard-response").remove(),t.addClass("rey-adminBtn--disabled --loading"),e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_register",security:reyAdminParams.ajax_dashboard_nonce,rey_purchase_code:r.trim(),rey_email_address:e('input[name="rey_email_address"]',s).val(),rey_subscribe_newsletter:e('input[name="rey_subscribe_newsletter"]',s).val()},success:function(e){t.removeClass("--loading"),e&&(e.success&&1==e.success?(t.text(reyAdminParams.dashboard_strings.reloading_text),setTimeout((function(){location.reload()}),2e3)):t.removeClass("rey-adminBtn--disabled").text(reyAdminParams.dashboard_strings.default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+e.data+"</p>"))},error:function(e,a,s){t.removeClass("rey-adminBtn--disabled").removeClass("--loading").text(reyAdminParams.dashboard_strings.default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.dashboard_strings.something_went_wrong+"</p>"),console.error(e),console.error(a),console.error(s)}})}));e(".js-dashBox-installRequired").on("click",(function(a){a.preventDefault();var s=e(this);s.addClass("rey-adminBtn--disabled").text(reyAdminParams.dashboard_strings.installing_btn_text),e(".reyAdmin-reqPlugin.--inactive:not(.--uninstallable)",s.closest(".rey-dashBox-content")).addClass("--is-installing"),e(".reyAdmin-response").remove();var t=new URL(window.location.href).searchParams.get("page")||"",n=function(){e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_install_plugins",security:reyAdminParams.ajax_dashboard_nonce,page:t},success:function(a){if(r(),a)if(a.success&&!0===a.success&&!1!==a.data){var t=s.closest(".rey-dashBox-content");e(".reyAdmin-reqPlugin[data-slug='"+a.data+"']",t).removeClass("--is-installing --inactive").addClass("--is-active"),n()}else s.text(reyAdminParams.dashboard_strings.reloading_text),setTimeout((function(){location.reload()}),2e3);else s.text(reyAdminParams.dashboard_strings.default_install_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+a.data+"</p>")},error:function(e,a,t){r(),s.text(reyAdminParams.dashboard_strings.default_install_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.dashboard_strings.something_went_wrong+"</p>"),console.error(e),console.error(a),console.error(t)}})};n()})),e(".js-subscribeNewsletterForm").on("submit",(function(a){a.preventDefault();var s=e(this),t=s.find(".rey-adminBtn");e(".rWizard-response").remove(),t.addClass("rey-adminBtn--disabled --loading"),e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_newsletter_subscribe",security:reyAdminParams.ajax_dashboard_nonce,rey_email_address:e('input[name="rey_email_address"]',s).val()},success:function(e){t.removeClass("--loading"),e&&(e.success&&1==e.success?(t.text(reyAdminParams.dashboard_strings.reloading_text),setTimeout((function(){location.reload()}),2e3)):t.removeClass("rey-adminBtn--disabled").text(reyAdminParams.dashboard_strings.subscribe_default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+e.data+"</p>")),r()},error:function(e,a,s){t.removeClass("rey-adminBtn--disabled").removeClass("--loading").text(reyAdminParams.dashboard_strings.subscribe_default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.dashboard_strings.something_went_wrong+"</p>"),console.error(e),console.error(a),console.error(s),r()}})})),e(".js-installChild").on("click",(function(a){a.preventDefault();var s=e(this);s.addClass("rey-adminBtn--disabled --loading"),e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_install_child",security:reyAdminParams.ajax_dashboard_nonce},success:function(a){s.removeClass("--loading"),a&&a.success&&(s.text(reyAdminParams.dashboard_strings.copying_settings),e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_dashboard_migrate_opts_child",security:reyAdminParams.ajax_dashboard_nonce},success:function(e){s.removeClass("--loading"),e&&e.success&&(s.text(reyAdminParams.dashboard_strings.reloading_text),setTimeout((function(){location.reload()}),2e3)),r()}}))},error:function(e,a,t){s.removeClass("rey-adminBtn--disabled").removeClass("--loading").text(reyAdminParams.dashboard_strings.installing_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.dashboard_strings.something_went_wrong+"</p>"),console.error(e),console.error(a),console.error(t)}})})),e(".js-childCopySettings").on("click",(function(a){a.preventDefault(),e(this).addClass("rey-adminBtn--disabled --loading")})),e("input#rey-purchase-code").on("change",(function(e){this.value=this.value.replace(/\s+/g,"")})),e("input#rey-purchase-code").on("keyup",(function(e){32==e.keyCode&&(this.value=this.value.replace(/\s+/g,""))}));new function(){var a=this;this.init=function(){this.$installWizardPage=e('body[class*="_page_rey-setup-wizard"]'),this.$installWizardPage.length&&(this.$installWrapper=e(".rey-wizard-wrapperInner",this.$installWizardPage),this.$installSteps=e(".rWizard-step",this.$installWizardPage),this.$registerForm=e(".js-rWizard-registrationForm",this.$installWizardPage),this.$registerStepButton=e(".rey-adminBtn.rey-adminBtn-primary",this.$registerForm),this.$disableWizardBtn=e(".js-skipWizard",this.$installWizardPage),this.startStep=e(".rWizard-step--1.--registered",this.$installWizardPage).length?1:0,1===this.startStep&&(this.startStep=e(".rWizard-step--2.--plugins-installed",this.$installWizardPage).length?2:1),this.$installSteps.eq(this.startStep).addClass("--active"),this.$installWrapper.height(this.$installSteps.eq(this.startStep).outerHeight()).css("opacity",1),this.testConnection(),this.events())},this.events=function(){this.$registerForm.on("submit",(function(s){s.preventDefault(),e(".rWizard-response").remove(),a.$registerStepButton.addClass("--loading").text(reyAdminParams.wizard_strings.registering_btn_text),e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_register_purchase_code",security:reyAdminParams.ajax_wizard_nonce,rey_purchase_code:e('input[name="rey_purchase_code"]',a.$registerForm).val(),rey_email_address:e('input[name="rey_email_address"]',a.$registerForm).val(),rey_subscribe_newsletter:e('input[name="rey_subscribe_newsletter"]',a.$registerForm).val()},success:function(s){if(a.$registerStepButton.removeClass("--loading"),s)if(s.success){var t=e(".js-reyAdmin-reqPlugins");t.addClass("--loading"),e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_wizard_get_required_plugins_markup",security:reyAdminParams.ajax_wizard_nonce},success:function(e){e.success&&t.empty().html(e.data),t.removeClass("--loading"),a.stepNewPanel(0,1)}})}else a.$registerStepButton.text(reyAdminParams.wizard_strings.default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+s.data+"</p>")},error:function(e,s,t){a.$registerStepButton.removeClass("--loading").text(reyAdminParams.wizard_strings.default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.wizard_strings.something_went_wrong+"</p>")}})})),e(".js-skipRegistration",this.$installWizardPage).on("click",(function(e){e.preventDefault(),a.stepNewPanel(0,1),a.$installWrapper.addClass("--skipped-registration")})),e(".js-backRegistration",this.$installWizardPage).on("click",(function(e){e.preventDefault(),a.$installSteps.removeClass("--active"),a.$installSteps.eq(0).addClass("--active").fadeIn(),a.$installWrapper.height(a.$installSteps.eq(0).outerHeight()).removeClass("--skipped-registration")})),e(".js-rWizard-btnStep-2",this.$installWizardPage).on("click",(function(s){s.preventDefault();var t=e(this),r=t.closest(".rWizard-step"),n=e("#wizardInstallChild").prop("checked");t.addClass("rey-adminBtn--disabled").text(reyAdminParams.wizard_strings.installing_btn_text),n&&(e("#wizardInstallChild").prop("disabled"),e(".rWizard-installChild",r).addClass("--is-installing")),e(".reyAdmin-reqPlugin.--inactive:not(.--uninstallable)",r).addClass("--is-installing"),e(".reyAdmin-response").remove();var i=new URL(window.location.href).searchParams.get("page")||"",o=function(){e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_wizard_install_plugins",security:reyAdminParams.ajax_wizard_nonce,page:i,child_theme:n},success:function(s){s?s.success&&!0===s.success&&!1!==s.data&&"child_theme"!==s.data?(e(".reyAdmin-reqPlugin[data-slug='"+s.data+"']",r).removeClass("--is-installing --inactive").addClass("--is-active"),o()):(a.stepNewPanel(1,2),e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_wizard_skip",security:reyAdminParams.ajax_wizard_nonce},success:function(e){e&&e.success&&1==e.success&&a.$disableWizardBtn.css("opacity",0)}}),e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_wizard_enable_child_theme",security:reyAdminParams.ajax_wizard_nonce}})):t.text(reyAdminParams.wizard_strings.default_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+s.data+"</p>")},error:function(e,a,s){t.text(reyAdminParams.wizard_strings.default_install_btn_text).parent().before("<p class='reyAdmin-response reyAdmin-notice --error'>"+reyAdminParams.wizard_strings.something_went_wrong+"</p>")}})};o()})),this.$disableWizardBtn.on("click",(function(a){a.preventDefault();var s=e(this);s.addClass("rey-adminBtn--disabled --loading"),e.ajax({method:"get",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_wizard_skip",security:reyAdminParams.ajax_wizard_nonce},success:function(e){e&&e.success&&1==e.success&&(s.text(reyAdminParams.wizard_strings.skipping_success),setTimeout((function(){window.location.href=reyAdminParams.dashboard_url}),2e3))}})}))},this.stepNewPanel=function(e,a){this.$installSteps.removeClass("--active"),this.$installSteps.eq(e).fadeOut(300),this.$installSteps.eq(a).addClass("--active"),this.$installWrapper.height(this.$installSteps.eq(a).outerHeight())},this.testConnection=function(){var s=document.getElementById("wizard-testing-connection");s&&(this.$registerForm.css({opacity:.5,"pointer-events":"none"}),e.ajax({method:"post",url:reyAdminParams.ajax_url,data:{action:"rey_wizard_test_connection"},success:function(e){e.success?rey.animation.fadeOut(s,(function(){a.$registerForm.css({opacity:"","pointer-events":""})})):(a.$installWrapper.addClass("--failed-connection"),e.data&&(s.innerHTML=e.data))}}))},this.init()},e("#dashbox-search-kb").on("keyup",(function(e){if(13==e.keyCode){var a=e.target.value.trim().replace(/ /g,"+");window.open(reyAdminParams.support_url+"?source=kb&s="+a,"_blank").focus()}}));var o=!1;window.addEventListener("beforeunload",(e=>{o&&(e.preventDefault(),e.returnValue="Updates in progress! Please don't close window.")})),e(".js-dashRollback").each((function(a,s){var t=e(s),n=t.prev("select"),i=t.siblings(".js-dashResponse");n.on("change",(function(e){e.target.value?t.removeClass("--disabled"):t.addClass("--disabled")})),t.on("click",(function(a){a.preventDefault(),i.text("");var s=e(this),t=s.closest(".rey-versionsStatus"),d=JSON.parse(s.attr("data-settings")||"{}"),l=n.val();confirm("Are you sure you want to rollback "+d.name+" to version "+l+"?")&&(s.addClass("--loading"),t.addClass("--locked"),o=!0,e.ajax({method:"post",url:reyAdminParams.ajax_url,cache:!1,data:{action:"rey_rollback_version",security:reyAdminParams.ajax_rollback_nonce,key:d.key,version:l},success:function(e){if(e){var a;if(!e.success)return s.removeClass("--loading").addClass("--disabled").text(reyAdminParams.rollback_strings.failed),void i.text((a=e.data,"string"!=typeof a?a.join("\r\n"):a));t.removeClass("--locked"),s.removeClass("--loading").addClass("--disabled").text(reyAdminParams.rollback_strings.skipping_success),o=!1,setTimeout((function(){window.location.href=reyAdminParams.dashboard_url}),2e3),r()}else i.text(reyAdminParams.rollback_strings.something_went_wrong)},error:function(e,a,s){console.error(e),console.error(a),console.error(s)}}))}))}));var d=document.querySelector(".rey-dashboard-main");d&&e.ajax({url:reyAdminParams.ajax_url,data:{action:"rey_dashbox_run_test"},success:function(e){rey.hooks.doAction("dashbox/test",e.success),e.success?(d.classList.add("--ready"),r()):e.data&&console.error(e.data)}});var l={init:function(){this.statusBox=document.querySelector(".rey-versionsStatus"),this.statusBox&&(this.mainBox=this.statusBox.closest(".rey-dashBox"),this.mainBox&&this.start())},start:function(){rey.hooks.addAction("dashbox/test",(function(e){l.mainBox.classList.remove("--loading"),e&&(l.statusBox.setAttribute("data-supports-updates","yes"),l.checkNewUpdates(),l.handleClickRefresh(),r())}))},handleClickRefresh:function(){this.refreshBtn=this.mainBox.querySelector(".js-check-updates"),this.refreshBtn&&this.refreshBtn.addEventListener("click",(function(a){a.preventDefault(),l.mainBox.classList.add("--loading"),l.mainBox.querySelectorAll(".rey-updateItem").forEach((e=>{e.remove()})),e.ajax({url:reyAdminParams.ajax_url,data:{action:"rey_dashbox_flush_versions",security:reyAdminParams.updates_nonce},success:function(e){e.success?(l.checkNewUpdates(),l.mainBox.classList.remove("--loading"),r()):e.data&&console.error(e.data)}})}))},checkNewUpdates:function(){["rey-core","rey"].forEach((a=>{var s=l.statusBox.querySelector(`.__version[data-slug="${a}"]`);if(s){var t=document.createElement("span");t.classList.add("rey-spinnerIcon");var n=s.querySelector(".__content");n&&(n.append(t),e.ajax({url:reyAdminParams.ajax_url,data:{action:"rey_dashbox_versions_check_update",security:reyAdminParams.updates_nonce,slug:a},success:function(e){t.remove(),e.success?(e.data&&n.append(rey.dom.createElementFromHTML(e.data)),l.registerUpdateButtons(n),r()):e.data&&console.error(e.data)}}))}}))},registerUpdateButtons:function(a){var s=a.querySelector(".rey-updateItem");s&&s.addEventListener("click",(function(a){a.preventDefault(),o=!0,s.classList.add("--loading"),d.classList.add("--disabled"),e.ajax({url:reyAdminParams.ajax_url,data:{action:"rey_dashbox_do_update",security:reyAdminParams.updates_nonce,slug:s.getAttribute("data-slug")},success:function(e){(s.classList.remove("--loading"),s.classList.add("--disabled"),d.classList.remove("--disabled"),e.success)?(s.textContent="Updated!",s.previousElementSibling.textContent=s.getAttribute("data-version"),setTimeout((function(){s.remove(),o=!1}),2e3)):e.data&&console.error(e.data)}})}))}};l.init(),rey.hooks.addAction("dashbox/test",(function(a){if(a){var s=document.querySelector(".rey-systemStatus .rey-apiConnection");s&&e.ajax({url:reyAdminParams.ajax_url,data:{action:"rey_dashbox_test_connection"},success:function(e){e.data&&(s.querySelector(".__status").innerHTML=e.data,r())}})}})),rey.elements.body.addEventListener("click",(function(e){if(!e.target.closest("[data-copy-contents]"))return;e.preventDefault();const a=document.createElement("input");document.body.appendChild(a),a.value=e.target.textContent,a.select(),document.execCommand("copy"),a.remove(),e.target.hasAttribute("data-corner-label")&&e.target.setAttribute("data-corner-label","Copied!")}))}(jQuery);