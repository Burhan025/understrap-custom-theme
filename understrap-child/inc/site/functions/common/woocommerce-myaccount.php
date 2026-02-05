<?php
//add_action( 'after_setup_theme', 'dk_theme_myaccount_custom_setup', 20 );
function dk_theme_myaccount_custom_setup() {
	remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
	add_action( 'woocommerce_register_form_end', 'wc_registration_privacy_policy_text', 20 );
}

add_action( 'wp_print_scripts', 'dk_theme_remove_password_strength', 10 );
function dk_theme_remove_password_strength() {
	wp_dequeue_script( 'wc-password-strength-meter' );
}

add_filter( 'password_hint', 'dk_theme_wp_password_hint', 10, 1 );
function dk_theme_wp_password_hint( $hint ) {
	$hint = __( 'Hint: For a stronger password, try using at least twelve characters, including a mix of upper and lower case letters, numbers, and symbols (e.g., ! ? $ % ^ & ).' );

	return $hint;
}

/**
 * Check password policy on register and update account detail page.
 */
add_action( 'woocommerce_process_registration_errors', 'dk_theme_validate_password_register_form', 10, 4 );
function dk_theme_validate_password_register_form( $validation_error, $username, $password, $email ) {
	if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
		if ( $password == '' ) {
			$validation_error->add( 'no_spam', __( 'Password is required', "dk-theme-" ) );
		}
//		else {
//			if ( strlen( $password ) < 6 ) {
//				$validation_error->add( 'no_spam', __( 'Password at least 6 characters', "dk-theme-" ) );
//			}
//		}

//		if ( ! preg_match( '/[A-Z]/', $password ) ) {
//			$validation_error->add( 'no_spam', __( 'Password at least 1 uppercase', "dk-theme-" ) );
//		}
//		if ( ! preg_match( '/[a-z]/', $password ) ) {
//			$validation_error->add( 'no_spam', __( 'Password at least 1 lowercase', "dk-theme-" ) );
//		}
//		if ( ! preg_match( '/[0-9]/', $password ) ) {
//			$validation_error->add( 'no_spam', __( 'Password at least 1 number', "dk-theme-" ) );
//		}
	}

	return $validation_error;
}

add_action( 'woocommerce_save_account_details_errors', 'dk_theme_validate_password_update_account', 10, 2 );
function dk_theme_validate_password_update_account( $errors, $user ) {
	if ( ! $user->user_pass ) {
		return $errors;
	}
	if ( $user->user_pass == '' ) {
		$errors->add( 'no_spam', __( 'Password is required', "dk-theme-" ) );
	}
//	else {
//		if ( strlen( $user->user_pass ) < 6 ) {
//			$errors->add( 'no_spam', __( 'Password at least 6 characters', "dk-theme-" ) );
//		}
//	}


//	if ( ! preg_match( '/[A-Z]/', $user->user_pass ) ) {
//		$errors->add( 'no_spam', __( 'Password at least 1 uppercase', "dk-theme-" ) );
//	}
//	if ( ! preg_match( '/[a-z]/', $user->user_pass ) ) {
//		$errors->add( 'no_spam', __( 'Password at least 1 lowercase', "dk-theme-" ) );
//	}
//	if ( ! preg_match( '/[0-9]/', $user->user_pass ) ) {
//		$errors->add( 'no_spam', __( 'Password at least 1 number', "dk-theme-" ) );
//	}

	return $errors;
}

add_action( 'template_redirect', 'dk_theme_bypass_logout_confirmation' );
function dk_theme_bypass_logout_confirmation() {
	global $wp;
	if ( isset( $wp->query_vars['customer-logout'] ) ) {
		wp_redirect( str_replace( '&amp;', '&', wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ) );
		exit;
	}
}

//Add code to validate field to input name and information
add_filter( 'woocommerce_process_myaccount_field_billing_first_name', 'dk_theme_billing_first_name_field_validation' );
function dk_theme_billing_first_name_field_validation($value) {
	if ( !empty($value ) && ! preg_match( "/^[a-zA-Z ,._-]+$/", $value ) ) {
		wc_add_notice( __( '<strong>First name</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

add_filter( 'woocommerce_process_myaccount_field_billing_last_name', 'dk_theme_billing_last_name_field_validation' );
function dk_theme_billing_last_name_field_validation($value) {
	if (!empty($value ) && ! preg_match( "/^[a-zA-Z ,._-]+$/", $value ) ) {
		wc_add_notice( __( '<strong>Last name</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Rule to validate Street Address
add_filter( 'woocommerce_process_myaccount_field_billing_address_1', 'dk_theme_billing_address_1_field_validation' );
function dk_theme_billing_address_1_field_validation($value) {
    if (!empty( $value ) &&  dk_theme_has_special_chars( $value )) {
		wc_add_notice( __( '<strong>Street address</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Rule to validate Town / City
add_filter( 'woocommerce_process_myaccount_field_billing_city', 'dk_theme_billing_city_field_validation' );
function dk_theme_billing_city_field_validation($value) {
	if (!empty( $value ) && dk_theme_has_special_chars( $value )) {
		wc_add_notice( __( '<strong>Town / City</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Add validate billing phone
add_filter( 'woocommerce_process_myaccount_field_billing_phone', 'dk_theme_billing_phone_field_validation' );
function dk_theme_billing_phone_field_validation($value) {
	if (!empty($value ) && function_exists('bs_validate_phone') && !bs_validate_phone($_POST['billing_phone'])) {
		wc_add_notice( __( '<strong>Phone number</strong> must be 10 digits.' ), 'error' );
	}
	return $value;
}

//Add validate shipping first name
add_filter( 'woocommerce_process_myaccount_field_shipping_first_name', 'dk_theme_shipping_first_name_field_validation' );
function dk_theme_shipping_first_name_field_validation($value) {
	if (!empty($value ) && ! preg_match( "/^[a-zA-Z ,._-]+$/", $value ) ) {
		wc_add_notice( __( '<strong>First name</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Add validate shipping last name
add_filter( 'woocommerce_process_myaccount_field_shipping_last_name', 'dk_theme_shipping_last_name_field_validation' );
function dk_theme_shipping_last_name_field_validation($value) {
	if (!empty($value ) && ! preg_match( "/^[a-zA-Z0-9 ,._-]+$/", $value ) ) {
		wc_add_notice( __( '<strong>Last name</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Rule to validate Shipping Street Address
add_filter( 'woocommerce_process_myaccount_field_shipping_address_1', 'dk_theme_shipping_address_1_field_validation' );
function dk_theme_shipping_address_1_field_validation($value) {
    if (!empty( $value ) &&  dk_theme_has_special_chars( $value )) {
        wc_add_notice( __( '<strong>Street address</strong> should have no special characters.' ), 'error' );
    }

	return $value;
}

//Rule to validate Shipping Town / City
add_filter( 'woocommerce_process_myaccount_field_shipping_city', 'dk_theme_shipping_city_field_validation' );
function dk_theme_shipping_city_field_validation($value) {
    if (!empty( $value ) &&  dk_theme_has_special_chars( $value )) {
		wc_add_notice( __( '<strong>Town / City</strong> should have no special characters.' ), 'error' );
	}

	return $value;
}

//Add validate Shipping phone
add_filter( 'woocommerce_process_myaccount_field_shipping_phone', 'dk_theme_shipping_phone_field_validation' );
function dk_theme_shipping_phone_field_validation($value) {
	if (!empty($value ) && function_exists('bs_validate_phone') && !bs_validate_phone($_POST['shipping_phone'])) {
		wc_add_notice( __( '<strong>Phone number</strong> must be 10 digits.' ), 'error' );
	}
	return $value;
}

// Validate data in page edit account details
add_action( 'template_redirect', 'dk_theme_account_details_validate', 1 );
function dk_theme_account_details_validate() {
	if ( function_exists( 'is_account_page' ) && is_account_page() && is_wc_endpoint_url( 'edit-account' ) ) {
		// Validate data for fist name
		if ( !empty( $_POST['account_first_name']) && isset( $_POST['account_first_name'] ) && ! preg_match( "/^[a-zA-Z ,._-]+$/", $_POST['account_first_name'] ) ) {
			wc_add_notice( __( '<strong>First name</strong> should have no special characters.' ), 'error' );
		}
		// Validate data for last name
		if (!empty( $_POST['account_last_name']) && isset( $_POST['account_last_name'] ) && ! preg_match( "/^[a-zA-Z ,._-]+$/", $_POST['account_last_name'] ) ) {
			wc_add_notice( __( '<strong>Last name</strong> should have no special characters.' ), 'error' );
		}

		if (isset($_POST['shipping_dob'])) {

			$dob = \DateTime::createFromFormat("Y-m-d", $_POST['shipping_dob']);
			$currentTime = new \DateTime();

			if ($dob && $dob < $currentTime ) {
				$diff = $currentTime->diff($dob);
				if ( $diff->y <= 18 && $diff->invert == 1) {
					wc_add_notice( __( "You must be at least 19 years old to purchase cannabis", 'woocommerce' ), 'error' );
				}
			} else if($dob && $dob > $currentTime ) {
				wc_add_notice( __( "Birthdate should be less than current date", 'woocommerce' ), 'error' );
			}

		}
	}
}

