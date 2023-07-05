<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images';
?>
<style type="text/css">
    .wt_pro_addon_row{float: left;width: 100%;margin: 10px;}
    .wt_pro_addon_tile{padding: 0.7em 2em 1em;background: #FFFFFF;box-shadow: 0px 12px 64px #DCECFF;border-radius: 7px;position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;width: 31%;float: left;box-sizing: border-box;margin: 10px 10px 20px 10px;min-height: 460px;}
    .wt_pro_addon_widget{margin-bottom:10%; }
    .wt_pro_addon_widget .wt_pro_addon_widget_wrapper{display: flex;}
    .wt_pro_addon_widget_column_1{padding-top: 18px;}
    .wt_pro_addon_widget_column_1 img{width: 60px;height: 60px;}
    .wt_pro_addon_widget_column_2{font-size: 15px;text-align: top;padding-left: 10px;width: 100%;height: 100px;}
    .wt_pro_addon_premium_btn{text-align: center;padding: 10px 1px 5px 1px;height: 25px;width: 100%;background: linear-gradient(90.67deg, #2608DF -34.86%, #3284FF 115.74%);box-shadow: 0px 4px 13px rgb(46 80 242 / 39%);border-radius: 5px;display: inline-block;font-style: normal;font-size: 14px;line-height: 18px;color: #FFFFFF;text-decoration: none;}
    .wt_pro_addon_premium_btn:hover{box-shadow: 0px 3px 13px rgb(46 80 242 / 50%);text-decoration: none;transform: translateY(2px);transition: all .2s ease;color: #FFFFFF;}
    .wt_pro_addon_features_list ul li:nth-child(n + 4){display: none;}
    .wt_pro_addon_features_list li{font-style: normal;font-weight: 500;font-size: 13px;line-height: 17px;color: #001A69;list-style: none;position: relative;padding-left: 49px;margin: 0 0 15px 0;display: flex;align-items: center;}
    .wt_pro_addon_features_list li:before{content: '';position: absolute;height: 15px;width: 15px;background-image: url(<?php echo esc_url($wf_admin_img_path.'/tick.svg'); ?>);background-size: contain;background-repeat: no-repeat;background-position: center;left: 15px;}
    .wt_addon_gurantee{padding: 5px 5px 5px 49px;background: #F6F5FF9C;margin: 20px 0;border-radius: 5px;}
    .wt_addon_gurantee li{font-style: normal;font-weight: bold;font-size: 14px;line-height: 24px;letter-spacing: -0.01em;list-style: none;position: relative;color: #091E80;padding-left: 28px;}
    .wt_addon_gurantee li.wt_pro_addon_money_back:before,.wt_addon_gurantee li.wt_pro_addon_support:before{content: '';position: absolute;left: 0;height: 24px;width: 16px;background-position: center;background-repeat: no-repeat;background-size: contain;}
    .wt_addon_gurantee li.wt_pro_addon_money_back:before{background-image: url(<?php echo esc_url($wf_admin_img_path.'/money-back.svg'); ?>);}
    .wt_addon_gurantee li.wt_pro_addon_support:before{background-image: url(<?php echo esc_url($wf_admin_img_path.'/support.svg'); ?>);}
    .wt_pro_show_more_less{text-align: center;position: absolute;bottom: 3%;left: 33%;}
    .wt_pro_show_more_less a span{background: #E8F3FF;color: #3171FB;border-radius: 50%;font-size: 18px;padding: 2px;}
    .wt_pro_show_more_less a p{margin: 2px 0 0 5px;display: inline-block;color: #3171FB;}
    .wt_pro_addon_show_less{display: none;}
</style>
<div class="wt_wrap">
    <div class="wt_heading_section">
        <h2 class="wp-heading-inline">
        <?php _e('Premium and free Add-ons','print-invoices-packing-slip-labels-for-woocommerce');?>
        </h2>
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/wt_pro_addons_list.php';
        ?>
    </div>
    <div class="wt_pro_addon_container" style="float: left;width: 100%;">
        <?php
            $not_activated_pro_addons = Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons();
            $not_activated_pro_addons = array_chunk($not_activated_pro_addons,3,true);
            $pro_addons_list_details = $pro_addons_list_details_addons_page;
        ?>
        <?php 
            foreach($not_activated_pro_addons as $current_pro_addon_arr){
                if(is_array($current_pro_addon_arr) && !empty($current_pro_addon_arr)){
                    ?>
                    <div class="wt_pro_addon_row">
                        <?php
                            foreach($current_pro_addon_arr as $pro_key){
                                if(isset($pro_addons_list_details[$pro_key]) && is_array($pro_addons_list_details[$pro_key]))
                                ?>
                                    <div class="wt_pro_addon_tile">
                                        <div class="wt_pro_addon_widget">
                                            <div class="wt_pro_addon_widget_wrapper">
                                                <div class="wt_pro_addon_widget_column_1">
                                                    <img src="<?php echo esc_url($pro_addons_list_details[$pro_key]['logo']); ?>">
                                                </div>
                                                <div class="wt_pro_addon_widget_column_2">
                                                    <h4 class="wt_pro_addon_title"><?php echo esc_html($pro_addons_list_details[$pro_key]['title']); ?></h4>
                                                </div>
                                            </div>
                                            <div class="wt_addon_btn">
                                                <div class="wt_addon_premium_btn">
                                                    <a href="<?php echo esc_url($pro_addons_list_details[$pro_key]['page_link']); ?>" class="wt_pro_addon_premium_btn" target="_blank">
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
                                            <div class="wt_pro_addon_features_list">
                                                <ul>
                                                    <?php
                                                        foreach($pro_addons_list_details[$pro_key]['features_list'] as $p_feature){
                                                            ?>
                                                            <li><?php echo esc_html_e($p_feature); ?></li>
                                                            <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="wt_pro_show_more_less">
                                            <a class="wt_pro_addon_show_more"><span class="dashicons dashicons-arrow-down-alt2"></span> <p><? echo __("Show More","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                                            <a class="wt_pro_addon_show_less"><span class="dashicons dashicons-arrow-up-alt2"></span> <p><? echo __("Show Less","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                                        </div>
                                    </div>
                                <?php
                            }
                        ?>
                    </div>
                    <?php
                }
            }
        ?>
    </div>
</div>