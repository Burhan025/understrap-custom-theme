<?php
add_filter( 'bs_locate_template', function ( $template, $templateName, $templatePath ) {
    $templateMapping = [
		'locations/list/map-style-item' => 'map-style-item.php',
		'locations/list/map-style' => 'map-style.php'
    ];
    $baseTemplatePath = get_stylesheet_directory() . '/inc/site/template-parts/custom-plugin/';

    if ( array_key_exists( $templateName, $templateMapping ) ) {
        $childThemeTemplate = $baseTemplatePath . $templateMapping[ $templateName ];
        if ( file_exists( $childThemeTemplate ) ) {
            return $childThemeTemplate;
        }
    }

    return $template;
}, 10, 3 );

add_filter('bs_location_select_store_text', function($text) {
    return 'SELECT STORE LOCATION';
});

add_filter('bs_location_map_cta_text', function($text) {
    return 'Shop Now';
});

add_filter('bs_location_show_map', '__return_false');


