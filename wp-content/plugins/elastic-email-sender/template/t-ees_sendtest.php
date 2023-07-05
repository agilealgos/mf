<?php
defined('EE_ADMIN_5120420526') or die('No direct access allowed.');

wp_enqueue_style('eesender-bootstrap-grid');
wp_enqueue_style('eesender-css');
wp_enqueue_script('eesender-jquery');
wp_enqueue_script('eesender-send-test');

$plugin_path = plugins_url() . '/' . get_option('ees_plugin_dir_name');

if (isset($_GET['settings-updated'])):
    ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Settings saved.', 'elastic-email-sender') ?></strong></p>
    </div>
<?php endif; ?>

<div class="eewp-evmab-frvvr">

<div class="eewp-container">
    <div class="col-12 col-md-12 col-lg-7">
        <?php
        if (get_option('ee_options')["ee_enable"] === 'yes') {

            if (get_option('ees-connecting-status') === 'disconnected') {
                include 't-ees_connecterror.php';
            } else { ?>
            <div class="ee-header">
                <div class="ee-pagetitle">
                    <h1><?php _e('Send test', 'elastic-email-sender') ?></h1>
                </div>
            </div>

            <div class="ee-send-test-container">

                <p class="ee-p test-description"><?php _e('Sending this testing email will provide you with the necessary information about the ability to send emails from your account as well as email and contact status. The email provided by you will be added to your All Contacts list, then the testing message will be sent to this contact. Be aware that if you are charged by the number of emails sent, sending these testing messages will have an impact on your credits.', 'elstic-email-sender') ?></p>

                <div class="form-box">
                    <div class="form-group">
                        <label><?php _e('Email to', 'elastic-email-sender') ?></label>
                        <input type="email" name="to" id="to" placeholder="<?php _e('Email to', 'elastic-email-sender') ?>">
                    </div>
                    <span class="valid hide" id="invalid_email"></span>
                    <div class="form-group">
                        <label><?php _e('Test message', 'elastic-email-sender') ?></label>
                        <textarea name="message" id="message" rows="5" cols="40" placeholder="<?php _e('Test message', 'elastic-email-sender') ?>e"></textarea>
                    </div>
                    <span class="valid hide" id="invalid_message"></span>
                    <div class="form-group">
                        <input class="ee-button-test" id="sendTest" type="submit" value="<?php _e('Send test', 'elastic-email-sender') ?>">
                    </div>
                </div>

                <div class="">
                    
                    <p id="statusInfoLineOne" class="ee-info-box hide">
                        <span class="status-more-info-bold">
                            <?php _e('Sending: ', 'elastic-email-sender') ?>
                        </span>
                        <span id="sendStatus-ok" class="ee-info-box hide">
                            <img class="ee-info-box" src="<?= $plugin_path . '/src/img/icon-ok.svg' ?>">
                        </span>
                        <span id="sendStatus-warning" class="ee-info-box hide">
                            <img src="<?= $plugin_path . '/src/img/icon-warning.svg' ?>">
                        </span>
                        <span class="ee-info-box__text" id="sendStatus"></span>
                    </p>

                    <p id="statusInfoLineTwo" class="ee-info-box hide">
                        <span class="status-more-info-bold">
                            <?php _e('Status: ', 'elastic-email-sender') ?>
                        </span>
                        <span id="recipientsStatus-ok" class="hide">
                            <img src="<?= $plugin_path . '/src/img/icon-ok.svg' ?>">
                        </span>
                        <span id="recipientsStatus-warning" class="hide">
                            <img src="<?= $plugin_path . '/src/img/icon-warning.svg' ?>">
                        </span>
                        <span class="ee-info-box__text" id="recipientsStatus"></span>
                    </p>

                    <p id="statusInfoLineThree" class="ee-info-box hide">
                        <span class="status-more-info-bold">
                            <?php _e('Error: ', 'elastic-email-sender') ?>
                        </span>
                        <span id="recipientsContactLastError-ok" class="ee-info-box hide">
                            <img class="ee-info-box" src="<?= $plugin_path . '/src/img/icon-ok.svg' ?>">
                        </span>
                        <span id="recipientsContactLastError-warning" class="ee-info-box hide">
                            <img src="<?= $plugin_path . '/src/img/icon-warning.svg' ?>">
                        </span>
                        <span class="ee-info-box__text" id="recipientsContactLastError"></span>
                    </p>

                    <div id="loader" class="loader hide"></div>
                </div>
            </div>

        <?php }
        } else {
            include 't-ees_apidisabled.php';
        }?>

    </div>

    <?php
    include 't-ees_marketing.php';
    ?>

</div>

</div>
