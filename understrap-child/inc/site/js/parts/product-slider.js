function equalizeFlickityHeights(slider) {
	const cells = slider.querySelectorAll('.product');
	const wrappers = slider.querySelectorAll('.flickity-slider');
	if (!cells.length) return;

	wrappers.forEach(wrapper => {
		wrapper.style.display = 'flex !important';
	});

	let maxHeight = 0;
	cells.forEach(cell => {
		cell.style.height = 'auto';
		const h = cell.offsetHeight;
		if (h > maxHeight) maxHeight = h;
	});

	cells.forEach(cell => {
		cell.style.height = maxHeight + 'px';
	});
}

document.addEventListener('DOMContentLoaded', function () {
	// Select all product sliders
	const sliders = document.querySelectorAll('.dk-products--slider, .related.products');

	if (!sliders.length) return;

	// Loop through all sliders found on the page
	sliders.forEach(function (wrapper) {
		const slider = wrapper.querySelector('.products');
		if (!slider) return;

		slider.classList.add('dk-flickity-init');

		// Default Flickity options
		let options = {
			cellAlign: 'center',
			// contain: true,
			groupCells: (width) => {
				// if (width < 480) return 1;
				if (width < 768) return 2;
				if (width < 1200) return 3;
				return 4;
			},
			wrapAround: true,
			imagesLoaded: true,
			prevNextButtons: true,
			// percentPosition: true,
			adaptiveHeight: true,
			// dragThreshold: 5,
			pageDots: true,
			draggable: true,
			arrowShape: {
				x0: 10,
				x1: 60, y1: 50,
				x2: 70, y2: 40,
				x3: 30
			}
		};

		// Parse options from data-slider attribute if provided
		const dataOptions = wrapper.getAttribute('data-slider');
		if (dataOptions) {
			try {
				const parsed = JSON.parse(dataOptions);
				options = Object.assign({}, options, parsed);
			} catch (err) {
				console.warn('⚠️ Invalid JSON in data-slider:', wrapper, err);
			}
		}

		options.on = {
			ready: function() {
				equalizeFlickityHeights(slider);
			}
		}

		// Initialize Flickity
		const flkty = new Flickity(slider, options);

		// flkty.on('ready', () => equalizeFlickityHeights(slider));
		// setTimeout(() => equalizeFlickityHeights(slider), 300);

		// Also on resize
		window.addEventListener('resize', () => equalizeFlickityHeights(slider));

	});
});
