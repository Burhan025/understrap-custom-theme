<?php
use BurhanAftab\Deepknead\Deepknead;

add_shortcode( "dk-current-store", "dk_theme_current_store_shortcode" );
function dk_theme_current_store_shortcode( $attributes ) {
	global $dk_current_location_store;
	if (!$dk_current_location_store || !method_exists($dk_current_location_store, 'name')) {
		return;
	}

	if ( ! is_array( $attributes ) ) {
		$attributes = [];
	}
	extract( shortcode_atts( array(
		'html_tag'   => 'span',
		'class'   => '',

	), $attributes ) );

    if(!$html_tag) {
        $html_tag = 'span';
    }

	ob_start();
    echo '<' . $html_tag . ' class="dk-current-store ' . $class . '">';
    echo esc_html($dk_current_location_store->name());
    echo '</' . $html_tag . '>';
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
