<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table">
	<tbody>
	</tbody>

	<tfoot>
        <tr class="cart-subtotal">
            <th class="product-name"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?> <?php echo '('.sprintf( __( '%1$s Items' ),count( WC()->cart->get_cart())).')' ;?> </th>
            <td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>


		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php //wc_cart_totals_shipping_html(); ?>
			<?php bs_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

                <?php
                    $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
                    if( !empty( $chosen_shipping_methods) && is_array( $chosen_shipping_methods)) {
                    $chosen_shipping_method = array_shift($chosen_shipping_methods);
                    $shipping_fee = WC()->cart->get_shipping_total();
                ?>
                <?php if (strpos($chosen_shipping_method, 'flat_rate') !== false && $shipping_fee) :  ?>
                    <tr class="shipping-fee">
                        <th>Shipping fee</th>
                        <td><?php echo wc_price($shipping_fee); ?></td>
                    </tr>
                <?php endif;
                    }
                ?>

                <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><h4><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></h4></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th class="divider-top"><h4 class="uppercase"><?php esc_html_e( 'Total', 'woocommerce' ); ?></h4></th>
			<td class="divider-top"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
