<?php
/**
 * Carousel component
 *
 * @package BurhanAftab\UnderstrapChild\Components
 */

namespace BurhanAftab\UnderstrapChild\Components;

/**
 * Carousel class
 */
class Carousel {
	/**
	 * Default options for the carousel
	 *
	 * @var array
	 */
	protected array $default_options;

	/**
	 * Constructor for the Carousel class
	 *
	 * @param array $default_options Default options.
	 */
	public function __construct( array $default_options = array() ) {
		$this->default_options = wp_parse_args(
			$default_options,
			array(
				'duration'        => 200,
				'per_page'        => 'auto',
				'per_page_mobile' => 2,
				'draggable'       => 'true',
				'snap'            => 'true',
				'dots'            => 'true',
				'arrows'          => 'false',
				'className'       => '',
				'item_width'      => 300,
			)
		);
	}

	/**
	 * Render the carousel
	 *
	 * @param array $product_ids Product IDs.
	 * @param array $options     Options.
	 */
	public function render( array $product_ids, array $options = array() ): void {
		if ( empty( $product_ids ) ) {
			return;
		}

		$options = wp_parse_args( $options, $this->default_options );
		?>

		<div data-aos="fade-up" class="product-carousel-wrapper <?php echo esc_attr( $options['className'] ); ?>"
			data-duration="<?php echo esc_attr( $options['duration'] ); ?>"
			data-per-page="<?php echo esc_attr( $options['per_page'] ); ?>"
			data-per-page-mobile="<?php echo esc_attr( $options['per_page_mobile'] ); ?>"
			data-draggable="<?php echo esc_attr( $options['draggable'] ); ?>"
			data-snap="<?php echo esc_attr( $options['snap'] ); ?>"
			data-dots="<?php echo esc_attr( $options['dots'] ); ?>"
			data-arrows="<?php echo esc_attr( $options['arrows'] ); ?>"
			data-item-width="<?php echo esc_attr( $options['item_width'] ); ?>"
		>
			<?php
			woocommerce_product_loop_start();
			foreach ( $product_ids as $product_id ) {
				$post_object = get_post( $product_id );
				if ( $post_object ) {
					$GLOBALS['post'] = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post_object );
					wc_get_template_part( 'content', 'product' );
				}
			}
			woocommerce_product_loop_end();
			?>
		</div>

		<?php
		wp_reset_postdata();
	}
}
