<?php

if ( ! class_exists( 'Wpcot_Frontend' ) ) {
	return;
}

add_action( 'woocommerce_cart_calculate_fees', 'dk_theme_manage_order_tips_based_on_shipping', 5 );
add_action( 'wp', 'dk_theme_manage_order_tips_based_on_shipping', 5 );
function dk_theme_manage_order_tips_based_on_shipping() {
	if ( ! ( is_checkout() || is_cart() || is_order_received_page() ) ) {
		return;
	}

	$wc_session    = WC()->session;
	$ship_to_radio = $wc_session->get( 'ship_to_radio', 'pickup' );

	if ( is_cart() || is_order_received_page() ) {
		$wc_session->set( 'wpcot_tips_backup', [] );
		$_SESSION['wpcot_tips_backup'] = [];
		// Clear all order tips
		$wc_session->set( 'wpcot_tips', [] );
		$_SESSION['wpcot_tips'] = serialize( [] );
	} else {
		if ( $ship_to_radio === 'pickup' || $ship_to_radio === 'mail_order' ) {
			// Store current tips as backup before removing
			$current_tips = $wc_session->get( 'wpcot_tips' );
			if ( $current_tips ) {
				if ( isset( $_POST['post_data'] ) ) {
					parse_str( $_POST['post_data'], $post_data );
					if ( is_array( $post_data ) && isset( $post_data['change_value_ship_to'] ) && $post_data['change_value_ship_to'] == "change" ) {
						$wc_session->set( 'wpcot_tips_backup', $current_tips );
					}
				}
			}

			// Clear all order tips
			$wc_session->set( 'wpcot_tips', [] );
			$_SESSION['wpcot_tips'] = serialize( [] );

		} else {
			// Restore previous tips or set default tips
			$current_tips = $wc_session->get( 'wpcot_tips' );

			if ( empty( $current_tips ) ) {
				// Try to restore from backup first
				$backup_tips = $wc_session->get( 'wpcot_tips_backup' );

				if ( $backup_tips ) {
					// Restore previous tips
					$wc_session->set( 'wpcot_tips', $backup_tips );
					$_SESSION['wpcot_tips'] = serialize( $backup_tips );

					$wc_session->set( 'wpcot_tips_backup', [] );
					$_SESSION['wpcot_tips_backup'] = [];
				} else {
					// Apply default tips
					$all_tips     = Wpcot_Helper()->get_tips( 'apply' );
					$default_tips = [];

					foreach ( $all_tips as $key => $tip ) {
						if ( ! empty( $tip['default'] ) ) {
							$default_tips[ $key ] = [
								'value' => $tip['default']
							];
						}
					}

					if ( ! empty( $default_tips ) ) {
						$wc_session->set( 'wpcot_tips', $default_tips );
						$_SESSION['wpcot_tips'] = serialize( $default_tips );
					}
				}
			}
		}
	}
}

// Also handle AJAX updates for dynamic shipping changes
add_action( 'wc_ajax_update_order_review', 'dk_theme_manage_order_tips_on_ajax_update', 15 );
function dk_theme_manage_order_tips_on_ajax_update() {
	dk_theme_manage_order_tips_based_on_shipping();
}

add_filter( 'woocommerce_update_order_review_fragments', function ( $fragments ) {
	// Add the additional classes to the existing wpcot-tips class
	$wc_session    = WC()->session;
	$ship_to_radio = $wc_session->get( 'ship_to_radio', 'pickup' );

	if ( $ship_to_radio === 'pickup' || $ship_to_radio === 'mail_order' ) {
		$fragments['.wpcot-tips'] = str_replace( 'class="wpcot-tips wpcot-btn-', 'class="wpcot-tips shipto-pickup-hidden d-none hidden wpcot-btn-', $fragments['.wpcot-tips'] );
	}

	return $fragments;
} );
