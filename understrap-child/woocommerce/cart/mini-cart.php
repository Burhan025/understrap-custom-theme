<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
	<div class="mini-cart-header">
		<?php /* translators: %d is the number of items in the cart */ ?>
		<h3><?php echo esc_html( sprintf( __( 'Your cart %d Items', 'woocommerce' ), WC()->cart->get_cart_contents_count() ) ); ?></h3>
		<button type="button" class="mini-cart-close" aria-label="<?php esc_attr_e( 'Close cart', 'woocommerce' ); ?>">×</button>
	</div>
	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				// Use parent product title for variations
				$is_variation = $_product->is_type( 'variation' );
				// Show regular + sale
				if ( $_product->is_on_sale() ) {
					$regular = wc_get_price_to_display( $_product, [ 'price' => $_product->get_regular_price() ] );
					$sale    = wc_get_price_to_display( $_product, [ 'price' => $_product->get_sale_price() ] );
					$unit_price_html = '<span class="price"><del>' . wc_price( $regular ) . '</del> <ins>' . wc_price( $sale ) . '</ins></span>';
				} else {
					$unit_price_html = '<span class="price"><ins>' . WC()->cart->get_product_price( $_product ) . '</ins></span>';
				}
				?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="mini-cart-item-column image-column">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									/* translators: %s is the product name */
									esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $cart_item_key ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</div>
						<?php
							if ( $_product->is_type( 'variation' ) ) {
								$var_text = '';
								$loc_text = '';

								// Pull raw attributes from the cart item
								$va = isset( $cart_item['variation'] ) ? $cart_item['variation'] : array();

								// Variation name
								if ( ! empty( $va['attribute_variations'] ) ) {
									$var_text = apply_filters( 'woocommerce_variation_option_name', $va['attribute_variations'] );
								}

								// Location
								if ( ! empty( $va['attribute_pa_locations'] ) ) {
									$slug = $va['attribute_pa_locations'];
									$term = get_term_by( 'slug', $slug, 'pa_locations' );
									$loc_text = $term ? $term->name : $slug;
								}

								if ( $var_text ) echo '<div class="mini-cart-variation-name"><strong>' . esc_html__( 'Variation', 'woocommerce' ) . ':</strong> ' . esc_html( $var_text ) . '</div>';
								if ( $loc_text ) echo '<div class="mini-cart-location"><strong>' . esc_html__( 'Location', 'woocommerce' ) . ':</strong> ' . esc_html( $loc_text ) . '</div>';

							// echo wc_get_formatted_cart_item_data
							} else {
								echo wc_get_formatted_cart_item_data( $cart_item );
							}
						?>
						<div class="mini-cart-item-details-bottom">

							<div class="quantity-wrapper">
								<?php
								if ( $_product->is_sold_individually() ) {
									$min_quantity = 1;
									$max_quantity = 1;
								} else {
									$min_quantity = 0;
									$max_quantity = $_product->get_max_purchase_quantity();
								}

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'             => "cart[{$cart_item_key}][qty]",
										'input_value'            => $cart_item['quantity'],
										'max_value'              => $max_quantity,
										'min_value'              => $min_quantity,
										'product_name'           => $product_name,
										'apply_max_restrictions' => true,
									),
									$_product,
									false
								);

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
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
			<?php
			/**
			 * Hook: woocommerce_widget_shopping_cart_total.
			 *
			 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
			 */
			do_action( 'woocommerce_widget_shopping_cart_total' );
			?>
		</p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<a class="continue-shopping-btn btn btn-outline-primary" href="javascript:void(0)"><i class="fa fa-arrow-left"></i> <?php esc_html_e( 'CONTINUE SHOPPING', 'woocommerce' ); ?></a>

		<div class="woocommerce-mini-cart__buttons buttons">
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout-button">
				<?php esc_html_e( 'Checkout Now', 'woocommerce' ); ?>
			</a>
		</div>

		<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>
	</div>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></p>
	<a class="continue-shopping-btn btn btn-outline-primary" href="javascript:void(0)"><i class="fa fa-arrow-left"></i> <?php esc_html_e( 'CONTINUE SHOPPING', 'woocommerce' ); ?></a>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
