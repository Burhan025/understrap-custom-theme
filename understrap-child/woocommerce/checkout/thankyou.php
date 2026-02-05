<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * @see              https://docs.woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
// $order = wc_get_order( 123098 );
?>

<div class="bs-order-complete">

	<?php if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
		<div class="large-12 col order-failed">
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		</div>

		<?php else : ?>
			<div class="container py-5">
				<div class="row">
					<div class="col-12">
						<div class="bs-order-complete-breadcrumbs">
							<a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'woocommerce' ); ?></a> >
							<a href="<?php echo esc_url( wc_get_page_permalink( 'checkout' ) ); ?>"><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></a> >
							<span><?php esc_html_e( 'Order Complete', 'woocommerce' ); ?></span>
						</div>
						<div class="bs-order-complete-header">
							<?php wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) ); ?>
						</div>
					</div>
				</div>
				<div class="row gap-3 gap-md-0 bs-order-complete-body">
					<div class="col-12 col-md-6">
						<div class="bs-order-payment-details">
							<div class="bs-order-payment-details__payment-method">
								<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
							</div>
							<div class="bs-order-payment-details__order-number">
								#<?php echo esc_html( $order->get_order_number() ); ?>
							</div>

							<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

								<li class="woocommerce-order-overview__date date">
									<?php esc_html_e( 'Order Date:', 'woocommerce' ); ?>
									<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
								</li>

								<li class="woocommerce-order-overview__status">
									<?php esc_html_e( 'Status', 'woocommerce' ); ?>
									<strong><?php echo esc_html( $order->get_status() ); ?></strong>
								</li>

								<li class="woocommerce-order-overview__shipping-method">
									<?php esc_html_e( 'Shipping', 'woocommerce' ); ?>
									<strong><?php echo esc_html( $order->get_shipping_method() ); ?></strong>
								</li>

								<?php
									$pickup_warehouse = $order->get_meta( 'cantec_local_pickup_name' );
									if ( $pickup_warehouse ) :
								?>
									<li class="woocommerce-order-overview__pickup-warehouse">
										<?php esc_html_e( 'Pickup Warehouse', 'woocommerce' ); ?>
										<strong><?php echo esc_html( $pickup_warehouse ); ?></strong>
									</li>
								<?php endif; ?>

								<?php
									$shipping_methods = $order->get_shipping_methods();
									$shipping         = ! empty( $shipping_methods ) ? reset( $shipping_methods ) : null;

									if ( $shipping ) {
										$delivery_date = $shipping->get_meta( 'delivery_date' );

										if ( ! empty( $delivery_date ) ) :
											$is_pickup = ( $shipping->get_method_id() === 'cantec_local_pickup' );
											$label     = $is_pickup ? __( 'Pickup Time', 'woocommerce' ) : __( 'Delivery Date', 'woocommerce' );
											?>
											<li class="woocommerce-order-overview__delivery-date">
												<?php echo esc_html( $label ); ?>:
												<strong><?php echo esc_html( $delivery_date ); ?></strong>
											</li>
											<?php
										endif;
									}
								?>

								<li class="woocommerce-order-overview__subtotal">
									<?php esc_html_e( 'Subtotal', 'woocommerce' ); ?>
									<strong><?php echo wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ); ?></strong>
								</li>

								<?php
								$tax_items = $order->get_tax_totals();
								if ( ! empty( $tax_items ) ) :
									foreach ( $tax_items as $tax ) : ?>
										<li class="woocommerce-order-overview__tax-amount">
											<?php echo esc_html( $tax->label ); ?>
											<strong><?php echo wp_kses_post( $tax->formatted_amount ); ?></strong>
										</li>
									<?php endforeach;
								endif;
								?>

								<?php
									$fees = $order->get_fees();
									if ( ! empty( $fees ) ) :
										foreach ( $fees as $fee ) : ?>
											<li class="woocommerce-order-overview__fee">
												<?php echo esc_html( ucfirst( $fee->get_name() ) ); ?>
												<strong>
													<?php echo wc_price( $fee->get_amount(), array( 'currency' => $order->get_currency() ) ); ?>
												</strong>
											</li>
										<?php endforeach;
									endif;
								?>

								<?php if ( $order->get_payment_method_title() ) : ?>
									<li class="woocommerce-order-overview__payment-method method">
										<?php esc_html_e( 'Payment Method', 'woocommerce' ); ?>
										<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
									</li>
								<?php endif; ?>

								<?php
									$shipping_total = $order->get_shipping_total();
									if ( $shipping_total > 0 ) :
								?>
									<li class="woocommerce-order-overview__shipping-fee">
										<?php esc_html_e( 'Shipping Fee', 'woocommerce' ); ?>
										<strong><?php echo wc_price( $shipping_total, array( 'currency' => $order->get_currency() ) ); ?></strong>
									</li>
								<?php endif; ?>

								<?php
									$coupon_codes   = $order->get_coupon_codes();
									$total_discount = floatval( $order->get_discount_total() );
								?>

								<?php if ( $total_discount > 0 ) : ?>
									<li class="woocommerce-order-overview__discount">
										<?php esc_html_e( 'Discount', 'woocommerce' ); ?>
										<strong>
											<?php echo wc_price( $total_discount, [ 'currency' => $order->get_currency() ] ); ?>
										</strong>
									</li>
									<?php if ( ! empty( $coupon_codes ) ) : ?>
										<li class="used-coupons__item">
											<?php echo esc_html( count( $coupon_codes ) === 1 ? __( 'Coupon', 'woocommerce' ) : __( 'Coupons', 'woocommerce' ) ); ?>
											<strong><?php echo esc_html( implode( ', ', $coupon_codes ) ); ?></strong>
										</li>
									<?php endif; ?>
								<?php endif; ?>

								<?php
									$customer_note = $order->get_customer_note();
									if ( ! empty( $customer_note ) ) :
								?>
									<li class="woocommerce-order-overview__order-note">
										<?php esc_html_e( 'Order Note', 'woocommerce' ); ?>
										<strong><?php echo wp_kses_post( nl2br( wptexturize( $customer_note ) ) ); ?></strong>
									</li>
								<?php endif; ?>

								<li class="woocommerce-order-overview__total total">
									<?php esc_html_e( 'Total', 'woocommerce' ); ?>
									<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore ?></strong>
								</li>
							</ul>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="bs-order-order-details">
							<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="container pb-5">
				<div class="row">
					<div class="col-12">
						<?php bp_print_continue_shopping_link(); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>


	<?php else : ?>

		<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>

	<?php endif; ?>

</div>
