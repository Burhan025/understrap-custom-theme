(/**
 * Handles various jQuery-based extensions and customizations for an e-commerce platform,
 * specifically tailored for single product pages, including serialization of form data,
 * AJAX add-to-cart functionality, and UI enhancements such as variation descriptions and sliders.
 *
 * @param {Object} $ - jQuery object for manipulation of DOM elements.
 */
function ($) {
	if (!jQuery('.single-product').length) {
		return;
	}
	const saleBadgeHtml = $('.single-product .onsale').first().prop('outerHTML');
	// Show more/less description
	var $form = $('form.variations_form');

	$form.on('show_variation', function( event, variation ) {
		var $descEl = $('.woocommerce-variation-description');
		if ($descEl.length) {
			$descEl.removeClass('variation-desc-clamped');

			var fullHeight = $descEl[0].scrollHeight;

			var lineHeight = parseFloat($descEl.css('line-height'));
			var clampHeight = lineHeight * 2;

			if (fullHeight > clampHeight) {
				$descEl.addClass('variation-desc-clamped');

				if ($descEl.next('.desc-readmore-toggle').length === 0) {
					$('<a class="desc-readmore-toggle">Read more</a>')
						.insertAfter($descEl)
						.on('click', function(e) {
							e.preventDefault();
							if ($descEl.hasClass('expanded')) {
								$descEl.removeClass('expanded variation-desc-clamped')
									.addClass('variation-desc-clamped');
								$(this).text('Read more');
							} else {
								$descEl.addClass('expanded').removeClass('variation-desc-clamped');
								$(this).text('Show less');
							}
						});
				}
			} else {
				$descEl.next('.desc-readmore-toggle').remove();
			}
		}

		// Show badge Sale
		$('.single-product .onsale').remove();
		if (variation && variation.display_price < variation.display_regular_price && saleBadgeHtml) {
			let $badge = $(saleBadgeHtml);
			const hasPercent = saleBadgeHtml.includes('%');
			if(hasPercent) {
				const discountPercent = Math.round((1 - (variation.display_price / variation.display_regular_price)) * 100);
				if(discountPercent > 0) {
					$badge.html('-' + discountPercent + '%');
				}
			}
			$('.woocommerce-product-gallery').before($badge);
		}
	});

	$form.on('hide_variation', function(){
		$('.single-product .onsale').remove();
	});


	/**
	 * Product Image Gallery Navigation
	 * Adds navigation arrows to the main product image for gallery navigation
	 */
	(function addProductGalleryNavigation() {
		document.addEventListener('DOMContentLoaded', function() {
			var checkInterval = setInterval(function() {
				var mainGalleryContainer = document.querySelector('.woocommerce-product-gallery');

				if (mainGalleryContainer) {
					clearInterval(checkInterval);

					var thumbnails = document.querySelectorAll('.flex-control-nav.flex-control-thumbs li');
					var thumbnailNav = document.querySelector('.flex-control-nav.flex-control-thumbs');
					var mainImages = document.querySelectorAll('.woocommerce-product-gallery__image');

					if (thumbnails.length === 0) return;

					mainGalleryContainer.querySelectorAll('.product-gallery-nav').forEach(function(arrow) {
						arrow.remove();
					});

					thumbnails[0].querySelector('img').classList.add('flex-active');


					var arrowSVGs = {
						prev: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 16L9.41 14.59L3.83 9L16 9L16 7L3.83 7L9.41 1.41L8 0L0 8L8 16Z" fill="white"/></svg>',
						next: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 0L6.59 1.41L12.17 7L0 7L0 9L12.17 9L6.59 14.59L8 16L16 8L8 0Z" fill="white"/></svg>'
					};

					var leftArrow = createArrow('product-gallery-nav--prev', arrowSVGs.prev);
					var rightArrow = createArrow('product-gallery-nav--next', arrowSVGs.next);


					function navigateToImage(newIndex) {
						if (newIndex < 0 || newIndex >= thumbnails.length) return;

						requestAnimationFrame(function() {
							thumbnails.forEach(function(thumb, index) {
								var thumbImg = thumb.querySelector('img');
								if (thumbImg) {
									thumbImg.classList.toggle('flex-active', index === newIndex);
								}
							});

							if (!tryFlexSliderNavigation(newIndex)) {
								setTimeout(function() {
									thumbnails[newIndex].click();
								}, 50);
							}

							updateMainImageDisplay(newIndex);
						});
					}


					function arrowClick(direction) {
						var currentIndex = getCurrentImageIndex();
						var newIndex = currentIndex + direction;


						if (direction === -1 && newIndex >= 0) {
							navigateToImage(newIndex);
						} else if (direction === 1 && newIndex < thumbnails.length) {
							navigateToImage(newIndex);
						}
					}


					function ensureMainImageSync() {
						var activeThumbnail = document.querySelector('.flex-control-nav.flex-control-thumbs li img.flex-active');
						if (activeThumbnail) {
							var activeIndex = getCurrentImageIndex();
							updateMainImageDisplay(activeIndex);
						}
					}


					leftArrow.addEventListener('click', function() { arrowClick(-1); });
					rightArrow.addEventListener('click', function() { arrowClick(1); });


					function createArrow(className, svgContent) {
						var arrow = document.createElement('div');
						arrow.className = 'product-gallery-nav ' + className;
						arrow.innerHTML = svgContent;
						return arrow;
					}


					thumbnails.forEach(function(thumbnail, index) {
						thumbnail.addEventListener('click', function() {

							requestAnimationFrame(function() {
								thumbnails.forEach(function(thumb, thumbIndex) {
									var thumbImg = thumb.querySelector('img');
									if (thumbImg) {
										thumbImg.classList.toggle('flex-active', thumbIndex === index);
									}
								});
							});


							setTimeout(function() {
								updateMainImageDisplay(index);
							}, 50);


							scrollThumbnailToActive(index);
						});
					});

					function getCurrentImageIndex() {
						var activeThumbnailImg = document.querySelector('.flex-control-nav.flex-control-thumbs li img.flex-active');

						if (activeThumbnailImg) {
							var activeThumbnail = activeThumbnailImg.closest('li');
							return activeThumbnail ? Array.prototype.indexOf.call(thumbnails, activeThumbnail) : 0;
						}
						return 0;
					}


					function tryFlexSliderNavigation(newIndex) {
						if (typeof jQuery === 'undefined' || !jQuery.fn.flexslider) return false;

						try {
							var $gallery = jQuery('.woocommerce-product-gallery');
							var $flexSlider = jQuery('.flex-viewport');
							var $wrapper = jQuery('.woocommerce-product-gallery__wrapper');


							if ($gallery.length && $gallery.data('flexslider')) {
								$gallery.flexslider('goTo', newIndex);
								return true;
							}

							if ($flexSlider.length && $flexSlider.data('flexslider')) {
								$flexSlider.flexslider('goTo', newIndex);
								return true;
							}

							if ($wrapper.length && $wrapper.data('flexslider')) {
								$wrapper.flexslider('goTo', newIndex);
								return true;
							}

							return false;
						} catch (error) {
							return false;
						}
					}

					function updateMainImageDisplay(newIndex) {
						try {
							var targetThumbnail = thumbnails[newIndex];
							if (!targetThumbnail) return;

							var targetThumbnailImg = targetThumbnail.querySelector('img');
							if (!targetThumbnailImg) return;

							var targetImageSrc = targetThumbnailImg.src;

							var targetMainImage = null;
							var mainImagesLength = mainImages.length;

							function normalizeUrl(url) {
								if (!url) return '';
								return url.replace(/-\d+x\d+(?=\.(jpg|jpeg|png|gif|webp)$)/i, '');
							}

							for (var i = 0; i < mainImagesLength; i++) {
								var container = mainImages[i];
								var img = container.querySelector('img');
								if (!img) continue;

								// Prefer container data-thumb only for accurate matching
								var candidateSrc = container.getAttribute('data-thumb');
								var mainImgBase = normalizeUrl(candidateSrc);
								var thumbImgBase = normalizeUrl(targetImageSrc);

								if (mainImgBase && thumbImgBase && mainImgBase === thumbImgBase) {
									targetMainImage = container;
									// If current src is a placeholder (e.g., svg data URI), swap to real src
									var currentSrcAttr = img.getAttribute('src') || '';
									if (/^data:image\//i.test(currentSrcAttr)) {
										var realSrc = img.getAttribute('data-src') || img.getAttribute('data-large_image') || candidateSrc;
										if (realSrc) {
											img.setAttribute('src', realSrc);
										}
									}
									break;
								}
							}

							if (targetMainImage) {
								requestAnimationFrame(function() {
									mainImages.forEach(function(img) {
										img.classList.remove('flex-active-slide');
									});

									targetMainImage.classList.add('flex-active-slide');

									setTimeout(function() {
										var flexViewport = document.querySelector('.flex-viewport .woocommerce-product-gallery__wrapper');
										if (flexViewport) {
											var imageWidth = targetMainImage.offsetWidth;
											var transformValue = 'translate3d(' + -(newIndex * imageWidth) + 'px, 0px, 0px)';

											if (flexViewport.style.transform !== transformValue) {
												flexViewport.style.transform = transformValue;
											}
										}
									}, 100);

									scrollThumbnailToActive(newIndex);
								});
							}
						} catch (error) {
						}
					}

					function scrollThumbnailToActive(activeIndex) {
						try {
							if (!thumbnailNav) return;

							var activeThumbnail = thumbnails[activeIndex];
							if (!activeThumbnail) return;


							var thumbnailWidth = activeThumbnail.offsetWidth;
							var thumbnailMargin = parseInt(window.getComputedStyle(activeThumbnail).marginRight) || 0;
							var totalThumbnailWidth = thumbnailWidth + thumbnailMargin;


							var scrollLeft = (activeIndex * totalThumbnailWidth) - (thumbnailNav.offsetWidth / 2) + (totalThumbnailWidth / 2);


							scrollLeft = Math.max(0, Math.min(scrollLeft, thumbnailNav.scrollWidth - thumbnailNav.offsetWidth));


							thumbnailNav.scrollTo({
								left: scrollLeft,
								behavior: 'smooth'
							});
						} catch (error) {
						}
					}


					mainGalleryContainer.appendChild(leftArrow);
					mainGalleryContainer.appendChild(rightArrow);


					updateArrowVisibility();


					setInterval(function() {
						ensureMainImageSync();
					}, 2000);

					var observer = new MutationObserver(function(mutations) {
						var hasRelevantChanges = mutations.some(function(mutation) {
							return mutation.type === 'childList' ||
								(mutation.type === 'attributes' &&
									(mutation.attributeName === 'src' || mutation.attributeName === 'class'));
						});

						if (hasRelevantChanges) {
							updateArrowVisibility();
						}
					});

					observer.observe(mainGalleryContainer, {
						childList: true,
						subtree: true,
						attributes: true,
						attributeFilter: ['src', 'class']
					});
				}
			}, 100);

			function updateArrowVisibility() {
				var mainImages = document.querySelectorAll('.woocommerce-product-gallery__image');
				var leftArrow = document.querySelector('.product-gallery-nav--prev');
				var rightArrow = document.querySelector('.product-gallery-nav--next');

				var shouldShow = mainImages.length > 1;
				var displayValue = shouldShow ? 'flex' : 'none';

				if (leftArrow) leftArrow.style.display = displayValue;
				if (rightArrow) rightArrow.style.display = displayValue;
			}
		});
	})();
})(jQuery);

