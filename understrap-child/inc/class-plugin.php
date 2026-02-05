<?php
/**
 * Plugin
 *
 * @package BurhanAftab\UnderstrapChild
 */

namespace BurhanAftab\UnderstrapChild;

/**
 * Plugin
 */
class Plugin {
	/**
	 * Init
	 */
	public function init() {
		$this->init_modules();
		$this->load_shortcodes();
	}

	/**
	 * Initialize modules
	 */
	private function init_modules() {

		require_once __DIR__ . '/utils.php';
		require_once __DIR__ . '/helpers.php';
		require_once __DIR__ . '/class-enqueue.php';
		require_once __DIR__ . '/class-navmenu.php';
		require_once __DIR__ . '/class-sidebar.php';
		require_once __DIR__ . '/woo-override/class-content-product-override.php';
		require_once __DIR__ . '/woo-override/class-shop-override.php';
		require_once __DIR__ . '/woo-override/class-checkout-override.php';
		require_once __DIR__ . '/woo-override/class-store-notice-override.php';
		require_once __DIR__ . '/class-understrap-override.php';

		( new Enqueue() )->init();
		( new NavMenu() )->init();
		( new Sidebar() )->init();
		global $wooOverrideContentProduct, $wooOverrideCheckout, $wooOverrideShop;
		$wooOverrideContentProduct = new WooOverride\Content_Product_Override();
		$wooOverrideContentProduct->init();
		$wooOverrideShop = new WooOverride\Shop_Override();
		$wooOverrideShop->init();
		$wooOverrideCheckout = new WooOverride\Checkout_Override();
		$wooOverrideCheckout->init();
		( new WooOverride\Store_Notice_Override() )->init();
		( new Understrap_Override() )->init();

	}

	/**
	 * Load shortcodes
	 */
	private function load_shortcodes() {
		require_once __DIR__ . '/shortcodes/class-main-shortcodes.php';
	}
}
