<?php
/**
 * Checkout billing information form
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

$fields = $checkout->get_checkout_fields( 'billing' );
?>
<div class="woocommerce-billing-fields">

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php woocommerce_form_field( 'billing_country', $fields['billing_country'], $checkout->get_value( 'billing_country' ) ); ?>
		<?php

		$row_firsts = array( 'billing_first_name', 'billing_phone', 'billing_city', 'billing_state', 'billing_address_1' );
		$row_lasts  = array( 'billing_last_name_field', 'billing_email', 'billing_postcode', 'billing_address_2' );
		$row_wide   = array();
		foreach ( $fields as $key => $field ) {
			if ( in_array( $key, $row_firsts ) ) {
				$fields[ $key ]['class']   = array();
				$fields[ $key ]['class'][] = 'form-row-first';
			}
			if ( in_array( $key, $row_lasts ) ) {
				$fields[ $key ]['class']   = array();
				$fields[ $key ]['class'][] = 'form-row-last';
			}
			if ( in_array( $key, $row_wide ) ) {
				$fields[ $key ]['class']   = array();
				$fields[ $key ]['class'][] = 'form-row-wide';
			}
		}
		$fields['billing_address_1']['placeholder'] = '';
		$fields['billing_address_2']['placeholder'] = '';
		?>

		<h4><?php esc_html_e( 'Billing Address', 'understrap-child' ); ?></h4>

		<div class="fields-list">
			<?php woocommerce_form_field( 'billing_first_name', $fields['billing_first_name'], $checkout->get_value( 'billing_first_name' ) ); ?>
			<?php woocommerce_form_field( 'billing_last_name', $fields['billing_last_name'], $checkout->get_value( 'billing_last_name' ) ); ?>
			<?php woocommerce_form_field( 'billing_phone', $fields['billing_phone'], $checkout->get_value( 'billing_phone' ) ); ?>
			<?php woocommerce_form_field( 'billing_email', $fields['billing_email'], $checkout->get_value( 'billing_email' ) ); ?>
			<?php woocommerce_form_field( 'billing_address_1', $fields['billing_address_1'], $checkout->get_value( 'billing_address_1' ) ); ?>
			<?php woocommerce_form_field( 'billing_city', $fields['billing_city'], $checkout->get_value( 'billing_city' ) ); ?>
			<?php woocommerce_form_field( 'billing_state', $fields['billing_state'], $checkout->get_value( 'billing_state' ) ); ?>
			<?php woocommerce_form_field( 'billing_postcode', $fields['billing_postcode'], $checkout->get_value( 'billing_postcode' ) ); ?>
			<?php woocommerce_form_field( 'billing_address_2', $fields['billing_address_2'], $checkout->get_value( 'billing_address_2' ) ); ?>
		</div>

	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
