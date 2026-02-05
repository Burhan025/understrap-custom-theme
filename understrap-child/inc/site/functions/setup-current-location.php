<?php
use BurhanAftab\Deepknead\Deepknead;

/**
 * Shortcode: [location_google_reviews]
 * Display google reviews following current location
 */
//add_shortcode('location_google_reviews', function () {
//
//	if (!class_exists(Deepknead::class)) {
//		return '';
//	}
//
//	// Get current location
//	$locationProvider = Deepknead::instance()->locationDataProvider();
//	$location = $locationProvider->currentLocation();
//
//	if (!$location || !method_exists($location, 'slug')) {
//		return '';
//	}
//
//	$slug = $location->slug();
//	$name = $location->name() ?? ucfirst($slug);
//
//	// Get mapping from ACF Options Page
//	$rows = get_field('location_reviews', 'option');
//	$shortcode = '';
//
//	if (!empty($rows) && is_array($rows)) {
//		foreach ($rows as $row) {
//			if (
//				!empty($row['location_slug']) &&
//				$row['location_slug'] === $slug &&
//				!empty($row['review_shortcode'])
//			) {
//				$shortcode = $row['review_shortcode'];
//				break;
//			}
//		}
//	}
//
//	if (!$shortcode) {
//		return '';
//	}
//
//	return do_shortcode($shortcode);
//});


/**
 * Load select field options for ACF field `location_slug`
 */

add_action('admin_init', function () {

	if ( ! class_exists(Deepknead::class) ) {
		return;
	}

	$locationProvider = Deepknead::instance()->locationDataProvider();
	$locations        = $locationProvider->allLocations();

	if ( empty($locations) ) {
		return;
	}

	/* ============================
		Helper: ensure repeater data
	============================= */
	$ensure_repeater_slugs = function($field_key, $field_map) use ($locations) {

		$rows = get_field($field_key, 'option');

		if ( ! is_array($rows) ) {
			$rows = [];
		}

		$existing_slugs = wp_list_pluck($rows, $field_map['slug_key']);

		foreach ($locations as $location) {

			if (!method_exists($location, 'slug')) {
				continue;
			}

			$slug = $location->slug();

			if (in_array($slug, $existing_slugs, true)) {
				continue;
			}

			$rows[] = array_merge(
				[$field_map['slug_key'] => $slug],
				$field_map['default_values']
			);
		}

		update_field($field_key, $rows, 'option');
	};

	/* ============================
		Apply for each repeater
	============================= */

	// 1. location_reviews
	$ensure_repeater_slugs('location_reviews', [
		'slug_key'       => 'location_slug',
		'default_values' => [
			'review_shortcode' => ''
		]
	]);

	// 2. location_hours
	$ensure_repeater_slugs('location_hours', [
		'slug_key'       => 'location_slug',
		'default_values' => [
			'open_time'  => '',
			'close_time' => ''
		]
	]);
	//  3. location_images
	$ensure_repeater_slugs('location_images', [
		'slug_key'       => 'location_slug',
		'default_values' => [
			'location_image' => null, // ACF image field
		]
	]);
});

