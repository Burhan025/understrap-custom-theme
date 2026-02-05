<?php
/**
 * Enqueue Google Fonts Montserrat
 */
add_action('wp_enqueue_scripts', 'glenora_enqueue_style', 10);
function glenora_enqueue_style() {

	$theme_version = wp_get_theme()->get( 'Version' );

	// Append timestamp to bust cache: mmddhhmmss
	$timestamp     = gmdate( 'mdHis' );
	$version       = $theme_version;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	if ( version_compare( get_option( 'woocommerce_version' ), '7.8.0', '>=' ) && ! wp_script_is( 'wc-cart-fragments' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	wp_enqueue_style( 'flickity-styles', get_stylesheet_directory_uri() . "/inc/site/assets/libs/flickity{$suffix}.css", array(), $version );

	wp_enqueue_script( 'flickity-scripts', get_stylesheet_directory_uri() . "/inc/site/assets/libs/flickity.pkgd{$suffix}.js", array(), $version, true );
}

add_action('wp_enqueue_scripts', 'glenora_dequeue_style', 20);
function glenora_dequeue_style() {
	wp_dequeue_style('google-fonts-poppins');
	wp_dequeue_style('wp-emoji-styles');
//	wp_dequeue_style('font-awesome-6');

	wp_dequeue_style('understrap-child-glider-styles');
	wp_dequeue_script('comment-reply');

	wp_dequeue_style('aos-css');
	wp_dequeue_script('aos-js');
}

// Add custom editor styles
add_action( 'after_setup_theme', function() {
//	add_theme_support( 'editor-styles' );
	add_editor_style( 'inc/site/assets/admin/editor-style.css' );
});
