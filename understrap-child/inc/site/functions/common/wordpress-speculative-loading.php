<?php
add_filter( 'wp_speculation_rules_href_exclude_paths', 'dk_theme_fix_speculative_loading' );
function dk_theme_fix_speculative_loading( array $exclude_paths ): array {
	$exclude_paths[] = '/checkout/*';
	$exclude_paths[] = '/my-account/*';

	return $exclude_paths;
}

add_filter('woocommerce_cart_redirect_after_error', function ($url) {
	$url = add_query_arg( 'add-to-cart-error', '' , $url );
	return $url;
}, 99);
