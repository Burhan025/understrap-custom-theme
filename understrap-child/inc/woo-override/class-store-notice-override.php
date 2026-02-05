<?php
/**
 * Checkout override
 *
 * @package BurhanAftab\UnderstrapChild\WooOverride
 */

namespace BurhanAftab\UnderstrapChild\WooOverride;

/**
 * Store notice override
 */
class Store_Notice_Override {
	/**
	 * WooCommerce option key for store notice status
	 *
	 * @var string
	 */
	const WOO_DEMO_STORE_FILTER = 'woocommerce_demo_store';

	/**
	 * WooCommerce option key for store notice text
	 *
	 * @var string
	 */
	const WOO_DEMO_STORE_NOTICE = 'woocommerce_demo_store_notice';

	/**
	 * Template path for the notice bar
	 *
	 * @var string
	 */
	const NOTICE_TEMPLATE_PATH = 'templates/notice-bar';

	/**
	 * Param name for the notice text
	 *
	 * @var string
	 */
	const PARAM_NOTICE_TEXT = 'notice_text';

	/**
	 * Cookie name for dismissed notice
	 *
	 * @var string
	 */
	const NOTICE_DISMISSED_COOKIE = 'wordpress_notice_dismissed';

	/**
	 * Constructor
	 */
	public function init() {
		add_filter( self::WOO_DEMO_STORE_FILTER, array( $this, 'noticebar_override' ) );
		remove_action('wp_footer', self::WOO_DEMO_STORE_FILTER, 10);
		add_action('wp_body_open', self::WOO_DEMO_STORE_FILTER, 10);
	}

    /**
     * Override the store notice to use our custom template
     *
     * @param string $notice_html Original notice HTML.
     * @return string Modified notice HTML.
     */
	public function noticebar_override( $notice_html ) {
		if ( ! $this->should_show_notice() ) {
			return '';
		}

		return $this->render_notice_template( $this->get_notice_text() );
	}

	/**
	 * Determine if notice should be shown based on all conditions
	 *
	 * @return boolean True if notice should be shown, false otherwise
	 */
	private function should_show_notice() {
		if ( ! $this->is_notice_enabled() ) {
			return false;
		}

		if ( empty( $this->get_notice_text() ) ) {
			return false;
		}

		if ( isset( $_COOKIE[self::NOTICE_DISMISSED_COOKIE] ) && $_COOKIE[self::NOTICE_DISMISSED_COOKIE] === 'true' ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the store notice is enabled in WooCommerce settings
	 *
	 * @return boolean True if notice is enabled, false otherwise
	 */
	private function is_notice_enabled() {
		return 'yes' === get_option( self::WOO_DEMO_STORE_FILTER );
	}

	/**
	 * Get the notice text from WooCommerce settings
	 *
	 * @return string Notice text
	 */
	private function get_notice_text() {
		return get_option( self::WOO_DEMO_STORE_NOTICE, '' );
	}

	/**
	 * Render the notice template with the provided text
	 *
	 * @param string $notice_text Text to display in the notice
	 * @return string Rendered notice HTML
	 */
	private function render_notice_template( $notice_text ) {
		ob_start();
		get_template_part( self::NOTICE_TEMPLATE_PATH, null, array( self::PARAM_NOTICE_TEXT => $notice_text ) );
		return ob_get_clean();
	}
}
