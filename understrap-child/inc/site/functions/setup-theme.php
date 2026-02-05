<?php

add_action( 'after_setup_theme', function () {

	unregister_nav_menu( 'footer_policies' );
	unregister_nav_menu( 'footer_social' );
	unregister_nav_menu( 'footer_primary' );

	register_nav_menus(
		array(
			'footer_home' => __( 'Footer - Home', 'understrap-child' ),
			'footer_about'   => __( 'Footer - About', 'understrap-child' ),
			'footer_locations'   => __( 'Footer - Locations', 'understrap-child' ),
		)
	);

}, 20 );


add_action( 'acf/init', function () {
	acf_add_options_page( array(
		'page_title' => 'Theme Options',
		'menu_slug'  => 'theme-options',
		'redirect'   => false,
	) );
} );

add_filter( 'body_class', 'glenora_add_body_woocommerce_class', 99 );
function glenora_add_body_woocommerce_class( $classes ) {
	if ( class_exists( 'WooCommerce' ) && !in_array( 'woocommerce', $classes, true ) ) {
		$classes[] = 'woocommerce';
	}

	return $classes;
}

// Remove Perfmatters "cssused" file after all CSS has finished loading
add_action('wp_footer', function () {
	if (!is_admin() && !is_user_logged_in()) {
		?>
		<script>
			window.addEventListener('load', function () {
				// Check if Perfmatters used CSS link exists
				const usedCss = document.getElementById('perfmatters-used-css');
				if (!usedCss) return;

				// Wait a bit to make sure all async CSS files have been applied
				setTimeout(() => {
					// Remove the main stylesheet
					if (usedCss && usedCss.parentNode) {
						usedCss.parentNode.removeChild(usedCss);
						console.log('Removed stylesheet:', usedCss.href);
					}

					// Remove the preload link (if it exists)
					const preloadLink = document.querySelector('link[rel="preload"][href*="cssused"]');
					if (preloadLink && preloadLink.parentNode) {
						preloadLink.parentNode.removeChild(preloadLink);
						console.log('Removed preload link:', preloadLink.href);
					}
				}, 1500); // 1.5s delay after window load
			});
		</script>
		<?php
	}
}, 999);

// Add custom class to <body> if cookie not set
//add_filter('body_class', function ($classes) {
//	$bs_age_gate = $_COOKIE['bs_age_gate'] ?? null;
//	$user_selected_location = $_COOKIE['wordpress_user_selected_location'] ?? null;
//	$cart_location = $_COOKIE['cart_location'] ?? null;
//
//	if ($bs_age_gate === null) {
//		$classes[] = 'age-gate-missing';
//	}

//	if ($user_selected_location === null || $cart_location === null ) {
//		$classes[] = 'user-selected-location-missing';
//	}

//	return $classes;
//});
