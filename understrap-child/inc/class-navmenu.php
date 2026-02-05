<?php
/**
 * Nav menu
 *
 * @package BurhanAftab\UnderstrapChild
 */

namespace BurhanAftab\UnderstrapChild;

/**
 * Nav menu
 */
class NavMenu {
	/**
	 * Initialize the nav menu class
	 */
	public function init() {
		add_action( 'after_setup_theme', array( $this, 'register_shop_nav_menu' ) );
		add_filter( 'nav_menu_item_title', array( $this, 'apply_shortcodes' ) );
	}

	/**
	 * Apply shortcodes to the nav menu item title
	 *
	 * @param string $title The title of the nav menu item.
	 * @return string The filtered title
	 */
	public function apply_shortcodes( string $title ): string {
		return do_shortcode( $title );
	}

	/**
	 * Register the shop nav menu
	 */
	public function register_shop_nav_menu() {
		register_nav_menus(
			array(
				'shop' => __( 'Shop Menu', 'understrap-child' ),
				'footer_primary'  => __( 'Footer - Primary Menu', 'understrap-child' ),
				'footer_products' => __( 'Footer - Product Categories', 'understrap-child' ),
				'footer_policies' => __( 'Footer - Policies', 'understrap-child' ),
				'footer_social'   => __( 'Footer - Social', 'understrap-child' ),
			)
		);
	}
}
