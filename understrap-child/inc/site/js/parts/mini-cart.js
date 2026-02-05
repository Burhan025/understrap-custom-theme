(function ($) {
	"use strict";
	const style = document.createElement("style");
	style.innerHTML = `
    @keyframes slideInRight {
      0%   { transform: translateX(100%); opacity: 0; }
      100% { transform: translateX(0); opacity: 1; }
		}
		@keyframes slideOutRight {
			0%   { transform: translateX(0); opacity: 1; }
			100% { transform: translateX(100%); opacity: 0; }
		}
		.animate-in  { animation: slideInRight 0.4s ease forwards; }
		.animate-out { animation: slideOutRight 0.4s ease forwards; }
  `;
	document.head.appendChild(style);

	const $popup = $("#cart-popup");
	const $panel = $popup.find(".cart-popup-inner");
	let isOpen = false;
	$(function () {
		//  MutationObserver follow popup state
		if ($popup.length && $panel.length) {
			const observer = new MutationObserver(function (mutations) {
				mutations.forEach(function (mutation) {
					if (mutation.attributeName === "class") {
						if ($popup.hasClass("active")) {
							// Popup open → trigger animate
							$panel.removeClass("animate-out").addClass("animate-in");
						} else if (isOpen) {
							// Popup close
							isOpen = false;
							$panel
								.removeClass("animate-in")
								.addClass("animate-out")
								.one("animationend webkitAnimationEnd", function () {
									$panel.removeClass("animate-out");
								});
						}
					}
				});
			});

			observer.observe($popup[0], { attributes: true });
		}
	});
})(jQuery);
