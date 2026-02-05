document.addEventListener('DOMContentLoaded', function () {
	const heading = document.querySelector('.bs-location-selector__shipping-heading');
	const header = document.querySelector('.bs-location-selector__header');
	const store_Heading = document.querySelector('.bs-location-selector__store-heading');

	if (heading && header) {
		heading.textContent = "Choose your pickup method";
		store_Heading.textContent = "Select Store Location";
		header.prepend(heading);
		header.prepend(store_Heading);
	}
});


