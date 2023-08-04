/**
 * WooCommerce Custom Fields Plugin Backend Scripts
 */
jQuery(document).ready(function() {

    /**
     * Bulk actions in list view
     */
    if (jQuery('#posts-filter select[name="action"]').length > 0) {

        // Enable Field
        jQuery('<option>').val('wccf_enable_field').text(wccf.labels.enable_field).insertAfter('#posts-filter select[name="action"] option[value="-1"]');
        jQuery('<option>').val('wccf_enable_field').text(wccf.labels.enable_field).insertAfter('#posts-filter select[name="action2"] option[value="-1"]');

        // Disable Field
        jQuery('<option>').val('wccf_disable_field').text(wccf.labels.disable_field).insertAfter('#posts-filter select[name="action"] option[value="wccf_enable_field"]');
        jQuery('<option>').val('wccf_disable_field').text(wccf.labels.disable_field).insertAfter('#posts-filter select[name="action2"] option[value="wccf_enable_field"]');
    }

    /**
     * Duplicate field control
     */
    jQuery('a.wccf_duplicate_field').click(function(e) {

        // Prevent default action
        e.preventDefault();

        // Check if field is page element
        var is_page_element = !jQuery(this).closest('tr').find('span.wccf_row_key').length;

        // Requre new key for regular fields
        if (!is_page_element) {

            // Display field key prompt
            var field_key = prompt(wccf.confirmation.duplicating_field);

            // No value provided
            if (field_key === null || field_key === '') {
                return;
            }
        }

        // Proceed with request
        var redirect_url = jQuery(this).prop('href') + '&wccf_field_key=' + encodeURIComponent(field_key);
        jQuery(location).attr('href', redirect_url);
    });

    /**
     * List tips
     */
    if (typeof jQuery.fn.tipTip === 'function') {
        jQuery('.wccf-tip').tipTip();
    }

    /**
     * Enable field sorting
     */
    jQuery('table.posts #the-list').sortable({
        items:          'tr',
        handle:         '.wccf_post_sort_handle',
        axis:           'y',
        containment:    jQuery('table.posts #the-list').closest('table'),
        tolerance:      'pointer',
        start: function(event, ui) {
            ui.placeholder.height(ui.helper.outerHeight());
            jQuery('table.posts #the-list').addClass('wccf_post_sorting');
            jQuery('table.posts #the-list').sortable('option', 'grid', [1, jQuery('table.posts #the-list tr').height()]);
        },
        stop: function(event, ui) {
            jQuery('table.posts #the-list').removeClass('wccf_post_sorting');
        },
        helper: function (event, ui) {
            ui.children().each(function() {
                jQuery(this).width(jQuery(this).width());
            });
            return ui;
        },
        update: function(event, ui) {
            jQuery.post(wccf.ajaxurl, {
                action:     'wccf_update_field_sort_order',
                sort_order: jQuery('table.posts #the-list').sortable('serialize')
            });
        }
    });

    /**
     * Disable actions meta box collapsing
     */
    jQuery('div#wccf_product_field_actions').each(function() {
        jQuery(this).find('.handlediv, .hndle').on('click.postboxes', function(e) {
            e.stopImmediatePropagation()
        });
        jQuery(this).find('.handlediv').remove();
        jQuery(this).removeClass('closed');
    });

    /**
     * Change page title to field label
     */
    if (jQuery('#poststuff .wccf_post_settings div.wccf_post_title').length > 0) {

        var post_title = jQuery('#poststuff .wccf_post_settings div.wccf_post_title');
        var h1_elements = jQuery('#wpbody-content .wrap h1');

        if (h1_elements.length === 0) {
            post_title.show();
        }
        else {
            h1_elements.first().replaceWith(post_title.html());
            post_title.remove();
        }
    }

    /**
     * Style field type selection
     */
    jQuery('#wccf_post_config_field_type').each(function () {
        if (jQuery(this).hasClass('wccf_grouped_select2') && !jQuery(this).data('select2')) {
            jQuery(this).rightpress_grouped_select2({
                rightpress_disabled_click_notice: wccf.error_messages.field_type_incompatible,
            });
        }
    });

    /**
     * Change field key to lowercase
     */
    jQuery('#wccf_post_config_key').on('keyup change', function(e) {
        jQuery(this).val(jQuery(this).val().toLowerCase());
    });

    /**
     * Update field label and field key
     */
    jQuery.each(['label', 'key'], function(index, field) {

        // Update immediatelly
        if (jQuery('#wccf_post_config_' + field).val() !== '') {
            jQuery('.wccf_field_' + field).html(jQuery('#wccf_post_config_' + field).val());
        }

        // Get placeholder
        var placeholder = field === 'label' ? 'New Field' : 'new_field';
        placeholder = typeof wccf === 'object' && typeof wccf.placeholders[field] !== 'undefined' ? wccf.placeholders[field] : placeholder;

        // Update on change
        jQuery('#wccf_post_config_' + field).on('keyup change', function() {
            var new_value = jQuery(this).val();
            new_value = new_value !== '' ? new_value : placeholder;
            jQuery('.wccf_field_' + field).html(new_value);
        });
    });

    /**
     * Only allow letters, numbers and underscore to be typed into the key field
     */
    jQuery('#wccf_post_config_key').on('keypress', function(e) {
        return restrict_input(e, 'key');
    });

    /**
     * Ensure field key is unique
     */
    var current_unique_field_key_validation_request = null;

    jQuery('#wccf_post_config_key').each(function() {

        var last_field_key_value = null;

        jQuery(this).on('keyup', function(e) {
            last_field_key_value = jQuery(this).val();
            unique_field_key_validation();
        });
        jQuery(this).on('change', function(e) {
            if (jQuery(this).val() === last_field_key_value) {
                return;
            }
            last_field_key_value = jQuery(this).val();
            unique_field_key_validation();
        });
        unique_field_key_validation();
    });

    /**
     * Only allow numbers and dot character to be typed into the pricing value field
     */
    jQuery('#wccf_post_config_pricing_value').on('keypress', function(e) {
        return restrict_input(e, 'float');
    });

    /**
     * Only allow numbers to be typed into the character limit field
     */
    jQuery('#wccf_post_config_character_limit').on('keypress', function(e) {
        return restrict_input(e, 'int');
    });

    /**
     * Restrict input to specified characters
     */
    function restrict_input(e, allow)
    {
        if (allow === 'key') {
            var allowable_characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';
        }
        else if (allow === 'float') {
            var allowable_characters = '1234567890.';
        }
        else if (allow === 'int') {
            var allowable_characters = '1234567890';
        }
        else {
            var allowable_characters = '';
        }

        var k = document.all ? parseInt(e.keyCode) : parseInt(e.which);

        if (k !== 13 && k !== 8 && k !== 0) {
            if (e.ctrlKey === false && e.altKey === false) {
                return (allowable_characters.indexOf(String.fromCharCode(k)) !== -1);
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
    }

    /**
     * Disable field types that current field type can't be changed to
     */
    jQuery('#wccf_post_config_original_field_type').each(function() {

        var original_type = jQuery(this).val();

        // Check if this is an existing object
        if (original_type !== '') {

            var interchangeable_with = (typeof wccf === 'object' ? wccf.interchangeable_fields[original_type] : []);

            // Iterate over all options
            jQuery('#wccf_post_config_field_type').find('option').each(function() {
                if (jQuery.inArray(jQuery(this).val(), interchangeable_with) === -1) {
                    jQuery(this).prop('disabled', 'disabled');
                }
            });
        }
    });

    /**
     * Disable sorting of meta boxes
     */
    jQuery('.meta-box-sortables').sortable({
        disabled: true
    });

    /**
     * jQuery UI Buttonsets
     */
    jQuery('#poststuff .wccf_post_buttonset').buttonset().css('display', 'block');

    /**
     * Conditions meta box switching
     */
    jQuery('#poststuff .wccf_post .wccf_post_config_conditional').each(function() {
        jQuery(this).change(function() {
            toggle_conditions_visibility(jQuery(this));
        });
        toggle_conditions_visibility(jQuery(this));
    });

    /**
     * Toggle conditions visibility
     */
    function toggle_conditions_visibility(field)
    {
        var meta_box = jQuery('#poststuff .wccf_field_conditions_meta_box');

        if (jQuery('#poststuff .wccf_post_config_conditional:checked').val() === '1') {
            meta_box.fadeIn();
        }
        else {
            meta_box.fadeOut();
            clear_items('condition');
        }
    }

    /**
     * Pricing meta box switching
     */
    jQuery('#poststuff .wccf_post .wccf_post_config_pricing').each(function() {
        jQuery(this).change(function() {
            toggle_price_fields();
        });
        toggle_price_fields();
    });

    /**
     * Set up options and conditions
     */
    jQuery.each(['options', 'conditions'], function(index, type) {

        var type_singular = type.replace(/s$/, '');

        // Select corresponding list
        jQuery('#poststuff .wccf_post_' + type + '_list').each(function() {

            var list = jQuery(this);

            // Check if any items exist in config
            if (typeof wccf_fb === 'object' && typeof wccf_fb[type] === 'object' && wccf_fb[type].length > 0) {
                for (var key in wccf_fb[type]) {
                    if (wccf_fb[type].hasOwnProperty(key)) {
                        add(type_singular, list, wccf_fb[type], key);
                    }
                }
            }

            // Bind click action
            list.closest('.wccf_post').find('.wccf_post_add_' + type_singular + ' button').click(function() {
                add(type_singular, list, false, false);
            });
        });
    });

    /**
     * Handle Field Type change
     */
    jQuery('#poststuff .wccf_post #wccf_post_config_field_type').each(function() {
        jQuery(this).change(function() {
            type_changed(jQuery(this));
        });
        type_changed(jQuery(this));
    });

    /**
     * Handle Display As change
     */
    jQuery('#poststuff .wccf_post #wccf_post_config_display_as').each(function() {
        jQuery(this).change(function() {
            display_as_changed(jQuery(this));
        });
        display_as_changed(jQuery(this));
    });

    /**
     * Add no options, conditions etc (var type) notice
     */
    function add_no(type, list)
    {
        prepend(list, 'no_' + type);
    }

    /**
     * Remove No Options, No Conditions etc (var type) notice
     */
    function remove_no(type, list)
    {
        list.find('.wccf_post_no_' + type).remove();
    }

    /**
     * Add one option, condition etc (var type)
     */
    function add(type, list, config, key)
    {

        // Add wrapper
        add_wrapper(type, list);

        // Make sure we don't have the No Options, No Conditions etc notice
        remove_no(type + 's', list);

        // Add element
        append(list.find('.wccf_post_' + type + '_wrapper'), type, null);

        // Select last item
        var last_item = list.find('.wccf_post_' + type).last();

        // Fix field ids, names and values
        fix_fields(last_item, last_item, type, config);

        // Condition specific function calls
        if (type === 'condition') {

            // Handle disabled or non-existent conditions on page load
            if (config !== false) {

                // Get identifier
                var identifier = get_identifier(last_item);

                // Condition is disabled or no longer exists
                jQuery.each(['_disabled', '_disabled_taxonomy', '_non_existent', '_non_existent_taxonomy', '_non_existent_other_custom_field'], function(flag_index, flag_type) {
                    if (typeof config[identifier][flag_type] !== 'undefined') {
                        last_item.find('.wccf_post_condition_content').html(get_template('condition' + flag_type));
                        last_item.closest('.wccf_post_condition').addClass('wccf_post_condition_disabled_state');
                        skip_condition = true;
                    }
                });
            }

            // Fix elements of current condition
            fix_condition(last_item, config);
        }

        /**
         * Restrict input on some option fields
         */
        if (type === 'option') {

            // Only allow letters, numbers and underscore to be typed into the key field
            last_item.find('.wccf_post_config_options_key').on('keypress', function(e) {
                return restrict_input(e, 'key');
            });

            // Custom validation to ensure unique option keys
            last_item.find('.wccf_post_config_options_key').on('change', function(e) {
                unique_option_key_validation();
            });

            // Only numbers and dot character to be typed into the pricing value field
            last_item.find('.wccf_post_config_pricing_value').on('keypress', function(e) {
                return restrict_input(e, 'float');
            });
        }

        // Handle delete action
        last_item.find('.wccf_post_' + type + '_remove_handle').click(function() {
            remove(type, jQuery(this).closest('.wccf_post_' + type));
        });

        // Make sure only one "Selected" item is set for Select and Radio button set fields
        last_item.find('.wccf_post_config_' + type + 's_selected').change(function() {
            if (jQuery(this).val() === '1' && jQuery.inArray(jQuery('#poststuff #wccf_post_config_field_type').val(), ['select', 'radio']) !== -1) {

                var current_id = jQuery(this).prop('id');

                jQuery(this).closest('.wccf_post_option_wrapper').find('.wccf_post_config_options_selected').each(function() {
                    if (jQuery(this).prop('id') !== current_id) {
                        clear_field_value(jQuery(this));
                    }
                });
            }
        });
    }

    /**
     * Remove one option, condition etc (var type)
     */
    function remove(type, element)
    {
        var list = element.closest('.wccf_post_' + type + 's_list');

        // Last element? Remove the entire wrapper and add No Options, No Conditions etc wrapper
        if (list.find('.wccf_post_' + type + '_wrapper').children().length < 2) {
            remove_wrapper(type, list);
            add_no(type + 's', list);
        }

        // Remove single element
        else {
            element.remove();
        }
    }

    /**
     * Add wrapper for options, conditions etc (var type)
     */
    function add_wrapper(type, list)
    {
        // Make sure we don't have one yet before proceeding
        if (list.find('.wccf_post_' + type + '_wrapper').length === 0) {

            // Add wrapper
            prepend(list, type + '_wrapper', null);

            // Maybe show price fields for type "option"
            if (type === 'option') {
                toggle_price_fields();
            }

            // Make it sortable
            list.find('.wccf_post_' + type + '_wrapper').sortable({
                axis:           'y',
                handle:         '.wccf_post_' + type + '_sort_handle',
                opacity:        0.7,
                containment:    list.find('.wccf_post_' + type + '_wrapper').first(),
                tolerance:      'pointer',
                stop: function(event, ui) {

                    // Remove styles added by jQuery UI
                    jQuery(this).find('.wccf_post_' + type).each(function() {
                        jQuery(this).removeAttr('style');
                    });
                }
            });
        }
    }

    /**
     * Remove option, condition etc (var type) wrapper
     */
    function remove_wrapper(type, list)
    {
        list.find('.wccf_post_' + type + '_header').remove();
        list.find('.wccf_post_' + type + '_wrapper').remove();
    }

    /**
     * Fix field id, name and value
     */
    function fix_fields(element, master_element, type, config)
    {

        // Get item identifier
        var identifier = get_identifier(master_element);

        // Pluralize type
        var type_plural = type + 's';

        // Iterate over all inputs of this element
        element.find('input, select').each(function() {

            // ID
            if (typeof jQuery(this).prop('id') !== 'undefined') {
                var new_value = jQuery(this).prop('id').replace(/(\{i\}|\d+)?$/, identifier);
                jQuery(this).prop('id', new_value);
            }

            // Name
            if (typeof jQuery(this).prop('name') !== 'undefined') {
                var new_value = jQuery(this).prop('name').replace(/^wccf_settings\[(options|conditions)\]\[(\{i\}|\d+)\]?/, 'wccf_settings[' + type + 's][' + identifier + ']');
                jQuery(this).prop('name', new_value);
            }

            // Get field key
            var field_key = jQuery(this).prop('id').replace(new RegExp('wccf_' + type_plural + '_'), '').replace(/(_\d+)?$/, '');

            // Select options in select fields
            if (jQuery(this).is('select')) {

                // Check if settings are defined
                if (config && typeof config[identifier] !== 'undefined' && typeof config[identifier][field_key] !== 'undefined' && config[identifier][field_key]) {

                    // Multiselect
                    if (is_multiselect(jQuery(this))) {

                        // Check if multiselect options are defined
                        if (typeof wccf_fb_multiselect_options !== 'undefined' && typeof wccf_fb_multiselect_options[type_plural] !== 'undefined' && typeof wccf_fb_multiselect_options[type_plural][identifier] !== 'undefined' && typeof wccf_fb_multiselect_options[type_plural][identifier][field_key] === 'object') {

                            // Iterate over settings
                            for (var k = 0; k < wccf_fb[type_plural][identifier][field_key].length; k++) {

                                // Reference options
                                var all_options = wccf_fb_multiselect_options[type_plural][identifier][field_key];
                                var current_option_key = wccf_fb[type_plural][identifier][field_key][k];

                                // Iterate over options
                                for (var l = 0; l < all_options.length; l++) {

                                    // Check if current option is selected
                                    if (typeof all_options[l] !== 'undefined' && typeof all_options[l]['id'] !== 'undefined' && all_options[l]['id'] == current_option_key) {

                                        // Add option to multiselect field
                                        var current_option_label = all_options[l]['text'];
                                        jQuery(this).append(jQuery('<option></option>').attr('value', current_option_key).prop('selected', true).text(current_option_label));
                                    }
                                }
                            }
                        }
                    }
                    // Regular select
                    else {

                        // Set value
                        jQuery(this).val(config[identifier][field_key]);
                    }
                }
            }
            // Check checkboxes
            else if (jQuery(this).is(':checkbox')) {

                // Check if settings are defined
                if (config && typeof config[identifier] !== 'undefined' && typeof config[identifier][field_key] !== 'undefined' && config[identifier][field_key]) {

                    // Check checkbox
                    jQuery(this).prop('checked', true);
                }
            }
            // Add value for text input fields
            else {

                // Check if settings are defined
                if (config && typeof config[identifier] !== 'undefined' && typeof config[identifier][field_key] !== 'undefined') {

                    // Set value
                    jQuery(this).prop('value', config[identifier][field_key]);
                }
            }

            // Initialize select2 multiselect
            if (jQuery(this).hasClass('wccf_select2')) {
                initialize_multiselect_select2(jQuery(this), field_key);
            }
        });

        // Toogle price fields for options
        toggle_price_fields();
    }

    /**
     * Initialize select2 on select
     */
    function initialize_select2(element)
    {

        // Only non-multiselect fields are supported
        if (is_multiselect(element)) {
            return;
        }

        // Make sure our Select2 reference is set
        if (typeof RP_Select2 === 'undefined') {
            return;
        }

        // Initialize Select2
        RP_Select2.call(element);
    }

    /**
     * Initialize select2 on multiselect
     */
    function initialize_multiselect_select2(element, key)
    {

        // Only multiselect fields are supported
        if (!is_multiselect(element)) {
            return;
        }

        // Make sure our Select2 reference is set
        if (typeof RP_Select2 === 'undefined') {
            return;
        }

        // Initialize Select2
        RP_Select2.call(element, {
            width: '100%',
            minimumInputLength: 1,
            placeholder: wccf.labels.select2_placeholder,
            language: {
                noResults: function (params) {
                    return wccf.labels.select2_no_results;
                }
            },
            ajax: {
                url:        wccf.ajaxurl,
                type:       'POST',
                dataType:   'json',
                delay:      250,
                data: function(params) {
                    return {
                        query:      params.term,
                        action:     'wccf_load_multiselect_options',
                        type:       key,
                        selected:   element.val()
                    };
                },
                dataFilter: function(raw_response) {
                    return parse_ajax_json_response(raw_response, true);
                },
                processResults: function(data, page) {
                    return {
                        results: data.results
                    };
                }
            }
        });
    }

    /**
     * Field type setting value changed
     */
    function type_changed(type_field)
    {

        var value = type_field.val();
        var options_meta_box = jQuery('#poststuff .wccf_field_options_meta_box');

        // Select, multiselect, checkbox and radio field types - show options
        if (jQuery.inArray(value, ['select', 'multiselect', 'checkbox', 'radio']) !== -1) {

            // Ensure that only one Selected item is selected if field type is Select or Radio buttons
            if (jQuery.inArray(value, ['select', 'radio']) !== -1) {

                var found_selected = false;

                jQuery('#poststuff .wccf_post_option .wccf_post_config_options_selected').each(function() {
                    if (jQuery(this).val() === '1') {
                        if (found_selected) {
                            clear_field_value(jQuery(this));
                        }
                        else {
                            found_selected = true;
                        }
                    }
                });
            }

            // Show options UI
            options_meta_box.fadeIn();
        }
        else {
            options_meta_box.fadeOut();
            clear_items('option');
        }

        // Toggle default value field visibility and change field type for default value
        jQuery('#poststuff #wccf_post_config_default_value').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (jQuery.inArray(value, ['text', 'textarea', 'password', 'email', 'number', 'decimal', 'color']) !== -1) {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }

            // Change field type for default value field
            if (value === 'email' && jQuery(this).prop('type') !== 'email') {
                jQuery(this).prop('type', 'email');
            }
            else if ((value === 'number' || value === 'decimal') && jQuery(this).prop('type') !== 'number') {
                jQuery(this).prop('type', 'number');
            }
            else if (value !== 'email' && value !== 'number' && value !== 'decimal' && jQuery(this).prop('type') !== 'text') {
                jQuery(this).prop('type', 'text');
            }

            // Color picker placeholder
            if (value === 'color') {
                jQuery(this).attr('placeholder', 'e.g. #eeeeee');
            }
            else {
                jQuery(this).removeAttr('placeholder');
            }
        });

        // Toggle character limit field visibility
        jQuery('#poststuff #wccf_post_config_character_limit').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (jQuery.inArray(value, ['text', 'textarea', 'password', 'email', 'number', 'decimal']) !== -1) {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }
        });

        // Toggle placeholder field visibility
        jQuery('#poststuff #wccf_post_config_placeholder').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (jQuery.inArray(value, ['text', 'textarea', 'password', 'email', 'number', 'decimal', 'date', 'time', 'datetime', 'color']) !== -1) {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }
        });

        // Toggle max selected field visibility
        jQuery('#poststuff #wccf_post_config_min_selected, #poststuff #wccf_post_config_max_selected').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (jQuery.inArray(value, ['checkbox', 'multiselect']) !== -1) {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }
        });

        // Toggle max value field visibility
        jQuery('#poststuff #wccf_post_config_min_value, #poststuff #wccf_post_config_max_value').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (jQuery.inArray(value, ['number', 'decimal']) !== -1) {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }
        });

        // Toggle heading level field visibility
        jQuery('#poststuff #wccf_post_config_heading_level').each(function() {

            var config_field = jQuery(this).closest('.wccf_config_field');

            if (value === 'heading') {
                enable_field(jQuery(this));
                config_field.show();
            }
            else {
                config_field.hide();
                disable_field(jQuery(this));
            }
        });

        // Toggle advanced pricing option visibility
        jQuery('#poststuff .wccf_post_config_pricing_method').each(function() {

            var fee_per_character_option = jQuery(this).find('option[value="advanced_fees_fee_per_character"]');
            var fee_x_value_option = jQuery(this).find('option[value="advanced_fees_fee_x_value"]');

            // Show both
            if (value === 'number' || value === 'decimal') {
                fee_per_character_option.closest('optgroup').show();
                fee_per_character_option.prop('disabled', false);
                fee_x_value_option.prop('disabled', false);
                fee_per_character_option.show();
                fee_x_value_option.show();
            }
            // Hide both
            else if (jQuery.inArray(value, ['text', 'textarea', 'password', 'email', 'number', 'decimal']) === -1) {

                // Reset options if selected
                if (jQuery(this).val() === 'advanced_fees_fee_per_character' || jQuery(this).val() === 'advanced_fees_fee_x_value') {
                    jQuery(this).prop('selectedIndex', 0);
                }

                fee_per_character_option.hide();
                fee_x_value_option.hide();
                fee_per_character_option.prop('disabled', true);
                fee_x_value_option.prop('disabled', true);
                fee_per_character_option.closest('optgroup').hide();
            }
            // Show fee_per_character and hide fee_x_value
            else {

                fee_per_character_option.closest('optgroup').show();
                fee_per_character_option.prop('disabled', false);
                fee_per_character_option.show();

                // Reset option if selected
                if (jQuery(this).val() === 'advanced_fees_fee_x_value') {
                    jQuery(this).prop('selectedIndex', 0);
                }

                fee_x_value_option.hide();
                fee_x_value_option.prop('disabled', true);
            }

            // Update select2
            if (jQuery(this).data('select2')) {
                jQuery(this).select2('destroy').rightpress_grouped_select2({
                    rightpress_disabled_click_notice: wccf.error_messages.pricing_option_incompatible,
                });
            }
        });

        // Check if page element type was selected (not real field)
        var is_page_element = (jQuery.inArray(value, ['heading', 'separator']) !== -1);

        // Toggle field key in page heading
        jQuery('.wp-heading-inline .wccf_field_key').each(function() {

            if (is_page_element) {
                jQuery(this).fadeOut();
            }
            else {
                jQuery(this).fadeIn();
            }
        });

        // Change field label in page heading for separator field
        jQuery('.wp-heading-inline .wccf_field_label').each(function() {

            if (value === 'separator') {
                jQuery(this).html(wccf.labels.separator);
            }
            else {
                var label_value = jQuery('#wccf_post_config_label').val();
                label_value = (typeof label_value !== 'undefined' && label_value !== '') ? label_value : wccf.placeholders.label;
                jQuery(this).html(label_value);
            }
        });

        // Toggle field label input for separator field
        jQuery('#wccf_post_config_label').each(function() {

            var label_field = jQuery(this).closest('.wccf_config_field');

            if (value === 'separator') {
                label_field.hide();
                jQuery(this).val('');
                disable_field(jQuery(this));
            }
            else {
                enable_field(jQuery(this));
                label_field.show();
            }
        });

        // Toggle unique key field
        jQuery('#wccf_post_config_key').each(function() {

            var key_field   = jQuery(this).closest('.wccf_config_field');
            var label_field = jQuery(this).closest('.wccf_post_settings').find('#wccf_post_config_label').closest('.wccf_config_field');

            var permanently_disabled = !!(typeof jQuery(this).data('wccf-permanently-disabled') !== 'undefined' && jQuery(this).data('wccf-permanently-disabled'));

            if (is_page_element) {

                key_field.hide();
                label_field.removeClass('wccf_config_field_half');

                if (!permanently_disabled) {
                    jQuery(this).val('');
                    disable_field(jQuery(this));
                }
            }
            else {

                if (!permanently_disabled) {
                    enable_field(jQuery(this));
                }

                key_field.show();
                label_field.addClass('wccf_config_field_half');
            }
        });

        // Toggle sidebar buttons not applicable to page elements
        jQuery('.wccf_post_checkboxes .wccf_post_buttonset').not(':has(#wccf_post_config_conditional_1)').each(function() {

            // Disable
            if (is_page_element) {

                jQuery(this).find('input[type="radio"]').prop('checked', false).prop('disabled', true);
                jQuery(this).buttonset('refresh');
            }
            // Enable if disabled
            else if (jQuery(this).find('input[type="radio"]:disabled').length) {

                jQuery(this).find('input[type="radio"]').prop('disabled', false).last().click();
                jQuery(this).buttonset('refresh');
            }
        });

        // Fix advanced section last input width
        var visible_advanced_elements = jQuery('.wccf_field_advanced_meta_box .wccf_post_advanced .wccf_config_field:visible');

        if (visible_advanced_elements.length % 2 === 0) {
            visible_advanced_elements.addClass('wccf_config_field_half');
        }
        else {
            visible_advanced_elements.last().removeClass('wccf_config_field_half');
        }

        // Toggle price fields
        toggle_price_fields();
    }

    /**
     * Display As setting value changed
     */
    function display_as_changed(display_as_field)
    {
        var value = display_as_field.val();

        // Take reference of the checkout position field
        var checkout_position_field = jQuery('#poststuff .wccf_post #wccf_post_config_position');

        // Maybe reset field value
        var selected_option = checkout_position_field.val();
        var reset_value = ((value === 'billing_address' && selected_option.indexOf('_billing') === -1) || (value === 'shipping_address' && selected_option.indexOf('_shipping') === -1));

        // Iterate over checkout position options
        checkout_position_field.find('option').each(function() {

            // Get current option
            var current_option = jQuery(this).prop('value');

            // Display all options
            if (value === 'user_profile') {
                if (jQuery(this).prop('disabled')) {
                    jQuery(this).prop('disabled', false);
                    jQuery(this).show();
                    reset_value = true;
                }
            }
            // Display only billing address options
            else if (value === 'billing_address') {

                // Show
                if (current_option.indexOf('_billing') !== -1) {
                    jQuery(this).prop('disabled', false);
                    jQuery(this).show();
                }
                // Hide
                else {
                    jQuery(this).prop('disabled', true);
                    jQuery(this).hide();
                }
            }
            // Display only shipping address options
            else if (value === 'shipping_address') {

                // Show
                if (current_option.indexOf('_shipping') !== -1) {
                    jQuery(this).prop('disabled', false);
                    jQuery(this).show();
                }
                // Hide
                else {
                    jQuery(this).prop('disabled', true);
                    jQuery(this).hide();
                }
            }
        });

        // Reset field value
        if (reset_value) {
            checkout_position_field.val(checkout_position_field.find('option:enabled').first().val());
        }
    }

    /**
     * Clear items - options or conditions
     */
    function clear_items(type_singular)
    {
        var list = jQuery('#poststuff .wccf_post .wccf_post_' + type_singular + 's_list').first();

        // Check if any items exist first
        if (list.find('.wccf_post_no_' + type_singular + 's').length === 0) {
            remove_wrapper(type_singular, list);
            add_no(type_singular + 's', list);
        }
    }

    /**
     * Fix condition
     */
    function fix_condition(element, config)
    {

        // Get current condition type
        var condition_type_field = element.find('.wccf_condition_type');
        var condition_type_value = condition_type_field.val();

        // Condition type
        element.find('.wccf_condition_type').change(function() {
            condition_type_value = condition_type_field.val();
            toggle_condition_fields(element, condition_type_value, false);
        });
        toggle_condition_fields(element, condition_type_value, config);

        // Style condition type selector
        if (condition_type_field.hasClass('wccf_grouped_select2') && !condition_type_field.data('select2')) {
            condition_type_field.rightpress_grouped_select2();
        }
    }

    /**
     * Toggle visibility of condition fields
     */
    function toggle_condition_fields(element, condition_type, config)
    {

        // Reference field wrapper
        var wrapper = element.find('.wccf_condition_setting_fields_wrapper');

        // Make sure we don't have required set of fields yet
        if (!wrapper.find('.wccf_condition_setting_fields_' + condition_type).length) {

            // Clear wrapper
            wrapper.html('');

            // Add condition fields
            append(wrapper, ('condition_setting_fields_' + condition_type), null);

            // Fix field ids, names and values
            fix_fields(wrapper, element, 'condition', config);
        }

        // Fix coupon, meta field, other custom field conditions
        element.find('.wccf_condition_method').change(function() {
            fix_coupons_applied_condition(element, condition_type);
            fix_meta_field_condition(element, condition_type);
            fix_other_custom_field_value(element);
        });
        fix_coupons_applied_condition(element, condition_type);
        fix_meta_field_condition(element, condition_type);
        fix_other_custom_field_value(element);

        // Other custom field condition other field input
        element.find('.wccf_condition_other_field_id').change(function() {
            fix_other_custom_field_methods(element);
        });
        fix_other_custom_field_methods(element);
    }

    /**
     * Fix fields of coupons applied condition
     */
    function fix_coupons_applied_condition(element, condition_type)
    {

        // Only proceed if condition type is coupons applied
        if (condition_type !== 'cart__coupons' && condition_type !== 'order__coupons') {
            return;
        }

        // Get current method
        var current_method = element.find('.wccf_condition_method').val();

        // Reference coupons field
        var coupons_field = element.find('.wccf_condition_coupons');

        // Proceed depending on current method
        if (jQuery.inArray(current_method, ['at_least_one_any', 'none_at_all']) !== -1) {
            element.find('.wccf_condition_setting_fields_' + condition_type).find('.wccf_condition_method').parent().removeClass('wccf_condition_setting_fields_single').addClass('wccf_condition_setting_fields_triple');
            coupons_field.parent().css('display', 'none');
            clear_field_value(coupons_field);
            coupons_field.prop('disabled', true);
        }
        else {
            element.find('.wccf_condition_setting_fields_' + condition_type).find('.wccf_condition_method').parent().removeClass('wccf_condition_setting_fields_triple').addClass('wccf_condition_setting_fields_single');
            coupons_field.parent().css('display', 'block');
            coupons_field.prop('disabled', false);
        }
    }

    /**
     * Fix fields of meta field condition
     */
    function fix_meta_field_condition(element, condition_type)
    {

        // Only proceed if condition type is meta field
        if (condition_type !== 'customer__meta' && condition_type !== 'product_property__meta') {
            return;
        }

        // Get current method
        var current_method = element.find('.wccf_condition_method').val();

        // Reference text field
        var text_field = element.find('.wccf_condition_text');

        // Proceed depending on current method
        if (jQuery.inArray(current_method, ['is_empty', 'is_not_empty', 'is_checked', 'is_not_checked']) !== -1) {
            element.find('.wccf_condition_setting_fields_' + condition_type).find('.wccf_condition_method').parent().removeClass('wccf_condition_setting_fields_single').addClass('wccf_condition_setting_fields_double');
            text_field.parent().css('display', 'none');
            clear_field_value(text_field);
            text_field.prop('disabled', true);
        }
        else {
            element.find('.wccf_condition_setting_fields_' + condition_type).find('.wccf_condition_method').parent().removeClass('wccf_condition_setting_fields_double').addClass('wccf_condition_setting_fields_single');
            text_field.parent().css('display', 'block');
            text_field.prop('disabled', false);
        }
    }

    /**
     * Fix other custom field condition methods
     */
    function fix_other_custom_field_methods(element)
    {

        // Get other custom field input set
        var other_custom_field = element.find('.wccf_condition_setting_fields_other__other_custom_field');

        // Check if set was found
        if (other_custom_field.length > 0) {

            // Get selected field type
            var selected_field_type = other_custom_field.find('.wccf_condition_other_field_id option:selected').data('wccf-condition-other-field-type');

            // Check if we can determine field type
            if (typeof selected_field_type === 'undefined' || !selected_field_type || typeof wccf.other_field_condition_methods_by_type[selected_field_type] === 'undefined') {
                return;
            }

            // Get supported condition methods
            var supported_methods = wccf.other_field_condition_methods_by_type[selected_field_type];

            // Check if we need to reset selection
            var reset = false;

            // Iterate over method field options
            other_custom_field.find('.wccf_condition_method option').each(function() {
                if (jQuery.inArray(jQuery(this).val(), supported_methods) !== -1) {
                    jQuery(this).prop('disabled', false).show();
                }
                else {
                    reset = reset ? reset : jQuery(this).is(':selected');
                    jQuery(this).prop('disabled', true).hide();
                }
            });

            // Reset selection if needed
            if (reset) {
                clear_field_value(element.find('.wccf_condition_method'));
                fix_other_custom_field_value(element);
            }
        }
    }

    /**
     * Fix fields of other_custom_field condition
     */
    function fix_other_custom_field_value(element)
    {

        // Get other custom field input set
        var other_custom_field = element.find('.wccf_condition_setting_fields_other__other_custom_field');

        // Check if set was found
        if (other_custom_field.length > 0) {

            // Get current method
            var current_method = other_custom_field.find('.wccf_condition_method').val();

            // Select text input
            var text_field = other_custom_field.find('.wccf_condition_text');

            // Proceed depending on current method
            if (jQuery.inArray(current_method, ['is_empty', 'is_not_empty', 'is_checked', 'is_not_checked']) !== -1) {
                other_custom_field.find('input, select').not('.wccf_condition_other_field_id').parent().removeClass('wccf_condition_setting_fields_single').addClass('wccf_condition_setting_fields_double');
                text_field.parent().css('display', 'none');
                clear_field_value(text_field);
                disable_field(text_field);
            }
            else {
                other_custom_field.find('input, select').parent().removeClass('wccf_condition_setting_fields_double').addClass('wccf_condition_setting_fields_single');
                text_field.parent().css('display', 'block');
                enable_field(text_field);
            }
        }
    }

    /**
     * Get item identifier
     */
    function get_identifier(element)
    {

        var identifier;

        // Identifier set on element
        if (typeof element.data('data-wccf-identifier') !== 'undefined') {

            // Set current identifier
            identifier = element.data('data-wccf-identifier');
        }
        // Identifier not set
        else {

            // Last identifier set on parent
            if (typeof element.parent().data('data-wccf-last-identifier') !== 'undefined') {

                // Set next identifier
                identifier = element.parent().data('data-wccf-last-identifier') + 1;
            }
            // Last identifier not set
            else {

                // Set next identifier
                identifier = 0;
            }

            // Set identifier on element
            element.data('data-wccf-identifier', identifier);

            // Set last identifier on parent
            element.parent().data('data-wccf-last-identifier', identifier);
        }

        // Return identifier
        return identifier;
    }

    /**
     * Toggle visibility of option price fields
     */
    function toggle_price_fields()
    {

        // Check if pricing is available for this object
        if (!jQuery('#poststuff .wccf_post_config_pricing').length || !jQuery('#poststuff .wccf_field_pricing_meta_box').length) {
            toggle_price_fields_options(false);
            return;
        }

        var pricing_enabled = (jQuery('#poststuff .wccf_post_config_pricing:checked').val() === '1');
        var field_type = jQuery('#poststuff #wccf_post_config_field_type').val();
        var field_type_has_options = (jQuery.inArray(field_type, ['select', 'multiselect', 'checkbox', 'radio']) !== -1);

        // Show pricing in options
        if (pricing_enabled && field_type_has_options) {
            toggle_price_fields_options(true);
            toggle_price_fields_meta_box(false);
            toggle_checkout_fee_tax_class_option('show');
        }
        // Show pricing in meta box
        else if (pricing_enabled) {
            toggle_price_fields_meta_box(true);
            toggle_price_fields_options(false);
            toggle_checkout_fee_tax_class_option('show');
        }
        // Hide pricing in options
        else {
            toggle_price_fields_options(false);
            toggle_price_fields_meta_box(false);
            toggle_checkout_fee_tax_class_option('hide');
        }
    }

    /**
     * Toggle visibility of pricing fields in options
     */
    function toggle_price_fields_options(is_visible)
    {

        // Select meta box
        var meta_box = jQuery('#poststuff .wccf_field_options_meta_box');

        // Iterate over options
        meta_box.find('.wccf_post_option_wrapper .wccf_post_option').each(function() {

            var option_price = jQuery(this).find('.wccf_post_option_price');

            // Pricing should be visible
            if (is_visible) {

                // Show inputs
                option_price.fadeIn();

                // Enable inputs
                enable_fields(option_price);

                // Initialize grouped select2
                option_price.find('.wccf_post_config_pricing_method').each(function() {
                    if (jQuery(this).hasClass('wccf_grouped_select2') && !jQuery(this).data('select2')) {
                        jQuery(this).rightpress_grouped_select2({
                            rightpress_disabled_click_notice: wccf.error_messages.pricing_option_incompatible,
                        });
                    }
                });
            }
            // Pricing should be hidden
            else {

                // Hide inputs
                option_price.hide();

                // Clear input values
                clear_field_values(option_price);

                // Disable inputs
                disable_fields(option_price);

                // Destroy select2
                option_price.find('.wccf_post_config_pricing_method').each(function() {
                    if (jQuery(this).data('select2')) {
                        jQuery(this).select2('destroy');
                    }
                });
            }
        });

        // Select pricing header
        var header = meta_box.find('.wccf_post_option_content_header .wccf_post_option_price');

        // Show or hide pricing header
        if (is_visible) {
            header.fadeIn();
        }
        else {
            header.hide();
        }

        // Resize other fields
        var new_size = is_visible ? '31%' : '46.5%';
        meta_box.find('.wccf_post_option_resize').css('width', new_size);
    }

    /**
     * Toggle visibility of pricing meta box
     */
    function toggle_price_fields_meta_box(is_visible)
    {

        // Select meta box
        var meta_box = jQuery('#poststuff .wccf_field_pricing_meta_box');

        // Pricing should be visible
        if (is_visible) {

            // Show meta box
            meta_box.fadeIn();

            // Enable inputs
            enable_fields(meta_box);

            // Initialize grouped select2
            meta_box.find('#wccf_post_config_pricing_method').each(function() {
                if (jQuery(this).hasClass('wccf_grouped_select2') && !jQuery(this).data('select2')) {
                    jQuery(this).rightpress_grouped_select2({
                        rightpress_disabled_click_notice: wccf.error_messages.pricing_option_incompatible,
                    });
                }
            });
        }
        // Pricing should be hidden
        else {

            // Hide meta box
            meta_box.fadeOut();

            // Clear values
            clear_field_values(meta_box);

            // Disable inputs
            disable_fields(meta_box);

            // Destroy select2
            meta_box.find('#wccf_post_config_pricing_method').each(function() {
                if (jQuery(this).data('select2')) {
                    jQuery(this).select2('destroy');
                }
            });
        }
    }

    /**
     * Toggle visibility of tax class option for checkout fields
     */
    function toggle_checkout_fee_tax_class_option(visibility)
    {
        var display = (visibility === 'show' ? 'block' : 'none');
        var tax_class_container = jQuery('select#wccf_post_config_tax_class').closest('.wccf_config_field');

        if (tax_class_container.length > 0) {

            // Show/hide container
            tax_class_container.css('display', display);

            // Clear value and disable field
            if (visibility === 'hide') {
                clear_field_values(tax_class_container);
                disable_fields(tax_class_container);
            }
            // Enable field
            else {
                enable_fields(tax_class_container);
            }
        }
    }

    /**
     * Custom field key validation
     */
    function unique_field_key_validation()
    {
        // Select key input field
        var input = jQuery('#wccf_post_config_key');
        var input_dom = input[0];

        // Get value
        var value = input.val();

        // Do nothing if input is empty (default error will be displayed)
        if (value === '') {
            input_dom.setCustomValidity('');
            return;
        }
        // Set error message while waiting for Ajax response
        else {
            input_dom.setCustomValidity(wccf.error_messages.field_key_validation_in_progress);
        }

        // Send Ajax request
        current_unique_field_key_validation_request = jQuery.ajax({
            type:   'POST',
            url:    wccf.ajaxurl,
            data:   {
                action:     'wccf_validate_field_key',
                post_id:    input.closest('form').find('input#post_ID').val(),
                post_type:  input.closest('form').find('input#post_type').val(),
                value:      value
            },
            beforeSend: function() {
                if (current_unique_field_key_validation_request !== null) {
                    current_unique_field_key_validation_request.abort();
                }
            },
            success: function(response) {

                // Parse response
                response = parse_ajax_json_response(response);

                // Check response
                if (typeof response === 'object' && typeof response.result !== 'undefined') {

                    // Reset error message if field key is unique
                    if (response.result === 'success') {
                        input_dom.setCustomValidity('');
                    }
                    // Set custom error message if it was returned
                    else if (typeof response.message !== 'undefined') {
                        input_dom.setCustomValidity(response.message);
                    }
                }
            }
        });
    }

    /**
     * Custom option key validation
     */
    function unique_option_key_validation()
    {
        // Track option keys
        var values = [];

        // Iterate over option keys
        jQuery('#poststuff .wccf_post_option input.wccf_post_config_options_key').each(function() {

            // Get value
            var value = jQuery(this).val().toLowerCase();

            // Check if such value exists
            if (jQuery.inArray(value, values) === -1) {
                this.setCustomValidity('');
            }
            else {
                this.setCustomValidity(wccf.error_messages.option_key_must_be_unique);
            }

            // Add value to values array
            values.push(value);
        });
    }

    /**
     * Display hints in settings
     */
    jQuery('.wccf_settings_container .wccf_setting').each(function() {

        // Get hint
        var hint = jQuery(this).prop('title');

        // Check if hint is set
        if (hint) {

            // Append hint element
            jQuery(this).parent().append('<div class="wccf_settings_hint">' + hint + '</div>');
        }
    });

    /**
     * Toggle checkout field single fee label field in plugin settings
     */
    jQuery('input#wccf_display_as_single_fee').change(function() {
        toggle_checkout_single_fee_label_field();
    });
    toggle_checkout_single_fee_label_field();

    function toggle_checkout_single_fee_label_field()
    {
        jQuery('input#wccf_display_as_single_fee').each(function() {
            if (jQuery(this).is(':checked')) {
                jQuery('input#wccf_single_fee_label').closest('tr').show();
            }
            else {
                jQuery('input#wccf_single_fee_label').closest('tr').hide();
            }
        });
    }

    /**
     * Toggle max combined file size
     */
    jQuery('input[name="wccf_settings[wccf_multiple_files]"]').change(function() {

        var display = jQuery(this).is(':checked') ? 'table-row' : 'none';
        jQuery('input[name="wccf_settings[wccf_max_combined_file_size_per_field]"]').closest('tr').css('display', display);
    }).change();

    /**
     * Warn users when deleting fields
     */
    jQuery('#posts-filter #doaction, #posts-filter #doaction2').click(function(e) {

        // Get bulk action
        var action = jQuery('select[name="action"]');
        action = action.length > 0 ? action.val() : null;

        // Trash
        if (action === 'trash') {
            trash_confirmation(e, false);
        }
        // Delete
        else if (action === 'delete') {
            trash_confirmation(e, true);
        }
    });
    jQuery('.row-actions .trash .submitdelete, .wccf_post_delete .submitdelete').click(function(e) {
        var delete_permanently = jQuery(this).hasClass('wccf_delete_permanently');
        trash_confirmation(e, delete_permanently);
    });
    jQuery('.row-actions .delete .submitdelete, #posts-filter input#delete_all').click(function(e) {
        trash_confirmation(e, true);
    });

    function trash_confirmation(event, delete_permanently)
    {

        // Check if field is page element
        if (jQuery(event.target).closest('tr').find('.column-wccf_key').length) {
            var is_page_element = !jQuery(event.target).closest('tr').find('.wccf_row_key').length;
        }
        else if (jQuery.inArray(jQuery(event.target).closest('form#post').find('#wccf_post_config_field_type').val(), ['heading', 'separator']) !== -1) {
            var is_page_element = true;
        }
        else if (jQuery(event.target).prop('id') === 'doaction' || jQuery(event.target).prop('id') === 'doaction2') {

            is_page_element = true;

            jQuery('.check-column input[type="checkbox"]:checked').each(function() {
                if (jQuery(this).closest('tr').find('.wccf_row_key').length) {
                    is_page_element = false;
                    return false;
                }
            });
        }
        else {
            var is_page_element = false;
        }

        // Ask to confirm action
        if (!is_page_element && typeof wccf !== 'undefined') {

            var message = delete_permanently ? wccf.confirmation.deleting_field : wccf.confirmation.trashing_field;

            if (!confirm(message)) {
                event.preventDefault();
            }
        }
    }

    /**
     * Warn users when archiving fields
     */
    jQuery('.wccf_post_actions button[type="submit"]').click(function(e) {

        // Get action
        var action = jQuery('.wccf_post_action_select select').val();

        // Check if action is archive
        if (action === 'archive') {
            archive_confirmation(e);
        }
    });
    jQuery('.row-actions .archive a').click(function(e) {
        archive_confirmation(e);
    });
    function archive_confirmation(event)
    {
        // Ask to confirm action
        if (typeof wccf !== 'undefined' && !confirm(wccf.confirmation.archiving_field)) {
            event.preventDefault();
        }
    }

    /**
     * Disable submitting archived field edit page
     */
    jQuery('form#post .wccf_post_action_select select option[value="object_archived"]').each(function() {

        // Disable actions meta box elements
        jQuery(this).closest('select').prop('disabled', 'disabled');
        jQuery(this).closest('.wccf_post_actions').find('button[type="submit"]').prop('disabled', 'disabled');

        // Disable form submit completely
        jQuery(this).closest('form#post').submit(function(e) {
            if (typeof wccf !== 'undefined') {
                alert(wccf.error_messages.editing_archived_field);
            }

            e.preventDefault();
        });
    });

    /**
     * Select2 for multiselect fields in settings
     */
    jQuery('select[multiple].wccf_field_select').each(function() {

        var config = {
            placeholder: jQuery(this).prop('id') === 'wccf_conditions_custom_taxonomies' ? wccf.labels.select2_placeholder_custom_product_taxonomies : wccf.labels.select2_placeholder,
            language: {
                noResults: function (params) {
                    return wccf.labels.select2_no_results;
                }
            },
        };

        // Extra settings for file extension lists
        if (jQuery(this).prop('id') === 'wccf_file_extension_whitelist' || jQuery(this).prop('id') === 'wccf_file_extension_blacklist') {
            config.tags         = true;
            config.allowClear   = true;
        }

        // Initialize Select2
        if (typeof RP_Select2 !== 'undefined') {
            RP_Select2.call(jQuery(this), config);
        }
        // Initialize Select2
        else if (typeof element.selectWoo !== 'undefined') {
            jQuery(this).selectWoo(config);
        }
    });

    /**
     * Focus on title field when creating new field
     */
    if (window.location.pathname.indexOf('post-new.php') !== -1) {
        jQuery('#wccf_post_config_label').focus();
    }

    /**
     * HELPER
     * Enable all fields contained by element
     */
    function enable_fields(element)
    {
        element.find('input, select').each(function() {
            enable_field(jQuery(this));
        });
    }

    /**
     * HELPER
     * Disable all fields contained by element
     */
    function disable_fields(element)
    {
        element.find('input, select').each(function() {
            disable_field(jQuery(this));
        });
    }

    /**
     * HELPER
     * Enable field
     */
    function enable_field(field)
    {
        field.prop('disabled', false);
    }

    /**
     * HELPER
     * Disable field
     */
    function disable_field(field)
    {
        field.prop('disabled', 'disabled');
    }

    /**
     * HELPER
     * Clear values of multiple fields contained by element
     */
    function clear_field_values(element)
    {
        element.find('input, select').each(function() {
            clear_field_value(jQuery(this));
        });
    }

    /**
     * HELPER
     * Clear field value
     */
    function clear_field_value(field)
    {
        if (field.is('select')) {
            field.prop('selectedIndex', 0);
        }
        else if (field.is(':radio, :checkbox')) {
            field.removeAttr('checked');
        }
        else {
            field.val('');
        }
    }

    /**
     * HELPER
     * Check if HTML element is multiselect field
     */
    function is_multiselect(element)
    {
        return (element.is('select') && typeof element.attr('multiple') !== 'undefined' && element.attr('multiple') !== false);
    }

    /**
     * HELPER
     * Append template with values to selected element's content
     */
    function append(selector, template, values)
    {
        var html = get_template(template, values);

        if (typeof selector === 'object') {
            selector.append(html);
        }
        else {
            jQuery(selector).append(html);
        }
    }

    /**
     * HELPER
     * Prepend template with values to selected element's content
     */
    function prepend(selector, template, values)
    {
        var html = get_template(template, values);

        if (typeof selector === 'object') {
            selector.prepend(html);
        }
        else {
            jQuery(selector).prepend(html);
        }
    }

    /**
     * HELPER
     * Get template's html code
     */
    function get_template(template, values)
    {
        return populate_template(jQuery('#wccf_template_' + template).html(), values);
    }

    /**
     * HELPER
     * Populate template with values
     */
    function populate_template(template, values)
    {
        for (var key in values) {
            if (values.hasOwnProperty(key)) {
                template = replace_macro(template, key, values[key]);
            }
        }

        return template;
    }

    /**
     * HELPER
     * Replace all instances of macro in string
     */
    function replace_macro(string, macro, value)
    {
        var macro = '{' + macro + '}';
        var regex = new RegExp(macro, 'g');
        return string.replace(regex, value);
    }

    /**
     * We are done by now, remove preloader
     */
    jQuery('#wccf_preloader').remove();

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
            var regex = return_raw_data ? /{"result.*"}]}/ : /{"result.*"}/;
            var valid_response = response.match(regex);

            // Check if we were able to fix it
            if (valid_response !== null) {
                response = valid_response[0];
            }
        }

        // Second attempt to parse response data
        return return_raw_data ? response : jQuery.parseJSON(response);
    }





});
