<?php
add_action( 'wp_head', 'dk_theme_header_scripts' );
function dk_theme_header_scripts() {
	$header_scripts = get_field( 'header_scripts', 'option' );
	if ( $header_scripts ) {
		echo $header_scripts;
	}
}

add_action( 'wp_footer', 'dk_theme_footer_scripts' );
function dk_theme_footer_scripts() {
	$footer_scripts = get_field( 'footer_scripts', 'option' );
	if ( $footer_scripts ) {
		echo $footer_scripts;
	}
}

