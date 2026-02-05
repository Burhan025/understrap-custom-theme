<?php
//function override_plant_type_filter_to_species($filters) {
//	// Check if plant_type filter exists
//	if (!isset($filters['plant_type'])) {
//		return $filters;
//	}
//
//	// Remove lookup (from lookup table) - we'll use postmeta instead
//	unset($filters['plant_type']['lookup']);
//
//	// Change meta key to Species
//	$filters['plant_type']['key'] = '_custom_product_meta__additional-details__species';
//
//	// Provide items array - plugin needs this to avoid using lookup
//	// Get distinct values from postmeta
//	$filters['plant_type']['items'] = get_plant_type_species_items();
//
//	return $filters;
//}
//add_filter('bs_product_filters', 'override_plant_type_filter_to_species', 10, 1);
//
///**
// * Get distinct species values from postmeta for filter dropdown
// *
// * @return array Array of distinct species values
// */
//function get_plant_type_species_items() {
//	global $wpdb;
//
//	$meta_key = '_custom_product_meta__additional-details__species';
//
//	// Query to get all distinct values
//	$results = $wpdb->get_col($wpdb->prepare("
//        SELECT DISTINCT pm.meta_value
//        FROM {$wpdb->postmeta} pm
//        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
//        WHERE pm.meta_key = %s
//        AND pm.meta_value != ''
//        AND pm.meta_value IS NOT NULL
//        AND p.post_type IN ('product', 'product_variation')
//        AND p.post_status = 'publish'
//        ORDER BY pm.meta_value ASC
//    ", $meta_key));
//
//	// Handle comma-separated values
//	$all_values = [];
//	foreach ($results as $value) {
//		if (empty($value)) continue;
//		$split_values = array_map('trim', explode(',', $value));
//		$all_values = array_merge($all_values, $split_values);
//	}
//
//	// Clean and return
//	$values = array_filter($all_values);
//	$values = array_unique($values);
//	sort($values);
//
//	return $values;
//}
function override_plant_type_filter_to_species($filters) {
	// Check if plant_type filter exists
	if (!isset($filters['plant_type'])) {
		return $filters;
	}

	// Remove lookup (from lookup table) - we'll use postmeta instead
	unset($filters['plant_type']['lookup']);

	// Change meta key to Species
	$filters['plant_type']['key'] = '_custom_product_meta__additional-details__species';

	// Provide items array - plugin needs this to avoid using lookup
	// Use optimized method with caching
	$filters['plant_type']['items'] = get_plant_type_species_items();

	return $filters;
}
add_filter('bs_product_filters', 'override_plant_type_filter_to_species', 10, 1);

/**
 * Get distinct species values using optimized WordPress API
 * Uses get_post_meta() with caching to avoid heavy queries
 *
 * @return array Array of distinct species values
 */
function get_plant_type_species_items() {
	$cache_key = 'bs_plant_type_species_items';
	$cached = get_transient($cache_key);

	// Return cached value if available
	if ($cached !== false) {
		return $cached;
	}

	$meta_key = '_custom_product_meta__additional-details__species';
	$all_values = [];

	// Use WP_Query with fields => ids to minimize data transfer
	$query = new WP_Query([
		'post_type' => ['product', 'product_variation'],
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids', // Only get IDs, not full post objects
		'meta_query' => [
			[
				'key' => $meta_key,
				'compare' => 'EXISTS',
			],
		],
		'no_found_rows' => true, // Skip pagination count
		'update_post_meta_cache' => false, // Don't cache all meta
		'update_post_term_cache' => false, // Don't cache terms
	]);

	// Get meta values only for products that have the meta key
	if ($query->have_posts()) {
		foreach ($query->posts as $post_id) {
			$meta_value = get_post_meta($post_id, $meta_key, true);

			if (empty($meta_value)) {
				continue;
			}

			// Handle comma-separated values
			$split_values = array_map('trim', explode(',', $meta_value));
			$all_values = array_merge($all_values, $split_values);
		}
	}

	wp_reset_postdata();

	// Clean, de-duplicate case-insensitively, preserve first seen casing
	$values = array_filter($all_values);
	$normalized = [];
	foreach ($values as $val) {
		$key = strtolower($val);
		if (!isset($normalized[$key])) {
			$normalized[$key] = $val;
		}
	}
	// Sort by lowercase for stable ordering
	uasort($normalized, static function ($a, $b) {
		return strcmp(strtolower($a), strtolower($b));
	});
	$values = array_values($normalized);

	// Cache for 2 hours to reduce queries
	set_transient($cache_key, $values, 2 * HOUR_IN_SECONDS);

	return $values;
}

/**
 * Clear cache when products are updated
 */
function clear_plant_type_species_cache($post_id) {
	if (get_post_type($post_id) === 'product' || get_post_type($post_id) === 'product_variation') {
		delete_transient('bs_plant_type_species_items');
	}
}
add_action('save_post', 'clear_plant_type_species_cache');
add_action('woocommerce_update_product', 'clear_plant_type_species_cache');
add_action('woocommerce_new_product', 'clear_plant_type_species_cache');


