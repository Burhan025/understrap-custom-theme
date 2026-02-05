/**
 * Filter Popup Functionality
 * Handles mobile filter popup behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterContainer = document.querySelector('.bs-filters');
    const filterBody = document.querySelector('.bs-filters__body');
    const body = document.body;

    if (!filterContainer || !filterBody) return;

    // Add close button to filter body for mobile
    function addCloseButton() {
        // Check if close button already exists
        if (filterBody.querySelector('.filter-close-btn')) return;

        // Create close button
        const closeButton = document.createElement('div');
        closeButton.className = 'filter-close-btn';
        closeButton.innerHTML = '×';
        closeButton.setAttribute('aria-label', 'Close filters');

        // Add to filter body
        filterBody.appendChild(closeButton);

        // Add click handler for close button
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            hideFilters();
        });
    }

    // Add close button (will be hidden on desktop with CSS)
    addCloseButton();

    // Function to show filters
    function showFilters() {
        // Wait for bs-filters--active class to exist, then add filters-active to body
        const checkClass = setInterval(() => {
            if (filterContainer.classList.contains('bs-filters--active')) {
                body.classList.add('filters-active');
                clearInterval(checkClass);
            }
        }, 100); // Check every 100ms
    }

    // Function to hide filters
    function hideFilters() {
        filterContainer.classList.remove('bs-filters--active');
        body.classList.remove('filters-active');
    }

    // Close on overlay click (outside filter body)
    filterContainer.addEventListener('click', function(e) {
        if (e.target === filterContainer) {
            hideFilters();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && filterContainer.classList.contains('bs-filters--active')) {
            hideFilters();
        }
    });

    // Add click handler for filter toggle button
    const filterToggle = document.querySelector('.bs-filters__toggle');
    if (filterToggle) {
        filterToggle.addEventListener('click', function() {
            showFilters();
        });
    }


});
