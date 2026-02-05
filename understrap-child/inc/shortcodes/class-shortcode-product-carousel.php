<?php
/**
 * Shortcode Product Carousel
 *
 * @package BurhanAftab\UnderstrapChild\Shortcodes
 */

namespace BurhanAftab\UnderstrapChild\Shortcodes;

use BurhanAftab\UnderstrapChild\Components\Carousel;

/**
 * Shortcode Product Carousel
 */
class Shortcode_Product_Carousel extends Base_Shortcode {

	/**
	 * Carousel instance.
	 *
	 * @var Carousel
	 */
	protected $carousel;

	/**
	 * Constructor
	 *
	 * @param Carousel $carousel Carousel instance.
	 */
	public function __construct( Carousel $carousel ) {
		$this->carousel = $carousel;
		parent::__construct();
	}

	/**
	 * Get shortcode name
	 *
	 * @return string
	 */
	public static function get_shortcode_name(): string {
		return 'understrap_child_product_carousel';
	}

	/**
	 * Output
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string Shortcode output.
	 */
	public function output( array $atts = array(), ?string $content = null ): string {
		$atts = shortcode_atts(
			array(
				'product_ids'     => '',
				'duration'        => 200,
				'per_page'        => 3,
				'per_page_mobile' => 2,
				'draggable'       => 'true',
				'snap'            => 'true',
				'dots'            => 'true',
				'arrows'          => 'false',
			),
			$atts
		);

		$product_ids = array_filter( array_map( 'intval', explode( ',', $atts['product_ids'] ) ) );
		if ( empty( $product_ids ) ) {
			return '';
		}

		ob_start();
		$this->carousel->render( $product_ids, $atts );
		return ob_get_clean();
	}
}
