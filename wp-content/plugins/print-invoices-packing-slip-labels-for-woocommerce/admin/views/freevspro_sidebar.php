<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wf_admin_img_path = WF_PKLIST_PLUGIN_URL . 'admin/images';
include WF_PKLIST_PLUGIN_PATH.'/admin/views/wt_pro_addons_list.php';
$not_activated_pro_addons = Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons('wt_qr_addon');
$pro_addons_list_details_free_pro = $pro_addons_list_details_addons_page;
?>
<style type="text/css">
    .wt_pro_addon_tile{padding: 0.7em 2em 1em;background: #FFFFFF;box-shadow: 0px 12px 64px #dcecff;border-radius: 7px;position: relative;min-width: 0;word-wrap: break-word;float: left;box-sizing: border-box;margin: 10px 10px 20px 10px;height: auto;}
    .wt_pro_addon_widget .wt_pro_addon_widget_wrapper{display: flex;}
    .wt_pro_addon_widget_column_1{padding-top: 18px;}
    .wt_pro_addon_widget_column_1 img{width: 60px;height: 60px;}
    .wt_pro_addon_widget_column_2{font-size: 15px;text-align: top;padding-left: 10px;width: 100%;height: 100px;}
    .wt_pro_addon_premium_btn{text-align: center;padding: 10px 1px 5px 1px;height: 25px;width: 100%;background: linear-gradient(90.67deg, #2608DF -34.86%, #3284FF 115.74%);box-shadow: 0px 4px 13px rgb(46 80 242 / 39%);border-radius: 5px;display: inline-block;font-style: normal;font-size: 14px;line-height: 18px;color: #FFFFFF;text-decoration: none;}
    .wt_pro_addon_premium_btn:hover{box-shadow: 0px 3px 13px rgb(46 80 242 / 50%);text-decoration: none;transform: translateY(2px);transition: all .2s ease;color: #FFFFFF;}
    .wt_addon_gurantee{padding: 2px 11px;background: #F6F5FF9C;margin: 20px 0;border-radius: 5px;}
    .wt_addon_gurantee li{font-style: normal;font-weight: bold;font-size: 13px;line-height: 22px;letter-spacing: -0.01em;list-style: none;position: relative;color: #091E80;padding-left: 28px;}
    .wt_addon_gurantee li.wt_pro_addon_money_back:before,.wt_addon_gurantee li.wt_pro_addon_support:before{content: '';position: absolute;left: 0;height: 24px;width: 16px;background-position: center;background-repeat: no-repeat;background-size: contain;}
    .wt_addon_gurantee li.wt_pro_addon_money_back:before{background-image: url(<?php echo esc_url($wf_admin_img_path.'/money-back.svg'); ?>);}
    .wt_addon_gurantee li.wt_pro_addon_support:before{background-image: url(<?php echo esc_url($wf_admin_img_path.'/support.svg'); ?>);}
     .freevspro_side_banners:nth-child(n + 2){display: none;}
</style>
<div class="wt_pklist_gopro_block">
    <?php
        foreach($not_activated_pro_addons as $c_pro){
            if(isset($pro_addons_list_details_free_pro[$c_pro]) && is_array($pro_addons_list_details_free_pro[$c_pro])){
                $this_addon = $pro_addons_list_details_free_pro[$c_pro];
                ?>
    <div class="wt_pro_addon_tile freevspro_side_banners" id="<?php echo esc_attr($c_pro.'_side_banner'); ?>">
        <div class="wt_pro_addon_widget">
            <div class="wt_pro_addon_widget_wrapper">
                <div class="wt_pro_addon_widget_column_1">
                    <img src="<?php echo esc_url($this_addon['logo']); ?>">
                </div>
                <div class="wt_pro_addon_widget_column_2">
                    <h4 class="wt_pro_addon_title"><?php echo esc_html($this_addon['title']); ?></h4>
                </div>
            </div>
            <div class="wt_addon_btn">
                <div class="wt_addon_premium_btn">
                    <a href="<?php echo esc_url($this_addon['page_link_fp']); ?>" class="wt_pro_addon_premium_btn" target="_blank">
                        <img src="<?php echo esc_url($wf_admin_img_path.'/other_solutions/promote_crown.png'); ?>" style="width: 14px;height: 13px;margin-right: 7px;"><?php 
                        echo __("Upgrade To Premium","print-invoices-packing-slip-labels-for-woocommerce"); ?>
                    </a>
                </div>
            </div>
            <div class="wt_addon_gurantee">
                <ul>
                    <li class="wt_pro_addon_money_back"><?php _e("30 Day Money Back Guarantee","print-invoices-packing-slip-labels-for-woocommerce"); ?></li>
                    <li class="wt_pro_addon_support"><?php _e("Fast and Superior Support","print-invoices-packing-slip-labels-for-woocommerce"); ?></li>
                </ul>
            </div>
        </div>
    </div>
                <?php
            }
        }
    ?>
</div>