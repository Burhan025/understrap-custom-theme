/*
* Put this-site JS here or create and import additional files from /parts/.
*
*/

document.addEventListener('DOMContentLoaded', function () {
	/* Age Gate Warning Message */
	const noButton = document.querySelector('.bs-modal__button--no');
	const warningMessage = document.querySelector('#agegate .bs-modal__error');

	if (noButton && warningMessage) {
		// Initially hide the warning message
		warningMessage.style.display = 'none';

		noButton.addEventListener('click', function() {
			// Show the warning message when "No" button is clicked
			warningMessage.style.display = 'block';

			// Optional: Add a class for styling purposes
			warningMessage.classList.add('show');
		});
	}
});
