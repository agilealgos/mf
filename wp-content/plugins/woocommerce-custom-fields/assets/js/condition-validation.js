/**
 * Rules Interface Validation Scripts
 */

jQuery(document).ready(function() {

    /**
     * Input validation methods
     *
     * Returns error message as string if validation fails
     * Returns null if validation succeeds
     */
    var input_validation_methods = {

        // Required
        required: function(input) {
            return input.val() ? null : wccf.error_messages.required;
        },

        // Number - Min 0
        number_min_0: function(input) {
            return (!input.val() || +input.val() >= 0) ? null : wccf.error_messages.number_min_0;
        },

        // Number - Natural
        number_natural: function(input) {
            return (!input.val() || +input.val() > 0) ? null : wccf.error_messages.number_natural;
        },

        // Number - Min 1
        number_min_1: function(input) {
            return (!input.val() || +input.val() >= 1) ? null : wccf.error_messages.number_min_1;
        },

        // Number - Whole
        number_whole: function(input) {
            return (!input.val() || (+input.val() % 1 === 0)) ? null : wccf.error_messages.number_whole;
        }
    };

    /**
     * Form submit handler
     */
    jQuery('form:has(.wccf_field_conditions_meta_box)').submit(function(e) {

        var is_valid = true;

        var form = jQuery(this);

        // Get conditions
        var conditions = form.find('.wccf_post_conditions .wccf_post_condition');

        // Check if field has any conditions
        if (conditions.length > 0) {

            // Validate conditions
            conditions.each(function() {

                var condition = jQuery(this);

                // Validate condition
                if (!validate_condition(condition)) {

                    // Scroll to invalid panel
                    jQuery('html, body').animate({
                        scrollTop: condition.offset().top
                    }, 500).promise().then(function() {

                        // Get elements with errors
                        var elements_with_errors = condition.find(':data(wccf-validation-error)');

                        // Display errors
                        elements_with_errors.each(function() {
                            display_error(jQuery(this));
                        });

                        // Focus first input
                        elements_with_errors.first().each(function() {
                            if (jQuery(this).is('input') || jQuery(this).is('select')) {
                                jQuery(this).focus();
                            }
                        });
                    });

                    is_valid = false;
                    return false;
                }
            });

            // Do not submit form
            if (!is_valid) {
                e.preventDefault();
            }
        }
    });

    /**
     * Validate single condition
     */
    function validate_condition(condition)
    {

        var is_valid = true;

        // Iterate over non-disabled fields and validate them
        if (is_valid) {
            condition.find('input[data-wccf-validation]:enabled, select[data-wccf-validation]:enabled').each(function() {
                if (!validate_input(jQuery(this))) {
                    is_valid = false;
                    return false;
                }
            });
        }

        // Non existent and disabled condition handling
        if (is_valid) {

            // Iterate over different flag types
            jQuery.each(['_disabled', '_disabled_taxonomy', '_non_existent', '_non_existent_taxonomy', '_non_existent_other_custom_field'], function(flag_index, flag_type) {

                // Find element with current type
                condition.find('.wccf_post_condition' + flag_type).each(function() {

                    // Other custom field non-existent message
                    if (flag_type === '_non_existent_other_custom_field') {
                        var error_message =  wccf.error_messages.condition_non_existent_other_custom_field;
                    }
                    // Condition non-existent message
                    else if (flag_type === '_non_existent' || flag_type === '_non_existent_taxonomy') {
                        var error_message =  wccf.error_messages.condition_non_existent;
                    }
                    // Condition disabled message
                    else {
                        var error_message = wccf.error_messages.condition_disabled;
                    }

                    // Set error message
                    set_error(jQuery(this), error_message);

                    // Set invalid
                    is_valid = false;
                    return false;
                });
            });
        }

        return is_valid;
    }

    /**
     * Validate single input
     */
    function validate_input(input)
    {

        var is_valid = true;

        // Get input validation rules
        var validation_rules = input.data('wccf-validation').split(',');

        // Check each validation rule
        jQuery.each(validation_rules, function(index, validation_rule) {

            // Validate input
            var error_message = input_validation_methods[validation_rule](input);

            // Check if error message was returned which indicates validation failure
            if (error_message !== null) {
                set_error(input, error_message);
                is_valid = false;
                return false;
            }
        });

        return is_valid;
    }

    /**
     * Set element state to error
     */
    function set_error(element, message)
    {

        // Get message
        if (typeof message === 'undefined' || message === null) {
            message = wccf.error_messages.generic_error;
        }

        // Set error
        element.data('wccf-validation-error', message);
    }

    /**
     * Display error
     */
    function display_error(element)
    {

        // Get message
        var message = element.data('wccf-validation-error');

        // Set tooltip
        element.on('mouseleave', function (event) {
            event.stopImmediatePropagation();
        }).tooltip({
            content: message,
            items: ':data(wccf-validation-error)',
            tooltipClass: 'wccf_validation_error',
            classes: {
                'ui-tooltip': 'wccf_validation_error'
            },
            position: {
                my: 'center top',
                at: 'left+110 bottom+10'
            },
            create: function() {

                // Adjust position for multiselect fields
                if (element.is('select[multiple]')) {
                    element.tooltip('option', 'position', {
                        my: 'center top',
                        at: 'left+110 bottom+31'
                    });
                }

                // Remove tooltip on interaction
                var removal_selectors = element.add('html, body');
                removal_selectors.on('click keyup change', {element: element, removal_selectors: removal_selectors}, remove_tooltip);
            }
        }).tooltip('open');
    }

    /**
     * Remove tooltip
     */
    function remove_tooltip(event)
    {

        // Get args
        var element = event.data.element;
        var removal_selectors = event.data.removal_selectors;

        // Destroy tooltip
        if (element.data('ui-tooltip')) {
            element.tooltip('destroy');
        }

        // Remove error message
        element.removeData('wccf-validation-error');

        // Remove event listeners
        removal_selectors.off('click keyup change', remove_tooltip);
    }





});
