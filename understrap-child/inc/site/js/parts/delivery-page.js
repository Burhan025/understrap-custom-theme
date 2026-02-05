/*
* Put this-site JS here or create and import additional files from /parts/.
*
*/

document.addEventListener('DOMContentLoaded', function () {
	/* Location Box Interaction */
	document.querySelectorAll('.location-box .icon-box').forEach(box => {
		box.addEventListener('click', function() {
			// If clicking on already active box, deactivate it and hide cities
			if (this.classList.contains('active')) {
				this.classList.remove('active');
				document.querySelectorAll('.ontario-city').forEach(city => city.style.display = 'none');
				return;
			}

			// Update active states
			document.querySelectorAll('.location-box .icon-box').forEach(item => item.classList.remove('active'));
			this.classList.add('active');

			// Hide all cities
			document.querySelectorAll('.ontario-city').forEach(city => city.style.display= 'none');

			// Show matching cities
			const parentClasses = Array.from(this.closest('.location-box').classList);
			parentClasses.filter(cls => cls.startsWith('ontario-city'))
				.forEach(cityClass => {
					const targetClass = cityClass.replace('-box', '');
					document.querySelectorAll('.' + targetClass).forEach(el => el.style.display= 'flex');
				});
		});
	});

	/* Move matching .ontario-city under its .location-box counterpart on small screens, restore on desktop */
	const BREAKPOINT_PX = 781;
	const originalPlaceholderMap = new WeakMap();

	function preparePlaceholders() {
		document.querySelectorAll('.ontario-city').forEach(cityEl => {
			if (originalPlaceholderMap.has(cityEl)) {
				return;
			}
			const placeholder = document.createElement('span');
			placeholder.className = 'ontario-city-placeholder';
			placeholder.style.display = 'none';
			cityEl.parentNode && cityEl.parentNode.insertBefore(placeholder, cityEl);
			originalPlaceholderMap.set(cityEl, placeholder);
		});
	}

	function moveCitiesToLocationBoxesIfMobile() {
		if (window.innerWidth >= BREAKPOINT_PX) return;
		preparePlaceholders();

		document.querySelectorAll('.ontario-city').forEach(cityEl => {
			if (cityEl.dataset.moved === '1') return;

			const variantClass = Array.from(cityEl.classList).find(cls => cls.startsWith('ontario-city-') && cls !== 'ontario-city');
			if (!variantClass) return;

			const target = document.querySelector('.location-box.' + variantClass + '-box');
			if (!target) return;

			target.appendChild(cityEl);
			cityEl.dataset.moved = '1';
		});
	}

	function restoreCitiesIfDesktop() {
		if (window.innerWidth < BREAKPOINT_PX) return;
		document.querySelectorAll('.ontario-city[data-moved="1"]').forEach(cityEl => {
			const placeholder = originalPlaceholderMap.get(cityEl);
			if (!placeholder || !placeholder.parentNode) return;
			// Insert the city back to its original position (after placeholder)
			placeholder.parentNode.insertBefore(cityEl, placeholder.nextSibling);
			delete cityEl.dataset.moved;
		});
	}

	function handleResizeOrInit() {
		if (window.innerWidth < BREAKPOINT_PX) {
			moveCitiesToLocationBoxesIfMobile();
		} else {
			restoreCitiesIfDesktop();
		}
	}

	function debounce(fn, wait) {
		let timeoutId;
		return function() {
			const args = arguments;
			clearTimeout(timeoutId);
			timeoutId = setTimeout(() => fn.apply(null, args), wait);
		};
	}
	handleResizeOrInit();
	window.addEventListener('resize', debounce(handleResizeOrInit, 150));
});
