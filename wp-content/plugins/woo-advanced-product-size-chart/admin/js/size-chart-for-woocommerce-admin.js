/*
 * Custom Script file
 */
(function($, window, document) {
	'use strict';
	var searchTimer;
	var sizeChartScripts = {
		init: function() {
			sizeChartScripts.sizeChartTableCellValidation();
			sizeChartScripts.loadSizeChartMenuScript();
			sizeChartScripts.loadChartCategorySelect2();
			sizeChartScripts.loadProductChartSelect2();
			sizeChartScripts.loadColorPicker();
			sizeChartScripts.loadPreviewSizeChart();
			sizeChartScripts.loadSizeChartProductMetaColumn();
			sizeChartScripts.loadSizeChartProductMetaAjax();
			sizeChartScripts.deleteSizeChartImage();
			sizeChartScripts.closeSizeChartModal();
			sizeChartScripts.requiredSizeChartTitle();
			sizeChartScripts.deleteAssignedProducts();
            sizeChartScripts.multiRowColumnModule();
            sizeChartScripts.sizeChartImportExportModule();
		},
		sizeChartTableCellValidation: function() {
		    $('#post').submit(function(e){
		        var isValid = true;

		        $('#size-chart-meta-fields .inputtable input').each(function(){
		            let tableFieldVal = $(this).val();
		            let regexPattern = /^[^"\u201C\u201D\\]*$/;
					if(regexPattern.test(tableFieldVal) === false ) {
						isValid = false;
						$(this).parent().css('border', '2px solid red');
					}
		        });

		        if(!isValid) {
		            e.preventDefault();
		            alert(sizeChartScriptObject.size_chart_field_validation);
		        }
		    });
		},
		loadSizeChartMenuScript: function() {
			var dotStoreMenu = $('#toplevel_page_dots_store');
			if ((
				'admin_page_size-chart-setting-page' === sizeChartScriptObject.size_chart_current_screen_id ||
				'dotstore-plugins_page_size-chart-information' === sizeChartScriptObject.size_chart_current_screen_id ||
				'dotstore-plugins_page_size-chart-import-export' === sizeChartScriptObject.size_chart_current_screen_id
			)) {
				dotStoreMenu.addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
				$('#toplevel_page_dots_store > a').addClass('wp-has-current-submenu current').removeClass('wp-not-current-submenu');
				$('li#menu-posts').removeClass('wp-not-current-submenu wp-has-current-submenu wp-menu-open current');
				$('li.mine').css('display', 'none');
				$('li.publish').css('display', 'none');
				$('a[href="admin.php?page=' + sizeChartScriptObject.size_chart_get_started_page_slug + '"]').parent().addClass('current');
			}

			if (
                'edit-size-chart' === sizeChartScriptObject.size_chart_current_screen_id ||
				'size-chart' === sizeChartScriptObject.size_chart_current_screen_id
			) {
				dotStoreMenu.addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
				$('#toplevel_page_dots_store > a').addClass('wp-has-current-submenu current').removeClass('wp-not-current-submenu');
				$('li#menu-posts').removeClass('wp-not-current-submenu wp-has-current-submenu wp-menu-open current');
				$('li.mine').css('display', 'none');
				$('li.publish').css('display', 'none');
				$('a[href="admin.php?page=' + sizeChartScriptObject.size_chart_get_started_page_slug + '"]').parent().addClass('current');
			}

			$('#toplevel_page_dots_store ul li').each(function() {
				if ('undefined' !== typeof sizeChartScriptObject.size_chart_plugin_menu_url) {
					if (sizeChartScriptObject.size_chart_plugin_name === $(this).text()) {
						$(this).find('a').attr('href', sizeChartScriptObject.size_chart_plugin_menu_url);
					}
					if (sizeChartScriptObject.size_chart_plugin_menu_url === $(this).find('a').attr('href')) {
						$(this).find('a').attr('href', sizeChartScriptObject.size_chart_plugin_menu_url);
					}
				}
			});

			if ('admin.php?page=' + sizeChartScriptObject.size_chart_get_started_page_slug === dotStoreMenu.find('a').attr('href')) {
				if ('undefined' !== typeof sizeChartScriptObject.size_chart_plugin_menu_url) {
					dotStoreMenu.find('a').attr('href', sizeChartScriptObject.size_chart_plugin_menu_url);
				}
			}
		},
		loadChartCategorySelect2: function() {

			/**
			 * Chart category select2.
			 * @type {{escapeMarkup: (function(*): *), maximumSelectionLength: number}}
			 */
			var sccSelectWoo = {
				escapeMarkup: function(m) {
					return m;
				},
				maximumSelectionLength: 100,
			};
			$('#chart-categories').selectWoo(sccSelectWoo).addClass('enhanced');

			/**
			 * Chart Tags select2.
			 * @type {{escapeMarkup: (function(*): *), maximumSelectionLength: number}}
			 */
			 var sctSelectWoo = {
				escapeMarkup: function(m) {
					return m;
				},
				maximumSelectionLength: 100,
			};
			$('#chart-tags').selectWoo(sctSelectWoo).addClass('enhanced');

			/**
			 * Chart Attributes select2.
			 * @type {{escapeMarkup: (function(*): *), maximumSelectionLength: number}}
			 */
			 var scaSelectWoo = {
				escapeMarkup: function(m) {
					return m;
				},
				maximumSelectionLength: 100,
			};
			$('#chart-attributes').selectWoo(scaSelectWoo);

            /**
			 * Country select2.
			 * @type {{escapeMarkup: (function(*): *), maximumSelectionLength: number}}
			 */
			 var sccSelectWoo2 = {
				escapeMarkup: function(m) {
					return m;
				},
				maximumSelectionLength: 100,
			};
			$('#chart-country').selectWoo(sccSelectWoo2);
		},
		loadProductChartSelect2: function() {

			/**
			 * Ajax customer search boxes.
			 */
			$(':input#prod-chart').filter(':not(.enhanced)').each(function() {
				var terms = [];
				var select2Args = {
					allowClear: $(this).data('allow_clear') ? true : false,
					placeholder: $(this).data('placeholder'),
					minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : '1',
					escapeMarkup: function(m) {
						return m;
					},
					ajax: {
						url: sizeChartScriptObject.size_chart_admin_url,
						dataType: 'json',
						delay: 1000,
						data: function(params) {
							return {
								'searchQueryParameter': params.term,
								action: 'size_chart_search_chart',
								security: $(this).data('nonce'),
								exclude: $(this).data('exclude'),
							};
						},
						processResults: function(data) {
							terms = [];
							if (data) {
								$.each(data, function(id, text) {
									terms.push({
										id: id,
										text: text,
									});
								});
							}
							return {
								results: terms,
							};
						},
						cache: true,
					},
				};

				$(this).selectWoo(select2Args).addClass('enhanced');

			});
		},
		loadColorPicker: function() {

			/**
			 * Load color picker.
			 */
			$('#color-picker1,#color-picker2,#color-picker3,#color-picker4,#color-picker5,#color-picker6').wpColorPicker();
		},
		loadPreviewSizeChart: function() {

			/**
			 * Preview size chart.
			 */
			$('a.preview_chart').click(function() {
				var dataObj = {},
					chartID = $(this).attr('id'),
					modal = '',
					cssSelector = sizeChartScriptObject.size_chart_plugin_dash_name + '-inline-css';
				$('.size-chart-model').css('padding', '0');
				$('#wait').show();
				$('[data-remodal-id=modal]').html('');
                var farr = [];
                var chart_color = {};
                var chart_border = {};
                var popup_style = $('#table-style').val();
                
				dataObj = {
					'action': 'size_chart_preview_post',
					chartID: chartID,
                    data: farr,
                    chart_color:chart_color,
                    chart_border:chart_border,
                    popup_style: popup_style,
					'security': sizeChartScriptObject.size_chart_nonce,
				};

				$.ajax({
					type: 'GET',
					url: sizeChartScriptObject.size_chart_admin_url,
					data: dataObj,
					dataType: 'json',
					beforeSend: function() {
						$('#wait').show().css('position', 'fixed');
					}, complete: function() {
						$('#wait').hide().css('position', '');
					}, success: function(response) {
						if (1 === response.success) {
							$('.size-chart-model').css('padding', '35px');
							modal = document.getElementById('md-size-chart-modal');
							modal.style.display = 'block';
							$('#md-size-chart-modal').removeClass('md-size-chart-hide');
							$('#md-size-chart-modal').addClass('md-size-chart-show');
							$('#md-poup').append(response.html);
							$('#' + cssSelector).text(response.css);
						} else {
							alert('size-chart-for-woocommerce-premium==>' + response.msg);
						}
					},
				});
			});
		},
		loadSizeChartProductMetaColumn: function() {

			/**
			 * Size chart metabox setting columns.
			 */
			$('#size-chart-menu-settings-column').bind('click', function(e) {
				var panelId, wrapper,
					target = $(e.target);
				if (target.hasClass('nav-tab-link')) {
					panelId = target.data('type');
					wrapper = target.parents('.size-chart-accordion-section-content').first();

					// upon changing tabs, we want to uncheck all checkboxes
					$('input', wrapper).removeAttr('checked');
					$('.tabs-panel-active', wrapper).removeClass('tabs-panel-active').addClass('tabs-panel-inactive');
					$('#' + panelId, wrapper).removeClass('tabs-panel-inactive').addClass('tabs-panel-active');
					$('.tabs', wrapper).removeClass('tabs');
					target.parent().addClass('tabs');

					// select the search bar.
					$('.quick-search', wrapper).focus();

					// Hide controls in the search tab if no items found.
					if ( !wrapper.find('.tabs-panel-active .menu-item-title').length) {
						wrapper.addClass('has-no-menu-item');
					} else {
						wrapper.removeClass('has-no-menu-item');
					}
					e.preventDefault();
				}
			});
		},
		loadSizeChartProductMetaAjax: function() {

			/**
			 * Size chart meta product and product pagination.
			 */
			$('div#tabs-panel-posttype-size-chart-all').on('click', 'ul.pagination li a.page-numbers', function(e) {
				var pageNumber, postID, postPerPage, data, subLiTag, subSpanTag, subATag, paginationSubLiTag, paginationSubTag, paginationClass;
				e.preventDefault();
				pageNumber = $(this).data('page-number');
				postID = $(this).data('post-id');
				postPerPage = $(this).data('post-per-page');
				data = {
					'action': 'size_chart_product_assign',
					'pageNumber': pageNumber,
					'postID': postID,
					'postPerPage': postPerPage,
					'security': $(this).parent().parent().data('nonce'),
				};

				$.ajax({
					type: 'GET',
					url: sizeChartScriptObject.size_chart_admin_url,
					data: data,
					dataType: 'json',
					beforeSend: function() {
						$('div#tabs-panel-posttype-size-chart-all .spinner').addClass('is-active');
					}, complete: function() {
						$('div#tabs-panel-posttype-size-chart-all .spinner').removeClass('is-active');
					}, success: function(response) {

						if (true === response.success) {
							$('ul#size-chart-checklist-all').empty();
							$.each(response.found_products, function(loopKey, loopValue) {
								subLiTag = $('<li/>');
								subATag = $('<a />', {'href': loopValue.href.replace('&#038;', '&'), text: loopValue.title});
								subATag.appendTo(subLiTag);
								subSpanTag = $('<span />', {'class': 'remove-product-icon', text: '×', 'data-id':loopKey});
								subSpanTag.appendTo(subLiTag);
								subLiTag.appendTo('ul#size-chart-checklist-all');
							});

							$('nav.pagination-box ul.pagination').empty();
							$.each(response.load_pagination, function(paginationKey, paginationValue) {
								paginationSubLiTag = $('<li/>');
								if ('number' === paginationValue.pagination_mode) {
									if ('span' === paginationValue.pagination_tag) {
										paginationSubTag = $('<span />', {
											class: 'page-numbers ' + paginationValue.pagination_class,
											text: paginationValue.page_text,
										});

									} else {
										paginationClass = 'page-numbers ';
										if ('' !== paginationValue.pagination_class) {
											paginationClass += paginationValue.pagination_class;
										}

										paginationSubTag = $('<a />', {
											href: 'javascript:void(0);',
											class: paginationClass,
											text: paginationValue.page_text,
											'data-post-id': paginationValue.post_id,
											'data-post-per-page': paginationValue.post_per_page,
											'data-page-number': paginationValue.page_number,
										});
									}
								} else if ('dots' === paginationValue.pagination_mode) {
									paginationSubTag = $('<span />', {
										class: 'page-numbers ' + paginationValue.pagination_class,
										text: paginationValue.page_text,
									});
								}
								paginationSubTag.appendTo(paginationSubLiTag);
								paginationSubLiTag.appendTo('nav.pagination-box ul.pagination');
							});

						}
					},
				});
			});

			/**
			 * Size chart meta search product.
			 */
			$('div#tabs-panel-posttype-size-chart-search').on('input', '.quick-search', function() {
				var $this = $(this);
				$this.attr('autocomplete', 'off');
				if (searchTimer) {
					clearTimeout(searchTimer);
				}
				searchTimer = setTimeout(function() {
					var panel, params,
						minSearchLength = 2,
						searchQueryParameter = $this.val(),
						subLiTag, subLabel, inputCheckbox;

					if (searchQueryParameter.length < minSearchLength) {
						return;
					}

					panel = $this.parents('.tabs-panel');
					params = {
						'action': 'size_chart_quick_search_products',
						'security': $this.data('nonce'),
						'postType': $this.data('post_type'),
						'searchQueryParameter': searchQueryParameter,
						'type': $this.attr('name'),
					};

					$.ajax({
						type: 'GET',
						url: sizeChartScriptObject.size_chart_admin_url,
						data: params,
						dataType: 'json',
						beforeSend: function() {
							$('.quick-search-wrap .spinner', panel).addClass('is-active');
						}, complete: function() {
							$('.quick-search-wrap .spinner', panel).removeClass('is-active');
						}, success: function(response) {
							$('ul#size-chart-search-checklist').empty();
							if (true === response.success) {
								$.each(response.found_products, function(loopKey, loopValue) {
									subLiTag = $('<li/>').appendTo('ul#size-chart-search-checklist');
									subLabel = $('<label />', {'for': 'size-chart-product-' + loopKey, text: loopValue.title});
									inputCheckbox = $('<input />', {type: 'checkbox', id: 'size-chart-product-' + loopKey, value: loopValue.id, class: 'product-item-checkbox', name: 'product-item[' + loopValue.id + ']'});
									inputCheckbox.prependTo(subLabel);
									subLabel.appendTo(subLiTag);
								});
							} else {
								subLiTag = $('<li/>').appendTo('ul#size-chart-search-checklist');
								subLabel = $('<p />', {text: response.msg});
								subLabel.appendTo(subLiTag);
							}
						},
					});

				}, 500);
			});

		},
		deleteSizeChartImage: function() {

			/**
			 * Ajax for delete image.
			 */
			$('a.delete-chart-image').click(function() {
				var postID = $(this).attr('id');
				var data = {
					'action': 'size_chart_delete_image',
					'postID': postID,
					'security': sizeChartScriptObject.size_chart_nonce,
				};

				$.ajax({
					type: 'GET',
					url: sizeChartScriptObject.size_chart_admin_url,
					data: data,
					beforeSend: function() {
						$('#wait').show().css('position', 'fixed');
					}, complete: function() {
						$('#wait').hide().css('position', '');
					}, success: function(response) {
						var result = $.parseJSON(response);
						if (1 === result.success) {
							$('#field-image img').attr({'src': result.url, 'width': '', 'height': ''});
							$('#primary-chart-image').val('');
							$('.delete-chart-image').css('display', 'none');
							alert(result.msg);
						} else {
							alert(result.msg);
						}
					},
				});
			});
		},
		closeSizeChartModal: function() {

			/**
			 * Close popup.
			 */
			// $('div#md-size-chart-modal .remodal-close').click(function() {
            $(document).on('click', 'div#md-size-chart-modal .remodal-close', function() {
				// var modal = document.getElementById('md-size-chart-modal');
				$('.chart-container').remove();
				$('.md-size-chart-close').remove();
				// modal.style.display = 'none';
				$('#md-size-chart-modal').removeClass('md-size-chart-show');
				$('#md-size-chart-modal').addClass('md-size-chart-hide');
			});

			/**
			 * Close popup.
			 */
			$('div.md-size-chart-overlay').click(function() {
				// var modal = document.getElementById('md-size-chart-modal');
				$('.chart-container').remove();
				$('.md-size-chart-close').remove();
				// modal.style.display = 'none';
				$('#md-size-chart-modal').removeClass('md-size-chart-show');
				$('#md-size-chart-modal').addClass('md-size-chart-hide');
			});
		},
		requiredSizeChartTitle: function() {

			/**
			 * Required the size chart.
			 */
			$('body').on('submit.edit-post', '#post', function() {
				var getPostType, sizeChartTitleSelector, sizeChartPostTitleRequiredMsg;
				getPostType = $('input#post_type').val();
				if (sizeChartScriptObject.size_chart_post_type_name === getPostType) {
					sizeChartTitleSelector = $('#title');
					if (0 === sizeChartTitleSelector.val().replace(/ /g, '').length) {
						if ( !$('#size-chart-title-required-msg').length) {
							sizeChartPostTitleRequiredMsg = sizeChartScriptObject.size_chart_post_title_required;

							$('<div/>', {
								'id': 'size-chart-title-required-msg',
							}).appendTo('div#titlewrap');

							$('<em/>', {
								text: sizeChartPostTitleRequiredMsg,
							}).appendTo('#size-chart-title-required-msg');

							$('input#title').css({
								'border': '1px solid #c00',
								'box-shadow': '0 0 2px rgb(204, 0, 0, 0.8)',
							});

						}
						$('#major-publishing-actions .spinner').hide();
						$('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');
						sizeChartTitleSelector.focus();
						return false;
					}
				}
			});
			$('input#title').on('change', function() {
				$('#size-chart-title-required-msg').remove();
				$('input#title').css({
					'border': '1px solid #ddd',
				});
			});

		},
		deleteAssignedProducts: function() {
			/**
			 * Ajax for assigning the product from chart
			 */
			 $('span.remove-product-icon').click(function(e) {
				var prompt_ask = confirm('Are you sure want to remove the product from chart?');
				if ( ! prompt_ask ) {
					return false;
				}
				var postID  = $(this).data('id');
				var chartID = $(this).data('chart');
				var data = {
					'action': 'size_chart_unassign_product',
					'postID': postID,
					'chartID': chartID,
					'security': sizeChartScriptObject.size_chart_nonce,
				};
                
                $.ajax({
					type: 'POST',
					url: sizeChartScriptObject.size_chart_admin_url,
					data: data,
					beforeSend: function() {
						console.log('before send triggered');
					}, complete: function() {
						console.log('complete triggered');
					}, success: function(response) {
						var result = $.parseJSON(response);
						if ( 1 === result.success ) {
							$(e.target).parent().remove();
							if ( $('#size-chart-checklist-all li').length === 0 ) {
								$('#size-chart-checklist-all').text(sizeChartScriptObject.size_chart_no_product_assigned);
							}
							// alert(result.msg);
						} else {
							alert(result.msg);
						}
					},
				});
			});
		},
        multiRowColumnModule: function(){

            var stored_style =$('#table-style').val();
            if( $('#table-style').length > 0 ) {
                sync_setting(stored_style);
                border_setting(stored_style);
            }
            if( 'advance-style' !== stored_style ){
                jQuery('.multiple_action_wrap tbody tr').not('.row_wrap').not('.column_wrap').hide();
            } else {
                jQuery('.multiple_action_wrap tbody tr').not('.row_wrap').not('.column_wrap').show();
            }
            $('#table-style').change(function(){
                var style_val = $(this).val();
                if( 'advance-style' !== style_val ){
                    jQuery('.multiple_action_wrap tbody tr').not('.row_wrap').not('.column_wrap').hide();
                } else {
                    jQuery('.multiple_action_wrap tbody tr').not('.row_wrap').not('.column_wrap').show();
                }
                sync_setting(style_val);
                border_setting(style_val);
            });

            //Row action
            $('#scfw_add_multi_row_action').click(function(){
                var count = $('#scfw_add_multi_row').val();
                for ( var i = 0; i < count; i++ ) {
                    $('.addrow').last().trigger('click');
                }
                sync_setting();
                border_setting();
            });
            $('#scfw_delete_multi_row_action').click(function(){
                var count = $('#scfw_add_multi_row').val();
                for ( var i = 0; i < count; i++ ) {
                    $('.delrow').last().trigger('click');
                }
            });

            //Column action
            $('#scfw_add_multi_column_action').click(function(){
                var count = $('#scfw_delete_multi_column').val();
                for (var index = 0; index < count; index++) {
                    $('.addcol').last().trigger('click');
                }
                sync_setting();
                border_setting();
            });
            $('table').on('click', '.addcol, .addrow, .delrow, .delcol', function(){
                setTimeout(function () {
                    sync_setting();
                    border_setting();
                }, 20);
            });
            $('#scfw_delete_multi_column_action').click(function(){
                var count = $('#scfw_delete_multi_column').val();
                for ( var j = 0; j < count; j++ ) {
                    $('.delcol').last().trigger('click');
                }
            });

            $('#scfw_header_bg_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tbody tr:first-child td:not(td:last-child)').css( 'background-color', ui.color.toString() );
                }
            });
            $('#scfw_even_row_bg_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tr:odd:not(:first-child) td:not(td:last-child)').css( 'background-color', ui.color.toString() );
                }
            });
            $('#scfw_odd_row_bg_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tr:even:not(:first-child) td:not(td:last-child)').css( 'background-color', ui.color.toString() );
                }
            });

            $('#scfw_text_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tbody tr:first-child td:not(td:last-child) input').css( 'color', ui.color.toString() );
                }
            });
            $('#scfw_even_text_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tr:odd:not(:first-child) td:not(td:last-child) input').css( 'color', ui.color.toString() );
                }
            });
            $('#scfw_odd_text_color').wpColorPicker({
                change: function (event, ui) {
                    jQuery('table.inputtable tr:even:not(:first-child) td:not(td:last-child) input').css( 'color', ui.color.toString() );
                }
            });
            //Border Color
            $('#scfw_border_color').wpColorPicker({
                change: function () {
                    border_setting('advance-style');
                }
            });
            $('#scfw_border_hb_style, #scfw_border_hw, #scfw_border_vb_style, #scfw_border_vw').on( 'input', function(){
                border_setting('advance-style');
            });
            
            function border_setting( ){
                var table_style =$('#table-style').val(); 
                var scfw_border_color, scfw_border_hb_style,scfw_border_hw,scfw_border_vb_style, scfw_border_vw;

                if( 'advance-style' === table_style ){
                    scfw_border_color = jQuery('#scfw_border_color').val();
                    scfw_border_hb_style = jQuery('#scfw_border_hb_style').val();
                    scfw_border_hw = jQuery('#scfw_border_hw').val();
                    scfw_border_vb_style = jQuery('#scfw_border_vb_style').val();
                    scfw_border_vw = jQuery('#scfw_border_vw').val();
                } else {
                    table_style = $('#table-style').val();
                    var table_style_data = sizeChartScriptObject.size_chart_chart_table_style[table_style];
                    scfw_border_color = table_style_data.border_color;
                    scfw_border_hb_style = table_style_data.border_hb_style;
                    scfw_border_hw = table_style_data.border_hw;
                    scfw_border_vb_style = table_style_data.border_vb_style;
                    scfw_border_vw = table_style_data.border_vw;
                }

                jQuery('table.inputtable tbody tr td:not(td:last-child)').css( 'border-top', scfw_border_hw + 'px ' + scfw_border_hb_style + ' ' +scfw_border_color );
                jQuery('table.inputtable tbody tr td:not(td:last-child)').css( 'border-bottom', scfw_border_hw + 'px ' + scfw_border_hb_style + ' ' +scfw_border_color );

                jQuery('table.inputtable tbody tr td:not(td:last-child)').css( 'border-left', scfw_border_vw + 'px ' + scfw_border_vb_style + ' ' +scfw_border_color );
                jQuery('table.inputtable tbody tr td:not(td:last-child)').css( 'border-right', scfw_border_vw + 'px ' + scfw_border_vb_style + ' ' +scfw_border_color );
            }

            function sync_setting() {
                var table_style =$('#table-style').val();
                var scfw_header_bg_color, scfw_even_row_bg_color, scfw_odd_row_bg_color, scfw_text_color, scfw_even_text_color, scfw_odd_text_color;

                if( 'advance-style' === table_style ){
                    scfw_header_bg_color = jQuery('#scfw_header_bg_color').val();
                    scfw_even_row_bg_color = jQuery('#scfw_even_row_bg_color').val();
                    scfw_odd_row_bg_color = jQuery('#scfw_odd_row_bg_color').val();
                    scfw_text_color = jQuery('#scfw_text_color').val();
                    scfw_even_text_color = jQuery('#scfw_even_text_color').val();
                    scfw_odd_text_color = jQuery('#scfw_odd_text_color').val();
                } else {
                    // table_style = $('#table-style').val();
                    var table_style_data = sizeChartScriptObject.size_chart_chart_table_style[table_style];
                    scfw_header_bg_color = table_style_data.header_bg_color;
                    scfw_even_row_bg_color = table_style_data.even_row_bg_color;
                    scfw_odd_row_bg_color = table_style_data.odd_row_bg_color;
                    scfw_text_color = table_style_data.text_color;
                    scfw_even_text_color = table_style_data.even_text_color;
                    scfw_odd_text_color = table_style_data.odd_text_color;
                }
                
                jQuery('table.inputtable tbody tr:first-child td:not(td:last-child)').css('background-color', scfw_header_bg_color);

                //For our table even rows
                jQuery('table.inputtable tr:odd:not(:first-child) td:not(td:last-child)').css('background-color', scfw_even_row_bg_color);

                //For our table odd rows
                jQuery('table.inputtable tr:even:not(:first-child) td:not(td:last-child)').css('background-color', scfw_odd_row_bg_color);
 
                jQuery('table.inputtable tbody tr:first-child td:not(td:last-child) input').css('color', scfw_text_color);

                //For our table even rows 
                jQuery('table.inputtable tr:odd:not(:first-child) td:not(td:last-child) input').css('color', scfw_even_text_color);

                //For our table odd rows 
                jQuery('table.inputtable tr:even:not(:first-child) td:not(td:last-child) input').css('color', scfw_odd_text_color);
            }
        },
        sizeChartImportExportModule: function(){
            /**
			 * Ajax for export size chart table data
			 */
			 $('.export_chart').click(function(e) {
                e.preventDefault();
				var prompt_ask = confirm('Are you sure want to export size chart data?');
				if ( ! prompt_ask ) {
					return false;
				}
                $('.inputtable').parent().parent().block({
                    message: null,
                    overlayCSS: {
                        background: 'rgb(255, 255, 255)',
                        opacity: 0.6,
                    },
                });
				var chartID  = $(this).attr('id');
				var data = {
					'action': 'size_chart_export_data',
					'chartID': chartID,
					'security': sizeChartScriptObject.size_chart_nonce,
				};
                
                $.ajax({
					type: 'POST',
					url: sizeChartScriptObject.size_chart_admin_url,
					data: data,
					success: function(response) {
                        if( response.data.download_path ){
                            var link = document.createElement('a');
                            document.body.appendChild(link);
                            link.href = response.data.download_path;
                            link.download = '';
                            link.click();
                        }
                        $('.inputtable').parent().parent().unblock();
					},
				});
			});

            /**
			 * Ajax for import size chart table data
			 */
            $('.import_chart').click(function(e) {
                e.preventDefault();
                jQuery('.scfw_import_file').trigger('click');
            });
            $('.scfw_import_file').change(function(e) {
                e.preventDefault();
                //Get reference of FileUpload.
                var fileUpload = $(this);
                var p = $('<p>');
                var msg = '';
                //Check whether the file is valid Image.
                var regex = new RegExp('([a-zA-Z0-9\s_\\.\-:])+(.json)$');
                if (regex.test(fileUpload.val().toLowerCase())) {
                    $('.thedotstore-main-table table').block({
                        message: null,
                        overlayCSS: {
                            background: 'rgb(255, 255, 255)',
                            opacity: 0.6,
                        },
                    });
                    var chartID  = $('.import_chart').attr('id');
                    var fd = new FormData();
                    fd.append('import_file', fileUpload[0].files[0]);  
                    fd.append('action', 'size_chart_import_data');
                    fd.append('chartID', chartID);
                    fd.append('security', sizeChartScriptObject.size_chart_nonce);
                    
                    $.ajax({
                        type: 'POST',
                        url: sizeChartScriptObject.size_chart_admin_url,
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if(response.success){
                                msg = response.data.message;
                                p.css('color', 'green');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 4000);
                            } else {
                                msg = response.data.message;
                                p.css('color', 'red');
                            }
                            
                            $('.inputtable').parent().parent().unblock();
                            p.text(msg);
                            fileUpload.parent().append(p);
                        },
                    });
                } else {
                    msg = 'Please upload JSON file';
                    p.css('color', 'red');
                }
                p.text(msg);
                fileUpload.parent().append(p);
            });

            
        }
	};
	$(document).ready(function(){
		$('#scsf_user_role').select2({             
			placeholder: 'Select a user role'
	    });
		function scfw_size_chart_position_options() {
	        $('select#position').on('change', function () {
	        	var optionSelected = $(this).val();
	        	if ( 'tab' === optionSelected ) {
		            $('.chart-tab-field').show();
		            $('.chart-popup-field').hide();
		        } else {
		            $('.chart-popup-field').show();
		            $('.chart-tab-field').hide();
		        }
			});
	    }
	    scfw_size_chart_position_options();

		$('body').on('click', '.dotstore_plugin_sidebar .content_box .sc-star-rating label', function(e){
			e.stopImmediatePropagation();
			var rurl = $('#sc-review-url').val();
			window.open( rurl, '_blank' );
		});

        //Default icon JS
        jQuery('input[name="default-icons"]').change(function(){
            var value = jQuery(this).val();
            if( value !== 'dashicons-none' ){
                jQuery('#chart-popup-icon').val(value); 
            } else { 
                jQuery('#chart-popup-icon').val('');
            }
        });
        $('#chart-popup-icon').on('input', function(){
            var value = $(this).val();
            jQuery('input[name="default-icons"]').prop('checked', false);
            if( '' !== value ){
                jQuery('input[name="default-icons"]').each(function( e, val ){
                    if( $(val).val() === value ){
                        jQuery('input[name="default-icons"][value="'+value+'"]').prop('checked', true);
                    }
                });
            } else {
                jQuery('input[name="default-icons"][value="dashicons-none"]').prop('checked', true);
            }
        });

        // Copy shortcode js
        $('.scfw-copy-shortcode').click(function (e) {
	        e.preventDefault();
	        /* Get the text field */
	        var copyText = $(this);
	        /* Select the text field */
	        copyText.select();
	        document.execCommand('copy');
	        jQuery('.scfw-after-copy-text').animate({
	          opacity: 1,
	          top: 36 + 'px'
	        }, 300);
	        setTimeout(function () {
	          jQuery('.scfw-after-copy-text').animate({
	            opacity: 0,
	            top: -25 + 'px'
	          }, 200);
	        }, 2000);
	    });

        // Tablecell validation js
	    $(document).on( 'change', '#size-chart-meta-fields .inputtable input', function () {
            let tableFieldVal = $(this).val();
			let regexPattern = /^[^"\u201C\u201D\\]*$/;
			if(regexPattern.test(tableFieldVal) === false ) {
				$(this).parent().addClass('invalid-character');
			} else {
				$(this).parent().removeClass('invalid-character');
			}
	    });
	});

	$(document).ready(sizeChartScripts.init);

    
})(jQuery, window, document);
