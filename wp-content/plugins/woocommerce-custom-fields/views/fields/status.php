<?php

/**
 * View for field edit page Status block
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="wccf_post wccf_post_status">

    <div class="wccf_post_buttonset" style="display: none;">

        <label for="wccf_post_config_status_1"><?php _e('Enabled', 'rp_wccf'); ?></label>
        <input type="radio" value="enabled" id="wccf_post_config_status_1" name="wccf_settings[status]" <?php checked(($object && $object->is_enabled())); ?>>

        <label for="wccf_post_config_status_0"><?php _e('Disabled', 'rp_wccf'); ?></label>
        <input type="radio" value="disabled" id="wccf_post_config_status_0" name="wccf_settings[status]" <?php checked((!$object || $object->is_disabled())); ?>>

    </div>

</div>
