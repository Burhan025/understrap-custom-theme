<?php
/**
 * Checkout Form
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

$ship_to_radio = WC()->session->get( 'ship_to_radio', 'pickup' );
?>

<div class="container my-5">


<form name="checkout" method="post" class="checkout woocommerce-checkout <?php echo esc_attr( $wrapper_classes ); ?>" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<div class="row">
		<div class="col-xxl-8">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<?php wc_get_template( 'checkout/shipping-methods.php' ); ?>

				<div id="customer_details" class="bs-checkout-section">
					<div class="title-delivery-detail bs-checkout-section__title">
						<?php if ( $ship_to_radio != 'pickup' ) : ?>
							<h4><?php echo esc_html__( 'Delivery Details', 'understrap-child' ); ?></h4>
						<?php else : ?>
							<h4><?php echo esc_html__( 'Contact Information', 'understrap-child' ); ?></h4>
						<?php endif; ?>
					</div>
					<div class="customer_details-wrap">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						<div class="bs-customer-details-billing">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
			<div class="product-shopping mb-4">
				<?php bp_print_continue_shopping_link(); ?>
			</div>
		</div>

		<div class="col-xxl-4">
			<div class="checkout-sidebar">

				<div class="order-summary bs-black-card">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

					<h3 id="order_review_heading" class="bs-black-card__title"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h3>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>

				<div class="order-detail bs-black-card">
					<?php
					wc_get_template(
						'checkout/order-detail.php',
						array(
							'checkout' => WC()->checkout(),
						)
					);
					?>
				</div>
			</div>
		</div>

	</div>
</form>
<div class="coupon-form-modal-overlay">
	<div class="coupon-form-modal">
		<div class="coupon-form-modal__header">
			<h3><?php esc_html_e( 'Have a coupon or promotion code?', 'understrap-child' ); ?></h3>
			<button class="close-modal" aria-label="Close">&times;</button>
		</div>
		<div class="coupon-form-modal__body">
			<p><?php esc_html_e( 'Please enter it below', 'understrap-child' ); ?>	</p>
			<form class="checkout_coupon woocommerce-form-coupon" method="post">
				<div class="coupon">
					<input type="text" name="coupon_code" class="input-text form-control" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
					<button type="submit" class="btn btn-outline-primary" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
					<?php do_action( 'woocommerce_cart_coupon' ); ?>
				</div>
			</form>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div> <!-- end .container -->
