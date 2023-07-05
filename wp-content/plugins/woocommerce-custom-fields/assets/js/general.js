/**
 * WooCommerce Custom Fields Plugin General Scripts
 */
jQuery(document).ready(function() {

    // Track if customer id cookie was set
    var customer_id_cookie_set = false;

    // Checkout color picker handlers
    var checkout_color_picker_handlers = {};

    // Character limit flash control
    var character_limit_flashing = false;

    /**
     * Ajax request delay function
     */
    var delay_while_typing = (function(){
        var timer = 0;
        return function(callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    /**
     * Limit multiselect or checkbox selections
     */
    jQuery('select[data-wccf-max-selected] option').click(function() {

        // Get selected options
        var select_field = jQuery(this).parent();
        var selected_options = select_field.find('option:selected');

        // Get counts
        var selected_option_count = selected_options.length;
        var max_selected = select_field.data('wccf-max-selected');
        var difference = selected_option_count - max_selected;

        // Limit reached
        if (selected_option_count > max_selected) {

            // Remove last
            jQuery(this).removeAttr('selected');

            // Limit is still reached (selection made while holding SHIFT)
            if (difference > 1) {
                for (var i = 1; i < difference; i++) {
                    select_field.find('option:selected').first().removeAttr('selected');
                }
            }
        }
    });
    jQuery('input[data-wccf-max-selected]').change(function() {

        var name = jQuery(this).prop('name');

        // Get checked checkboxes
        var checked_checkboxes = jQuery(this).closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').find('input[name="' + name + '"]:checked');

        // Limit reached
        if (checked_checkboxes.length > jQuery(this).data('wccf-max-selected')) {
            this.checked = false;
        }
    });

    /**
     * Frontend setup
     */
    if (!wccf_general_config.is_backend) {

        /**
         * Dynamic extra fees on checkout
         */
        jQuery('form .wccf_checkout_field').each(function() {

            // Check if field has pricing
            if (!jQuery(this).data('wccf-checkout-pricing')) {
                return;
            }

            // Handle field value change
            jQuery(this).change(function(e) {

                // Trigger checkout refresh but only if this is not a file field (handled separately)
                if (jQuery(this).prop('type') !== 'file') {
                    jQuery('body').trigger('update_checkout');
                }
            });
        });

        /**
         * Toggle character limit
         */
        jQuery('.wccf[data-wccf-character-limit]').each(function() {
            set_up_character_limit_for_field(jQuery(this));
        });

        /**
         * Set customer id cookie if none exists yet
         */
        jQuery('.wccf_field_container input.wccf_file').first().each(function() {
            maybe_set_customer_id_cookie();
        });

        /**
         * Maybe reload fields via Ajax on variation change
         */
        if (jQuery('form.variations_form div[data-wccf-uses-attribute-conditions]').length) {
            jQuery('.variations_form').on('woocommerce_variation_has_changed', function() {
                refresh_product_fields_view();
            }).closest('form.cart').on('found_variation', function() {
                refresh_product_fields_view();
            });
        }

        /**
         * Maybe reload fields via Ajax on quantity change
         */
        jQuery('form.cart input[name="quantity"]').each(function() {

            // Quantity change
            jQuery(this).change(function() {
                handle_product_quantity_change(jQuery(this));
            });

            // Initial setup
            if (jQuery(this).val() > 1) {
                handle_product_quantity_change(jQuery(this));
            }
        });
    }

    /**
     * Set up character limit for single field
     */
    function set_up_character_limit_for_field(field)
    {
        var character_limit_element = field.closest('.wccf_field_container').find('.wccf_character_limit');

        // Show or hide character limit
        if (character_limit_element.length) {
            field.focus(function() {
                character_limit_element.fadeIn();
            });
            field.focusout(function() {
                character_limit_element.fadeOut();
            });
        }

        // Update character limit
        field.keyup(function() {
            update_characters_remaining(jQuery(this));
        });
        field.change(function() {
            update_characters_remaining(jQuery(this));
        });
        update_characters_remaining(field);

        // Prevent form submit
        field.closest('form').submit(function(e) {
            if (field.val().length > field.data('wccf-character-limit')) {
                field.focus();
                e.preventDefault();
            }
        });
    }

    /**
     * Update characters remaining
     */
    function update_characters_remaining(field)
    {

        // Select character limit element
        var character_limit_element = field.closest('.wccf_field_container').find('.wccf_character_limit');

        // Check if character limit element exists
        if (character_limit_element.length) {

            // Get character limit value
            var limit = field.data('wccf-character-limit');

            // Get number of remaining character
            var remaining = limit - field.val().length;

            // Update value
            character_limit_element.find('.wccf_characters_remaining').html(remaining);

            // Over limit
            if (remaining < 0) {

                // Set over quota class
                character_limit_element.addClass('wccf_character_limit_over_quota');

                // Flash element twice
                if (!character_limit_flashing) {
                    character_limit_flashing = true;
                    character_limit_element.fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100, function() {
                        character_limit_flashing = false;
                    });
                }
            }
            // Not over limit
            else {

                // Unset over quota class
                character_limit_element.removeClass('wccf_character_limit_over_quota');
            }
        }
    }

    /**
     * Set customer id cookie if none exists yet
     */
    function maybe_set_customer_id_cookie()
    {
        // Check if cookie is already set
        if (customer_id_cookie_set) {
            return;
        }

        // Send request to retrieve customer id cookie data
        jQuery.get(
            wccf_general_config.ajaxurl,
            {
                action:     'wccf_get_customer_id_cookie_data'
            },
            function(response) {

                // Parse response
                response = parse_ajax_json_response(response);

                // Error occurred
                if (typeof response !== 'object' || typeof response.result === 'undefined' || response.result !== 'success' || typeof response.data !== 'object') {
                    return;
                }

                // Get cookie data
                var cookie_data = response.data;

                // Cookie already exists
                if (cookie_exists(cookie_data.name)) {
                    return;
                }

                // Set customer id cookie
                set_cookie(cookie_data);
            }
        );

        customer_id_cookie_set = true;
    }

    /**
     * Check if cookie exists
     */
    function cookie_exists(cookie_name)
    {
        return document.cookie.indexOf(cookie_name + '=') !== -1;
    }

    /**
     * Set cookie
     */
    function set_cookie(cookie_data)
    {
        // Generate cookie string
        var cookie = cookie_data.name + '=' + cookie_data.value + '; expires=' + cookie_data.expiration + '; path=' + cookie_data.path;

        // Optionally make cookie secure
        if (cookie_data.secure) {
            cookie += '; secure';
        }

        // Optionally set cookie domain
        if (cookie_data.domain) {
            cookie += '; domain=' + cookie_data.domain;
        }

        // Set cookie
        document.cookie = cookie;
    }

    /**
     * Maybe reload fields via Ajax on quantity change
     */
    function handle_product_quantity_change(quantity_field) {
        if (quantity_field.closest('form.cart').find('.wccf[data-wccf-quantity-based]').length) {
            refresh_product_fields_view();
        }
    }

    /**
     * Refresh product fields view
     */
    function refresh_product_fields_view()
    {

        // Take reference of the form
        var form = jQuery('form.cart');

        // Block UI until we update fields
        form.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        // Prepare data
        var form_data = form.serialize();

        // Add product id
        form.find('button[type="submit"][name="add-to-cart"]').each(function() {

            var product_id = jQuery(this).val();

            if (product_id) {
                form_data += (form_data !== '' ? '&' : '') + 'rightpress_reference_product_id=' + product_id;
            }
        });

        // Update product fields
        jQuery.post(
            wccf_general_config.ajaxurl,
            {
                action:     'wccf_refresh_product_field_view',
                data:       form_data
            },
            function(response) {

                // Parse response
                response = parse_ajax_json_response(response);

                // Error occurred or response is malformed
                if (typeof response !== 'object' || typeof response.result === 'undefined' || response.result !== 'success' || typeof response.fields !== 'object') {
                    alert(wccf_general_config.messages.error_reload_notice + ' ' + wccf_general_config.messages.page_reload);
                    location.reload();
                    return;
                }

                // Take reference of old container
                var old_container = jQuery('div#wccf_product_field_master_container');

                // Check if we have any fields
                if (response.fields.length) {

                    // Insert hidden new container before old container
                    old_container.before('<div id="wccf_product_field_master_container_copy" style="display: none;"></div>');

                    // Take reference of new container
                    var new_container = jQuery('div#wccf_product_field_master_container_copy');

                    // Iterate over fields
                    jQuery.each(response.fields, function(index, field) {

                        // Move existing field from old container to new container (detach method copies all events too)
                        if (old_container.find('#' + field.element_id).length) {
                            old_container.find('#' + field.element_id).closest('div.wccf_field_container').detach().appendTo(new_container);
                        }
                        // Special handling for existing checkbox and radio button sets
                        else if (['checkbox', 'radio'].indexOf(field.field_type) !== -1 && old_container.find('input[name="' + field.element_name + '"]').length) {
                            old_container.find('input[name="' + field.element_name + '"]').closest('div.wccf_field_container').detach().appendTo(new_container);
                        }
                        // Field does not exist
                        else {

                            // Print new field
                            new_container.append(field.html);

                            // Take reference of new field element
                            var new_field = new_container.find('.wccf_field_container').last();

                            // Set up field functionality after dynamic field view update
                            set_up_field_functionality_after_view_update(new_field, form, false);

                            // Set up live product update
                            form.trigger('rightpress_live_product_update_attach_input', new_field);
                        }
                    });

                    // Remove old container along with no longer needed fields
                    old_container.remove();

                    // Change id of new container
                    jQuery('div#wccf_product_field_master_container_copy').prop('id', 'wccf_product_field_master_container').fadeIn();
                }
                // No fields returned
                else {

                    // Remove all fields from old container
                    old_container.empty();
                }

                // Recheck frontend conditions
                wccf_check_frontend_conditions(form);

                // Trigger live product update
                form.trigger('rightpress_live_product_update_trigger');

                // Unblock UI
                form.unblock();
            }
        );
    }

    /**
     * Set up field functionality after dynamic field view update
     */
    function set_up_field_functionality_after_view_update(field, form, preserve_existing_files_on_server)
    {

        // Set up character limit
        field.find('.wccf[data-wccf-character-limit]').each(function() {
            set_up_character_limit_for_field(jQuery(this));
        });

        // Set customer id cookie if not exists
        field.find('input.wccf_file').first().each(function() {
            maybe_set_customer_id_cookie();
        });

        // Set up date/time pickers
        jQuery.each(['date', 'time', 'datetime'], function(index, type) {
            field.find('.wccf_' + type).each(function() {
                set_up_datetimepicker(jQuery(this), type);
            });
        });

        // Set up color picker
        field.find('.wccf_color').each(function() {
            set_up_color_picker(jQuery(this));
        });

        // Set up frontend condition checks for this element
        field.find('.wccf').each(function() {
            wccf_initialize_frontend_condition_checks(jQuery(this), form);
        });

        // Set up file uploads
        field.find('.wccf_file').each(function() {
            wccf_set_up_file_upload_handler_for_field(jQuery(this), preserve_existing_files_on_server);
        });
    }

    /**
     * Date/time pickers
     */
    jQuery.each(['date', 'time', 'datetime'], function(index, type) {
        jQuery('.wccf_' + type).each(function() {
            set_up_datetimepicker(jQuery(this), type);
        });
    });

    /**
     * Color picker
     */
    jQuery('.wccf_color').each(function() {
        set_up_color_picker(jQuery(this));
    });

    /**
     * Check frontend conditions
     */
    function wccf_check_frontend_conditions(form)
    {

        var fields_with_pricing_displayed = false;

        // Do not check frontend conditions in cart item product field editing form
        if (form.prop('id') === 'wccf_cart_item_product_field_editing_form') {
            return;
        }

        // Iterate over all custom fields in this form
        form.find('.wccf').each(function() {

            // Get element id and conditions variable name
            var id = jQuery(this).prop('id');
            var conditions = 'wccf_conditions_' + id;

            // Check if we have any conditions for this field
            if (typeof window[conditions] !== 'undefined') {

                // Track if all conditions match
                var conditions_match = true;

                // Reference context
                var context = window[conditions]['context'];

                // Iterate over conditions
                for (var i = 0; i < window[conditions]['conditions'].length; i++) {

                    // Reference current condition and context
                    var condition = window[conditions]['conditions'][i];

                    // Check if condition is matched
                    if (!check_condition(form, condition, context)) {
                        conditions_match = false;
                    }
                }

                // Show field
                if (conditions_match) {
                    jQuery('#' + id).closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').each(function() {
                        jQuery(this).fadeIn().find('input, select, textarea').removeAttr('disabled').each(function() {
                            if (typeof jQuery(this).data('wccf-pricing') !== 'undefined') {
                                fields_with_pricing_displayed = true;
                            }
                        });
                    });
                }
                // Hide field
                else {
                    jQuery('#' + id).closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').each(function() {
                        jQuery(this).fadeOut().find('input, select, textarea').attr('disabled', 'disabled');
                    });
                }
            }
        });

        // Trigger live product update if needed
        if (fields_with_pricing_displayed) {
            form.trigger('rightpress_live_product_update_trigger');
        }
    }

    /**
     * Check single frontend condition
     */
    function check_condition(form, condition, context)
    {
        if (condition.type === 'other__other_custom_field') {
            return check_condition_other_custom_field(form, condition, context);
        }

        return false;
    }

    /**
     * Check other custom field condition
     */
    function check_condition_other_custom_field(form, condition, context)
    {

        // Reference field that we are checking against
        var other_field = form.find('.wccf_' + context + '[name="wccf[' + context + '][' + condition.other_field_id + ']"]:not(:disabled), .wccf_' + context + '[name="wccf_ignore[' + context + '][' + condition.other_field_id + ']"]:not(:disabled)');

        // Field not found? Maybe it accepts multiple values
        if (!other_field.length) {
            other_field = form.find('.wccf_' + context + '[name="wccf[' + context + '][' + condition.other_field_id + '][]"]:not(:disabled), .wccf_' + context + '[name="wccf_ignore[' + context + '][' + condition.other_field_id + '][]"]:not(:disabled)');
        }

        // Reference method
        var method = condition.method_option;

        // Reference field not found on page
        if (!other_field.length) {

            // Negative conditions
            if (['is_empty', 'does_not_contain', 'does_not_equal', 'is_not_checked'].indexOf(method) !== -1) {
                return true;
            }
            // Positive conditions
            else {
                return false;
            }
        }

        // Is Empty
        if (method === 'is_empty') {
            return is_empty(form, other_field);
        }

        // Is Not Empty
        else if (method === 'is_not_empty') {
            return !is_empty(form, other_field);
        }

        // Contains
        else if (method === 'contains') {
            return contains(form, other_field, condition.text);
        }

        // Does Not Contain
        else if (method === 'does_not_contain') {
            return !contains(form, other_field, condition.text);
        }

        // Equals
        else if (method === 'equals') {
            return equals(form, other_field, condition.text);
        }

        // Does Not Equal
        else if (method === 'does_not_equal') {
            return !equals(form, other_field, condition.text);
        }

        // Less Than
        else if (method === 'less_than') {
            return less_than(form, other_field, condition.text);
        }

        // Less Or Equal To
        else if (method === 'less_or_equal_to') {
            return !more_than(form, other_field, condition.text);
        }

        // More Than
        else if (method === 'more_than') {
            return more_than(form, other_field, condition.text);
        }

        // More Or Equal
        else if (method === 'more_or_equal') {
            return !less_than(form, other_field, condition.text);
        }

        // Is Checked
        else if (method === 'is_checked') {
            return is_checked(form, other_field);
        }

        // Is Not Checked
        else if (method === 'is_not_checked') {
            return !is_checked(form, other_field);
        }
    }

    /**
     * Check if field element is empty
     */
    function is_empty(form, field)
    {
        // Get value of field that we are checking against
        var field_value = get_value(form, field);

        // Check if value is empty
        return (field_value === '' || field_value === null || field_value.length === 0);
    }

    /**
     * Check if field element contains string
     */
    function contains(form, field, value)
    {
        // Get value of field that we are checking against
        var field_value = get_value(form, field);

        // Will check subscring in string and whole array value in array (for select fields)
        if (field_value !== null && field_value.indexOf(value) > -1) {
            return true;
        }

        return false;
    }

    /**
     * Check if field element equals string
     */
    function equals(form, field, value)
    {
        // Get value of field that we are checking against
        var field_value = get_string_value(get_value(form, field));

        // Check if it equals given string
        if (field_value !== false && field_value === value) {
            return true;
        }

        return false;
    }

    /**
     * Check if field element is less than value
     */
    function less_than(form, field, value)
    {
        // Get value of field that we are checking against
        var field_value = get_number_value(get_string_value(get_value(form, field)));

        // Check if value is less than given value
        if (field_value !== false && field_value < value) {
            return true;
        }

        return false;
    }

    /**
     * Check if field element is more than value
     */
    function more_than(form, field, value)
    {
        // Get value of field that we are checking against
        var field_value = get_number_value(get_string_value(get_value(form, field)));

        // Check if value is less than given value
        if (field_value !== false && field_value > value) {
            return true;
        }

        return false;
    }

    /**
     * Get number value from string value
     */
    function get_number_value(value)
    {
        // Empty string equals zero
        if (value === '') {
            return 0;
        }

        // Try parsing value to float
        var float_value = parseFloat(value);

        // Value appears to be a number
        if (!isNaN(float_value)) {
            return float_value;
        }

        // Value does not appear to be a number
        return false;
    }

    /**
     * Check if field element is checked
     */
    function is_checked(form, field)
    {
        // In case of radio or checkbox
        if (field.is(':checkbox') || field.is(':radio')) {
            if (field.is(':checked')) {
                return true;
            }
        }

        // In case of other fields - make sure it's not empty
        else {
            if (!is_empty(form, field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get value depending on field type
     */
    function get_value(form, field)
    {
        // Special handling for checkboxes and radio buttons
        if (field.is(':radio') || field.is(':checkbox')) {

            // Store values
            var values = [];

            // Iterate over options and pick those that are checked
            field.each(function() {
                if (jQuery(this).is(':checked')) {
                    values.push(jQuery(this).val());
                }
            });

            // Return values
            return values.length > 0 ? values : '';
        }
        // Special handling for file fields
        else if (field.is(':file')) {

            // Get uploaded files
            var upload_list = field.closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').find('.wccf_file_upload_list');

            // No uploaded files found
            if (!upload_list.length) {
                return '';
            }

            // Store values
            var values = [];

            // Iterate over uploaded files
            upload_list.each(function() {
                if (jQuery(this).find('._' + field.prop('id')).length) {
                    var upload_value = jQuery(this).find('._' + field.prop('id')).val();

                    if (upload_value) {
                        values.push(upload_value);
                    }
                }
            });

            // Return values
            return values.length > 0 ? values : '';
        }
        // Field selected successfully - return value
        else {
            return (typeof field.val() === 'string' ? field.val().trim() : field.val());
        }
    }

    /**
     * Get string value from any value
     */
    function get_string_value(value)
    {
        // In case of string
        if (typeof value === 'string') {
            return value.trim();
        }

        // In case of array with single element
        if (value !== null && typeof value === 'object' && value.length === 1 && typeof value[0] === 'string') {
            return value[0].trim();
        }

        return false;
    }

    /**
     * Clear field value
     */
    function clear_field_value(field)
    {
        if (field.is('select')) {
            field.prop('selectedIndex', 0);
            if (field.hasClass('rightpress_select2')) {
                field.val('').change();
            }
        }
        else if (field.is(':radio, :checkbox')) {
            field.removeAttr('checked');
        }
        else {
            field.val('');
        }
    }

    /**
     * Initialize frontend condition checks
     */
    function wccf_initialize_frontend_condition_checks(field, form)
    {
            // Key up
            field.keyup(function() {
                wccf_check_frontend_conditions(form);
            });

            // Value change
            field.change(function() {
                wccf_check_frontend_conditions(form);
            });
    }
    jQuery('form:has(.wccf)').each(function() {

        // Get reference
        var form = jQuery(this);

        // Check conditions on page load
        wccf_check_frontend_conditions(form);

        // Check conditions on interaction with elements
        form.find('.wccf').each(function() {
            wccf_initialize_frontend_condition_checks(jQuery(this), form);
        });
    });

    /**
     * Handle file uploads
     */
    function wccf_set_up_file_upload_handler_for_field(field, preserve_existing_files_on_server)
    {

        // Get field container for current element
        var field_container = field.closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container');
        var form = field.closest('form');

        // Get current element properties
        var field_id = field.prop('id');
        var is_multiple = field.prop('multiple');

        // Prepare hidden field name and id
        var hidden_field_class = '_' + field_id;
        var hidden_field_name = field.prop('name').replace('wccf_ignore[', 'wccf[');

        // Hidden field name must always accept multiple values so we get them in an array on server and processing is the same in all cases
        if (hidden_field_name.indexOf('[]', (hidden_field_name.length - 2)) === -1) {
            hidden_field_name += '[]';
        }

        // Track progress of individual file uploads
        var wccf_upload_progress_ids = [];
        var wccf_upload_progress_id = 0;

        // Keep track of uploaded file sizes for concurrent file upload validation
        field_container.data('wccf_uploaded_file_sizes', {});

        // Define additional data to be passed to server
        var additional_data = [{
            name:       'action',
            value:      'wccf_file_upload'
        }];

        // Add post ID if present
        jQuery('form[name="post"] input[name="post_ID"], form#your-profile input[name="user_id"]').each(function() {
            additional_data.push({
                name:   'item_id',
                value:  jQuery(this).val()
            });
        });

        var jqXHR = null;

        // Abort any previous uploads and reset upload progress file counter
        field.change(function() {
            if (jqXHR !== null) {
                jqXHR.abort();
            }
        });

        // Initialize file upload handler
        field.fileupload({
            url: wccf_general_config.ajaxurl,
            sequentialUploads: true,
            type: 'POST',
            formData: function () {
                return additional_data.concat([{name: 'form_data', value: form.serialize()}]);
            },
            dataType: 'json',
            dataFilter: function(raw_response) {
                return parse_ajax_json_response(raw_response, true);
            },
            add: function (e, data) {

                // Increment upload progress identifier
                wccf_upload_progress_id++;

                // Set identifier to data object
                data.wccf_upload_progress_id = wccf_upload_progress_id;
                wccf_upload_progress_ids.push(wccf_upload_progress_id);

                // Clear previous file if multiple files are not allowed
                if (!is_multiple) {
                    field_container.find('.wccf_file_upload_list').html('');
                    field_container.find('.wccf_file_upload_status').remove();
                }

                // Get message
                var message = wccf_general_config.messages.file_uploading + ' ' + data.files[0].name;

                // Append progress bar
                field_container.find('input[type="file"]').before('<div class="wccf_file_upload_status wccf_file_upload_status_' + wccf_upload_progress_id + '"><small class="wccf_file_upload_message">' + message + '</small><small class="wccf_file_upload_progress_wrapper"><small class="wccf_file_upload_progress"></small></small></div>');

                // Start uploading
                jqXHR = data.submit();
            },
            progress: function (e, data) {

                // Select correct status element
                var status_element = field_container.find('.wccf_file_upload_status_' + data.wccf_upload_progress_id);

                // Get progress
                var progress = parseInt(data.loaded / data.total * 100, 10);

                // Update progress
                status_element.find('small.wccf_file_upload_progress').css('width', progress + '%');
            },
            done: function (e, data) {

                // Remove from tracked progress ids array
                if (wccf_upload_progress_ids.indexOf(data.wccf_upload_progress_id) !== -1) {
                    wccf_upload_progress_ids.splice(wccf_upload_progress_ids.indexOf(data.wccf_upload_progress_id), 1);
                }

                // Handle error
                if (typeof data.result !== 'object' || typeof data.result.result === 'undefined' || data.result.result !== 'success') {
                    handle_file_upload_error(data, field_container);
                    return;
                }

                // Add file size to array
                var current_file_sizes = field_container.data('wccf_uploaded_file_sizes');
                current_file_sizes[data.result.access_key] = data.result.file_size;
                field_container.data('wccf_uploaded_file_sizes', current_file_sizes);

                // Display uploaded file
                field_container.find('.wccf_file_upload_status_' + data.wccf_upload_progress_id).animate({'opacity': 0}, 500, function() {

                    // Add file list container if needed
                    if (field_container.find('.wccf_file_upload_list').length === 0) {

                        // Special handling for user fields in backend user edit page
                        if (field_container.hasClass('wccf_user_profile_field_container')) {
                            field_container.find('.wccf_user_field_file').before('<div class="wccf_file_upload_list"></div>');
                        }
                        else {
                            field_container.find('label[for="' + field_id + '"]').after('<div class="wccf_file_upload_list"></div>');
                        }
                    }

                    // Add file to list
                    field_container.find('.wccf_file_upload_list').append('<small class="wccf_file_upload_item">' + data.files[0].name + ' <span class="wccf_file_upload_delete">' + wccf_general_config.messages.file_upload_delete + '</span></small>');

                    // Maybe add left border class
                    toggle_file_upload_list_border(field_container);

                    // Select last element in list
                    field_container.find('.wccf_file_upload_list .wccf_file_upload_item').last().each(function() {

                        // Inject hidden field that will submit file access keys along with regular form submit
                        jQuery(this).append('<input type="hidden" class="' + hidden_field_class + '" name="' + hidden_field_name + '" value="' + data.result.access_key + '" data-wccf-file-access-key="' + data.result.access_key + '">');

                        // Handle file removal
                        file_removal_setup(jQuery(this), field_container, false);
                    });

                    // Remove upload status message
                    jQuery(this).remove();

                    // Trigger live product update
                    form.trigger('rightpress_live_product_update_trigger');

                    // Refresh checkout view if this is a checkout field with pricing
                    refresh_checkout_on_checkout_file_field_change(field);

                    // Toggle required property for file field in backend
                    toggle_file_required(field_container.find('input[data-wccf-field-id]').first());

                    // Recheck frontend conditions
                    wccf_check_frontend_conditions(field_container.closest('form'));
                });
            },
            fail: function (e, data) {

                // Remove from tracked progress ids array
                if (wccf_upload_progress_ids.indexOf(data.wccf_upload_progress_id) !== -1) {
                    wccf_upload_progress_ids.splice(wccf_upload_progress_ids.indexOf(data.wccf_upload_progress_id), 1);
                }

                // Handle error
                handle_file_upload_error(data, field_container);

                // Toggle required property for file field in backend
                toggle_file_required(field_container.find('input[data-wccf-field-id]').first());

                // Recheck frontend conditions
                wccf_check_frontend_conditions(field_container.closest('form'));
            },
            send: function (e, data) {
                data.data.append('wccf_uploaded_file_sizes', JSON.stringify(field_container.data('wccf_uploaded_file_sizes')));
            }
        });

        // Disable form submit if there's at least one file uploading
        form.submit(function(e) {
            if (wccf_upload_progress_ids.length) {
                e.preventDefault();
            }
        });

        // Set up file removal for any existing files
        field_container.find('.wccf_file_upload_list .wccf_file_upload_item').each(function() {
            file_removal_setup(jQuery(this), field_container, preserve_existing_files_on_server);
        });
    }
    jQuery('form .wccf_file').each(function() {
        wccf_set_up_file_upload_handler_for_field(jQuery(this), false);
    });

    /**
     * Handle file upload error
     */
    function handle_file_upload_error(data, field_container)
    {
        // Select correct status element
        var status_element = field_container.find('.wccf_file_upload_status_' + data.wccf_upload_progress_id);

        // Handle abort
        if (typeof data === 'object' && typeof data.errorThrown !== 'undefined' && data.errorThrown === 'abort') {
            status_element.remove();
        }

        // Get error message
        if (typeof data === 'object' && typeof data.result === 'object' && typeof data.result.error_message !== 'undefined') {
            var error_message = data.result.error_message;
        }
        else if (typeof data === 'object' && typeof data.files === 'object' && typeof data.files[0] === 'object' && typeof data.files[0].name !== 'undefined') {
            var error_message = wccf_general_config.messages.file_upload_error + ' ' + data.files[0].name;
        }
        else {
            var error_message = wccf_general_config.messages.file_upload_error;
        }

        // Remove progess message
        status_element.find('.wccf_file_upload_message').remove();

        // Add error message
        status_element.prepend('<small class="wccf_file_upload_error">' + error_message + '</small>');

        // Remove upload status div with animation
        status_element.find('.wccf_file_upload_progress_wrapper').css('background-color', '#e44b23');
        status_element.find('.wccf_file_upload_progress').animate({'width': 0}, 500, function() {
            status_element.delay(2500).animate({'opacity': 0}).animate({'width': 0, 'height': 0}, 500, function() {
                jQuery(this).remove();
            });
        });
    }

    /**
     * Toggle file upload list border
     * Displayed when multiple items are present
     */
    function toggle_file_upload_list_border(field_container)
    {
        var list = field_container.find('.wccf_file_upload_list');

        if (list.find('.wccf_file_upload_item').length > 1) {
            if (!list.hasClass('wccf_file_upload_left_border')) {
                list.addClass('wccf_file_upload_left_border');
            }
        }
        else {
            list.removeClass('wccf_file_upload_left_border');
        }
    }

    /**
     * Set up file removal
     */
    function file_removal_setup(file_upload_item, field_container, preserve_existing_files_on_server)
    {

        file_upload_item.find('.wccf_file_upload_delete').click(function() {

            // Reference hidden field
            var hidden_field = file_upload_item.closest('.wccf_file_upload_item').find('input[data-wccf-file-access-key]');

            if (hidden_field) {

                // Get access key and field id
                var access_key = hidden_field.val();
                var field_id = hidden_field.closest('.wccf_file_upload_list').parent().find('input[data-wccf-field-id]').data('wccf-field-id');

                // Check if file can be removed from server
                if (!preserve_existing_files_on_server) {

                    // Send request to remove from server
                    jQuery.post(
                        wccf_general_config.ajaxurl,
                        {
                            action:     'wccf_remove_file',
                            access_key: access_key,
                            field_id:   field_id
                        }
                    );
                }

                // Remove from file sizes list
                var current_file_sizes = field_container.data('wccf_uploaded_file_sizes');

                if (typeof current_file_sizes[access_key] !== 'undefined') {
                    delete current_file_sizes[access_key];
                    field_container.data('wccf_uploaded_file_sizes', current_file_sizes);
                }
            }

            // Remove from page
            file_upload_item.closest('.wccf_file_upload_item').animate({'opacity': 0}, 500, function() {

                // Trigger live product update
                file_upload_item.closest('form').trigger('rightpress_live_product_update_trigger');

                // Remove file
                file_upload_item.remove();

                // Maybe remove left border class
                toggle_file_upload_list_border(field_container);

                // Toggle required property for file field
                toggle_file_required(field_container.find('input[data-wccf-field-id]').first());

                // Recheck frontend conditions
                wccf_check_frontend_conditions(field_container.closest('form'));

                // Refresh checkout view if this is a checkout field with pricing
                refresh_checkout_on_checkout_file_field_change(field_container.find('input.wccf_file'));
            });
        });
    }

    /**
     * Set up existing file removal
     */
    jQuery('.wccf_meta_box_field_container, .wccf_user_profile_field_container, .wccf_field_container_user_field, .wccf_field_container_file').each(function() {

        var field_container = jQuery(this);

        field_container.find('.wccf_file_upload_list .wccf_file_upload_item').each(function() {
            file_removal_setup(jQuery(this), field_container, false);
        });
    });

    /**
     * Refresh checkout view if this is a checkout field with pricing
     */
    function refresh_checkout_on_checkout_file_field_change(file_upload_field)
    {
        if (file_upload_field.hasClass('wccf_checkout_field') && file_upload_field.data('wccf-checkout-pricing')) {
            jQuery('body').trigger('update_checkout');
        }
    }
    /**
     * Checkout field view update
     */
    jQuery(document.body).on('updated_checkout', function() {

        // Take reference of the form
        var form = jQuery('form.checkout');

        // Get list of field ids displayed in page
        var field_ids = [];

        jQuery('.wccf_checkout_field[data-wccf-field-id]').each(function() {
            field_ids.push(jQuery(this).data('wccf-field-id'));
        });

        // Send request
        jQuery.ajax({
            type:       'POST',
            url:        wccf_general_config.ajaxurl,
            data: {
                action:     'wccf_update_checkout_field_view',
                field_ids:  field_ids
            },
            dataType:   'json',
            dataFilter: jQuery.rightpress.sanitize_json_response,
            success: function (response) {

                // Check if request was successful
                if (typeof response === 'object' && response !== null && typeof response.result !== 'undefined' && response.result === 'success') {

                    // Check if there are fields to remove
                    if (typeof response.remove === 'object') {

                        // Iterate over fields to remove
                        for (var field_id in response.remove) {
                            if (response.remove.hasOwnProperty(field_id)) {

                                // Remove field
                                jQuery('.wccf_checkout_field[data-wccf-field-id="' + field_id + '"]').closest('.wccf_field_container').fadeOut(function() {
                                    remove_checkout_field(jQuery(this));
                                });
                            }
                        }
                    }

                    // Check if there are fields to add
                    if (typeof response.add === 'object') {

                        // Iterate over positions
                        for (var position in response.add) {
                            if (response.add.hasOwnProperty(position)) {

                                // Get position properties
                                var position_method     = response.add[position]['position']['method'];
                                var position_selector   = response.add[position]['position']['selector'];

                                // Get fields to add to current position
                                var fields = response.add[position]['fields'];

                                // Maybe reverse order of fields
                                if (position_method === 'after' || position_method === 'append') {
                                    fields.reverse();
                                }

                                // Iterate over fields of current position
                                jQuery.each(fields, function(index, field) {

                                    // Insert field html into DOM
                                    jQuery(position_selector)[position_method](field.html);

                                    // Reference new field
                                    var new_field = jQuery('.wccf_checkout_field[data-wccf-field-id="' + field.id + '"]').closest('.wccf_field_container');

                                    // Animate field
                                    new_field.hide().fadeIn();

                                    // Set up field functionality after dynamic field view update
                                    set_up_field_functionality_after_view_update(new_field, form, false);
                                });
                            }
                        }

                        // Recheck frontend conditions
                        wccf_check_frontend_conditions(form);
                    }
                }
            }
        });
    });

    /**
     * Remove checkout field
     */
    function remove_checkout_field(field)
    {

        // Remove color picker mouseup event listener from document
        if (field.find('.wccf_checkout_field_color').length) {
            unbind_color_picker_toggle(field.find('.wccf_checkout_field_color').last());
        }

        // Remove field
        field.remove();
    }

    /**
     * Trigger checkout update on payment method change
     */
    function set_up_payment_method_checkout_update()
    {
        jQuery('form.checkout input[name="payment_method"]').each(function() {
            if (typeof jQuery(this).data('wccf_payment_method_checkout_update') === 'undefined') {
                jQuery(this).change(function() {
                    jQuery('body').trigger('update_checkout');
                });
                jQuery(this).data('wccf_payment_method_checkout_update', true);
            }
        });
    }
    jQuery('body').on('updated_checkout', set_up_payment_method_checkout_update);
    set_up_payment_method_checkout_update();

    /**
     * Backend field editing
     */
    jQuery('span.wccf_backend_editing_value[data-wccf-backend-editing="1"]').each(function() {

        // Bind click event
        jQuery(this).click(function() {

            var value_element = jQuery(this);

            // Get field element via ajax
            jQuery.post(
                wccf_general_config.ajaxurl,
                {
                    action:         'wccf_get_backend_editing_field',
                    field_id:       value_element.data('wccf-field-id'),
                    item_id:        value_element.data('wccf-item-id'),
                    quantity_index: value_element.data('wccf-quantity-index')
                },
                function(response) {

                    // Parse response
                    response = parse_ajax_json_response(response);

                    // Check for errors
                    if (typeof response === 'object' && typeof response.result !== 'undefined' && response.result === 'success' && typeof response.field !== 'undefined') {

                        // Display field and hide value
                        value_element.after(response.field).hide();

                        // Date/time picker setup
                        jQuery.each(['date', 'time', 'datetime'], function(index, type) {
                            value_element.parent().find('.wccf_backend_editing_' + type).each(function() {
                                set_up_datetimepicker(jQuery(this), type);
                            });
                        });

                        // Color picker setup
                        value_element.parent().find('.wccf_backend_editing_color').each(function() {
                            set_up_color_picker(jQuery(this));
                        });
                    }
                }
            );
        });
    });

    /**
     * Enable form validation on user edit page
     */
    jQuery('form#your-profile, form#createuser, form#adduser').filter(':has(.wccf)').removeAttr('novalidate');

    /**
     * Parse Ajax JSON response
     */
    function parse_ajax_json_response(response, return_raw_data)
    {
        // Check if we need to return parsed object or potentially fixed raw data
        var return_raw_data = (typeof return_raw_data !== 'undefined') ?  return_raw_data : false;

        try {

            // Attempt to parse data
            var parsed = jQuery.parseJSON(response);

            // Return appropriate value
            return return_raw_data ? response : parsed;
        }
        catch (e) {

            // Attempt to fix malformed JSON string
            var valid_response = response.match(/{"result.*"}/);

            // Check if we were able to fix it
            if (valid_response !== null) {
                response = valid_response[0];
            }
        }

        // Second attempt to parse response data
        return return_raw_data ? response : jQuery.parseJSON(response);
    }

    /**
     * Fix HTML5 required validation for file uploads
     */
    function toggle_file_required(file_field)
    {
        // Get uploaded files
        var upload_list = file_field.closest('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').find('.wccf_file_upload_list').children();

        // Remove required property
        if (upload_list.length > 0 && file_field.prop('required')) {
            file_field.prop('required', false);
            file_field.data('wccf-file-field-was-required', 1);
        }
        // Add required property back
        else if (upload_list.length == 0 && file_field.data('wccf-file-field-was-required')) {
            file_field.prop('required', true);
            file_field.data('wccf-file-field-was-required', 0);
        }
    }
    jQuery('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').find('.wccf_file[required]').each(function() {
        toggle_file_required(jQuery(this));
    });

    /**
     * Fix HTML5 required validation for checkbox set
     */
    jQuery('.wccf_field_container, .wccf_meta_box_field_container, .wccf_user_profile_field_container').has('.wccf_checkbox').each(function() {

        // Reference all checkboxes
        var checkboxes = jQuery(this).find('.wccf_checkbox');

        // Remove required attribute from all checkboxes when at least one is checked
        if (checkboxes.length > 1 && checkboxes.prop('required')) {

            function toggle_checkboxes_required()
            {
                // Get min selected value
                var min_selected = checkboxes.data('wccf-min-selected');
                min_selected = typeof min_selected !== 'undefined' ? min_selected : 1;

                // Check if enough checkboxes were selected
                if (checkboxes.filter(':checked').length >= min_selected) {
                    checkboxes.prop('required', false);
                }
                else {
                    checkboxes.prop('required', true);
                }
            }
            checkboxes.change(function() {
                toggle_checkboxes_required();
            });
            toggle_checkboxes_required();
        }
    });

    /**
     * Fix HTML5 min selected validation for multiselect fields
     */
    jQuery('.wccf_multiselect[data-wccf-min-selected]').each(function() {

        // Reference field
        var multiselect = jQuery(this);

        // Get min selected value
        var min_selected = multiselect.data('wccf-min-selected');

        // Handle form submit
        multiselect.closest('form').submit(function(e) {

            // More options need to be selected
            if (multiselect.find('option:selected').length < min_selected) {
                jQuery(this).find('option:selected').prop('selected', false);
                e.preventDefault();
            }
        });
    });

    /**
     * Set up color picker
     */
    function set_up_color_picker(input)
    {

        // Get current input value
        var value = input.val();

        // Get color to show
        var color = (typeof value !== 'undefined' && value !== '') ? value : '#000000';

        // Set color on input element
        input.css('border-left', ('29px solid ' + color));

        // Change callback
        wccf_color_picker_config.change = function(event, ui) {

            // Update value
            input.val(ui.color.toString());

            // Update color
            input.css('border-left', ('29px solid ' + ui.color.toString()));

            // Trigger change event
            input.change();
        };

        // Initialize Iris color picker
        input.iris(wccf_color_picker_config);

        // Fix position
        input.closest('.wccf_field_container').find('.iris-picker').css('position', 'absolute').css('z-index', '999');

        // Bind color picker toggle event
        bind_color_picker_toggle(input);
    }

    /**
     * Bind color picker toggle event
     */
    function bind_color_picker_toggle(input)
    {

        // Format dynamic function name
        var function_name = 'toggle_color_picker_' + input.prop('id');

        // Make sure function does not exist yet
        if (typeof checkout_color_picker_handlers[function_name] === 'undefined') {

            // Add function with dynamic reference
            checkout_color_picker_handlers[function_name] = function(event) {
                toggle_color_picker(event);
            };

            // Bind mouseup event
            jQuery(document).on('mouseup', '*', {input: input}, checkout_color_picker_handlers[function_name]);
        }
    }

    /**
     * Unbind color picker toggle event
     */
    function unbind_color_picker_toggle(input)
    {

        // Format dynamic function name
        var function_name = 'toggle_color_picker_' + input.prop('id');

        // Check if function is defined
        if (typeof checkout_color_picker_handlers[function_name] !== 'undefined') {

            // Unbind mouseup event
            jQuery(document).off('mouseup', '*', checkout_color_picker_handlers[function_name]);

            // Remove function
            delete checkout_color_picker_handlers[function_name];
        }
    }

    /**
     * Color picker state toggling
     */
    function toggle_color_picker(event)
    {

        var container = jQuery(event.data.input).closest('.wccf_field_container');

        // Clicked on color input
        if (jQuery(event.target).is(event.data.input)) {
            event.data.input.iris('show');
        }
        // Clicked outside input and container
        else if (!container.is(event.target) && !container.has(event.target).length) {
            event.data.input.iris('hide');
        }
    }

    /**
     * Set up datetime picker
     */
    function set_up_datetimepicker(element, type)
    {

        // Select config
        if (type === 'date') {
            var config = wccf_datetimepicker_date_config.x;
        }
        else if (type === 'time') {
            var config = wccf_datetimepicker_time_config.x;
        }
        else {
            var config = wccf_datetimepicker_datetime_config.x;
        }

        // Set z-index
        if (typeof config.style !== 'undefined') {
            config.style += ' z-index: 999999999;';
        }
        else {
            config.style = 'z-index: 999999999';
        }

        // Initialize datepicker
        element.datetimepicker(config);

        // Set locale
        jQuery.datetimepicker.setLocale(wccf_datetimepicker_locale.x);
    }

    /**
     * Checkout validation
     */
    jQuery('form.checkout').submit(function() {
        unset_field_invalid_flags(jQuery(document.body));
    });

    if (jQuery('form.checkout').length) {
        jQuery(document.body).on('checkout_error', function() {
            set_field_invalid_flags(jQuery(document.body));
        });
    }

    /**
     * Set field invalid flags
     *
     * Note: This is used for both checkout field validation and cart item product field editing validation
     */
    function set_field_invalid_flags(parent)
    {

        // Find active error messages
        parent.find('[data-wccf-error-field-id]').each(function() {

            // Get field id
            var field_id = jQuery(this).data('wccf-error-field-id');

            // Select field by id
            parent.find('.wccf[data-wccf-field-id="' + field_id + '"]').each(function() {

                // Reference input and container
                var input       = jQuery(this);
                var container   = input.closest('.wccf_field_container');

                // Add invalid class to container
                container.addClass('wccf_field_invalid');

                // Remove invalid class on input focus or change
                input.on('focus change keyup', function() {
                    container.removeClass('wccf_field_invalid');
                });
            });
        });
    }

    /**
     * Unset field invalid flags
     *
     * Note: This is used for both checkout field validation and cart item product field editing validation
     */
    function unset_field_invalid_flags(parent)
    {
        parent.find('.wccf_field_invalid').removeClass('wccf_field_invalid');
    }

    /**
     * Simple product field frontend validation
     */
    jQuery('#wccf_product_field_master_container').closest('form').submit(function(e) {

        var focused = false;

        // Clear all error states
        jQuery(this).find('.wccf_field_invalid').removeClass('wccf_field_invalid');

        // Select all required visible non-disabled product field inputs
        jQuery(this).find('.wccf_product_field[data-wccf-required-field]:not(:disabled):visible').each(function() {

            // Reference input and container
            var input       = jQuery(this);
            var container   = input.closest('.wccf_field_container');

            // Check if field has value
            if (input.prop('type') === 'file') {
                var has_value = !!container.find('.wccf_file_upload_item').length;
            }
            else if (input.prop('type') === 'checkbox' || input.prop('type') === 'radio') {
                var has_value = !!container.find('.wccf_product_field:checked').length;
            }
            else {
                var has_value = !!jQuery(this).val();
            }

            // Check if field is empty
            if (!has_value) {

                // Focus on first input with error
                if (!focused) {
                    input.focus();
                    focused = true;
                }

                // Add invalid class
                container.addClass('wccf_field_invalid');

                // Print error message
                input.after('<div class="wccf_required_field_error">' + wccf_general_config.messages.required_field + '</div>');

                // Clean up on focus or value change
                input.on('focus change keyup', function() {

                    // Remove invalid class
                    container.removeClass('wccf_field_invalid');

                    // Remove error message
                    container.find('.wccf_required_field_error').remove();
                });

                // Do not submit form
                e.preventDefault();
            }
        });
    });

    /**
     * Cart item product field editing
     */
    if (jQuery('#wccf_cart_item_product_field_editing_modal').length) {

        // Reference modal
        var modal   = jQuery('#wccf_cart_item_product_field_editing_modal');
        var wrapper = modal.find('.wccf_modal_wrapper');
        var content = modal.find('.wccf_modal_content');
        var close   = modal.find('.wccf_modal_close');
        var form    = null;

        // Define preloader
        var preloader = '<div class="wccf_modal_preloader"></div>';

        // Open modal on click
        jQuery('form.woocommerce-cart-form .wccf_cart_item_product_field_editing').click(function(e) {

            // Get field id and cart item key
            var field_id        = jQuery(this).data('wccf-cart-item-edit-field-id');
            var cart_item_key   = jQuery(this).data('wccf-cart-item-key');

            // Open modal
            cart_item_product_field_editing_open(field_id, cart_item_key);

            // Prevent file download
            e.preventDefault();
        });

        // Close on close target click
        close.on('click', cart_item_product_field_editing_close);

        function cart_item_product_field_editing_open(field_id, cart_item_key)
        {

            // Make field ids array
            // Note: currently we support single field editing but this can be changed to support multiple fields
            var field_ids = [field_id];

            // Add preloader
            content.html(preloader);

            // Load fields
            load_fields(field_ids, cart_item_key);

            // Fade in modal
            modal.css('opacity', 0).css('display', 'table').animate({opacity: 1}, function() {

                // Close on click outside of modal
                jQuery(document).on('mousedown', cart_item_product_field_editing_close_callback);
            });
        }

        function cart_item_product_field_editing_close_callback(event)
        {

            if (!wrapper.is(event.target) && !wrapper.has(event.target).length) {
                cart_item_product_field_editing_close();
            }
        }

        function cart_item_product_field_editing_close()
        {

            // Fade out modal
            modal.animate({opacity: 0}, function() {

                // Hide modal
                modal.css('display', 'none');

                // Clear content
                content.html('');

                // Unset form
                form = null;

                // Remove click outside of modal callback
                jQuery(document).off('mousedown', cart_item_product_field_editing_close_callback);
            });
        }

        function load_fields(field_ids, cart_item_key)
        {

            // Send request
            jQuery.ajax({
                type:       'POST',
                url:        wccf_general_config.ajaxurl,
                data: {
                    action:         'wccf_get_cart_item_product_field_editing_view',
                    field_ids:      field_ids,
                    cart_item_key:  cart_item_key,
                },
                dataType:   'json',
                dataFilter: jQuery.rightpress.sanitize_json_response,
                success: function (response) {

                    // Check if request was successful
                    if (typeof response === 'object' && response !== null && typeof response.result !== 'undefined' && response.result === 'success') {

                        // Set fields html
                        content.html(response.html);

                        // Set form
                        form = content.find('form#wccf_cart_item_product_field_editing_form');

                        // Set up advanced field functionality on all inputs
                        form.find('.wccf_field_container').each(function() {
                            set_up_field_functionality_after_view_update(jQuery(this), form, true);
                        });

                        // Reference first input
                        var first_input = form.find('.wccf_product_field').first();

                        // Focus on first element and move typing cursor to the back for some file types
                        if ((first_input.is('input') && ['text', 'password', 'email', 'number'].indexOf(first_input.prop('type')) !== -1) || first_input.is('textarea')) {
                            var field_value = first_input.val();
                            first_input.focus().val('').val(field_value);
                        }

                        // Handle submit event
                        form.submit(function(e) {

                            // Handle submit
                            update_cart_item_field_values();

                            // Do not navigate away from page
                            e.preventDefault();
                        });
                    }
                    // Unable to get fields view
                    else {

                        handle_load_fields_error();
                    }
                },
                error: function () {

                    handle_load_fields_error();
                }
            });

            function update_cart_item_field_values()
            {

                // Unset current error messages
                form.find('.wccf_modal_validation_error').fadeOut(function() { jQuery(this).remove(); });
                unset_field_invalid_flags(form);

                // Prepare data
                var form_data = form.serialize();

                // Send request
                jQuery.ajax({
                    type:       'POST',
                    url:        wccf_general_config.ajaxurl,
                    data: {
                        action: 'wccf_update_cart_item_product_field_values',
                        data:   form_data,
                    },
                    dataType:   'json',
                    dataFilter: jQuery.rightpress.sanitize_json_response,
                    success: function (response) {

                        // Request processed successfully
                        if (typeof response === 'object' && response !== null && typeof response.result !== 'undefined' && response.result === 'success') {

                            // Show success message
                            content.html('<div class="wccf_modal_success">' + wccf_general_config.messages.field_values_updated + '<br>' + wccf_general_config.messages.page_reload + '</div>');

                            // Reload page after a pause
                            setTimeout(function(){
                                window.location.reload(true);
                            }, 1500);
                        }
                        // Field validation error
                        else if (typeof response === 'object' && response !== null && typeof response.result !== 'undefined' && response.result === 'error' && typeof response.validation_error_messages === 'object' && response.validation_error_messages !== null && response.validation_error_messages.length) {

                            // Handle validation errors
                            handle_validation_errors(response.validation_error_messages);
                        }
                        // Unknown error occurred while processing request
                        else {

                            handle_update_cart_item_field_values_error();
                        }
                    },
                    error: function () {

                        handle_update_cart_item_field_values_error();
                    }
                });
            }

            function handle_load_fields_error()
            {

                // Display error message
                content.html('<div class="wccf_modal_error">' + wccf_general_config.messages.error_loading_field_view + '<br>' + wccf_general_config.messages.try_again + '</div>');

                // Close modal after a pause
                setTimeout(function(){
                    cart_item_product_field_editing_close();
                }, 2500);
            }

            function handle_update_cart_item_field_values_error()
            {

                // Display error message
                content.html('<div class="wccf_modal_error">' + wccf_general_config.messages.error_updating_field_values + '<br>' + wccf_general_config.messages.try_again + '</div>');

                // Close modal after a pause
                setTimeout(function(){
                    cart_item_product_field_editing_close();
                }, 2500);
            }

            function handle_validation_errors(messages)
            {

                // Iterate over validation error messages
                for (var message_index in messages) {
                    if (messages.hasOwnProperty(message_index)) {

                        // Add validation error
                        add_validation_error(messages[message_index]);
                    }
                }

                // Show errors
                form.find('.wccf_modal_validation_error').fadeIn();

                // Set invalid flags
                set_field_invalid_flags(form);
            }

            function add_validation_error(message)
            {

                form.prepend('<div class="wccf_modal_validation_error">' + message + '</div>');
            }
        }
    }







});
