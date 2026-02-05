<?php
/**
 * Sidebar
 *
 * @package BurhanAftab\UnderstrapChild
 */

namespace BurhanAftab\UnderstrapChild;

/**
 * Sidebar
 */
class Sidebar {
	/**
	 * Constructor
	 */
	public function init() {
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}

	/**
	 * Register sidebar
	 */
	public function register_sidebars() {
		register_sidebar(
			array(
				'name'          => __( 'Shop Sidebar', 'understrap-child' ),
				'id'            => 'sidebar-shop',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
