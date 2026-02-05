document.addEventListener('DOMContentLoaded', function () {
	const carousel = document.querySelector('#imageCarousel');
	if (!carousel) return;

	const carouselInner = carousel.querySelector('.carousel-inner');
	const indicators = carousel.querySelector('.carousel-indicators');
	const allImages = Array.from(carouselInner.querySelectorAll('img'));
	let currentBreakpoint = '';

	function getImagesPerSlide() {
		if (window.innerWidth < 576) return 1;   // Mobile
		if (window.innerWidth < 992) return 2;   // Tablet
		return 5;                                // Desktop
	}

	function getImageSrc(img) {
		// Nếu lazyload có data-src => dùng nó, nếu không thì fallback sang src
		const realSrc = img.getAttribute('data-src') || img.getAttribute('src');
		// Bỏ qua placeholder dạng data:image/svg+xml
		if (realSrc && realSrc.startsWith('data:image/svg')) return '';
		return realSrc;
	}

	function renderCarousel() {
		const perSlide = getImagesPerSlide();
		const chunks = [];
		for (let i = 0; i < allImages.length; i += perSlide) {
			chunks.push(allImages.slice(i, i + perSlide));
		}

		let html = '';
		chunks.forEach((group, index) => {
			html += `<div class="carousel-item ${index === 0 ? 'active' : ''}">
        <div class="row g-3 justify-content-center">`;

			group.forEach(img => {
				const src = getImageSrc(img);
				if (!src) return; // Bỏ ảnh placeholder
				html += `
          <div class="col-12 col-sm-6 col-lg-3 d-flex justify-content-center">
            <img
              src="${src}"
              alt="${img.alt || ''}"
              class="img-fluid w-100 rounded"
              loading="lazy"
            >
          </div>`;
			});

			html += `</div></div>`;
		});

		carouselInner.innerHTML = html;

		let dots = '';
		chunks.forEach((_, i) => {
			dots += `<button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="${i}" ${i === 0 ? 'class="active"' : ''} aria-label="Slide ${i + 1}"></button>`;
		});
		indicators.innerHTML = dots;
	}

	function handleResize() {
		let newBp = window.innerWidth < 576 ? 'xs' : window.innerWidth < 992 ? 'md' : 'lg';
		if (newBp !== currentBreakpoint) {
			currentBreakpoint = newBp;
			renderCarousel();
		}
	}

	window.addEventListener('resize', handleResize);
	handleResize();
});
