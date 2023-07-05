<?php

/**
 * View for Field Templates
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="wccf_templates" style="display: none;">

    <!-- NO OPTIONS -->
    <div id="wccf_template_no_options">
        <div class="wccf_post_no_options"><?php _e('No options configured.', 'rp_wccf'); ?></div>
    </div>

    <!-- NO CONDITIONS -->
    <div id="wccf_template_no_conditions">
        <div class="wccf_post_no_conditions"><?php _e('No conditions configured.', 'rp_wccf'); ?></div>
    </div>

    <!-- OPTION WRAPPER -->
    <div id="wccf_template_option_wrapper">
        <div class="wccf_post_option_header">
            <div class="wccf_post_option_sort wccf_post_option_sort_header"></div>
            <div class="wccf_post_option_content wccf_post_option_content_header">
                <div class="wccf_post_option_header_item wccf_post_option_resize"><label><?php _e('Label', 'rp_wccf'); ?></label></div>
                <div class="wccf_post_option_header_item wccf_post_option_resize"><label><?php _e('Unique Key', 'rp_wccf'); ?></label></div>
                <?php if ($this->supports_pricing()): ?>
                    <div class="wccf_post_option_header_item wccf_post_option_price" style="display: none;"><label><?php _e('Pricing', 'rp_wccf'); ?></label></div>
                <?php endif; ?>
                <div class="wccf_post_option_header_item wccf_post_option_header_small_select"><label><?php _e('Selected', 'rp_wccf'); ?></label></div>
            </div>
            <div class="wccf_post_option_remove wccf_post_option_remove_header"></div>
            <div style="clear: both;"></div>
        </div>
        <div class="wccf_post_option_wrapper"></div>
    </div>

    <!-- CONDITIONS WRAPPER -->
    <div id="wccf_template_condition_wrapper">
        <div class="wccf_post_condition_wrapper"></div>
    </div>

    <!-- OPTION -->
    <div id="wccf_template_option">
        <div class="wccf_post_option">
            <div class="wccf_post_option_sort">
                <div class="wccf_post_option_sort_handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
            </div>

            <div class="wccf_post_option_content">

                <div class="wccf_post_option_setting wccf_post_option_setting_single wccf_post_option_resize">
                    <?php RightPress_Forms::text(array(
                        'id'        => 'wccf_options_label_{i}',
                        'name'      => 'wccf_settings[options][{i}][label]',
                        'required'  => 'required',
                    )); ?>
                </div>

                <div class="wccf_post_option_setting wccf_post_option_setting_single wccf_post_option_resize">
                    <?php RightPress_Forms::text(array(
                        'id'        => 'wccf_options_key_{i}',
                        'name'      => 'wccf_settings[options][{i}][key]',
                        'class'     => 'wccf_post_config_options_key',
                        'pattern'   => '[a-zA-Z0-9_]*',
                        'maxlength' => 100,
                        'required'  => 'required',
                        'style'     => 'text-transform: lowercase;',
                    )); ?>
                </div>

                <?php if ($this->supports_pricing()): ?>
                    <div class="wccf_post_option_setting wccf_post_option_setting_single wccf_post_option_price" style="display: none;">
                        <div class="wccf_post_config_pricing_method_wrapper">
                            <?php WCCF_FB::grouped_select(array(
                                'id'        => 'wccf_options_pricing_method_{i}',
                                'name'      => 'wccf_settings[options][{i}][pricing_method]',
                                'class'     => 'wccf_post_config_pricing_method wccf_grouped_select2',
                                'options'   => WCCF_Pricing::get_pricing_methods_list($context, true),
                                'disabled'  => 'disabled',
                            ), null, true); ?>
                        </div>
                        <div class="wccf_post_config_pricing_value_wrapper">
                            <?php RightPress_Forms::text(array(
                                'id'            => 'wccf_options_pricing_value_{i}',
                                'name'          => 'wccf_settings[options][{i}][pricing_value]',
                                'class'         => 'wccf_post_config_pricing_value',
                                'placeholder'   => '0.00',
                                'pattern'       => '[0-9.]*',
                                'disabled'      => 'disabled',
                            )); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="wccf_post_option_setting wccf_post_option_setting_small_select">
                    <?php RightPress_Forms::select(array(
                        'id'        => 'wccf_options_selected_{i}',
                        'name'      => 'wccf_settings[options][{i}][selected]',
                        'class'     => 'wccf_post_config_options_selected',
                        'options'   => array(
                            '0' => __('No', 'rp_wccf'),
                            '1' => __('Yes', 'rp_wccf'),
                        ),
                    )); ?>
                </div>
                <div style="clear: both;"></div>
            </div>

            <div class="wccf_post_option_remove">
                <div class="wccf_post_option_remove_handle">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- CONDITION -->
    <div id="wccf_template_condition">
        <div class="wccf_post_condition">
            <div class="wccf_post_condition_sort">
                <div class="wccf_post_condition_sort_handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
            </div>

            <div class="wccf_post_condition_content">

                <div class="wccf_post_condition_setting wccf_post_condition_setting_single">
                    <?php RightPress_Forms::grouped_select(array(
                        'id'                    => 'wccf_conditions_type_{i}',
                        'name'                  => 'wccf_settings[conditions][{i}][type]',
                        'class'                 => 'wccf_condition_type wccf_grouped_select2',
                        'options'               => WCCF_Controller_Conditions::get_items_for_display($context),
                        'data-wccf-validation'  => 'required',
                    ), true); ?>
                </div>

                <div class="wccf_condition_setting_fields_wrapper"></div>

                <?php RightPress_Forms::hidden(array(
                    'id'    => 'wccf_conditions_uid_{i}',
                    'name'  => 'wccf_settings[conditions][{i}][uid]',
                ), false); ?>

                <div style="clear: both;"></div>
            </div>

            <div class="wccf_post_condition_remove">
                <div class="wccf_post_condition_remove_handle">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- CONDITION FIELDS -->
    <?php foreach(WCCF_Controller_Conditions::get_items_for_display($context) as $group_key => $group): ?>
        <?php foreach($group['options'] as $option_key => $option): ?>

            <?php $combined_key = $group_key . '__' . $option_key; ?>

            <div id="wccf_template_condition_setting_fields_<?php echo $combined_key ?>">
                <div class="wccf_condition_setting_fields wccf_condition_setting_fields_<?php echo $combined_key ?>">

                    <?php WCCF_Controller_Conditions::display_fields($context, $combined_key, 'before'); ?>

                    <div class="wccf_condition_setting_fields_<?php echo (in_array($combined_key, array('customer__logged_in'), true) ? 'triple' : 'single'); ?>">
                        <?php RightPress_Forms::select(array(
                            'id'                    => 'wccf_conditions_method_option_{i}',
                            'name'                  => 'wccf_settings[conditions][{i}][method_option]',
                            'class'                 => 'wccf_condition_method wccf_select2',
                            'options'               => WCCF_Controller_Conditions::get_condition_method_options_for_display($combined_key),
                            'data-wccf-validation'  => 'required',
                        )); ?>
                    </div>

                    <?php WCCF_Controller_Conditions::display_fields($context, $combined_key, 'after'); ?>

                    <div style="clear: both;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- DISABLED CONDITION -->
    <div id="wccf_template_condition_disabled">
        <div class="wccf_post_condition_disabled">
            <div class="wccf_post_condition_disabled_text">
                <?php _e('Condition type was disabled. Enable it or delete this placeholder after reviewing your settings.', 'rp_wccf'); ?>
            </div>
        </div>
    </div>

    <!-- DISABLED CUSTOM TAXONOMY CONDITION -->
    <div id="wccf_template_condition_disabled_taxonomy">
        <div class="wccf_post_condition_disabled_taxonomy">
            <div class="wccf_post_condition_disabled_taxonomy_text">
                <?php _e('Custom taxonomy condition was disabled. Enable it or delete this placeholder after reviewing your settings.', 'rp_wccf'); ?>
            </div>
        </div>
    </div>

    <!-- NON EXISTENT CONDITION -->
    <div id="wccf_template_condition_non_existent">
        <div class="wccf_post_condition_non_existent">
            <div class="wccf_post_condition_non_existent_text">
                <?php _e('Condition type no longer exists. Delete this placeholder after reviewing your settings.', 'rp_wccf'); ?>
            </div>
        </div>
    </div>

    <!-- NON EXISTENT TAXONOMY CONDITION -->
    <div id="wccf_template_condition_non_existent_taxonomy">
        <div class="wccf_post_condition_non_existent_taxonomy">
            <div class="wccf_post_condition_non_existent_taxonomy_text">
                <?php _e('Custom taxonomy no longer exists. Delete this placeholder after reviewing your settings.', 'rp_wccf'); ?>
            </div>
        </div>
    </div>

    <!-- NON EXISTENT CONDITION OTHER CUSTOM FIELD -->
    <div id="wccf_template_condition_non_existent_other_custom_field">
        <div class="wccf_post_condition_non_existent_other_custom_field">
            <div class="wccf_post_condition_non_existent_other_custom_field_text">
                <?php _e('Other custom field that was used in this condition no longer exists. Delete this placeholder after reviewing your settings.', 'rp_wccf'); ?>
            </div>
        </div>
    </div>

</div>
