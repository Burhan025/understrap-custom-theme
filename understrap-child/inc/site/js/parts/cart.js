(function ($) {
	"use strict";

	/**
	 * Auto update cart when qty input changes
	 */
	function initCartAutoUpdate() {
		var debounceTimer = null;
		$(document).on("input change", ".woocommerce-cart-form .cart_item .qty", function () {
			clearTimeout(debounceTimer);

			debounceTimer = setTimeout(function () {
				var $form = $(".woocommerce-cart-form");

				// Ensure update_cart hidden input exists
				var $hidden = $form.find('input[name="update_cart"]');
				if (!$hidden.length) {
					$form.append('<input type="hidden" name="update_cart" value="1">');
				} else {
					$hidden.val("1");
				}

				// Trigger WooCommerce AJAX update
				$(document.body).trigger("wc_update_cart");
			}, 800); // debounce delay
		});
	}

	/**
	 * Handle coupon modal open/close and reset on WC refresh
	 */
	function initCouponModal() {
		// Open modal
		$(document).on("click", ".show-coupon-link", function (e) {
			e.preventDefault();
			$(".coupon-form-modal-overlay").addClass("active");
		});

		// Close modal
		$(document).on("click", ".close-modal, .coupon-form-modal-overlay", function (e) {
			if (
				$(e.target).is(".close-modal") ||
				$(e.target).is(".coupon-form-modal-overlay")
			) {
				$(".coupon-form-modal-overlay").removeClass("active");
			}
		});

		// Reset modal state after WC AJAX refresh
		$(document.body).on("updated_wc_div updated_cart_totals updated_checkout", function () {
			$(".coupon-form-modal-overlay").removeClass("active");
		});
	}

	/**
	 * Prevent cart popup on Cart & Checkout pages
	 */
	function disableCartPopupOnCartCheckout() {
		if ($("body").hasClass("woocommerce-cart") || $("body").hasClass("woocommerce-checkout")) {
			$(document).off("click", ".cart-icon a");
		}

	}

	/**
	 * Init on DOM ready
	 */
	$(function () {
		initCartAutoUpdate();
		initCouponModal();
		disableCartPopupOnCartCheckout();

		// Set max qty when input from keyboard
		$(document).on(
			"keyup",
			".woocommerce-mini-cart-item .quantity input",
			function () {
				var maxValue = $(this).attr("max");
				if (parseInt($(this).val()) > parseInt(maxValue)) {
					$(this).val(maxValue);
				}
			},
		);
	});

})(jQuery);
