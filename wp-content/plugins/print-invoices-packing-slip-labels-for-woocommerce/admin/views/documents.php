<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wt_pklist_common_modules=get_option('wt_pklist_common_modules');
if($wt_pklist_common_modules===false)
{
    $wt_pklist_common_modules=array();
}
$wt_pklist_common_modules_main=array_chunk($wt_pklist_common_modules,3,true);
$wt_pklist_common_modules_main = $wt_pklist_common_modules;
$document_module_labels=Wf_Woocommerce_Packing_List_Public::get_document_module_labels();
?>
<style type="text/css">
    .wfte_doc_col-3{width: 30%;display: block;border: 1px solid #ededed;float: left;margin: 10px 10px 15px 10px;justify-content: center;}
    .wfte_doc_outter_div{display: flex;background: #fafafa;align-items: center;padding: 0px 15px;justify-content: space-evenly;min-height: 6em;}
    .wfte_doc_title_image{padding: 10px 0;width: 80%;}
    .wfte_doc_title_image > a{display:flex;align-items: center;text-decoration: none;}
    .wfte_doc_title_image > a > img {width: 15%;}
    .wfte_doc_title_image > a > h3 {margin: 5px 0 10px 10px;color: #4F575F;font-size: 15px;}
    .wfte_doc_setting_toggle{margin: 10px 0;width: 15%;}
    .wf_pklist_dashboard_box_footer_up{align-items: center;display: flex;width: 100%;}
</style>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
    <form method="post">
        <?php
            // Set nonce:
            if (function_exists('wp_nonce_field'))
            {
                wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
            }
        ?>
        <div class="wfte_doc_row" style="float:left;width: 100%;display: table;align-items: center;">
        <?php
            foreach($wt_pklist_common_modules_main as $k => $v){

        ?>
            <?php
                //only document modules
                if(isset($document_module_labels[$k]))
                {
                    $module_id=Wf_Woocommerce_Packing_List::get_module_id($k);
                    $settings_url=admin_url('admin.php?page='.$module_id);
                    // echo $module_id;
                    ?>
                    <div class="wfte_doc_col-3">
                        <div class="wfte_doc_outter_div">
                            <div class="wfte_doc_title_image">
                                <a class="doc_module_link" href="<?php echo esc_url($settings_url); ?>" data-href="<?php echo esc_url($settings_url); ?>">
                                    <img src="<?php echo WF_PKLIST_PLUGIN_URL;?>assets/images/<?php echo $k;?>_logo.png" style="">
                                    <h3><?php _e(Wf_Woocommerce_Packing_List_Public::$modules_label[$k], 'wf-woocommerce-packing-list'); ?></h3>
                                </a>
                            </div>
                            <div class="wfte_doc_setting_toggle">
                                <div  class="wf_pklist_dashboard_box_footer_up">
                                    <div class="wf_pklist_dashboard_checkbox">
                                        <input type="checkbox" value="1" name="wt_pklist_common_modules[<?php echo $k;?>]" <?php echo ((1 === $v || "1" === $v )? 'checked' : '');?> class="wf_slide_switch wt_document_module_enable" id="wt_pklist_<?php echo $k; ?>">   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }else{
                    do_action('wt_pklist_add_module_tiles',$k,$v);
                }
            ?>
        <?php
            }
        ?>
        </div>
    </form>
</div>