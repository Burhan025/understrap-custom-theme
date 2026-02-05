<?php
/**
 * Shipping method selection
 *
 * @package WooCommerce\Templates
 */

if ( ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
	return;
}

$show_pickup_tab               = true;
$show_delivery_tab             = true;
$show_mail_order_tab           = false;
$show_mail_order_in_delivery   = false;
$delivery_shipping_description = '';

global $bsCurrentLocation;

if ( ! empty( $bsCurrentLocation ) ) {
	$show_pickup_tab     = $bsCurrentLocation->isPickupEnabled();
	$show_delivery_tab   = $bsCurrentLocation->isDeliveryEnabled();
	$show_mail_order_tab = $bsCurrentLocation->isMailOrder();
}

$ship_to_radio = WC()->session->get( 'ship_to_radio', 'pickup' );

// If delivery is not available, set pickup as default.
if ( $ship_to_radio == 'delivery' && $show_delivery_tab == false ) {
	?>
	<script>
		jQuery(document).ready(function () {
			jQuery('#ship-to-pickup').prop("checked", true).trigger('click');
		});
	</script>
	<?php
}

// If pickup is not available, set delivery as default.
if ( $ship_to_radio == 'pickup' && $show_pickup_tab == false ) {
	?>
	<script>
		jQuery(document).ready(function () {
			jQuery('#ship-to-delivery').prop("checked", true).trigger('click');
		});
	</script>
	<?php
}
?>

<div id="ship-to" class="bs-delivery-type-wrapper bs-checkout-section">
	<div class="bs-delivery-type-title bs-checkout-section__title">
		<h4><?php echo esc_html__( 'Order Type', 'understrap-child' ); ?></h4>
	</div>
	<ul class="ship_to_options bs-delivery-type-list">
		<?php if ( $show_pickup_tab ) : ?>
		<li>
			<input id="ship-to-pickup" class="" type="radio" name="ship_to_radio" value="pickup"
			<?php if ( $ship_to_radio == 'pickup' ) : ?>
				checked="checked"<?php endif; ?>>
			<label for="ship-to-pickup"><span><?php echo esc_html__( 'Pickup', 'understrap-child' ); ?></span></label>
		</li>
		<?php endif; ?>
		<?php if ( ( $show_delivery_tab || ( $show_mail_order_tab && $show_mail_order_in_delivery ) ) ) : ?>
		<li>
			<input id="ship-to-delivery" class="" type="radio" name="ship_to_radio" value="delivery"
			<?php if ( $ship_to_radio == 'delivery' ) : ?>
				checked="checked"<?php endif; ?>>
			<label for="ship-to-delivery"><span><?php echo esc_html__( 'Delivery', 'understrap-child' ); ?></span>
				<?php
				if ( ! empty( $delivery_shipping_description ) ) {
					 // phpcs:ignore
					echo '<small>' . esc_html__( $delivery_shipping_description, 'understrap-child' ) . '</small>';
				}
				?>
			</label>
		</li>
		<?php endif; ?>
		<?php if ( $show_mail_order_tab && ! $show_mail_order_in_delivery ) : ?>
		<li>
			<input id="ship-to-mail_order" class="" type="radio" name="ship_to_radio" value="mail_order"
			<?php
			if ( $ship_to_radio == 'mail_order' ) :
				?>
				checked="checked"<?php endif; ?>>
			<label for="ship-to-mail_order"><span><?php echo esc_html__( 'Mail Order', 'understrap-child' ); ?></span></label>
		</li>
		<?php endif; ?>
	</ul>
</div>
<input type="hidden" name="change_value_ship_to" id="change-value-ship-to" />