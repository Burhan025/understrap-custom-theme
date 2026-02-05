document.addEventListener("click", function (e) {
	const wrap = e.target.closest(".mobile-search-wrapper .bs-search-field");
	if (!wrap) return;

	// Nếu user click vào vùng icon bên phải
	const rect = wrap.getBoundingClientRect();
	const clickX = e.clientX - rect.left;

	if (clickX > rect.width - 50) {
		wrap.closest("form").submit();
	}
});
