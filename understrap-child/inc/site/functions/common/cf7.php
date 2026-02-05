<?php
//Add custom validate fata of fields First Name and Last Name Contact form
// @return data of fields
add_filter( 'wpcf7_validate_text*', 'dk_theme_custom_text_validation_filter', 20, 2 );
function dk_theme_custom_text_validation_filter( $result, $tag ) {
	if ( 'your-first-name' == $tag->name ) {
		$firstNameValue = $_POST['your-first-name'];
		if ( ! preg_match( "/^[a-zA-Z ,._-]+$/", $firstNameValue ) ) {
			$result->invalidate( $tag, "First name should have no special characters." );
		}
	}

	if ( 'your-last-name' == $tag->name ) {
		$lastNameValue = $_POST['your-last-name'];
		if ( ! preg_match( "/^[a-zA-Z ,._-]+$/", $lastNameValue ) ) {
			$result->invalidate( $tag, "Last name should have no special characters." );
		}
	}

	return $result;
}

add_filter( 'wpcf7_validate_tel', 'dk_theme_custom_phone_validation', 10, 2 );
add_filter( 'wpcf7_validate_tel*', 'dk_theme_custom_phone_validation', 10, 2 );
function dk_theme_custom_phone_validation( $result, $tag ) {

	$type = $tag->type;
	$name = $tag->name;

	if ( $type == 'tel' || $type == 'tel*' ) {
		$phoneNumber = isset( $_POST[ $name ] ) ? trim( $_POST[ $name ] ) : '';
		$phoneNumber = preg_replace( '/[() .+-]/', '', $phoneNumber );
		if ( strlen( (string) $phoneNumber ) < 10 ) {
			$result->invalidate( $tag, 'Phone number must be 10 digits.' );
		}
	}

	return $result;
}
