<?php
add_filter( 'woocommerce_product_tabs', 'dk_custom_reorder_tabs', 10 );
function dk_custom_reorder_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['priority'] = 10;
	}

	return $tabs;
}

add_filter( 'woocommerce_output_related_products_args', 'glenora__custom_related_products', 100 );
function glenora__custom_related_products( $args ) {
	$args['posts_per_page'] = 8; // show 6 related products
	$args['columns'] = 5; // 5 per row
	return $args;
}

/**
 * Display Brand name above product title
 */
add_action( 'woocommerce_single_product_summary', 'dk_display_product_brand_above_title', 4 );
function dk_display_product_brand_above_title() {
	global $product;

	if ( ! $product ) {
		return;
	}

	// Get the product ID (handle both simple and variable products)
	$product_id = $product->get_id();

	// For variable products, get the first variation's brand
	if ( $product->is_type( 'variable' ) ) {
		$variations = $product->get_available_variations();
		if ( ! empty( $variations ) ) {
			$variation_id = $variations[0]['variation_id'];
			$brand = get_post_meta( $variation_id, '_custom_product_meta__additional-details__brand', true );
		}
	} else {
		// For simple products, get brand from product meta
		$brand = get_post_meta( $product_id, '_custom_product_meta__additional-details__brand', true );
	}

	// Display brand if it exists
	if ( ! empty( $brand ) ) {
		echo '<div class="product-brand-above-title">';
		echo '<p class="brand-label font-body-xs"><strong>' . esc_html( $brand ) . '</strong></p>';
		echo '</div>';
	}
}

// Disable zoom on product images
add_action( 'after_setup_theme', function() {
	remove_theme_support( 'wc-product-gallery-zoom' );
}, 99);

// Change Just display Variation Product Name when have notice Outstock
add_filter('woocommerce_cart_product_not_enough_stock_message', function ($message, $product, $stock_quantity) {

	if ($product->is_type('variation')) {
		$attributes = $product->get_attributes(); // key => value
		$variation_values = [];

		foreach ($attributes as $attr_key => $attr_value) {
			// Bỏ attribute "locations"
			if (strpos($attr_key, 'locations') !== false) {
				continue;
			}

			$variation_values[] = is_array($attr_value) ? implode(', ', $attr_value) : $attr_value;
		}

		$variation_name = implode(', ', $variation_values);

		if (empty($variation_name)) {
			$variation_name = $product->get_sku() ?: __('Variation', 'woocommerce');
		}

		$message = sprintf(
			__('You cannot add that amount of "%1$s" to the cart because there is not enough stock (%2$s remaining).', 'woocommerce'),
			$variation_name,
			wc_format_stock_quantity_for_display($stock_quantity, $product)
		);
	}

	return $message;

}, 10, 3);

