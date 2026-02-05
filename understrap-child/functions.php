<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', __DIR__ . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );

require_once __DIR__ . '/inc/class-plugin.php';
require_once __DIR__ . '/inc/cpt/index.php';

/**
 * Handling ACF; see RCDC-239.
 */
// require_once __DIR__ . '/acf/acf.php'; // Original location
require_once __DIR__ . '/inc/site/acf/acf.php'; // New Location

( new BurhanAftab\UnderstrapChild\Plugin() )->init();

/**
 * Include 'function-parts' and' this-site' functions
 */
include_once __DIR__ . '/inc/function-parts/aos.php';
include_once __DIR__ . '/inc/site/functions/this-site-functions.php';
include_once __DIR__ . '/inc/function-parts/contact-form-7.php';
