<?php
/**
 * Main shortcodes
 *
 * @package BurhanAftab\UnderstrapChild\Shortcodes
 */

namespace BurhanAftab\UnderstrapChild\Shortcodes;

use BurhanAftab\UnderstrapChild\Components\Carousel;

/**
 * Main shortcodes
 */
class Main_Shortcodes {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_shortcodes();
	}

	/**
	 * Load shortcodes
	 */
	private function load_shortcodes() {
		require_once __DIR__ . '/class-base-shortcode.php';
		require_once __DIR__ . '/class-shortcode-product-carousel.php';
		require_once __DIR__ . '/../components/class-carousel.php';

		new Shortcode_Product_Carousel( new Carousel() );
	}
}

new Main_Shortcodes();
