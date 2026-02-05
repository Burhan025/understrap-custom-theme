<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
$chosen_methods           = WC()->session->get( 'chosen_shipping_methods', array() );
$canada_post_description  = get_theme_mod( 'cannada_post_popup_description_content' );
?>
<tr class="woocommerce-shipping-totals shipping">
	<th><h4>Delivery</h4></th>
	<td data-title="<?php echo esc_attr( $package_name ); ?>">
		<?php if ( $available_methods ) : ?>
			<ul id="shipping_method" class="woocommerce-shipping-methods">
				<?php
				$canada_post_count = 0;
				foreach ( $available_methods as $method ) :
					?>
					<?php
					$dataShippingMethod = '';
					if ( $method->id == 'bsof_local_shipping'
						|| ( stripos( $method->id, 'canada_post' ) !== false )
						|| ( stripos( $method->id, 'cantec_delivery' ) !== false )
						|| ( stripos( $method->id, 'breadstack_canfleet' ) !== false )
					) {
						$dataShippingMethod = 'delivery';
					} else {
						$dataShippingMethod = 'pick_up_in_store';
					}
					// Check to count Canada Post method
					if ( $method->method_id == 'canada_post' ) {
						++$canada_post_count;
					}
					if ( $canada_post_count == 1 ) {
						echo '<li class="canada_post_group_title"><h3>' . esc_html( 'Mail Order', 'woocommerce' ) . '</h3></li>';
					}
					?>
					<li class="woocommerce-shipping-method woocommerce_shipping_method_<?php echo esc_attr( sanitize_title( $method->id ) ); ?>
					<?php
					if ( ( 1 < count( $available_methods ) ) && ( $method->id == $chosen_methods[0] ) ) {
						echo 'current-method';}
					?>
					">
						<?php
						if ( 1 < count( $available_methods ) ) {
							printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s data-shipping-method="%5$s" data-chosen_methods="%6$s" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_methods[0], false ), $dataShippingMethod, $chosen_methods[0] ); // WPCS: XSS ok.
						} else {
							printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" data-shipping-method="%4$s" data-chosen_methods="%5$s" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), $dataShippingMethod, $chosen_methods[0] ); // WPCS: XSS ok.
						}
						if ( $method->method_id == 'canada_post' ) {
							printf(
								'<label for="shipping_method_%1$s_%2$s">%3$s</label>',
								$index,
								esc_attr( sanitize_title( $method->id ) ),
								wc_cart_totals_shipping_method_label( $method )
							); // WPCS: XSS ok.
							if ( $method->id == $chosen_method ) {
								echo '<p><a href="#canada_post_popup" class="btn-canada-post-popup" data-mfp-src="#canada_post_popup" data-open="#canada_post_popup" data-close="close" data-pos="center">
									<strong>' . __( 'When will your order ship out?', 'understrap-child' ) . '</strong>
							  	</a></p>';
							}
						} else {
							printf(
								'<label for="shipping_method_%1$s_%2$s">%3$s</label>',
								$index,
								esc_attr( sanitize_title( $method->id ) ),
								wc_cart_totals_shipping_method_label( $method )
							);// WPCS: XSS ok.
						}
						do_action( 'woocommerce_after_shipping_rate', $method, $index );
						?>

					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $canada_post_count != 0 ) { ?>
				<div id="canada_post_popup" class="canada-post-popup lightbox-content lightbox-white mfp-hide">
					<div class="">
						<?php
						if ( ! empty( $canada_post_description ) ) :
							echo do_shortcode( $canada_post_description );
						else :
							?>
							<h2><?php _e( 'When will your order ship out?', 'understrap-child' ); ?></h2>
							<p><?php _e( 'If we received your payment before 9am PST , your order will be shipped out by 4pm PST the same business day.', 'understrap-child' ); ?><br/>
								<?php _e( 'If we received your payment after 9am PST , your order will be shipped out by 4pm PST the next business day.', 'understrap-child' ); ?><br/>
								<?php _e( 'Payments received for orders placed on Friday after 9am PST (12pm EST) cutoff and all day Saturdays and Sundays will be mailed out on Monday before 4pm PST.', 'understrap-child' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( is_cart() ) : ?>
				<p class="woocommerce-shipping-destination">
					<?php
					if ( $formatted_destination ) {
						// Translators: $s shipping destination.
						printf( esc_html__( 'Shipping to %s.', 'woocommerce' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' );
						$calculator_text = esc_html__( 'Change address', 'woocommerce' );
					} else {
						echo wp_kses_post( apply_filters( 'woocommerce_shipping_estimate_html', __( 'Shipping options will be updated during checkout.', 'understrap-child' ) ) );
					}
					?>
				</p>
			<?php endif; ?>
			<?php
		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			if ( is_cart() && 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_not_enabled_on_cart_html', __( 'Shipping costs are calculated during checkout.', 'understrap-child' ) ) );
			} else {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your address to view shipping options.', 'understrap-child' ) ) );
			}
		elseif ( ! is_cart() ) :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'understrap-child' ) ) );
		else :
			// Translators: $s shipping destination.
			echo wp_kses_post( apply_filters( 'woocommerce_cart_no_shipping_available_html', sprintf( esc_html__( 'No shipping options were found for %s.', 'understrap-child' ) . ' ', '<strong>' . esc_html( $formatted_destination ) . '</strong>' ) ) );
			$calculator_text = esc_html__( 'Enter a different address', 'understrap-child' );
		endif;
		?>

		<?php if ( $show_package_details ) : ?>
			<?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
		<?php endif; ?>

		<?php if ( $show_shipping_calculator ) : ?>
			<?php woocommerce_shipping_calculator( $calculator_text ); ?>
		<?php endif; ?>
	</td>
</tr>
