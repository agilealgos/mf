<?php 
global $wpsl_settings, $wpsl;

$output         = $this->get_custom_css(); 
$autoload_class = ( !$wpsl_settings['autoload'] ) ? 'class="wpsl-not-loaded"' : '';

$output .= '<div id="wpsl-wrap">' . "\r\n";
$output .= "\t" . '<div class="wpsl-search wpsl-clearfix ' . $this->get_css_classes() . '">' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-search-wrap">' . "\r\n";
$output .= "\t\t\t" . '<form autocomplete="off">' . "\r\n";
$output .= "\t\t\t" . '<div class="wpsl-input" style="display:none;">' . "\r\n";
$output .= "\t\t\t\t" . '<div><label for="wpsl-search-input">' . esc_html( $wpsl->i18n->get_translation( 'search_label', __( 'Your location', 'wpsl' ) ) ) . '</label></div>' . "\r\n";
$output .= "\t\t\t\t" . '<input id="wpsl-search-input" type="text" value="' . apply_filters( 'wpsl_search_input', '' ) . '" name="wpsl-search-input" placeholder="" aria-required="true" />' . "\r\n";
$output .= "\t\t\t" . '</div>' . "\r\n";


$output .= "\t\t\t" . '<div class="wpsl-select-wrap">' . "\r\n";
$output .= "\t\t\t\t" . '<div><label for="wpsl-search-state">State</label></div>' . "\r\n";
$output .= "\t\t\t\t" . '<div><select name="wpsl-search-state" style="display: block;" onchange="stateChange(this);">' . "\r\n";
$output .= '<option value="" data-city="">Select State</option>';
$output .= '<option value="Karnataka" data-city="Bangalore,Mangaluru">Karnataka</option>';
$output .= '<option value="Maharashtra" data-city="Mumbai,Thane">Maharashtra</option>';
$output .= '<option value="Punjab" data-city="Ludhiana,Chandigarh">Punjab</option>';
$output .= '<option value="Telangana" data-city="Hyderabad">Telangana</option>';
$output .= '<option value="Tamil Nadu" data-city="Chennai">Tamil Nadu</option>';
$output .= '<option value="Delhi" data-city="Delhi">Delhi</option>';
$output .= '<option value="Haryana" data-city="Gurugram">Haryana</option>';
$output .= '<option value="Andhra Pradesh" data-city="Vijayawada">Andhra Pradesh</option>';
$output .= '<option value="Uttar Pradesh" data-city="Noida">Uttar Pradesh</option>';
$output .= '</select></div>';
$output .= "\t\t\t" . '</div>' . "\r\n";


$output .= "\t\t\t" . '<div class="wpsl-select-wrap">' . "\r\n";
$output .= "\t\t\t\t" . '<div><label for="wpsl-search-city">City</label></div>' . "\r\n";
$output .= "\t\t\t\t" . '<div><select name="wpsl-search-city" id="city-dropdown" style="display: block;" onchange="cityChange(this);">' . "\r\n";
$output .= '<option value="">Select City</option>';
$output .= '</select></div>';
$output .= "\t\t\t" . '</div>' . "\r\n";

$output .= "<script>
    function stateChange(select){
        var state = select.value;
        var opt = select.options[select.selectedIndex];
        var city = opt.dataset.city;
        var citys = city.split(',');
        var html = '<option value=\"\">Select City</option>';
        for (let x in citys) {
            html += '<option value=\"'+state+' '+citys[x]+'\">'+citys[x]+'</option>';
        }
        jQuery('#city-dropdown').html(html);
        jQuery('#wpsl-search-input').val(state);
    }
    function cityChange(select){
        city = select.value;
        jQuery('#wpsl-search-input').val(city);
    }
</script>";

if ( $wpsl_settings['radius_dropdown'] || $wpsl_settings['results_dropdown']  ) {
    $output .= "\t\t\t" . '<div class="wpsl-select-wrap">' . "\r\n";

    if ( $wpsl_settings['radius_dropdown'] ) {
        $output .= "\t\t\t\t" . '<div id="wpsl-radius">' . "\r\n";
        $output .= "\t\t\t\t\t" . '<label for="wpsl-radius-dropdown">' . esc_html( $wpsl->i18n->get_translation( 'radius_label', __( 'Search radius', 'wpsl' ) ) ) . '</label>' . "\r\n";
        $output .= "\t\t\t\t\t" . '<select id="wpsl-radius-dropdown" class="wpsl-dropdown" name="wpsl-radius">' . "\r\n";
        $output .= "\t\t\t\t\t\t" . $this->get_dropdown_list( 'search_radius' ) . "\r\n";
        $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
        $output .= "\t\t\t\t" . '</div>' . "\r\n";
    }

    if ( $wpsl_settings['results_dropdown'] ) {
        $output .= "\t\t\t\t" . '<div id="wpsl-results">' . "\r\n";
        $output .= "\t\t\t\t\t" . '<label for="wpsl-results-dropdown">' . esc_html( $wpsl->i18n->get_translation( 'results_label', __( 'Results', 'wpsl' ) ) ) . '</label>' . "\r\n";
        $output .= "\t\t\t\t\t" . '<select id="wpsl-results-dropdown" class="wpsl-dropdown" name="wpsl-results">' . "\r\n";
        $output .= "\t\t\t\t\t\t" . $this->get_dropdown_list( 'max_results' ) . "\r\n";
        $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
        $output .= "\t\t\t\t" . '</div>' . "\r\n";
    } 

    $output .= "\t\t\t" . '</div>' . "\r\n";
}

if ( $this->use_category_filter() ) {
    $output .= $this->create_category_filter();
}

$output .= "\t\t\t\t" . '<div class="wpsl-search-btn-wrap"><input id="wpsl-search-btn" type="submit" value="' . esc_attr( $wpsl->i18n->get_translation( 'search_btn_label', __( 'Search', 'wpsl' ) ) ) . '"></div>' . "\r\n";

$output .= "\t\t" . '</form>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t" . '</div>' . "\r\n";
    
$output .= "\t" . '<div id="wpsl-gmap" class="wpsl-gmap-canvas"></div>' . "\r\n";

$output .= "\t" . '<div id="wpsl-result-list">' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-stores" '. $autoload_class .'>' . "\r\n";
$output .= "\t\t\t" . '<ul></ul>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t\t" . '<div id="wpsl-direction-details">' . "\r\n";
$output .= "\t\t\t" . '<ul></ul>' . "\r\n";
$output .= "\t\t" . '</div>' . "\r\n";
$output .= "\t" . '</div>' . "\r\n";

if ( $wpsl_settings['show_credits'] ) { 
    $output .= "\t" . '<div class="wpsl-provided-by">'. sprintf( __( "Search provided by %sWP Store Locator%s", "wpsl" ), "<a target='_blank' href='https://wpstorelocator.co'>", "</a>" ) .'</div>' . "\r\n";
}

$output .= '</div>' . "\r\n";

return $output;