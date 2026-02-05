<?php
add_filter( 'woocommerce_package_rates', function ( $rates, $package ) {

	$mail_order_key = 'flat_rate';
	$cart_total     = WC()->cart->get_displayed_subtotal();

	foreach ( $rates as $rate_id => $rate ) {
		if (strpos($rate_id, $mail_order_key) !== false) {

			if ( $cart_total < 50 ) {
				$rates[$rate_id]->label = __( 'Mail Order (Minimum order $50)', 'dk-theme' );
			} elseif ( $cart_total >= 150 ) {
				$rates[$rate_id]->label = __( 'Mail Order: Free', 'dk-theme' );
				$rates[$rate_id]->cost = 0;
				//FIX TAX: reset shipping taxes to 0
				$rates[$rate_id]->set_taxes([]);
			} else {
				$rates[$rate_id]->label = __( 'Mail Order Flat Fee', 'dk-theme' );
			}
		}
	}

	return $rates;
}, 10, 2 );

add_action( 'woocommerce_checkout_process', function () {
	$chosen_shipping = WC()->session->get( 'chosen_shipping_methods' );
	$cart_total      = WC()->cart->get_displayed_subtotal();

	if ( isset( $chosen_shipping[0] ) && ( strpos( $chosen_shipping[0], 'flat_rate' ) !== false ) && ( $cart_total < 50 ) ) {
		wc_add_notice( __( 'Minimum order for Mail Order is $50.', 'dk-theme' ), 'error' );
	}
} );

add_action( 'init', function () {
	if ( function_exists( 'WC' ) && isset( WC()->session ) ) {
		$ship_to_radio = WC()->session->get( 'ship_to_radio', '' );
		if ( !$ship_to_radio ) {
			WC()->session->set( 'ship_to_radio', 'delivery' );
		}
	}
}, 2 );


add_filter( 'woocommerce_states', function ( $states ) {
	if ( is_cart() ) {
		$states['CA'] = array(
			'BC' => 'British Columbia',
		);
	}
	return $states;
} );

add_filter( 'woocommerce_countries_shipping_country_states', function ( $states ) {
	$states[ 'CA' ] = array( 'BC' => __( 'British Columbia', 'woocommerce' ) );
	return $states;
} );

add_action( 'wp_footer', function () {
	if ( ! is_checkout() ) {
		return;
	}
	?>
	<script>
		jQuery(document).ready(function($) {

			$(document.body).on('country_to_state_changed', function() {
				function set_shipping_state(state) {
					var $shipping_state = $('#shipping_state');
					var $shipping_state_option = $('#shipping_state option[value="' + state + '"]');
					var $shipping_state_option_no = $('#shipping_state option[value!="' + state + '"]');
					$shipping_state_option_no.remove();
					$shipping_state_option.attr('selected', true);
				}

				var $shipping_country = $('#shipping_country');

				var new_shipping_state = '';

				switch($shipping_country.val()) {
					case 'CA':
						new_shipping_state = 'BC';
						break;
				}

				if( ! $.isEmptyObject(new_shipping_state)) {
					set_shipping_state(new_shipping_state);
				}

			});

		});
	</script>
	<?php
} );

