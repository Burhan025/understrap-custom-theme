<?php
/**
 * Add the "Shop" link to breadcrumb
 */
add_filter( 'woocommerce_get_breadcrumb', 'dk_theme_add_shop_link_to_breadcrumb', 10, 2 );
function dk_theme_add_shop_link_to_breadcrumb( $crumbs, $WC_Breadcrumb ) {

	if ( is_woocommerce() && !is_shop() ) {
		$new_breadcrumb = [
			_x( 'Shop', 'breadcrumb', 'woocommerce' ),
			get_permalink( wc_get_page_id( 'shop' ) )
		];
		array_splice( $crumbs, 1, 0, [ $new_breadcrumb ] );
	}

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

/**
 * Change default delimiter on breadcrumb
 * @retun new delimiter
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'dk_theme_change_default_delimiter_on_breadcrumb', 99, 1 );
function dk_theme_change_default_delimiter_on_breadcrumb( $args ) {
	$args['delimiter'] = ' > ';
	return $args;
}
