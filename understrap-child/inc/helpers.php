<?php
/**
 * Cart Totals Shipping HTML
 */
function bs_cart_totals_shipping_html() {
	$packages             = WC()->shipping()->get_packages();
	$first                = true;
	$user_pickup_location = isset( $_COOKIE['user_selected_location'] ) ? stripslashes( $_COOKIE['user_selected_location'] ) : '';
	$user_pickup_location = $user_pickup_location != '' ? json_decode( $user_pickup_location, true ) : array();

	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

		// check amd replace chosen_method by method shipping get from cookie
		if ( ! empty( $user_pickup_location ) ) {
			if ( $user_pickup_location['shipping'] == 'delivery' ) {
				if ( $chosen_method == 'bsof_local_shipping' ) {
					$chosen_method = 'bsof_local_shipping';
				} elseif ( ( stripos( WC()->session->chosen_shipping_methods[ $i ], 'cantec_delivery' ) !== false ) ) {
					$chosen_method = WC()->session->chosen_shipping_methods[ $i ];
				}
			} elseif ( $user_pickup_location['shipping'] == 'pick_up_in_store' ) {
				$chosen_method = '';
				$rates         = $package['rates'];
				if ( ! empty( $rates ) ) {
					foreach ( $rates as $key => $val ) {
						if ( stripos( $key, 'cantec_local_pickup' ) !== false ) {
							$chosen_method = $key;
						}
					}
				}
			}
		}

		$product_names = array();

		if ( count( $packages ) > 1 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
			}
			$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
		}

		// check pickup
		$ship_to_radio = WC()->session->get( 'ship_to_radio', 'pickup' );

		foreach ( $package['rates'] as $key => $item ) {
			if ( $ship_to_radio == 'pickup' ) {
				if ( strpos( $key, 'pickup' ) === false ) {
					unset( $package['rates'][ $key ] );
				}
			} elseif ( $ship_to_radio == 'mail_order' ) {
				if ( strpos( $key, 'flat_rate' ) === false ) {
					unset( $package['rates'][ $key ] );
				}
			} else {
				if ( strpos( $key, 'pickup' ) !== false ) {
					unset( $package['rates'][ $key ] );
				}
				if ( strpos( $key, 'flat_rate' ) !== false ) {
					unset( $package['rates'][ $key ] );
				}
				if ( strpos( $key, 'free_shipping' ) !== false ) {
					unset( $package['rates'][ $key ] );
				}
			}
		}

		$shipping_was_selected = null;
		if ( isset( $_POST['shipping_method'] ) && isset( $_POST['shipping_method'][0] ) ) {
			$shipping_was_selected = $_POST['shipping_method'][0];
		}

		$current_method = WC()->session->get( 'chosen_shipping_methods' );
		$current_method = ! empty( $current_method[0] ) ? $current_method[0] : null;

		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );
			if ( is_array( $post_data ) && isset( $post_data['change_value_ship_to'] ) && $post_data['change_value_ship_to'] == 'change' ) {

				if ( $shipping_was_selected === null && $current_method !== null ) {
					$shipping_was_selected = $current_method;
				}

				if ( isset( $post_data['ship_to_radio'] ) && $post_data['ship_to_radio'] == 'pickup' ) {
					if ( $shipping_was_selected && mb_strpos( $shipping_was_selected, 'pickup' ) === false ) {
						$shipping_was_selected = null;
					}
				}

				if ( isset( $post_data['ship_to_radio'] ) && $post_data['ship_to_radio'] == 'delivery' && $shipping_was_selected ) {
					if ( mb_strpos( $shipping_was_selected, 'pickup' ) !== false ) {
						$shipping_was_selected = null;
					}
					if ( strpos( $shipping_was_selected, 'flat_rate' ) !== false ) {
						$shipping_was_selected = null;
					}
				}

				if ( isset( $post_data['ship_to_radio'] ) && $post_data['ship_to_radio'] == 'mail_order' && $shipping_was_selected ) {
						$shipping_was_selected = null;
				}

				if ( $shipping_was_selected && $shipping_was_selected !== $current_method ) {
					WC()->session->set( 'chosen_shipping_methods', array( $shipping_was_selected ) );
					WC()->cart->calculate_totals();
				}
			}

			if ( is_array( $post_data ) && $current_method === null ) {
				if ( isset( $post_data['ship_to_radio'] ) && $post_data['ship_to_radio'] == 'pickup' ) {
					foreach ( $package['rates'] as $key => $value ) {
						if ( mb_strpos( $key, 'pickup' ) !== false ) {
							WC()->session->set( 'chosen_shipping_methods', array( $key ) );
							WC()->cart->calculate_totals();
							break;
						}
					}
				}
			}
		}

		wc_get_template(
			'cart/cart-shipping.php',
			array(
				'package'                  => $package,
				'available_methods'        => $package['rates'],
				'show_package_details'     => count( $packages ) > 1,
				'show_shipping_calculator' => is_cart() && apply_filters( 'woocommerce_shipping_show_shipping_calculator', $first, $i, $package ),
				'package_details'          => implode( ', ', $product_names ),
				/* translators: %d: shipping package number */
				'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
				'index'                    => $i,
				'chosen_method'            => $chosen_method,
				'formatted_destination'    => WC()->countries->get_formatted_address( $package['destination'], ', ' ),
				'has_calculated_shipping'  => WC()->customer->has_calculated_shipping(),
			)
		);

		$first = false;
	}
}
