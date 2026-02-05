// Add your custom JS here.
import Glider from "glider-js";
import { initQuantityInputHandlers, quantityButtonClickHandler, QTY_BUTTON_SELECTOR } from "./handlers/quantity-input-handler.js";

document.addEventListener("DOMContentLoaded", () => {
	const sliders = document.querySelectorAll(".product-carousel-wrapper");
	if (sliders.length == 0) {
		return;
	}

	// Add the woocommerce class to the body to fix the slider
	document.body.classList.add("woocommerce");

	sliders.forEach((slider) => {
		initSlider(slider);
	});
});

function initSlider(slider) {
	if (slider._glider) {
		slider._glider.destroy();
	}

	const duration = slider.dataset.duration || 200;
	const draggable = slider.dataset.draggable === "true";
	const scrollLock = slider.dataset.snap === "true";
	const dots = slider.dataset.dots === "true";
	const arrows = slider.dataset.arrows === "true";
	const perPage = slider.dataset.perPage || "auto";
	const perPageMobile = slider.dataset.perPageMobile || 2;
	const itemWidth = slider.dataset.itemWidth || 300;

	let gliderOptions = {
		slidesToShow: perPageMobile,
		slidesToScroll: perPageMobile,
		itemWidth: itemWidth,
		exactWidth: false,
		duration: duration / 1000,
		draggable,
		dragVelocity: 3.3,
		easing: function (x, t, b, c, d) {
			return c * (t /= d) * t + b;
		},
		scrollPropagate: false,
		eventPropagate: true,
		scrollLock,
		scrollLockDelay: 150,
		resizeLock: true,
		responsive: [
			{
				breakpoint: 768,
					settings: {
					slidesToShow: perPage,
					slidesToScroll: perPage,
					itemWidth: itemWidth,
				}
			}
		]
	}

	// Create navigation elements
	if (arrows) {
		if (slider.querySelector(".glider-prev") === null) {
			const prevButton = document.createElement("div");
			prevButton.className = "glider-prev";
			prevButton.innerHTML = "&#10094;"; // Left arrow symbol

			slider.appendChild(prevButton);
		}

		if (slider.querySelector(".glider-next") === null) {
			const nextButton = document.createElement("div");
			nextButton.className = "glider-next";
			nextButton.innerHTML = "&#10095;"; // Right arrow symbol

			slider.appendChild(nextButton);
		}

		gliderOptions.arrows = {
			prev: slider.querySelector('.glider-prev'),
			next: slider.querySelector('.glider-next'),
		}
	}

	if (slider.querySelector(".dots") === null) {
		const el = document.createElement("div");
		el.className = "dots" + (dots ? "" : " hide-on-desktop");
		slider.appendChild(el);
	}

	gliderOptions.dots = slider.querySelector('.dots');

	slider.querySelectorAll("select").forEach((select) => {
		select.addEventListener("mousedown", (e) => e.stopPropagation());
		select.addEventListener("touchstart", (e) => e.stopPropagation());
	});

	slider._glider = new Glider(slider.querySelector("ul.products"), gliderOptions);
}

document.addEventListener("DOMContentLoaded", () => {
	const layoutSwitcher = document.querySelectorAll(
		".bs-layout-switcher__button"
	);
	if (layoutSwitcher) {
		layoutSwitcher.forEach((button) => {
			button.addEventListener("click", () => {
				const layout = button.dataset.layout;
				if (layout === "list") {
					document
						.querySelector(".woocommerce")
						.classList.add("bs-layout-list");
				} else {
					document
						.querySelector(".woocommerce")
						.classList.remove("bs-layout-list");
				}
			});
		});
	}
});

document.addEventListener("DOMContentLoaded", () => {
	const searchButton = document.querySelector(".search-button");
	if (searchButton) {
		searchButton.addEventListener("click", (e) => {
			e.preventDefault();
			document.querySelector("#search-form").classList.toggle("active");
		});
	}

	const navDropdown = document.querySelectorAll(
		".nav-dropdown .menu-item-has-children"
	);
	if (navDropdown) {
		navDropdown.forEach((dropdown) => {
			dropdown.addEventListener("click", (e) => {
				if (e.target == dropdown) {
					e.preventDefault();
					dropdown.classList.toggle("active");
				}
			});
		});
	}
});


const ON_LOAD_SERVICES = [
	initQuantityInputHandlers
];

window.addEventListener('load', () => {
	ON_LOAD_SERVICES.forEach((service) => service());
});

const QUANTITY_CLICK_HANDLERS = {
	[QTY_BUTTON_SELECTOR]: quantityButtonClickHandler
};

document.addEventListener('click', (e) => {
	for (const [selector, handler] of Object.entries(QUANTITY_CLICK_HANDLERS)) {
		if (e.target.closest(selector)) {
			handler(e);
			break;
			}
		}
	});

jQuery(document).ready(function ($) {
	$(".show-coupon-link").on("click", function (e) {
		e.preventDefault();
		$(".coupon-form-modal-overlay").addClass("active");
	});
	$(".close-modal, .coupon-form-modal-overlay").on("click", function (e) {
		if (
			$(e.target).is(".close-modal") ||
			$(e.target).is(".coupon-form-modal-overlay")
		) {
			$(".coupon-form-modal-overlay").removeClass("active");
		}
	});

	// Handle mini-cart quantity updates
	$(document).on('change', '.woocommerce-mini-cart-item .quantity input', function() {
		const $input = $(this);
		const cartItemKey = $input.closest('.woocommerce-mini-cart-item').find('.remove_from_cart_button').data('cart_item_key');
		const quantity = $input.val();

		// Show loading state
		$input.prop('disabled', true);
		$(".cart-popup-inner").addClass("loading");

		// Make AJAX call to update cart
		$.ajax({
			type: 'POST',
			url: wc_add_to_cart_params.ajax_url,
			data: {
				action: 'update_mini_cart_quantity',
				cart_item_key: cartItemKey,
				quantity: quantity,
				security: wc_add_to_cart_params.nonce
			},
			success: function(response) {
				if (response.fragments) {
					// Update cart fragments
					$.each(response.fragments, function(key, value) {
						$(key).replaceWith(value);
					});

					// Trigger event for other scripts
					$(document.body).trigger('wc_fragments_refreshed');
				}
				$(".cart-popup-inner").removeClass("loading");
			},
			complete: function() {
				$input.prop('disabled', false);
			}
		});
	});
});

/**
 * Mini Cart
 */
jQuery(document).ready(function ($) {
	$(document).on("click", ".cart-icon a", function (e) {
		if ($("#cart-popup").length == 0) {
			return;
		}

		e.preventDefault();
		$("#cart-popup").addClass("active");
	});
	$(document).on("click", ".cart-popup-banner, .continue-shopping-btn", function (e) {
		$("#cart-popup").removeClass("active");
	});

	$( document.body ).on( 'added_to_cart', function() {
		if ($("#cart-popup").length) {
			$("#cart-popup").addClass("active");
		}
	} )
});

// Mini Cart close button
document.addEventListener('click', e => {
  if (!e.target.closest('.mini-cart-close')) return;

  const inner = e.target.closest('.cart-popup-inner');
  if (!inner) return;

  // Hide via classes
  inner.classList.remove('active','open','is-visible');
  inner.parentElement?.classList.remove('active','open','is-visible');
  document.body.classList.remove('cart-popup-open','cart-open','mini-cart-open','offcanvas-open','bs-cart-open');

  inner.style.removeProperty('display');
  inner.removeAttribute('aria-hidden');
});
