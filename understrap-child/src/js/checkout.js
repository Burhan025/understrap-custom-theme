/* global wc_checkout_params */
jQuery(function($) {
    jQuery('document').ready(function($) {

		/**
		 * Sync shipping fields with billing fields
		 */
        $(".woocommerce-shipping-fields").on("input change", 'input,select', function() {
            var id = $(this).prop('id');
            var common_name = id.substr(id.indexOf('_'));
            var billing_field = $("#billing" + common_name);

            billing_field.val($(this).val());
            if (billing_field.prop('tagName') == 'SELECT') {
                setTimeout(function() {
                    billing_field.trigger('change');
                }, 100);
            }
        });

		/**
		 * Initialize select2 for shipping select fields
		 */
        $('.woocommerce-shipping-fields select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).selectWoo();
            }
        })

		/**
		 * Toggle billing form by payment method
		 * @param {string} current_payment_method
		 */
        var toggle_billing_form = function(current_payment_method) {
            let needed_payment_methods = ['stripe', 'paypal', 'authorize_net_cim_credit_card', 'authnet'];
            if (needed_payment_methods.indexOf(current_payment_method) != -1) {
                $('.woocommerce-billing-fields').show();
            } else {
                $('.woocommerce-billing-fields').hide();
            }
        };

        let selected_payment_method = $('.canb_payment_method [name="payment_method"]:checked').val();
        toggle_billing_form(selected_payment_method);

        $(document).on('change', '.canb_payment_method [name="payment_method"]', function() {
            toggle_billing_form($(this).val());

            if ($('.canb_payment_method input.input-radio').length > 1) {
                let target_payment_box = $('div.payment_box.' + $(this).attr('ID')),
                    is_checked = $(this).is(':checked');

                if (is_checked && target_payment_box.is(':visible')) {
                    $('.wc_payment_method').removeClass('payment-active')
					$(this).parents('.wc_payment_method').addClass('payment-active')
                }
            } else {
                $('div.payment_box').show();
            }
        });
    });

    /**
     * Add script to process logic change shipping method tabs
     */
    jQuery(document).ready(function () {

        var changeValueShipTo = false;
        var previousShipToradio = jQuery("input[name=ship_to_radio]:checked").val();

        var temp_shipping_address_1;
        var temp_shipping_address_2;
        var temp_shipping_city;
        var temp_shipping_postcode;

		/**
		 * Check currently selected shipping method and toggle shipping address fields.
		 */
        function check_ship_to_options() {
            var shipToRadio = jQuery("input[name=ship_to_radio]:checked").val();
            if (shipToRadio == "delivery" || shipToRadio == "mail_order") {
                jQuery('.shipping_address .address-field').css('display', 'block');
                jQuery('.title-delivery-detail').html('<h4>Delivery Details</h4>');

                jQuery( document.body ).trigger('change_ship_to_delivery');
            } else {
                jQuery('.shipping_address .address-field').css('display', 'none');
                jQuery('.title-delivery-detail').html('<h4>Contact Information</h4>');

                jQuery( document.body ).trigger('change_ship_to_pickup');
            }
        }

		/**
		 * Save temporary shipping address
		 */
        function save_temp_shipping_address() {
            temp_shipping_address_1 = $('#shipping_address_1').val();
            temp_shipping_address_2 = $('#shipping_address_2').val();
            temp_shipping_city = $('#shipping_city').val();
            temp_shipping_postcode = $('#shipping_postcode').val();
        }

		/**
		 * Restore temporary shipping address
		 */
        function restore_temp_shipping_adress() {
            $('#shipping_address_1').val(temp_shipping_address_1);
            $('#shipping_address_2').val(temp_shipping_address_2);
            $('#shipping_city').val(temp_shipping_city);
            $('#shipping_postcode').val(temp_shipping_postcode);
        }


        jQuery( document.body ).on( 'update_checkout', () => {
            if (changeValueShipTo) {
                jQuery('#change-value-ship-to').val('change');
            }
            setTimeout(function () {
                check_ship_to_options();
                jQuery('#change-value-ship-to').val('');
                changeValueShipTo = false;
            }, 100);
        } )

        check_ship_to_options();

        jQuery("#shipping_state").on('blur', function () {
            changeValueShipTo = true;
        });

        var setTimeoutWhenChangePostCode = setTimeout(function(){}, 100);
        jQuery("input[name=shipping_postcode]").on('change', function () {
            clearTimeout(setTimeoutWhenChangePostCode);
            setTimeoutWhenChangePostCode = setTimeout(function() {
                jQuery('#change-value-ship-to').val('change');
                jQuery('body').trigger('update_checkout');
            }, 2000);
        });

        jQuery("input[name=ship_to_radio]").on('change', function () {
            jQuery('#change-value-ship-to').val('change');
            if (previousShipToradio != "pickup") {
                save_temp_shipping_address();
            }

            if (jQuery(this).val() == "delivery" || jQuery(this).val() == "mail_order") {
                jQuery('.shipping_address .address-field').css('display', 'block');
                jQuery('.title-delivery-detail').html('<h4>Delivery Details</h4>');
                restore_temp_shipping_adress();
            } else {
                jQuery('.shipping_address .address-field').css('display', 'none');
                jQuery('.shipping_address input:not(#shipping_country, #shipping_first_name, #shipping_last_name, #shipping_phone, #shipping_email)').val('');

                jQuery('.title-delivery-detail').html('<h4>Your Details</h4>');
            }
            jQuery('body').trigger('update_checkout');
            previousShipToradio = jQuery(this).val();
        });
    });
});