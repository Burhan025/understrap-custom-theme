<?php
/**
 * Shortcode Interface
 *
 * @package BurhanAftab\UnderstrapChild\Shortcodes
 */

namespace BurhanAftab\UnderstrapChild\Shortcodes;

// phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound

/**
 * Shortcode Interface
 */
interface Shortcode_Interface {
	/**
	 * Get shortcode name
	 *
	 * @return string
	 */
	public static function get_shortcode_name(): string;

	/**
	 * Output
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string Shortcode output.
	 */
	public function output( array $atts = array(), ?string $content = null ): string;
}

/**
 * Base Shortcode
 */
abstract class Base_Shortcode implements Shortcode_Interface {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( static::get_shortcode_name(), array( $this, 'output' ) );
	}

	/**
	 * Default output
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string Shortcode output.
	 */
	public function output( array $atts = array(), ?string $content = null ): string {
		return '';
	}
}
