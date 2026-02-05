<?php
add_filter( 'body_class', 'dk_theme_current_store_location_body_classes' );
function dk_theme_current_store_location_body_classes( $classes ) {
	global $dk_current_location_store;
	if (!$dk_current_location_store || !method_exists($dk_current_location_store, 'name')) {
		return $classes;
	} else {
		$location_slug = esc_html($dk_current_location_store->slug()) ?? "none";
	}

	if ( ! empty( $location_slug ) ) {
		$classes[] = 'location-' . $location_slug;
	}

	return $classes;
}

add_action( 'wp_footer', 'dk_theme_current_store_location_footer_style' );
function dk_theme_current_store_location_footer_style() {
	global $dk_current_location_store;
	if (!$dk_current_location_store || !method_exists($dk_current_location_store, 'name')) {
		return;
	} else {
		$current_store_slug = esc_html($dk_current_location_store->slug());
	}
	?>
    <style class="dk-theme-show-conditional-location">
        [class*="show-inline-on-location-"],
        [class*="show-on-location-"] {
            display: none;
        }

        .location-<?php echo $current_store_slug; ?> .show-on-location-<?php echo $current_store_slug; ?> {
            display: block;
        }
        .location-<?php echo $current_store_slug; ?> .show-inline-on-location-<?php echo $current_store_slug; ?> {
            display: inline-block;
        }
        <?php
         if( function_exists( 'is_order_received_page' ) && function_exists( 'dk_theme_get_order_location_slug' ) && (is_order_received_page() || is_view_order_page()) ) {
            global $wp;
            $order_id = $wp->query_vars['order-received'] ?? $wp->query_vars['view-order'];
            $order_location_slug = dk_theme_get_order_location_slug($order_id);
            if($order_location_slug) { ?>
                .woocommerce-order [class*="show-inline-on-location-"],
                .woocommerce-order [class*="show-on-location-"] {
                    display: none !important;
                }
                .woocommerce-order .show-on-location-<?php echo $order_location_slug; ?> {
                    display: block !important;
                }
                .woocommerce-order .show-inline-on-location-<?php echo $order_location_slug; ?> {
                    display: inline-block !important;
                }
                <?php
            }
        }
        ?>
    </style>
	<?php
}

add_action( 'woocommerce_email_before_order_table', 'dk_theme_custom_email_styles', 9, 3 );

function dk_theme_custom_email_styles( $order, $sent_to_admin, $plain_text = false ) {
	if ( ! function_exists( 'dk_theme_get_order_location_slug' ) || $sent_to_admin ) {
		return;
	}
	$order_id      = $order->get_order_number();
	$location_slug = dk_theme_get_order_location_slug( $order_id );
	if ( ! $location_slug ) {
		return;
	}

	?>
    <style>
    [class*="show-inline-on-location-"],
    [class*="show-on-location-"] {
        display: none;
    }

    .show-on-location-<?php echo $location_slug; ?> {
        display: block;
    }

    .show-inline-on-location-<?php echo $location_slug; ?> {
        display: inline-block;
    }
    </style><?php
	echo PHP_EOL;
}
