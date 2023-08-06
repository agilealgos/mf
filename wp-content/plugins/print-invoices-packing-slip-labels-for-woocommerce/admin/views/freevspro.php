<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$not_activated_pro_addons = Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons('wt_qr_addon');
$inv_text = __("Invoice","print-invoices-packing-slip-labels-for-woocommerce");
$psl_text = __("Packing slip","print-invoices-packing-slip-labels-for-woocommerce");
$cnt_text = __("Credit note","print-invoices-packing-slip-labels-for-woocommerce");
$shp_text = __("Shipping label","print-invoices-packing-slip-labels-for-woocommerce");
$dis_text = __("Dispatch label","print-invoices-packing-slip-labels-for-woocommerce");
$del_text = __("Delivery note","print-invoices-packing-slip-labels-for-woocommerce");
$pri_text = __("Proforma invoice","print-invoices-packing-slip-labels-for-woocommerce");
$pcl_text = __("Pick list","print-invoices-packing-slip-labels-for-woocommerce");
$adl_text = __("Address label","print-invoices-packing-slip-labels-for-woocommerce");
$my_acc_ol_page = __("My account - Order lists page","print-invoices-packing-slip-labels-for-woocommerce");
$my_acc_od_page = __("My account - Order details page","print-invoices-packing-slip-labels-for-woocommerce");
$my_acc_oe_page = __("Order email","print-invoices-packing-slip-labels-for-woocommerce");
$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images';

$no_icon='<span class="dashicons dashicons-dismiss" style="color:#ea1515;margin-top:3px"></span>&nbsp;';
$yes_icon='<span class="dashicons dashicons-yes-alt" style="color:#18c01d;margin-top:3px"></span>&nbsp;';

global $wp_version;
if(version_compare($wp_version, '5.2.0')<0)
{
    $yes_icon='<img src="'.$wf_admin_img_path.'/tick_icon_green.png" style="float:left;" />&nbsp;';
}

require_once(plugin_dir_path( __FILE__ ).'wt_ipc_free_pro.php');
require_once(plugin_dir_path( __FILE__ ).'wt_sdd_free_pro.php');
require_once(plugin_dir_path( __FILE__ ).'wt_pl_free_pro.php');
require_once(plugin_dir_path( __FILE__ ).'wt_pi_free_pro.php');

$pro_addons_list_details = array(
    'wt_ipc_addon' => $wt_ipc_addon_arr,
    'wt_sdd_addon' => $wt_sdd_addon_arr,
    'wt_pl_addon' => $wt_pl_addon_arr,
    'wt_pi_addon' => $wt_pi_addon_arr,
    'wt_al_addon' => $wt_al_addon_arr,
);
?>
<style type="text/css">
    table.fold-table-free-pro {width: 100%;border-collapse: collapse; }
    table.fold-table-free-pro  th {text-align: left;border-bottom: 1px solid #ccc;}
    table.fold-table-free-pro  th,table.fold-table-free-pro  td {padding: 0.4em 1.4em;}
    table.fold-table-free-pro > tbody > tr.view td,table.fold-table-free-pro > tbody > tr.view th {cursor: pointer;}
    table.fold-table-free-pro > tbody > tr.view td.filter_actions{text-align: right;width: 50%;}
    table.fold-table-free-pro > tbody > tr.view:hover {background: #f4f4f4;}
    table.fold-table-free-pro > tbody > tr.view.open {border-color: #fff;}
    table.fold-table-free-pro > tbody > tr.fold {display: none;}
    table.fold-table-free-pro > tbody > tr.fold.open {display: table-row;}
    table.fold-table-inner{border-collapse: collapse;}
    table.fold-table-inner td,table.fold-table-inner th {border-collapse: collapse; border: 1px solid #ccc;}
    table .fold-table-inner th:first-child, table .fold-table-inner td:first-child{background:#F8F9FA;}
    .pro_plugin_title span{background: #E8F3FF;color: #3171FB;border-radius: 50%;font-size: 18px;padding: 2px;}
    .pro_plugin_title b{color: #007FFF;font-size: 16px;}
    .free_pro_show_more,.free_pro_show_less{margin-right: 5px;}
</style>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
    <table class="wp-list-table fixed fold-table-free-pro" width="100%">
        <tbody>
            <?php
            $i = 0;
            foreach($not_activated_pro_addons as $current_addon){
                $pro_addon = $pro_addons_list_details[$current_addon];
            ?>
            <tr class="bordered view <?php if(0 === $i){echo "open";} ?>" data-banner-id="<?php echo esc_attr($current_addon); ?>">
                <td>
                    <p class="pro_plugin_title">
                        <b><span class="free_pro_show_more dashicons dashicons-arrow-down-alt2" style="display:<?php if(0 !== $i){echo "inline-block";}else{echo "none"; }; ?>"></span>
                            <span class="free_pro_show_less dashicons dashicons-arrow-up-alt2" style="display:<?php if(0 === $i){echo "inline-block";}else{echo "none"; }; ?>"></span>
                            <?php _e($pro_addons_list_details[$current_addon]['title']); ?></b>
                    </p>
                </td>
            </tr>
            <tr class="bordered fold <?php if(0 === $i){echo "open";} ?>">
                <td>
                    <table class="wp-list-table fold-table-inner" width="100%" style="line-height:25px;">
                        <thead>
                            <th style="width:40%;"><?php _e("Features","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
                            <th style="width:30%;"><?php _e("Free","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
                            <th style="width:30%;"><?php _e("Premium","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
                        </thead>
                        <tbody>
                            <?php 
                                
                                if(isset($pro_addon['features_list']) && is_array($pro_addon['features_list']) && !empty($pro_addon['features_list'])){
                                    $features_list = $pro_addon['features_list'];
                                    foreach($features_list as $fea){
                                        ?>
                                        <tr class="bordered">
                                            <td>
                                                <?php echo ($fea['feature_title']); ?>
                                            </td>
                                            <td>
                                                <ul>
                                                    <?php for($i = 0; $i<count($fea['free']); $i++){
                                                        echo '<li>';
                                                        printf('%1$s %2$s',
                                                            (true === $fea['free'][$i]['v_status']) ? $yes_icon : $no_icon,
                                                            isset($fea['free'][$i]['v_label']) ? $fea['free'][$i]['v_label'] : ""
                                                        );
                                                        echo '</li>';
                                                    }
                                                    ?>
                                                </ul>
                                            </td>
                                            <td>
                                                <ul>
                                                <?php for($i = 0; $i<count($fea['pro']); $i++){
                                                    echo '<li>';
                                                    printf('%1$s %2$s',
                                                        (true === $fea['pro'][$i]['v_status']) ? $yes_icon : $no_icon,
                                                        isset($fea['pro'][$i]['v_label']) ? $fea['pro'][$i]['v_label'] : ""
                                                    );
                                                    echo '</li>';
                                                }
                                                ?>
                                                </ul>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                
                            ?>
                        </tbody>
                    </table>
                    <div class="wt_addon_gurantee" style="width:60%;float: left;display: block;">
                        <ul style="display: inline-flex;margin: 5px;">
                            <li class="wt_pro_addon_money_back" style="margin-right:12px;"><?php _e("30 Day Money Back Guarantee","print-invoices-packing-slip-labels-for-woocommerce"); ?></li>
                            <li class="wt_pro_addon_support" style="margin-right:12px;"><?php _e("Fast and Superior Support","print-invoices-packing-slip-labels-for-woocommerce"); ?></li>
                        </ul>
                    </div>
                    <div class="wt_addon_btn" style="width:30%;float: right;display: block;margin: 20px 0;">
                        <div class="wt_addon_premium_btn">
                            <a href="<?php echo esc_url($pro_addon['page_link']); ?>" class="wt_pro_addon_premium_btn" target="_blank">
                                <img src="<?php echo esc_url($wf_admin_img_path.'/other_solutions/promote_crown.png'); ?>" style="width: 14px;height: 13px;margin-right: 7px;"><?php 
                                echo __("Upgrade To Premium","print-invoices-packing-slip-labels-for-woocommerce"); ?>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
                $i++;
            }
            ?>
        </tbody>
    </table>
</div>