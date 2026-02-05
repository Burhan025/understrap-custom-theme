/*
* Put this-site JS here or create and import additional files from /parts/.
*
*/

/*  Scroll Header Animation */
document.addEventListener('DOMContentLoaded', function () {
	const header = document.getElementById("wrapper-navbar");
	const searchForm = document.getElementById("search-form");

	if (header && header.classList.contains("transparent-header")) {
		window.addEventListener("scroll", function () {
			if (window.scrollY > 10) {
				header.classList.add("is-scrolled");
			} else {
				const searchFormVisible =
					searchForm && searchForm.classList.contains("active");
				if (!searchFormVisible) {
					header.classList.remove("is-scrolled");
				}
			}
		});
	}

	function initMobileSearchToggle(options = {}) {
		const {
			buttonSelector = ".search-button-mb",
			formSelector = "#search-form",
			activeClass = "active",
			inputSelector = 'input[name="s"]',
		} = options;

		const mobileSearchBtn = document.querySelector(buttonSelector);
		const searchForm = document.querySelector(formSelector);

		if (!mobileSearchBtn || !searchForm) return;

		mobileSearchBtn.addEventListener("click", (e) => {
			e.preventDefault();
			searchForm.classList.toggle(activeClass);

			if (searchForm.classList.contains(activeClass)) {
				const input = searchForm.querySelector(inputSelector);
				if (input) input.focus();
			}
		});
	}
	function initSearchButtonScrollToggle() {
		const searchButtons = document.querySelectorAll(".search-button");
		const header = document.querySelector(".transparent-header");

		if (!header || searchButtons.length === 0) return;

		searchButtons.forEach((btn) => {
			btn.addEventListener("click", (e) => {
				e.preventDefault();
				header.classList.add("is-scrolled");
			});
		});
	}
	function initFooterMenuAccordion(menuSelector) {
		const menuItems = document.querySelectorAll(
			`${menuSelector} .menu-item-has-children`,
		);

		menuItems.forEach(function (menuItem) {
			// Create the wrapper div for <a> and <button>
			const wrapper = document.createElement("div");
			wrapper.classList.add("menu-item-wrapper");

			// Create toggle button
			const toggleBtn = document.createElement("button");
			toggleBtn.classList.add("submenu-toggle");
			toggleBtn.setAttribute("aria-label", "Toggle submenu");
			toggleBtn.innerHTML = '<i class="fas fa-angle-down"></i>';

			// Find the link <a> element
			const link = menuItem.querySelector("a");
			if (link) {
				// Move <a> and <button> into the new wrapper
				wrapper.appendChild(link);
				wrapper.appendChild(toggleBtn);

				// Insert the wrapper before the <ul class="sub-menu">
				const submenu = menuItem.querySelector(".sub-menu");
				if (submenu) {
					menuItem.insertBefore(wrapper, submenu); // Insert the wrapper above the submenu
				}
			}

			// Click event for accordion
			toggleBtn.addEventListener("click", function (e) {
				e.preventDefault();

				const submenu = menuItem.querySelector(".sub-menu");
				const icon = this.querySelector("i");
				const isOpen = submenu.style.display === "block";

				// Close all other submenus
				menuItems.forEach(function (otherItem) {
					const otherSubmenu = otherItem.querySelector(".sub-menu");
					const otherIcon = otherItem.querySelector(".submenu-toggle i");
					if (otherSubmenu && otherItem !== menuItem) {
						otherSubmenu.style.display = "none";
						if (otherIcon) {
							otherIcon.classList.add("fa-angle-down");
							otherIcon.classList.remove("fa-angle-up");
						}
					}
				});

				// Toggle the clicked submenu
				submenu.style.display = isOpen ? "none" : "block";
				icon.classList.toggle("fa-angle-down", isOpen);
				icon.classList.toggle("fa-angle-up", !isOpen);
			});
		});
	}

	initMobileSearchToggle();
	initSearchButtonScrollToggle();
	initFooterMenuAccordion(".footer-menu");

	document.addEventListener("click", function (e) {
		const btn = e.target.closest(".icon-close");
		if (!btn) return; // clicked somewhere else

		e.preventDefault();

		const wrapper =
			btn.closest(".message-wrapper") ||
			btn.closest(".woocommerce-message") ||
			btn.closest(".woocommerce-error") ||
			btn.closest(".woocommerce-notices-wrapper");
		btn.closest(".my-store-notice");

		if (wrapper) {
			wrapper.style.display = "none";
		}
	});
});
