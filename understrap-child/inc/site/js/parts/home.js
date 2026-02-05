document.addEventListener('DOMContentLoaded', function () {
	function initFlickityCarousel(selector, options = {}) {
		const breakpoint = typeof options.breakpoint === 'number' ? options.breakpoint : 768;
		const elements = document.querySelectorAll(selector);

		// debounce helper
		function debounce(fn, wait = 100) {
			let t;
			return function (...args) {
				clearTimeout(t);
				t = setTimeout(() => fn.apply(this, args), wait);
			};
		}

		// helper: wating for image element load (hoặc timeout)
		function imagesLoadedPromise(parent, timeout = 2500) {
			return new Promise((resolve) => {
				const imgs = Array.from(parent.querySelectorAll('img'));
				if (imgs.length === 0) return resolve();

				let remaining = imgs.length;
				let done = false;

				const check = () => {
					if (done) return;
					remaining--;
					if (remaining <= 0) {
						done = true;
						resolve();
					}
				};

				imgs.forEach(img => {
					if (img.complete && img.naturalHeight !== 0) {
						check();
					} else {
						const onLoadOrErr = () => {
							img.removeEventListener('load', onLoadOrErr);
							img.removeEventListener('error', onLoadOrErr);
							check();
						};
						img.addEventListener('load', onLoadOrErr);
						img.addEventListener('error', onLoadOrErr);
					}
				});

				setTimeout(() => {
					if (!done) {
						done = true;
						resolve();
					}
				}, timeout);
			});
		}

		elements.forEach((carouselElem) => {
			let flkty = null;
			let flickityResizeHandler = null;
			let flickitySettleHandler = null;

			function setEqualHeight(cellSelector) {
				const cells = Array.from(carouselElem.querySelectorAll(cellSelector));
				if (!cells.length) return;

				cells.forEach(c => { c.style.height = ''; });
				let maxH = 0;
				cells.forEach(c => {
					const h = Math.round(c.getBoundingClientRect().height);
					if (h > maxH) maxH = h;
				});
				if (maxH > 0) {
					cells.forEach(c => { c.style.height = maxH + 'px'; });
				}
			}

			function resetHeights(cellSelector) {
				const cells = Array.from(carouselElem.querySelectorAll(cellSelector));
				cells.forEach(c => { c.style.height = ''; });
			}

			async function handleInitDestroy() {
				const shouldInit = window.innerWidth < breakpoint;
				const cellSelector = options.cellSelector || '.wp-block-column';

				if (shouldInit && !flkty) {
					// chỉ xóa Bootstrap class khi thực sự khởi tạo Flickity
					carouselElem.classList.remove(
						'd-lg-flex',
						'row',
						'row-cols-1',
						'row-cols-sm-2',
						'row-cols-lg-4',
						'g-4'
					);

					// instance Flickity
					flkty = new Flickity(carouselElem, Object.assign({
						cellSelector: cellSelector,
						cellAlign: options.cellAlign || 'center',
						contain: options.contain ?? true,
						wrapAround: options.wrapAround ?? true,
						pageDots: options.pageDots ?? true,
						prevNextButtons: options.prevNextButtons ?? true,
						groupCells: options.groupCells ?? 1,
					}, options));

					await imagesLoadedPromise(carouselElem, 3500);
					setEqualHeight(cellSelector);

					flickityResizeHandler = debounce(() => setEqualHeight(cellSelector), 80);
					flickitySettleHandler = () => setEqualHeight(cellSelector);

					flkty.on('resize', flickityResizeHandler);
					flkty.on('settle', flickitySettleHandler);

				} else if (!shouldInit && flkty) {
					try {
						if (flickityResizeHandler) flkty.off('resize', flickityResizeHandler);
						if (flickitySettleHandler) flkty.off('settle', flickitySettleHandler);
					} catch (e) {}

					try { flkty.destroy(); } catch (e) {}
					flkty = null;

					resetHeights(cellSelector);
				}
			}

			handleInitDestroy();

			const debouncedHD = debounce(handleInitDestroy, 140);
			window.addEventListener('resize', debouncedHD);

			const mo = new MutationObserver(() => {
				if (!document.body.contains(carouselElem)) {
					window.removeEventListener('resize', debouncedHD);
					mo.disconnect();
					if (flkty) {
						try { flkty.destroy(); } catch (e) {}
						flkty = null;
					}
				}
			});
			mo.observe(document.body, { childList: true, subtree: true });
		});
	}

	//  Init Carousel
	initFlickityCarousel('.js-locations-carousel', {
		cellSelector: '.wp-block-column',
		breakpoint: 768, //
		groupCells: true,
	});

	initFlickityCarousel('.our-blog .blog-cards-block .blog-cards-carousel', {
		cellSelector: '.post-col',
		breakpoint: 768,
	});
});
