<?php
/**
 * Override understrap parent theme functions.
 *
 * @package BurhanAftab\UnderstrapChild
 */

namespace BurhanAftab\UnderstrapChild;

/**
 * Understrap override.
 */
class Understrap_Override {
	/**
	 * Constructor
	 */
	public function init() {
		add_filter( 'theme_mod_understrap_bootstrap_version', array( $this, 'bootstrap_version' ), 20 );
		add_action( 'init', array( $this, 'remove_woocommerce_wrapper' ) );
		add_filter( 'theme_page_templates', array( $this, 'remove_page_templates' ), 10 );

		add_action( 'init', array( $this, 'override_excerpt_more' ) );

		$this->override_customizer_settings();
	}

	/**
	 * Overrides the theme_mod to default to Bootstrap 5
	 *
	 * This function uses the `theme_mod_{$name}` hook and
	 * can be duplicated to override other theme settings.
	 *
	 * @return string
	 */
	public function bootstrap_version() {
		return 'bootstrap5';
	}

	/**
	 * Remove woocommerce wrapper
	 */
	public function remove_woocommerce_wrapper() {
		remove_action( 'woocommerce_before_main_content', 'understrap_woocommerce_wrapper_start', 10 );
		remove_action( 'woocommerce_after_main_content', 'understrap_woocommerce_wrapper_end', 10 );
	}

	/**
	 * Remove unused page templates
	 *
	 * @param array $post_templates Post templates registered in the theme.
	 * @return array
	 */
	public function remove_page_templates( $post_templates ) {
		$templates_to_hide = array(
			'page-templates/blank.php'             => 'Blank Page Template',
			'page-templates/both-sidebarspage.php' => 'Left and Right Sidebar Layout',
			'page-templates/empty.php'             => 'Empty Page Template',
			'page-templates/fullwidthpage.php'     => 'Full Width Page',
			'page-templates/left-sidebarpage.php'  => 'Left Sidebar Layout',
			'page-templates/no-title.php'          => 'No Title, Full Width Page',
			'page-templates/right-sidebarpage.php' => 'Right Sidebar Layout',
		);

		foreach ( $templates_to_hide as $file => $label ) {
			unset( $post_templates[ $file ] );
		}

		return $post_templates;
	}

	/**
	 * Remove the excerpt more link
	 */
	public function override_excerpt_more() {
		remove_filter( 'wp_trim_excerpt', 'understrap_all_excerpts_get_more_link' );
		remove_filter( 'excerpt_more', 'understrap_custom_excerpt_more' );
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
	}

	/**
	 * Remove the excerpt more link
	 *
	 * @param string $more The excerpt more link.
	 * @return string
	 */
	public function excerpt_more( $more ) { // phpcs:ignore
		return '...';
	}

	/**
	 * Unregister customizer settings.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 */
	public function unregister_customizer_settings( $wp_customize ) {
		// Remove controls.
		$wp_customize->remove_control( 'understrap_bootstrap_version' );
		$wp_customize->remove_control( 'understrap_container_type' );
		$wp_customize->remove_control( 'understrap_navbar_type' );
		$wp_customize->remove_control( 'understrap_sidebar_position' );

		// Optionally remove the settings too.
		$wp_customize->remove_setting( 'understrap_bootstrap_version' );
		$wp_customize->remove_setting( 'understrap_container_type' );
		$wp_customize->remove_setting( 'understrap_navbar_type' );
		$wp_customize->remove_setting( 'understrap_sidebar_position' );
	}

	/**
	 * Initialize customizer settings overrides.
	 */
	public function override_customizer_settings() {
		add_action( 'customize_register', array( $this, 'unregister_customizer_settings' ), 20 );

		add_filter(
			'theme_mod_understrap_bootstrap_version',
			static function () {
				return 'bootstrap5';
			}
		);

		add_filter(
			'theme_mod_understrap_container_type',
			static function () {
				return 'container';
			}
		);

		add_filter(
			'theme_mod_understrap_navbar_type',
			static function () {
				return 'collapse';
			}
		);

		add_filter(
			'theme_mod_understrap_sidebar_position',
			static function () {
				return 'none';
			}
		);
	}
}
