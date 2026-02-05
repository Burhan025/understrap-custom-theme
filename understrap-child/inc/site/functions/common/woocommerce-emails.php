<?php
add_action( 'woocommerce_email_footer', 'dk_theme_custom_remove_get_the_app_ad', 8 );
function dk_theme_custom_remove_get_the_app_ad() {
	remove_action( 'woocommerce_email_footer', array( $object, 'mobile_messaging' ), 9 );
}


/**
 * Send Cancel order email to client
 */
add_filter( 'woocommerce_email_recipient_cancelled_order', 'dk_theme_cancelled_order_email_to_customer', 9999, 3 );
function dk_theme_cancelled_order_email_to_customer( $email_recipient, $email_object, $email ) {
	if ( is_admin() ) {
		return $email_recipient;
	}

	if ( $email_object->get_billing_email() && is_email( $email_object->get_billing_email() ) ) {
		$email_recipient .= ', ' . $email_object->get_billing_email();
	}

	return $email_recipient;
}
