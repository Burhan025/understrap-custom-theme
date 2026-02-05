<?php
//function dk_custom_filter_titles( $filters ) {
////	print_r($filters);
//	// Change specific filter titles
//	$filters['net_weight']['title'] = 'Size';
//	unset( $filters['package_date'] );
//
//	return $filters;
//}
//
//add_filter( 'bs_product_filters', 'dk_custom_filter_titles', 10, 1 );

function dk_custom_filter_options( $options ) {
//	print_r($options);
	// Only process terpenes filter
	if ( $options['name'] === 'terpenes' && ! empty( $options['items'] ) ) {
		// Filter out empty values from the items
		$options['items'] = array_filter( $options['items'], function ( $item ) {
			// Handle both string and array items
			if ( is_array( $item ) ) {
				$value = $item['value'] ?? $item['label'] ?? '';
			} else {
				$value = $item;
			}

			return ! empty( trim( $value ) );
		} );

		// Re-index the array
		$options['items'] = array_values( $options['items'] );
	}

	//Change Unit of THC and CBD mg/unit
	if ( $options['name'] === 'thc' || $options['name'] === 'cbd' ) {
		$options['postfix'] = 'mg/unit';
	}
	if ( in_array( $options['name'], array( "brand", "plant_type", "terpenes" ) ) && ! $options['hideSearch'] ) {
		$options['limit'] = 0;
	} else {
		$options['limit'] = 5;
	}
	if ( $options['name'] === 'sugar_free' || $options['name'] === 'vegan' ) {
		// Select
		$options['items'] = array( 'Yes', 'No' );

		// Drop down
		/*
		$options['items'] = array(
			array(
				'value' => 'Yes',
				'label' => 'Yes'
			),
			array(
				'value' => 'No',
				'label' => 'No'
			),
		);
		*/
	}

	return $options;
}

add_filter( 'bs_product_filter_options', 'dk_custom_filter_options', 20, 1 );


/**
 * Override the "Discount only" text in the on-sale filter
 * Override the "Category" text in the category filter
 * Since no override templates, override the translation
 */
add_filter( 'gettext', function ( $translated_text, $text, $domain ) {
//	if ( $domain === 'deepknead' && $text === 'Discount only' ) {
//		return 'On Sale';
//	}
	if ( $domain === 'deepknead' && $text === 'Category' ) {
		return 'Product Category';
	}

	return $translated_text;
}, 20, 3 );

/**
 * Change the text of the sortings
 */
add_filter( 'bs_all_sortings', 'dk_change_text_sortings', 20 );
function dk_change_text_sortings( $sortings ) {
	$custom_labels = [
		'date'          => __( 'Newest', 'dk-theme' ),
		'popularity'    => __( 'Popularity', 'dk-theme' ),
		'price'         => __( 'Price: Low to high', 'dk-theme' ),
		'price-desc'    => __( 'Price: High to low', 'dk-theme' ),
		'alphabet'      => __( 'Alphabetically: a - z', 'dk-theme' ),
		'alphabet-desc' => __( 'Alphabetically: z - a', 'dk-theme' ),
		'thc'           => __( 'THC %: Low to high', 'dk-theme' ),
		'thc-desc'      => __( 'THC %: High to low', 'dk-theme' ),
		'cbd'           => __( 'CBD %: Low to high', 'dk-theme' ),
		'cbd-desc'      => __( 'CBD %: High to low', 'dk-theme' ),
	];

	foreach ( $custom_labels as $key => $label ) {
		$sortings[ $key ] = $label;
	}

	return $sortings;
}

add_filter( 'gettext', function( $translated_text, $text, $domain ) {
	if ( $text === 'Filter' && $domain === 'deepknead' ) {
		$translated_text = 'Filters';
	}
	return $translated_text;
}, 10, 3 );
