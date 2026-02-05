<?php
/**
 * Convert % or mg by each category
 */
function dk_theme_convert_unit_percent_to_mg($unit, $variationId, $lower = true)
{
	if ( ! empty( $variationId ) && dk_theme_is_variation_in_category( $variationId ) ) {
		$unit = $lower ? 'mg' : 'MG';
	}

	if ( empty( $unit ) ) {
		$unit = '%';
	}

	return $unit;
}

function dk_theme_is_variation_in_category( $variation_id ) {
	$variation_product = wc_get_product( $variation_id );
	if ( $variation_product && $variation_product->is_type( 'variation' ) ) {
		$parent_id = $variation_product->get_parent_id();
		if ( $parent_id ) {
			if ( is_product_taxonomy() ) {
				$current_term       = get_queried_object();
				$product_categories = [ $current_term->slug ];
			} else {
				$categories         = wp_get_post_terms( $parent_id, 'product_cat' );
				$extract_slugs      = function ( $filter ) use ( $categories ) {
					return array_map( function ( $cat ) {
						return $cat->slug;
					}, array_filter( $categories, $filter ) );
				};
				$child_slugs        = $extract_slugs( function ( $term ) {
					return (int) $term->parent !== 0;
				} );
				$product_categories = ! empty( $child_slugs ) ? $child_slugs : $extract_slugs( function ( $term ) {
					return (int) $term->parent === 0;
				} );
			}
//			$categories_valid = array( 'edibles', 'capsules-tablets', 'cbd-edibles', 'drinks', 'chocolates', 'gummies', 'other-edibles', 'alternatives', 'oils', 'capsules', 'plants', 'seeds', 'topicals' );
			return dk_theme_has_matching_category($product_categories);
		}
	}

	return false;
}

function dk_theme_has_matching_category($product_categories)
{

	$categories_valid = array('edibles', 'capsules-tablets', 'cbd-edibles', 'drinks', 'beverages', 'wellness', 'chocolates', 'gummies', 'other-edibles', 'alternatives', 'oils', 'capsules', 'plants', 'seeds', 'topicals');

	foreach ($product_categories as $product_category) {
		if (in_array($product_category, $categories_valid, true)) {
			return true;
		}
	}

	return false;
}


function dk_theme_has_special_chars( $str ) {
	$specialChars = '!@#$%^&*()_=+[{]};:"<>/?\\|';

	return strpbrk( $str, $specialChars ) !== false;
}

/**
 * Get a location slug of order
 *
 * @param $input_order order id or order object
 */
function dk_theme_get_order_location_slug( $input_order ) {
	$order    = null;
	$location = null;
	if ( is_numeric( $input_order ) ) {
		$order = wc_get_order( $input_order );
	}

	if ( is_object( $input_order ) ) {
		$order = $input_order;
	}

	if ( is_object( $order ) ) {
		$items = $order->get_items();
		if ( is_array( $items ) && count( $items ) > 0 ) {
			$first_item   = array_shift( $items );
			$variation_id = $first_item->get_variation_id();
			$variation    = wc_get_product( $variation_id );
			if ( $variation ) {
				$attributions = $variation->get_attributes( 'pa_locations' );
				$location     = $attributions['pa_locations'] ?? null;
			}
		}
	}

	return $location;
}
