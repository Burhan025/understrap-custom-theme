<?php
/**
 * Shop override
 *
 * @package BurhanAftab\UnderstrapChild\WooOverride
 */

namespace BurhanAftab\UnderstrapChild\WooOverride;

/**
 * Content product override
 */
class Shop_Override {
	/**
	 * Constructor
	 */
	public function init() {
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'bs_cart_count_fragments' ) );
		add_shortcode( 'bs_cart_icon', array( $this, 'cart_icon_shortcode' ) );
		add_action( 'wp_ajax_update_mini_cart_quantity', array( $this, 'handle_mini_cart_quantity_update' ) );
		add_action( 'wp_ajax_nopriv_update_mini_cart_quantity', array( $this, 'handle_mini_cart_quantity_update' ) );
	}

	/**
	 * Cart count fragments
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public function bs_cart_count_fragments( $fragments ) {
		ob_start();
		$this->cart_icon();
		$fragments['.cart-icon'] = ob_get_clean();

		ob_start();
		echo '<div class="widget_shopping_cart_content">';
		woocommerce_mini_cart();
		echo '</div>';
		$fragments['.widget_shopping_cart_content'] = ob_get_clean();

		return $fragments;
	}

	/**
	 * Cart icon shortcode
	 */
	public function cart_icon_shortcode() {
		ob_start();
		$this->cart_icon();
		$this->mini_cart();
		return ob_get_clean();
	}

	/**
	 * Mini cart markup
	 */
	public function mini_cart() {
		?>
		<div id="cart-popup">
			<div class="cart-popup-banner"></div>
			<div class="cart-popup-inner">
				<div class="widget_shopping_cart">
					<div class="widget_shopping_cart_content">
						<?php woocommerce_mini_cart(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Cart icon markup
	 */
	public function cart_icon() {
		?>
		<div class="cart-icon">
			<?php $cart_count = WC()->cart->get_cart_contents_count(); ?>
			<a
				href="<?php echo esc_url( wc_get_cart_url() ); ?>"
				<?php if ( $cart_count > 0 ) : ?>
				data-cart-count="<?php echo esc_attr( $cart_count ); ?>"
				<?php endif; ?>
				>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M16.5463 13C17.2963 13 17.9563 12.59 18.2963 11.97L21.8763 5.48C22.2463 4.82 21.7663 4 21.0063 4H6.20628L5.26628 2H1.99628V4H3.99628L7.59628 11.59L6.24628 14.03C5.51628 15.37 6.47628 17 7.99628 17H19.9963V15H7.99628L9.09628 13H16.5463ZM7.15628 6H19.3063L16.5463 11H9.52628L7.15628 6ZM7.99628 18C6.89628 18 6.00628 18.9 6.00628 20C6.00628 21.1 6.89628 22 7.99628 22C9.09628 22 9.99628 21.1 9.99628 20C9.99628 18.9 9.09628 18 7.99628 18ZM17.9963 18C16.8963 18 16.0063 18.9 16.0063 20C16.0063 21.1 16.8963 22 17.9963 22C19.0963 22 19.9963 21.1 19.9963 20C19.9963 18.9 19.0963 18 17.9963 18Z" fill="#0D0D0B"/>
				</svg>
			</a>
		</div>
		<?php
	}

	/**
	 * Handle mini-cart quantity updates via AJAX
	 */
	public function handle_mini_cart_quantity_update() {
		$cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
		$quantity      = intval( $_POST['quantity'] );

		if ( $cart_item_key && $quantity > 0 ) {
			WC()->cart->set_quantity( $cart_item_key, $quantity );

			// Get updated cart fragments
			$data = array(
				'fragments' => apply_filters(
					'woocommerce_add_to_cart_fragments',
					array()
				),
				'cart_hash' => WC()->cart->get_cart_hash(),
			);

			wp_send_json( $data );
		}

		wp_die();
	}
}
