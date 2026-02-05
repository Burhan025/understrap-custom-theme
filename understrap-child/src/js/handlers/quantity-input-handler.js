/**
 * Quantity Input Handler
 * Handles quantity input functionality including max quantity restrictions
 */

// Constants
export const QTY_BUTTON_SELECTOR = '.qty-btn';

/**
 * Updates the quantity input value and handles max quantity logic
 * @param {HTMLInputElement} input - The quantity input element
 * @param {number} newValue - The new value to set
 */
function updateQuantityValue(input, newValue) {
	// Get min and max values from input attributes
	const min = parseInt(input.getAttribute("min")) || 1;
	const max = parseInt(input.getAttribute("max")) || 999999;

	// Ensure value is within min and max bounds
	newValue = Math.max(min, Math.min(max, newValue));
	input.value = newValue;

	// Handle max quantity message and button state
	handleMaxQuantityState(input, newValue, max);

	// Trigger change event
	const event = new Event("change", { bubbles: true });
	input.dispatchEvent(event);
}

/**
 * Handles the max quantity message visibility and plus button state
 * @param {HTMLInputElement} input - The quantity input element
 * @param {number} currentValue - The current quantity value
 * @param {number} maxValue - The maximum allowed quantity
 */
function handleMaxQuantityState(input, currentValue, maxValue) {
	const wrapper = input.closest(".bs-quantity-wrapper");
	if (!wrapper) return;

	const quantityContainer = wrapper.closest('.quantity');
	const maxMessage = quantityContainer ? quantityContainer.querySelector('.dk-max-quantity-message') : null;
	const plusButton = wrapper.querySelector('.qty-btn.plus');

	if (maxMessage && plusButton) {
		if (currentValue >= maxValue) {
			// Show max quantity message and disable plus button
			maxMessage.classList.remove('hidden');
			maxMessage.classList.add('visible');
			plusButton.setAttribute('disabled', 'disabled');
		} else {
			// Hide max quantity message and enable plus button
			maxMessage.classList.remove('visible');
			maxMessage.classList.add('hidden');
			plusButton.removeAttribute('disabled');
		}
	}
}

/**
 * Handles quantity button clicks (plus/minus)
 * @param {Event} e - The click event
 */
export function quantityButtonClickHandler(e) {
	const wrapper = e.target.closest(".bs-quantity-wrapper");
	if (!wrapper) return;

	const input = wrapper.querySelector('input[type="number"]');
	if (!input) return;

	const step = parseInt(input.getAttribute("step")) || 1;
	const currentValue = parseInt(input.value);

	// Handle plus/minus button clicks
	if (e.target.classList.contains("minus")) {
		updateQuantityValue(input, currentValue - step);
	} else if (e.target.classList.contains("plus")) {
		updateQuantityValue(input, currentValue + step);
	}
}

/**
 * Handles direct input changes to quantity fields
 * @param {Event} e - The input event
 */
export function handleQuantityInputChange(e) {
	if (!e.target.matches('.quantity input[type="number"]')) return;

	const input = e.target;
	const wrapper = input.closest(".bs-quantity-wrapper");
	if (!wrapper) return;

	const max = parseInt(input.getAttribute("max")) || 999999;
	const currentValue = parseInt(input.value) || 0;

	// Handle max quantity message and button state
	handleMaxQuantityState(input, currentValue, max);
}

/**
 * Initializes quantity input handlers
 */
export function initQuantityInputHandlers() {
	// Handle direct input changes
	document.addEventListener("input", handleQuantityInputChange);
}
