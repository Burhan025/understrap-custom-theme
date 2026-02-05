<?php
/**
 * Mini-cart (Child theme override)
 *
 * - Phân biệt variation thật dựa trên $cart_item['variation']
 * - Nếu variation CHỈ có locations => gán class un-real-variation
 *
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
	<div class="mini-cart-header">
		<h3>
			<?php
			echo esc_html(
				sprintf(
					__( 'Your cart %d Items', 'woocommerce' ),
					WC()->cart->get_cart_contents_count()
				)
			);
			?>
		</h3>
		<button type="button" class="mini-cart-close" aria-label="<?php esc_attr_e( 'Close cart', 'woocommerce' ); ?>">×</button>
	</div>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if (
				$_product &&
				$_product->exists() &&
				$cart_item['quantity'] > 0 &&
				apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key )
			) {

				$product_name      = wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

				// Giá
				if ( $_product->is_on_sale() ) {
					$regular = wc_get_price_to_display( $_product, [ 'price' => $_product->get_regular_price() ] );
					$sale    = wc_get_price_to_display( $_product, [ 'price' => $_product->get_sale_price() ] );
					$unit_price_html = '<span class="price"><del>' . wc_price( $regular ) . '</del> <ins>' . wc_price( $sale ) . '</ins></span>';
				} else {
					$unit_price_html = '<span class="price"><ins>' . WC()->cart->get_product_price( $_product ) . '</ins></span>';
				}

				/**
				 * 🔥 LOGIC PHÂN BIỆT VARIATION THẬT
				 */
				$is_unreal_variation = false;

				if ( $_product->is_type( 'variation' ) && ! empty( $cart_item['variation'] ) ) {

					$variation_attrs = $cart_item['variation'];

					// Bỏ locations
					unset( $variation_attrs['attribute_pa_locations'] );
					unset( $variation_attrs['attribute_locations'] );

					// Nếu không còn attribute nào khác => chỉ là location
					if ( empty( array_filter( $variation_attrs ) ) ) {
						$is_unreal_variation = true;
					}
				}

				$li_classes = apply_filters(
					'woocommerce_mini_cart_item_class',
					'mini_cart_item',
					$cart_item,
					$cart_item_key
				);

				if ( $is_unreal_variation ) {
					$li_classes .= ' un-real-variation';
				}
				?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr( $li_classes ); ?>">

					<div class="mini-cart-item-column image-column">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo $thumbnail; ?>
							</a>
						<?php endif; ?>
					</div>

					<div class="mini-cart-item-column details-column">

						<div class="mini-cart-item-details-top">
							<h4 class="product-title">
								<?php if ( empty( $product_permalink ) ) : ?>
									<?php echo $product_name; ?>
								<?php else : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>">
										<?php echo $product_name; ?>
									</a>
								<?php endif; ?>
							</h4>

							<?php
							echo apply_filters(
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s">&times;</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $cart_item_key )
								),
								$cart_item_key
							);
							?>
						</div>

						<?php
						/**
						 * VARIATION / LOCATION
						 */
						if ( $_product->is_type( 'variation' ) ) {

							$va = $cart_item['variation'];

							// Variation name
							if ( ! $is_unreal_variation && ! empty( $va['attribute_variations'] ) ) {
								echo '<div class="mini-cart-variation-name"><strong>' .
									esc_html__( 'Variation', 'woocommerce' ) .
									':</strong> ' .
									esc_html( apply_filters( 'woocommerce_variation_option_name', $va['attribute_variations'] ) ) .
									'</div>';
							}

							// Location
							if ( ! empty( $va['attribute_pa_locations'] ) ) {
								$slug = $va['attribute_pa_locations'];
								$term = get_term_by( 'slug', $slug, 'pa_locations' );
								$loc  = $term ? $term->name : $slug;

								echo '<div class="mini-cart-location"><strong>' .
									esc_html__( 'Location', 'woocommerce' ) .
									':</strong> ' .
									esc_html( $loc ) .
									'</div>';
							}

						} else {
							echo wc_get_formatted_cart_item_data( $cart_item );
						}
						?>

						<div class="mini-cart-item-details-bottom">
							<div class="quantity-wrapper">
								<?php
								$min_quantity = $_product->is_sold_individually() ? 1 : 0;
								$max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();

								echo woocommerce_quantity_input(
									[
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $cart_item['quantity'],
										'max_value'   => $max_quantity,
										'min_value'   => $min_quantity,
									],
									$_product,
									false
								);
								?>
							</div>

							<div class="price-wrapper">
								<?php echo wp_kses_post( $unit_price_html ); ?>
							</div>
						</div>

					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<div class="mini-cart-footer">
		<p class="woocommerce-mini-cart__total total">
			<?php do_action( 'woocommerce_widget_shopping_cart_total' ); ?>
		</p>

		<a class="continue-shopping-btn btn btn-outline-primary" href="javascript:void(0)">
			<?php esc_html_e( 'CONTINUE SHOPPING', 'woocommerce' ); ?>
		</a>

		<div class="woocommerce-mini-cart__buttons buttons">
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout-button">
				<?php esc_html_e( 'Checkout Now', 'woocommerce' ); ?>
			</a>
		</div>
	</div>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
