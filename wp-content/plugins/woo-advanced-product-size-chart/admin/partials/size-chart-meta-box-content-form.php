<?php

/**
 * Provide a admin area form view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    size-chart-for-woocommerce
 * @subpackage size-chart-for-woocommerce/admin/partials
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
// Add an nonce field so we can check for it later.
wp_nonce_field( 'size_chart_inner_custom_box', 'size_chart_inner_custom_box' );
$size_cart_post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
// Use get_post_meta to retrieve an existing value from the database.
$chart_label = scfw_size_chart_get_label_by_chart_id( $size_cart_post_id );
$chart_sub_title = scfw_size_chart_get_sub_title_by_chart_id( $size_cart_post_id );
$chart_tab_label = scfw_size_chart_get_tab_label_by_chart_id( $size_cart_post_id );
$chart_popup_label = scfw_size_chart_get_popup_label_by_chart_id( $size_cart_post_id );
$chart_popup_type = scfw_size_chart_get_popup_type_by_chart_id( $size_cart_post_id );
$chart_position = scfw_size_chart_get_position_by_chart_id( $size_cart_post_id );
$chart_table = scfw_size_chart_get_chart_table_by_chart_id( $size_cart_post_id, false );
$table_style = scfw_size_chart_get_chart_table_style_by_chart_id( $size_cart_post_id );
$chart_attributes = scfw_size_chart_get_attributes( $size_cart_post_id );

if ( scfw_fs()->is__premium_only() && scfw_fs()->can_use_premium_code() ) {
    $chart_color = scfw_size_chart_color_details__premium_only( $size_cart_post_id );
    $chart_border = scfw_size_chart_border_details__premium_only( $size_cart_post_id );
    $chart_country = scfw_size_chart_country__premium_only( $size_cart_post_id );
}

$chart_popup_note = scfw_size_chart_popup_note( $size_cart_post_id );
// Display the form, using the current value.
$size_chart_img = scfw_size_chart_get_primary_chart_image_data_by_chart_id( $size_cart_post_id );
$image_id = $size_chart_img['attachment_id'];
$image_url = $size_chart_img['url'];
$img_width = $size_chart_img['width'];
$img_height = $size_chart_img['height'];
$close_icon_enable = $size_chart_img['close_icon_status'];
$is_disable = true;
?>
<div id="size-chart-meta-fields">
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="label">
					<?php 
esc_html_e( 'Size Chart Popup Title', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
			<?php 
esc_html_e( 'Empty to hide/remove a title', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="text" name="label" id="label" value="<?php 
echo  esc_attr( $chart_label ) ;
?>"/>
        </div>
    </div>

    <div id="field">
        <div class="field-title">
            <h4>
                <label for="size-chart-sub-title">
                    <?php 
esc_html_e( 'Sub Title', 'size-chart-for-woocommerce' );
?>
                    <span class="deprecation-message">
                        (<?php 
echo  sprintf( wp_kses_post( '%1$sWarning:%2$s We are removing this field from next update. From now you can use them from content editor.' ), '<strong>', '</strong>' ) ;
?>)
                    </span>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Chart Sub Title', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="text" class="disabled" name="size-chart-sub-title" id="size-chart-sub-title" value="<?php 
echo  esc_attr( $chart_sub_title ) ;
?>" disabled />
        </div>
    </div>

    <div id="field">
        <div class="field-title">
            <h4>
                <label for="primary-chart-image">
					<?php 
esc_html_e( 'Primary Chart Image', 'size-chart-for-woocommerce' );
?>
                    <span class="deprecation-message">
                        (<?php 
echo  sprintf( wp_kses_post( '%1$sWarning:%2$s We are removing this field from next update. From now you can use them from content editor.' ), '<strong>', '</strong>' ) ;
?>)
                    </span>
                </label>
            </h4>
        </div>
        <div class="field-description">
			<?php 
esc_html_e( 'Add/Edit primary chart image below', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="hidden" name="primary-chart-image" id="primary-chart-image" value="<?php 
echo  esc_attr( $image_id ) ;
?>"/>
        </div>

        <div id="field-image">
            <div class="field_image_box">
                <img src="<?php 
echo  esc_url( $image_url ) ;
?>" width="<?php 
echo  esc_attr( $img_width ) ;
?>" height="<?php 
echo  esc_attr( $img_height ) ;
?>" id="meta_img" alt="<?php 
esc_attr_e( 'Primary Chart Image', 'size-chart-for-woocommerce' );
?>"/>
				<?php 

if ( true === $close_icon_enable ) {
    ?>
                    <a id="<?php 
    echo  esc_attr( $size_cart_post_id ) ;
    ?>" class="delete-chart-image">
                        <img src="<?php 
    echo  esc_url( plugins_url( 'images/close-icon.png', dirname( __FILE__ ) ) ) ;
    ?>" alt="<?php 
    esc_attr_e( 'close icon', 'size-chart-for-woocommerce' );
    ?>"/>
                    </a>
				<?php 
}

?>
            </div>
        </div>
        <div class="field-item">
            <input type="button" class="button" data-uploader-title="<?php 
esc_attr_e( 'Select File', 'size-chart-for-woocommerce' );
?>" data-uploader-button-text="<?php 
esc_attr_e( 'Include File', 'size-chart-for-woocommerce' );
?>" value="<?php 
esc_attr_e( 'Upload', 'size-chart-for-woocommerce' );
?>" disabled/>
        </div>
    </div>
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="position">
					<?php 
esc_html_e( 'Size Chart Position', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
			<?php 
esc_html_e( 'Select if the chart will display as a popup or as a additional tab', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <select name="position" id="position">
                <option value="tab" <?php 
selected( $chart_position, 'tab', true );
?>><?php 
esc_html_e( 'Additional Tab', 'size-chart-for-woocommerce' );
?></option>
                <option value="popup" <?php 
selected( $chart_position, 'popup', true );
?>><?php 
esc_html_e( 'Modal Pop Up', 'size-chart-for-woocommerce' );
?></option>
            </select>
        </div>
    </div>
    <?php 
$defalut_chart_position = ( isset( $chart_position ) && '' !== $chart_position ? $chart_position : 'tab' );
?>
    <div id="field" class="chart-tab-field <?php 
echo  ( isset( $defalut_chart_position ) && 'tab' === $defalut_chart_position ? 'enable' : 'disable' ) ;
?>">
        <div class="field-title">
            <h4>
                <label for="chart-tab-label">
                    <?php 
esc_html_e( 'Tab Label', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Chart Tab Label', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="text" name="chart-tab-label" id="chart-tab-label" value="<?php 
echo  esc_attr( $chart_tab_label ) ;
?>"/>
        </div>
    </div>
    <div id="field" class="chart-popup-field <?php 
echo  ( isset( $defalut_chart_position ) && 'popup' === $defalut_chart_position ? 'enable' : 'disable' ) ;
?>">
        <div class="field-title">
            <h4>
                <label for="chart-popup-label">
                    <?php 
esc_html_e( 'Size Chart Link Title', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Add Size Chart Link Title; Default it will showcase from global settings', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="text" name="chart-popup-label" id="chart-popup-label" value="<?php 
echo  esc_attr( $chart_popup_label ) ;
?>"/>
        </div>
    </div>
    <div id="field" class="chart-popup-field <?php 
echo  ( isset( $defalut_chart_position ) && 'popup' === $defalut_chart_position ? 'enable' : 'disable' ) ;
?>">
        <div class="field-title">
            <h4>
                <label for="chart-popup-type">
                    <?php 
esc_html_e( 'Size Chart Link Type', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Select size chart link type; Default it will consider global settings', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <select name="chart-popup-type" id="chart-popup-type">
                <option value="global" <?php 
selected( scfw_size_chart_get_popup_type_by_chart_id( $size_cart_post_id ), 'global' );
?> ><?php 
esc_html_e( 'Global Setting', 'size-chart-for-woocommerce' );
?></option>
                <option value="text" <?php 
selected( scfw_size_chart_get_popup_type_by_chart_id( $size_cart_post_id ), 'text' );
?> ><?php 
esc_html_e( 'Text', 'size-chart-for-woocommerce' );
?></option>
                <option value="button" <?php 
selected( scfw_size_chart_get_popup_type_by_chart_id( $size_cart_post_id ), 'button' );
?> ><?php 
esc_html_e( 'Button', 'size-chart-for-woocommerce' );
?></option>
            </select>
        </div>
    </div>
    <div id="field" class="chart-popup-field <?php 
echo  ( isset( $defalut_chart_position ) && 'popup' === $defalut_chart_position ? 'enable' : 'disable' ) ;
?>">
        <div class="field-title">
            <h4>
                <label for="chart-popup-icon">
                    <?php 
esc_html_e( 'Popup Icon', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Selected chart popup icon will show before chart popup link title.', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="hidden" name="chart-popup-icon" id="chart-popup-icon" value="<?php 
echo  esc_attr_e( scfw_size_chart_get_popup_icon_by_chart_id( $size_cart_post_id ), 'size-chart-for-woocommerce' ) ;
?>"/>
        </div>
        <div class="field-default-icon-wrap">
            <label>
                <input type="radio" name="default-icons" value="dashicons-none" <?php 
checked( scfw_size_chart_get_popup_icon_by_chart_id( $size_cart_post_id ), '' );
?> />
                <span class="dashicons dashicons-none">None</span>
            </label>
            <?php 
foreach ( glob( SCFW_PLUGIN_DIR_PATH . 'includes/chart-icons/*.svg' ) as $icon_path ) {
    $patharr = explode( '/', $icon_path );
    $filename = end( $patharr );
    $filevalue = explode( '.', $filename );
    $filevalue = ( $filevalue[0] ? $filevalue[0] : '' );
    
    if ( !empty($filevalue) ) {
        ?>
                <label>
                    <input type="radio" name="default-icons" value="<?php 
        echo  esc_attr( $filevalue ) ;
        ?>" <?php 
        checked( scfw_size_chart_get_popup_icon_by_chart_id( $size_cart_post_id ), $filevalue );
        ?>/>
                    <span class="dashicons">
                        <img src="<?php 
        echo  esc_url( SCFW_PLUGIN_URL . 'includes/chart-icons/' . $filename ) ;
        ?>" />
                    </span>
                </label>
                <?php 
    }

}
?>
        </div>
    </div>
    <?php 

if ( scfw_fs()->is__premium_only() && scfw_fs()->can_use_premium_code() ) {
    ?>
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="chart-country">
                    <?php 
    esc_html_e( 'Select Countries to Show', 'size-chart-for-woocommerce' );
    ?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
    esc_html_e( 'Leave empty to show size chart for all countries.', 'size-chart-for-woocommerce' );
    ?>
        </div>
        <div class="field-item">
            <?php 
    global  $woocommerce ;
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->__get( 'countries' );
    ?>
                <select id="chart-country" name="chart-country[]" multiple="multiple">
                    <?php 
    foreach ( $countries as $country_code => $country_name ) {
        $selected = ( !empty($chart_country) && in_array( $country_code, $chart_country, true ) ? 'selected=selected' : '' );
        ?>
                        <option value="<?php 
        echo  esc_attr( $country_code ) ;
        ?>" <?php 
        echo  esc_attr( $selected ) ;
        ?>><?php 
        echo  esc_html( $country_name ) ;
        ?></option>
                    <?php 
    }
    ?>
                </select>
        </div>
    </div>
    <?php 
}

?>
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="table-style">
					<?php 
esc_html_e( 'Chart Table Style', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
			<?php 
esc_html_e( 'Chart Table Styles (Default Style)', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <select name="table-style" id="table-style">
                <option value="default-style" <?php 
selected( $table_style, 'default-style', true );
?>><?php 
esc_html_e( 'Default Style', 'size-chart-for-woocommerce' );
?></option>
                <option value="minimalistic" <?php 
selected( $table_style, 'minimalistic', true );
?>><?php 
esc_html_e( 'Minimalistic', 'size-chart-for-woocommerce' );
?></option>
                <option value="classic" <?php 
selected( $table_style, 'classic', true );
?>><?php 
esc_html_e( 'Classic', 'size-chart-for-woocommerce' );
?></option>
                <option value="modern" <?php 
selected( $table_style, 'modern', true );
?>><?php 
esc_html_e( 'Modern', 'size-chart-for-woocommerce' );
?></option>
                <?php 

if ( scfw_fs()->is__premium_only() & scfw_fs()->can_use_premium_code() ) {
    ?>
                    <option value="custom-style" <?php 
    selected( $table_style, 'custom-style', true );
    ?> <?php 
    disabled( $is_disable, true, true );
    ?> ><?php 
    esc_html_e( 'Custom Style', 'size-chart-for-woocommerce' );
    ?></option>
                    <option value="advance-style" <?php 
    selected( $table_style, 'advance-style', true );
    ?> <?php 
    disabled( $is_disable, true, true );
    ?> ><?php 
    esc_html_e( 'Advance Style', 'size-chart-for-woocommerce' );
    ?></option>
                <?php 
}

?>
            </select>
        </div>
    </div>
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="chart-table">
					<?php 
esc_html_e( 'Chart Table', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
			<?php 
esc_html_e( 'Add/Edit chart below', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <table class="multiple_action_wrap" width="100%">
                <tr class="row_wrap">
                    <td>
                        <label for="scfw_add_multi_row"><?php 
esc_html_e( 'Enter Row count to insert', 'size-chart-for-woocommerce' );
?></label>
                    </td>
                    <td colspan="3">
                        <input type="number" name="scfw_add_multi_row" id="scfw_add_multi_row" style="width: 50% !important;" />
                        <button type="button" name="scfw_add_multi_row_action" id="scfw_add_multi_row_action" class="button button-primary"><?php 
esc_html_e( 'Add', 'size-chart-for-woocommerce' );
?></button>
                        <button type="button" name="scfw_delete_multi_row_action" id="scfw_delete_multi_row_action" class="button button-secondary"><?php 
esc_html_e( 'Delete', 'size-chart-for-woocommerce' );
?></button>
                    </td>
                </tr>
                <tr class="column_wrap">
                    <td>
                        <label for="scfw_delete_multi_column"><?php 
esc_html_e( 'Enter Column count to insert', 'size-chart-for-woocommerce' );
?></label>
                    </td>
                    <td colspan="3">
                        <input type="number" name ="scfw_delete_multi_column" id="scfw_delete_multi_column" style="width: 50% !important;" />
                        <button type="button" name="scfw_add_multi_column_action" id="scfw_add_multi_column_action" class="button button-primary"><?php 
esc_html_e( 'Add', 'size-chart-for-woocommerce' );
?></button>
                        <button type="button" name="scfw_delete_multi_column_action" id="scfw_delete_multi_column_action" class="button button-secondary"><?php 
esc_html_e( 'Delete', 'size-chart-for-woocommerce' );
?></button>
                    </td>
                </tr>
                <?php 

if ( scfw_fs()->is__premium_only() && scfw_fs()->can_use_premium_code() ) {
    ?>
                    <tr>
                        <td colspan="4"><h3><?php 
    esc_html_e( 'Table Color', 'size-chart-for-woocommerce' );
    ?></h3></td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Header background', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_header_bg_color" id="scfw_header_bg_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_header_bg_color'] ) ;
    ?>" />
                            </div>
                        </td>
                        <td>
                            <?php 
    esc_html_e( 'Text Color', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_text_color" id="scfw_text_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_text_color'] ) ;
    ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Even row background', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_even_row_bg_color" id="scfw_even_row_bg_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_even_row_bg_color'] ) ;
    ?>" />
                            </div>                        
                        </td>
                        <td>
                            <?php 
    esc_html_e( 'Even row text', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_even_text_color" id="scfw_even_text_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_even_text_color'] ) ;
    ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Odd row background', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_odd_row_bg_color" id="scfw_odd_row_bg_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_odd_row_bg_color'] ) ;
    ?>" />
                            </div> 
                        </td>
                        <td>
                            <?php 
    esc_html_e( 'Odd row text', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_color_picker">
                                <input type="text" name="scfw_odd_text_color" id="scfw_odd_text_color" value="<?php 
    echo  esc_attr( $chart_color['scfw_odd_text_color'] ) ;
    ?>" />
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4"><h3><?php 
    esc_html_e( 'Border Color', 'size-chart-for-woocommerce' );
    ?></h3></td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Horizontal border style', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_border_setting">
                                <select name="scfw_border_hb_style" id="scfw_border_hb_style">
                                    <option value="<?php 
    echo  esc_attr( 'solid' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_hb_style'], 'solid', true );
    ?>><?php 
    esc_html_e( 'Solid', 'size-chart-for-woocommerce' );
    ?></option>
                                    <option value="<?php 
    echo  esc_attr( 'dotted' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_hb_style'], 'dotted', true );
    ?>><?php 
    esc_html_e( 'Dotted', 'size-chart-for-woocommerce' );
    ?></option>
                                    <option value="<?php 
    echo  esc_attr( 'dashed' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_hb_style'], 'dashed', true );
    ?>><?php 
    esc_html_e( 'Dashed', 'size-chart-for-woocommerce' );
    ?></option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <?php 
    esc_html_e( 'Horizontal width', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_border_setting">
                                <input type="number" name="scfw_border_hw" id="scfw_border_hw" value="<?php 
    echo  esc_attr( $chart_border['scfw_border_hw'] ) ;
    ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Vertical border style', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_border_setting">
                                <select name="scfw_border_vb_style" id="scfw_border_vb_style">
                                    <option value="<?php 
    echo  esc_attr( 'solid' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_vb_style'], 'solid', true );
    ?>><?php 
    esc_html_e( 'Solid', 'size-chart-for-woocommerce' );
    ?></option>
                                    <option value="<?php 
    echo  esc_attr( 'dotted' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_vb_style'], 'dotted', true );
    ?>><?php 
    esc_html_e( 'Dotted', 'size-chart-for-woocommerce' );
    ?></option>
                                    <option value="<?php 
    echo  esc_attr( 'dashed' ) ;
    ?>" <?php 
    selected( $chart_border['scfw_border_vb_style'], 'dashed', true );
    ?>><?php 
    esc_html_e( 'Dashed', 'size-chart-for-woocommerce' );
    ?></option>
                                </select>
                            </div>                        
                        </td>
                        <td>
                            <?php 
    esc_html_e( 'Vertical width', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_border_setting">
                                <input type="number" name="scfw_border_vw" id="scfw_border_vw" value="<?php 
    echo  esc_attr( $chart_border['scfw_border_vw'] ) ;
    ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
    esc_html_e( 'Border color', 'size-chart-for-woocommerce' );
    ?>
                        </td>
                        <td>
                            <div class="scfw_border_setting">
                                <input type="text" name="scfw_border_color" id="scfw_border_color" value="<?php 
    echo  esc_attr( $chart_border['scfw_border_color'] ) ;
    ?>" />
                            </div> 
                        </td>
                    </tr>
                <?php 
}

?>
            </table>
            <textarea id="chart-table" class="chart-table" name="chart-table"><?php 
echo  esc_html( $chart_table ) ;
?></textarea>
        </div>
        <div class="field-item">
            <?php 
echo  sprintf(
    '<a id="%s" href="javascript:void(0);" class="preview_chart button button-primary" title="%s" rel="permalink">%s</a>',
    esc_attr( $size_cart_post_id ),
    esc_attr__( 'Click here for preview', 'size-chart-for-woocommerce' ),
    esc_attr__( 'Preview', 'size-chart-for-woocommerce' )
) ;
?>
            <?php 
echo  sprintf(
    '<a id="%s" href="javascript:void(0);" class="import_chart button button-primary" title="%s" rel="permalink">%s</a>',
    esc_attr( $size_cart_post_id ),
    esc_attr__( 'Click to import size chart data', 'size-chart-for-woocommerce' ),
    esc_attr__( 'Import Chart Table', 'size-chart-for-woocommerce' )
) ;
?>
            <input type="file" class="scfw_import_file" accept="application/json" style="display:none;" />
            <?php 
echo  sprintf(
    '<a id="%s" href="javascript:void(0);" class="export_chart button button-primary" title="%s" rel="permalink">%s</a>',
    esc_attr( $size_cart_post_id ),
    esc_attr__( 'Click to export size chart data', 'size-chart-for-woocommerce' ),
    esc_attr__( 'Export Chart Table', 'size-chart-for-woocommerce' )
) ;
?>
        </div>
    </div>
    <div id="field">
        <div class="field-title">
            <h4>
                <label for="chart-popup-note">
                    <?php 
esc_html_e( 'Popup Note', 'size-chart-for-woocommerce' );
?>
                </label>
            </h4>
        </div>
        <div class="field-description">
            <?php 
esc_html_e( 'Add notes about the table sizes like it\'s in Inches, Meters, Centimeter, etc.', 'size-chart-for-woocommerce' );
?>
        </div>
        <div class="field-item">
            <input type="text" name="chart-popup-note" id="chart-popup-note" value="<?php 
echo  esc_attr( $chart_popup_note ) ;
?>"/>
        </div>
    </div>
</div>