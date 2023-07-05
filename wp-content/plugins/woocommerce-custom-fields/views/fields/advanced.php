<?php

/**
 * View for field settings panel
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<style type="text/css">
    #submitdiv {
        display: none;
    }
</style>

<div class="wccf_post wccf_post_advanced">

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::text(array(
                'id'            => 'wccf_post_config_description',
                'name'          => 'wccf_settings[description]',
                'value'         => ($object ? $object->get_description() : ''),
                'label'         => __('Description', 'rp_wccf'),
                'placeholder'   => __('Displayed to customers', 'rp_wccf'),
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::text(array(
                'id'            => 'wccf_post_config_private_note',
                'name'          => 'wccf_settings[private_note]',
                'value'         => ($object ? $object->get_private_note() : ''),
                'label'         => __('Private Note', 'rp_wccf'),
                'placeholder'   => __('For your own reference', 'rp_wccf'),
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::text(array(
                'id'    => 'wccf_post_config_placeholder',
                'name'  => 'wccf_settings[placeholder]',
                'value' => ($object ? $object->get_placeholder() : ''),
                'label' => __('Placeholder', 'rp_wccf'),
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::text(array(
                'id'    => 'wccf_post_config_default_value',
                'name'  => 'wccf_settings[default_value]',
                'value' => ($object ? $object->get_default_value() : ''),
                'label' => __('Default Value', 'rp_wccf'),
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::number(array(
                'id'            => 'wccf_post_config_min_value',
                'name'          => 'wccf_settings[min_value]',
                'placeholder'   => __('No limit', 'rp_wccf'),
                'value'         => ($object ? $object->get_min_value() : ''),
                'label'         => __('Min Value', 'rp_wccf'),
                'pattern'       => '[0-9]*',
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::number(array(
                'id'            => 'wccf_post_config_max_value',
                'name'          => 'wccf_settings[max_value]',
                'placeholder'   => __('No limit', 'rp_wccf'),
                'value'         => ($object ? $object->get_max_value() : ''),
                'label'         => __('Max Value', 'rp_wccf'),
                'pattern'       => '[0-9]*',
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::number(array(
                'id'            => 'wccf_post_config_min_selected',
                'name'          => 'wccf_settings[min_selected]',
                'placeholder'   => __('No limit', 'rp_wccf'),
                'value'         => ($object ? $object->get_min_selected() : ''),
                'label'         => __('Min Selected', 'rp_wccf'),
                'pattern'       => '[0-9]*',
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::number(array(
                'id'            => 'wccf_post_config_max_selected',
                'name'          => 'wccf_settings[max_selected]',
                'placeholder'   => __('No limit', 'rp_wccf'),
                'value'         => ($object ? $object->get_max_selected() : ''),
                'label'         => __('Max Selected', 'rp_wccf'),
                'pattern'       => '[0-9]*',
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::number(array(
                'id'            => 'wccf_post_config_character_limit',
                'name'          => 'wccf_settings[character_limit]',
                'placeholder'   => __('No limit', 'rp_wccf'),
                'value'         => ($object ? $object->get_character_limit() : ''),
                'label'         => __('Character Limit', 'rp_wccf'),
                'pattern'       => '[0-9]*',
            )); ?>
        </div>

        <?php if ($this->get_context() === 'checkout_field' && wc_tax_enabled()): ?>
            <div class="wccf_config_field wccf_config_field_half">
                <?php WCCF_FB::select(array(
                    'id'        => 'wccf_post_config_tax_class',
                    'name'      => 'wccf_settings[tax_class]',
                    'class'     => 'wccf_select2',
                    'value'     => ($object ? $object->get_tax_class() : ''),
                    'options'   => RightPress_Help::get_wc_tax_class_list(array('wccf_not_taxable' => __('Not Taxable', 'rp_wccf'))),
                    'label'     => __('Tax Class', 'rp_wccf'),
                )); ?>
            </div>
        <?php endif; ?>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::select(array(
                'id'        => 'wccf_post_config_heading_level',
                'name'      => 'wccf_settings[heading_level]',
                'class'     => 'wccf_select2',
                'value'     => (($object && $object->get_heading_level()) ? $object->get_heading_level() : 'h3'),
                'options'   => array(
                    'h1' => __('Heading 1', 'rp_wccf'),
                    'h2' => __('Heading 2', 'rp_wccf'),
                    'h3' => __('Heading 3', 'rp_wccf'),
                    'h4' => __('Heading 4', 'rp_wccf'),
                    'h5' => __('Heading 5', 'rp_wccf'),
                    'h6' => __('Heading 6', 'rp_wccf'),
                ),
                'label'     => __('Heading Level', 'rp_wccf'),
            )); ?>
        </div>

        <div class="wccf_config_field wccf_config_field_half">
            <?php WCCF_FB::text(array(
                'id'            => 'wccf_post_config_custom_css',
                'name'          => 'wccf_settings[custom_css]',
                'placeholder'   => 'e.g. width: 50%;',
                'value'         => ($object ? $object->get_custom_css() : ''),
                'label'         => __('Custom CSS Rules', 'rp_wccf'),
            )); ?>
        </div>

        <div style="clear: both;"></div>

</div>
