<?php
/**
 * Your functions here.
 */
use BurhanAftab\Deepknead\Deepknead;
global $dk_current_location_store;
if (class_exists('\BurhanAftab\Deepknead\Deepknead')) {
	$location = Deepknead::instance()->locationDataProvider()->currentLocation();
	if ($location && method_exists($location, 'name')) {
		$dk_current_location_store = $location;
	}
}


include_once __DIR__ . '/helper-functions.php';

include_once __DIR__ . '/register-assets.php';
include_once __DIR__ . '/class-acf-autoload-blocks.php';
include_once __DIR__ . '/class-acf-create-block-cli.php';

include_once __DIR__ . '/common/acf-fallbacks.php';
include_once __DIR__ . '/common/cf7.php';
include_once __DIR__ . '/common/current-store-shortcode.php';
include_once __DIR__ . '/common/show-content-by-location.php';
include_once __DIR__ . '/common/woocommerce-breadcrumbs.php';
include_once __DIR__ . '/common/woocommerce-cart.php';
include_once __DIR__ . '/common/woocommerce-checkout.php';
include_once __DIR__ . '/common/woocommerce-ordertip.php';
include_once __DIR__ . '/common/woocommerce-emails.php';
include_once __DIR__ . '/common/woocommerce-myaccount.php';
include_once __DIR__ . '/common/woocommerce-order.php';
include_once __DIR__ . '/common/woocommerce-product-single.php';
include_once __DIR__ . '/common/woocommerce-shop.php';
include_once __DIR__ . '/common/wordpress-speculative-loading.php';
include_once __DIR__ . '/common/setup-theme.php';

include_once __DIR__ . '/setup-theme.php';
include_once __DIR__ . '/setup-agegate.php';
include_once __DIR__ . '/setup-templates.php';
include_once __DIR__ . '/setup-pages.php';
//include_once __DIR__ . '/setup-woocommerce.php';
include_once __DIR__ . '/setup-shop-page.php';
include_once __DIR__ . '/setup-shop-page-filter.php';
include_once __DIR__ . '/setup-product-page.php';
include_once __DIR__ . '/setup-cart-page.php';
include_once __DIR__ . '/setup-checkout-page.php';
//include_once __DIR__ . '/setup-order-completed-page.php';
include_once __DIR__ . '/setup-myaccount-page.php';
//include_once __DIR__ . '/setup-blog-page.php';
include_once __DIR__ . '/setup-current-location.php';
include_once __DIR__ . '/setup-woocommerce-email.php';
include_once __DIR__ . '/setup-shop-filter.php';

include_once __DIR__ . '/custom-plugin-hooks.php';



