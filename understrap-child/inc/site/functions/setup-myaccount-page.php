<?php
/**
 * WooCommerce My Account Page Setup
 */

add_filter( 'body_class', 'glenora_add_login_status_class' );
function glenora_add_login_status_class( $classes ) {
	if ( is_user_logged_in() ) {
		$classes[] = 'logged-in';
	} else {
		$classes[] = 'not-logged-in';
	}

	return $classes;
}


add_filter( 'woocommerce_default_address_fields', 'glenora_override_address2_fields', 99 );
function glenora_override_address2_fields( $address_fields ) {

	// Change label for Address Line 2
	$address_fields['address_2']['label'] = 'Apartment, suite, unit, etc.';

	return $address_fields;
}

/**
 * WooCommerce: Rename My Account Endpoint breadcrumb Page Title
 */
add_filter( 'woocommerce_endpoint_lost-password_title', 'glenora_my_account_endpoint_page_title', 9999, 3 );
function glenora_my_account_endpoint_page_title( $title, $endpoint, $action ) {
	$title = "Forgot password";
	return $title;
}

/**
 * WooCommerce: Rename My Account Endpoint Page Title
 */
add_filter( 'the_title', 'glenora_endpoint_page_title', 10, 2 );
function glenora_endpoint_page_title( $title, $id ) {
	if ( is_wc_endpoint_url( 'lost-password' ) && in_the_loop() ) {
		$title = "Forgot password";
	}
	return $title;
}
