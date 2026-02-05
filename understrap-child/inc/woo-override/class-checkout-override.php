<?php
/**
 * Checkout override
 *
 * @package BurhanAftab\UnderstrapChild\WooOverride
 */

namespace BurhanAftab\UnderstrapChild\WooOverride;

/**
 * Checkout override
 */
class Checkout_Override {

	/**
	 * Minimum order amount required for checkout validation.
	 *
	 * @var float The minimum order total in the store's base currency (e.g., $0.10)
	 */
	const MINIMUM_ORDER_AMOUNT = 0.10;

	/**
	 * Constructor
	 */
	public function init() {
		// Checkout page - Remove coupon form and payment form.
		add_action( 'after_setup_theme', array( $this, 'checkout_override' ), 0 );

		// Checkout page - Override checkout fields.
		add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ), 20 );
		add_filter( 'woocommerce_default_address_fields', array( $this, 'change_text_address_fields' ), 99 );

		// Checkout page - Hide shipping methods by condition.
		add_filter( 'woocommerce_package_rates', array( $this, 'hide_shipping_methods_by_condition' ), 20, 1 );

		// Checkout page - Add shipping custom extra fields.
		add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
		add_filter( 'woocommerce_shipping_fields', array( $this, 'add_shipping_custom_extra_fields' ) );

		// Checkout page - Change text address fields.

		// Checkout page - Add payment method form under customer details.
		add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'add_payment_method' ), 20 );

		// Checkout page - Add pickup location and submit button, and remove order notes.
		add_action( 'woocommerce_checkout_order_review', array( $this, 'user_pickup_location' ), 19 );
		add_action( 'woocommerce_checkout_order_review', array( $this, 'add_checkout_submit_btn' ), 20 );
		add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

		// Checkout page - Update payment gateways by shipping method.
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'unset_cod_on_not_pickup' ) );
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'get_available_payment_by_shipping_method' ) );

		// Checkout ajax update - set shipping method.
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'update_order_review_shipping' ), 10, 1 );

		// Checkout validation.
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validation_birthday' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout_flow' ), 21, 2 );

		// Shipping method validation
		add_action( 'woocommerce_checkout_process', array( $this, 'shipping_method_require_validation' ), 20 );
		// Add minimum order amount validation
		add_action( 'woocommerce_checkout_process', array( $this, 'validate_minimum_order_amount' ) );

		// Checkout - Save phone to user meta.
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'update_user_meta' ), 10, 2 );

		// Admin order - Add phone and email to admin order.
		add_filter( 'woocommerce_admin_shipping_fields', array( $this, 'admin_shipping_fields' ) );

		// Order details - Add phone and email to order details.
		add_action( 'woocommerce_order_details_after_customer_details', array( $this, 'add_custom_information_after_customer_details' ) );

		// Admin user profile - Add phone field.
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'user_account_billing_fields' ) );

		// My Account - save phone and birthday.
		add_action( 'woocommerce_save_account_details', array( $this, 'save_account_details' ) );

		add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'thankyou_order_received_text' ) );

		add_filter( 'woocommerce_endpoint_order_received_title', array( $this, 'thankyou_order_received_title' ) );

		// Block special characters
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'block_special_chars' ), 19, 2 );


	}

	/**
	 * BS checkout override
	 * Remove coupon form
	 * Remove payment form
	 */
	public function checkout_override() {
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	}

	/**
	 * Show pickup location
	 */
	public function user_pickup_location() {
		global $bsCurrentLocationName;
		if ( $bsCurrentLocationName ) : ?>
			<div class="wc-user-pickup-location">
				<?php esc_html_e( 'Shopping at', 'understrap-child' ); ?> - <?php echo esc_html( $bsCurrentLocationName ); ?>
			</div>
			<?php
		endif;
	}

	/**
	 * Update checkout fields
	 * Remove shipping company field
	 * Add mobile phone field
	 * Add email field
	 *
	 * @param array $fields Checkout fields.
	 */
	public function checkout_fields( $fields ) {
		// Filter shipping form.
		unset( $fields['shipping']['shipping_company'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['ship_to_radio'] ) && $_POST['ship_to_radio'] == 'pickup' ) {
			/**
			 * This method is invoked at two points. One when loading the checkout page and another when the checkout is submitted.
			 *
			 * The $_POST is only set when the checkout is submitted.
			 * At checkout, Woocommerce uses this method to get all fields that needs to be validated.
			 *
			 * So on checkout submission, when the delivery method is set to 'pickup', we can remove the shipping related fields from the validation.
			 */
			unset( $fields['billing']['billing_postcode'] );
			unset( $fields['shipping']['shipping_postcode'] );
			unset( $fields['shipping']['shipping_address_1'] );
			unset( $fields['shipping']['shipping_address_2'] );
			unset( $fields['shipping']['shipping_city'] );
			unset( $fields['shipping']['shipping_state'] );
		}

		$fields['shipping']['shipping_phone'] = array(
			'label'        => __( 'Mobile Phone', 'understrap-child' ),
			'required'     => 1,
			'type'         => 'tel',
			'class'        => array( 'form-row-first' ),
			'validate'     => array( 'phone' ),
			'autocomplete' => 'tel',
			'priority'     => 21,
		);

		$fields['shipping']['shipping_email'] = array(
			'label'    => __( 'Email', 'understrap-child' ),
			'required' => 1,
			'type'     => 'email',
			'class'    => array( 'form-row-last' ),
			'validate' => array( 'email' ),
			'priority' => 23,
		);

		$fields['shipping']['shipping_dob'] = array(
			'label'       => __( 'Date of Birth', 'understrap-child' ),
			'placeholder' => __( 'MM/DD/YYYY', 'understrap-child' ),
			'required'    => 1,
			'type'        => 'date',
			'class'       => array( 'form-row-wide' ),
			'validate'    => array( 'date' ),
			'priority'    => 100,
			'custom_attributes' => array(
				'max' => date('Y-m-d'),
				'min' => '1900-01-01',
			),
		);

		$fields['shipping']['shipping_address_1']['class']       = array( 'form-row-first', 'address-field' );
		$fields['shipping']['shipping_address_2']['class']       = array( 'form-row-last', 'address-field' );
		$fields['shipping']['shipping_address_2']['label']       = __( 'Apartment, suite, unit, etc. (optional)', 'understrap-child' );
		$fields['shipping']['shipping_address_2']['placeholder'] = __( 'Apartment, suite, unit, etc', 'understrap-child' );
		unset( $fields['shipping']['shipping_address_2']['label_class'] );

		// Filter billing form.
		foreach ( $fields['billing'] as $field_name => &$attr ) {
			$attr['required'] = false;
		}

		return $fields;
	}

	/**
	 * Update admin shipping fields
	 * Add email to Admin Order overview
	 *
	 * @param array $fields Admin shipping fields.
	 */
	public function admin_shipping_fields( $fields ) {
		$fields['email'] = array(
			'label' => esc_html__( 'Shipping Email', 'understrap-child' ),
		);
		$fields['phone'] = array(
			'label'         => esc_html__( 'Shipping Phone', 'understrap-child' ),
			'wrapper_class' => '_shipping_state_field', // Borrow a class from WC that will float it right.
		);

		return $fields;
	}

	/**
	 * Add custom information after customer details
	 *
	 * @param WC_Order $order Order.
	 */
	public function add_custom_information_after_customer_details( $order ) {
		$email = $order->get_meta( '_shipping_email', true );
		$phone = $order->get_meta( '_shipping_phone', true );
		?>
		<div class="extra-customer-details">
			<?php if ( $email ) : ?>
				<dt"><?php esc_html_e( 'Shipping Email', 'understrap-child' ); ?>:</dt>
				<dd class="extra-customer-details__value"><?php echo esc_html( $email ); ?></dd>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<dt"><?php esc_html_e( 'Shipping Phone', 'understrap-child' ); ?>:</dt>
				<dd class="extra-customer-details__value"><?php echo esc_html( $phone ); ?></dd>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Update user meta
	 *
	 * @param int   $user_id User ID.
	 * @param array $data User data.
	 */
	public function update_user_meta( $user_id, $data ) {
		if ( isset( $data['shipping_phone'] ) ) {
			$shipping_phone = sanitize_text_field( $data['shipping_phone'] );
			update_user_meta( $user_id, 'shipping_phone', $shipping_phone );
		}
	}

	/**
	 * Thank you order received text
	 *
	 * @param string $text Text.
	 */
	public function thankyou_order_received_text( $text ) {
		return esc_html__( 'Thank you for supporting your local retailer', 'understrap-child' );
	}

	/**
	 * Thank you order received title
	 *
	 * @param string $title Title.
	 */
	public function thankyou_order_received_title( $title ) {
		return esc_html__( 'Your order was placed sucessfully', 'understrap-child' );
	}

	/**
	 * Update user account billing fields
	 * Add mobile phone field
	 *
	 * @param array $fields User account billing fields.
	 */
	public function user_account_billing_fields( $fields ) {
		$fields['shipping']['fields']['shipping_phone'] = array(
			'label'       => __( 'Mobile Phone', 'understrap-child' ),
			'description' => __( 'This phone number will be used for shipping notifications.', 'understrap-child' ),
		);

		return $fields;
	}

	/**
	 * Save account details
	 *
	 * @param int $user_id User ID.
	 */
	public function save_account_details( $user_id ) {
		// phpcs:disable WordPress.Security
		if ( isset( $_POST['shipping_phone'] ) && ! empty( $_POST['shipping_phone'] ) ) {
			if ( ! bs_validate_phone( wp_unslash( $_POST['shipping_phone'] ) ) ) {
				wc_add_notice( __( '<strong>Phone number</strong> must be 10 digits.', 'understrap-child' ), 'error' );
			} else {
				update_user_meta( $user_id, 'shipping_phone', wc_clean( wp_unslash( $_POST['shipping_phone'] ) ) );
			}
		}
		if ( isset( $_POST['shipping_dob'] ) && ! empty( $_POST['shipping_dob'] ) ) {
			update_user_meta( $user_id, 'shipping_dob', sanitize_text_field( wp_unslash( $_POST['shipping_dob'] ) ) );
		}
		// phpcs:enable WordPress.Security
	}

	/**
	 * Validate birthday
	 *
	 * @param array $data Data.
	 */
	public function validation_birthday( $data ) {
		if ( isset( $data['shipping_dob'] ) ) {
			$dob         = \DateTime::createFromFormat( 'Y-m-d', $data['shipping_dob'] );
			$currentTime = new \DateTime();

			if ( $dob && $dob < $currentTime ) {
				$diff = $currentTime->diff( $dob );
				if ( $diff->y <= 18 && $diff->invert == 1 ) {
					wc_add_notice( __( 'You must be at least 19 years old to purchase cannabis', 'woocommerce' ), 'error' );
				}
			} elseif ( $dob && $dob > $currentTime ) {
				wc_add_notice( __( 'Birthdate should be less than current date', 'woocommerce' ), 'error' );
			}
		}
	}

	/**
	 * Add checkout submit button
	 */
	public function add_checkout_submit_btn() {
		wc_get_template( 'checkout/button-submit.php', array( 'order_button_text' => __( 'COMPLETE ORDER', 'understrap-child' ) ) );
	}

	/**
	 * Add payment method
	 */
	public function add_payment_method() {
		?>
		<div class="canb_payment_method bs-checkout-section">
			<div class="title-section-payment-method bs-checkout-section__title">
				<h4 class="section-title"><?php esc_html_e( 'Payment Method', 'understrap-child' ); ?></h4>
			</div>
			<?php woocommerce_checkout_payment(); ?>
		</div>
		<?php
	}

	/**
	 * Update order review shipping.
	 * Set ship to radio
	 *
	 * @param string $posted Posted data.
	 */
	public function update_order_review_shipping( $posted ) {
		parse_str( $posted, $data );
		if ( is_array( $data ) && isset( $data['ship_to_radio'] ) ) {
			WC()->session->set( 'ship_to_radio', $data['ship_to_radio'] );
		}
	}

	/**
	 * Unset Cash on Delivery payment method if shipping method is not pickup.
	 *
	 * @param array $available_gateways Available gateways.
	 */
	public function unset_cod_on_not_pickup( $available_gateways ) {
		if ( WC()->session ) {
			$ship_to_radio = WC()->session->get( 'ship_to_radio', 'pickup' );

			if ( $ship_to_radio != 'pickup' ) {
				unset( $available_gateways['cod'] );
			}
		}

		return $available_gateways;
	}

	/**
	 * Shipping method require validation
	 */
	public function shipping_method_require_validation() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['shipping_method'] ) ) {
			wc_add_notice( __( 'You need to choose your a shipping option.', 'understrap-child' ), 'error' );
		}
	}

	/**
	 * Validate minimum order amount
	 * Prevents checkout if order total is below the minimum threshold
	 */
	public function validate_minimum_order_amount() {
		$cart_total = (float) WC()->cart->get_total( 'edit' );

		if ( $cart_total < self::MINIMUM_ORDER_AMOUNT ) {
			wc_add_notice(
				sprintf(
					/* translators: %1$s: current order total, %2$s: minimum required amount */
					__( 'Your order total is %1$s. Minimum order amount is %2$s to checkout.', 'understrap-child' ),
					wc_price( $cart_total ),
					wc_price( self::MINIMUM_ORDER_AMOUNT )
				),
				'error'
			);
		}
	}	/**
	 * Validate checkout flow
	 *
	 * @param array     $data Data.
	 * @param \WP_Error $errors Errors.
	 */
	public function validate_checkout_flow( $data, $errors ) {
		$shipping_method = wc_get_chosen_shipping_method_ids()[0] ?? null;

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $shipping_method == 'cantec_local_pickup' && isset( $_POST['cantec_local_pickup_empty'] ) && $_POST['cantec_local_pickup_empty'] == true ) {
			$needed_err_code = 'cantec-local-pickup';
			if ( ! empty( $errors->get_error_data( $needed_err_code ) ) ) {
				$errors->remove( $needed_err_code );
				$errors->add(
					$needed_err_code,
					__( 'There are some products that are out of stock, please select other products or other location', 'understrap-child' ),
					array( 'id' => $needed_err_code )
				);
			}
		}
		if ( ( $shipping_method != 'cantec_local_pickup' && $shipping_method != 'curbside_pickup' ) && $data['payment_method'] == 'cod' ) {
			$errors->add(
				'invalid_payment_method',
				__( 'COD payment method is only supported with Pickup in store shipping method', 'understrap-child' ),
				array( 'id' => 'invalid_payment_method' )
			);
		}
	}

	/**
	 * Get available payment by shipping method
	 *
	 * @param array $available_gateways Available gateways.
	 */
	public function get_available_payment_by_shipping_method( $available_gateways ) {
		// Not in backend (admin) and Not in order pay page.
		if ( is_admin() || is_wc_endpoint_url( 'order-pay' ) ) {
			return $available_gateways;
		}

		// Get chosen shipping methods.
		$chosen_shipping_methods = isset( WC()->session ) && WC()->session->has_session() && WC()->session->get( 'chosen_shipping_methods' ) ? WC()->session->get( 'chosen_shipping_methods' ) : array();

		if ( ! empty( $chosen_shipping_methods ) ) {
			$chosen_shipping_methods = explode( ':', $chosen_shipping_methods[0] );
			if ( ! in_array( 'cantec_local_pickup', $chosen_shipping_methods ) && ! in_array( 'curbside_pickup', $chosen_shipping_methods ) ) {
				unset( $available_gateways['cod'] );
			}
		}

		return $available_gateways;
	}

	/**
	 * Hide shipping methods by condition
	 *
	 * @param array $rates Rates.
	 */
	public function hide_shipping_methods_by_condition( $rates ) {
		foreach ( $rates as $rate_key => $rate ) {
			if ( $rate->method_id == 'cantec_local_pickup' ) {
				$rates[ $rate_key ]->label = __( 'Pickup', 'understrap-child' );
			}
		}

		return $rates;
	}

	/**
	 * Add shipping custom extra fields
	 *
	 * @param array $fields Fields.
	 */
	public function add_shipping_custom_extra_fields( $fields ) {
		$fields['shipping_phone'] = array(
			'label'    => __( 'Phone', 'woocommerce' ),
			'required' => true,
			'priority' => 99,
		);
		$fields['shipping_email'] = array(
			'label'    => __( 'Email address', 'woocommerce' ),
			'required' => true,
			'validate' => array( 'email' ),
			'priority' => 99,
		);
		return $fields;
	}

	/**
	 * Change text address fields
	 *
	 * @param array $fields Fields.
	 */
	public function change_text_address_fields( $fields ) {
		if ( isset( $fields['address_1'] ) ) {
			$fields['address_1']['class'] = array( 'form-row-first', 'address-field' );
		}

		if ( isset( $fields['address_2'] ) ) {
			$fields['address_2']['class']       = array( 'form-row-last', 'address-field' );
			$fields['address_2']['label']       = __( 'Apartment, suite, unit, etc. (optional)', 'understrap-child' );
			$fields['address_2']['placeholder'] = __( 'Apartment, suite, unit, etc', 'understrap-child' );
			unset( $fields['address_2']['label_class'] );
		}
		$fields['dob'] = array(
			'label'       => __( 'Date of Birth', 'understrap-child' ),
			'placeholder' => __( 'MM/DD/YYYY', 'understrap-child' ),
			'required'    => 1,
			'type'        => 'date',
			'class'       => array( 'form-row-wide' ),
			'validate'    => array( 'date' ),
			'priority'    => 100,
		);
		return $fields;
	}

	/**
	 * Block special characters during checkout validation.
	 *
	 * Blocked: ! @ # $ % ^ & * ( ) { } [ ] | \ / + = < >
	 *
	 * @param array     $data   WooCommerce normalized checkout data.
	 * @param \WP_Error $errors Validation errors container.
	 */
	public function block_special_chars( $data, $errors ) {

		static $pattern = null;
		if ( null === $pattern ) {
			$blocked = '!@#$%^&*(){}[]|\\/+=<>';
			$pattern = '/[' . preg_quote( $blocked, '/' ) . ']/u';
		}

		$fields_to_check = array(
			'shipping_first_name' => __( 'First name', 'woocommerce' ),
			'shipping_last_name'  => __( 'Last name', 'woocommerce' ),
			'shipping_address_1'  => __( 'Street address', 'woocommerce' ),
			'shipping_city'       => __( 'Town / City', 'woocommerce' ),
		);

		foreach ( $fields_to_check as $key => $label ) {

			$value = '';
			if ( is_array( $data ) && array_key_exists( $key, $data ) ) {
				$value = (string) $data[ $key ];
			} elseif ( isset( $_POST[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$value = (string) wp_unslash( $_POST[ $key ] );
			}

			$value = trim( $value );

			if ( '' !== $value && preg_match( $pattern, $value ) ) {

				$code = 'dk_blocked_chars_' . $key;

				if ( method_exists( $errors, 'get_error_message' ) && $errors->get_error_message( $code ) ) {
					continue;
				}

				$errors->add(
					$code,
					sprintf(
						__( 'Special characters are not allowed in %s.', 'woocommerce' ),
						$label
					)
				);
			}
		}
	}

}
