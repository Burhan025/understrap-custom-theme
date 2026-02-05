<?php
add_filter( 'woocommerce_get_breadcrumb', 'dk_theme_order_received_woocommerce_breadcrumb', 20, 2 );
function dk_theme_order_received_woocommerce_breadcrumb( $crumbs, $breadcrumb ) {
	if ( is_order_received_page() ) {
		$new_crumbs = array(
			array( 'Home', home_url() ),
			array( 'Shop', get_permalink( wc_get_page_id( 'shop' ) ) ),
			array( 'Order received', '' )
		);

		return $new_crumbs;
	}

	return $crumbs;
}
