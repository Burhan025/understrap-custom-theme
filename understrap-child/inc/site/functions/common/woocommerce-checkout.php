<?php
add_filter( 'woocommerce_enable_order_notes_field', '__return_true', 99 );

add_filter( 'woocommerce_checkout_fields', 'dk_theme_customize_order_fields' );
function dk_theme_customize_order_fields( $fields ) {
	$fields['order']['order_comments']['label']       = __( 'Order Notes', 'dk-theme' );
	$fields['order']['order_comments']['placeholder'] = __( 'Notes about your order, e.g. special notes for delivery.', 'dk-theme' );

	if( isset($fields['address_2']) ) {
		$fields['address_2']['label']       = __( 'Apartment, Unit, PO Box, etc.', "dk-theme" );
		$fields['address_2']['placeholder'] = __( 'Apartment, Unit, PO Box, etc. (optional)', "dk-theme" );
	}

	return $fields;
}

add_filter( 'gettext_with_context_woocommerce', 'dk_theme_remove_text_shipping_billing_checkout_notice', 99, 3 );
function dk_theme_remove_text_shipping_billing_checkout_notice( $translation, $text, $context ) {
	if( $context == 'checkout-validation' && ($text == "Shipping %s" || $text == "Billing %s") ) {
		$translation = "%s";
	}
	return $translation;
}


add_action( 'woocommerce_new_order', 'dk_theme_woocommerce_new_order_action', 10, 2 );

/**
 * Function for `woocommerce_new_order` action-hook.
 *
 * @param int       $order_id Order ID.
 * @param \WC_Order $order    Order object.
 *
 * @return void
 */
function dk_theme_woocommerce_new_order_action( $order_id, $order ) {

    $order_comments = $_POST['order_comments']?? '';
    if ($order_comments != "") {
        $order->add_order_note( $order_comments );
    }
}



/**
 * Filter to change COD name base on Order Type
 * When Order type is Delivery, COD will be Pay on Delivery
 * When Order type is Pickup, COD will be named Pay on pickup
 */
function dk_theme_change_gateway_name($title, $gateway_id)
{
	if (is_admin()) {
		return $title;
	}

	if ($gateway_id == 'cod') {
		$ship_to_radio = WC()->session->get('ship_to_radio', 'pickup');
		if ($ship_to_radio == "delivery") {
			$title = "Pay on Delivery";
		}

		if ($ship_to_radio == "pickup") {
			$title = "Pay on Pickup";
		}
	}

	return $title;
}
add_filter('woocommerce_gateway_title', 'dk_theme_change_gateway_name', 30, 2);



/**
 * Remove hook that use for remove COD on delivery
 * Because this site use COD for Pickup and Delivery
 */
function dk_theme_allow_cod_payment_for_delivery() {
	global $wooOverrideCheckout;
	if ($wooOverrideCheckout) {
		remove_action('woocommerce_available_payment_gateways', [$wooOverrideCheckout, 'unset_cod_on_not_pickup']);	
		remove_action('woocommerce_available_payment_gateways', [$wooOverrideCheckout, 'get_available_payment_by_shipping_method']);	
		remove_action('woocommerce_after_checkout_validation', [$wooOverrideCheckout, 'validate_checkout_flow'], 21);	
	}
}

add_action('init', 'dk_theme_allow_cod_payment_for_delivery');


/**
 * Remove COD (Pay on Delivery) on Location Chemong Outpost (1154 Chemong Rd) when select Delivery
 */
function dk_theme_custom_checkout_gateways( $available_gateways ) {
	if ( WC()->session ) {

		$ship_to_radio = WC()->session->get( 'ship_to_radio', 'delivery' );
		global $bsCurrentLocationSlug;

		if ( $ship_to_radio == 'delivery' && $bsCurrentLocationSlug  == "chemong-outpost") {
			foreach ( $available_gateways as $key => $value ) {
				if ( $key == 'cod' ) {
					unset( $available_gateways[ $key ] );
				}
			}
		}

		// Remove Card payment on some locations base on requirement of client
		$list_location_dont_use_card_for_delivery = ['white-oaks-outpost', 'bell-city-outpost', 'elliot-lake-outpost', 'rose-city-outpost'];
		if ( $ship_to_radio == 'delivery' && in_array($bsCurrentLocationSlug, $list_location_dont_use_card_for_delivery)) {
			foreach ( $available_gateways as $key => $value ) {
				if ( $key == 'breadstack-moneris' ) {
					unset( $available_gateways[ $key ] );
				}
			}
		}
	}

	return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'dk_theme_custom_checkout_gateways' );



add_action( 'woocommerce_init', function() {
	if ( ! class_exists( 'BETPG_Email_Transfer_Gateway' ) ) {
		return;
	}

	$gateways = WC()->payment_gateways()->payment_gateways();
	foreach ( $gateways as $gateway ) {
		if ( $gateway instanceof BETPG_Email_Transfer_Gateway ) {
			remove_action( 'woocommerce_thankyou', [ $gateway, 'thankyou_page' ], 1 );
			add_action( 'woocommerce_thankyou_betpg', [ $gateway, 'thankyou_page' ], 1 );
		}
	}
}, 20 );
