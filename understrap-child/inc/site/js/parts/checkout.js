document.addEventListener('change', function (e) {
	if (e.target.matches('#payment [name="payment_method"]')) {

		const paymentRadios = document.querySelectorAll('#payment input.input-radio');

		if (paymentRadios.length > 1) {
			const targetPaymentBox = document.querySelector('div.payment_box.' + e.target.id);
			const isChecked = e.target.checked;

			if (isChecked && targetPaymentBox && targetPaymentBox.offsetParent !== null) {
				document.querySelectorAll('.wc_payment_method').forEach(el => {
					el.classList.remove('payment-active');
				});
				e.target.closest('.wc_payment_method').classList.add('payment-active');
			}
		} else {
			document.querySelectorAll('div.payment_box').forEach(el => {
				el.style.display = 'block';
			});
		}
	}
});
