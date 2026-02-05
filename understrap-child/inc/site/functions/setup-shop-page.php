<?php
add_action( 'woocommerce_before_main_content', function () {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		?>
		<section class="section-hero-banner">
			<div class="section-hero-banner-center">

				<div class="hero-character mx-auto">

				</div>

				<div class="speech-bubble"
					 data-text-1="C’mon dude,
					 treat yourself."
					 data-text-2="You deserve
					 to high as well."
					 data-text-3="C’mon dude,
					 treat yourself."
					 data-text-4="You deserve
					 to high as well."
				>
				</div>


			</div>
		</section>
		<?php
	}
}, 1 );


/**
 * Outputs the custom top content block (after hero section) for the shop page if it is set.
 *
 * @return void This function does not return a value but echoes the content directly if applicable.
 */
function glenora_theme_shop_page_block_top_content() {
	if ( function_exists( 'is_shop' ) && ( is_shop() ) ) {
		$categories = get_field( 'feature_categories_banner', 'option' );
		set_query_var('shortcode_categories', $categories);
		$block      = array(
			'id'        => 'block_' . uniqid(),
			'data'      => array( 'featured_categories' => $categories, 'section_title' => '' ),
		);

		echo render_block( array(
			'blockName'    => 'acf/block-dk-featured-categories',
			'attrs'        => array( 'data' => $block['data'], 'id' => $block['id'] )
		) );
	}

}

add_action( 'woocommerce_before_main_content', 'glenora_theme_shop_page_block_top_content', 2 );
add_action( 'woocommerce_before_main_content', function () {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		?>
		<section class="section-hero section-hero-woocommerce d-none d-lg-block" >
			<div class="section-hero-content">
				<div class="breadcrumbs">
					<?php woocommerce_breadcrumb(); ?>
				</div>
				<?php
				/**
				 * Hook: woocommerce_show_page_title.
				 *
				 * Allow developers to remove the product taxonomy archive page title.
				 *
				 * @since 2.0.6.
				 */
				if ( apply_filters( 'woocommerce_show_page_title', true ) ) :
					?>
					<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>

				<?php
				/**
				 * Hook: woocommerce_archive_description.
				 *
				 * @since 1.6.2.
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				do_action( 'woocommerce_archive_description' );
				?>
			</div>
		</section>

	<?php }
}, 3 );
add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) && wp_is_mobile() ) {
		$classes[] = 'bs-layout-list';
	}
	return $classes;
});

/**
 * Use variation name on shop loop:
 * - If loop item is a variation: show its "variations" attribute only
 * - If loop item is a variable parent: show first variation's "variations"
 * - If no variation/attribute: show normal product title
 */

add_action('after_setup_theme', function () {

	// Remove WooCommerce default title output
	remove_action(
		'woocommerce_shop_loop_item_title',
		'woocommerce_template_loop_product_title',
		10
	);

	// Add custom title output
	add_action(
		'woocommerce_shop_loop_item_title',
		'bs_shop_loop_product_title_variation_only',
		10
	);
});

/**
 * Callback for shop loop title
 */
function bs_shop_loop_product_title_variation_only()
{
	global $product;

	if (!$product instanceof WC_Product) {
		return;
	}

	// Get the title
	$title = bs_get_loop_title_from_variation_or_product($product);

	echo '<h2 class="woocommerce-loop-product__title">' . esc_html($title) . '</h2>';
}

/**
 * Decide which title to use for the loop item
 *
 * @param WC_Product $product
 * @return string
 */
function bs_get_loop_title_from_variation_or_product(WC_Product $product)
{

	// 1) If loop item is a VARIATION (product-type-variation)
	if ($product->is_type('variation')) {

		// Custom attribute name is "variations"
		$short = $product->get_attribute('variations');

		// If attribute exists, use it as title
		if (!empty($short)) {
			return $short;
		}

		// Fallback: variation full name
		return $product->get_name();
	}

	// 2) If loop item is a VARIABLE parent, use its first child variation's "variations" value
	if ($product->is_type('variable')) {
		$children = $product->get_children();

		if (!empty($children)) {
			$first_child = wc_get_product(reset($children));

			if ($first_child instanceof WC_Product) {
				$short = $first_child->get_attribute('variations');
				if (!empty($short)) {
					return $short;
				}
			}
		}

		// Fallback: parent product name
		return $product->get_name();
	}

	// 3) Simple / other product types - just use normal product title
	return $product->get_name();
}
add_action( 'woocommerce_shop_loop_item_title', 'theme_show_brand_above_title', 5 );
function theme_show_brand_above_title() {

	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$brand = '';

	// if is variable product →  variation
	if ( $product->is_type( 'variable' ) ) {

		// Lấy tất cả variations
		$variations = $product->get_children();

		foreach ( $variations as $variation_id ) {

			$variation = wc_get_product( $variation_id );
			if ( ! $variation ) {
				continue;
			}

			// OPTIONAL: if  there location context, check here

			$brand = get_post_meta(
				$variation_id,
				'_custom_product_meta__additional-details__brand',
				true
			);

			if ( ! empty( $brand ) ) {
				break;
			}
		}

	} else {
		// Simple product fallback
		$brand = get_post_meta(
			$product->get_id(),
			'_custom_product_meta__additional-details__brand',
			true
		);
	}

	if ( empty( $brand ) ) {
		return;
	}

	echo '<div class="product-brand text-center">' . esc_html( $brand ) . '</div>';
}



/**
 * Add EQUIVALENT_TO filter with dynamic items from database
 */
//add_filter('bs_product_filters', function ($filters) {
//	// If there no query param 'equivalent_to', do not display filter field
//	if (!isset($_GET['equivalent_to'])) {
//		return $filters;
//	}
//
//	// Get unique values from database
//	global $wpdb;
//	$meta_key = '_custom_product_meta__details__equivalent-to';
//
//	$items = $wpdb->get_col($wpdb->prepare("
//        SELECT DISTINCT pm.meta_value
//        FROM {$wpdb->postmeta} pm
//        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
//        WHERE pm.meta_key = %s
//        AND p.post_type IN ('product', 'product_variation')
//        AND p.post_status = 'publish'
//        AND pm.meta_value != ''
//        ORDER BY CAST(pm.meta_value AS DECIMAL(10,2))
//    ", $meta_key));
//
//	if (!empty($items)) {
//		$filters['equivalent_to'] = [
//			'title' => __('Equivalent To', 'deepknead'),
//			'type' => 'select',
//			'key' => $meta_key,
//			'items' => $items,
//			'atts' => [
//				'hideSearch' => count($items) > 5,
//				'postfix' => 'g',
//			],
//		];
//	}
//
//	return $filters;
//}, 10, 1);


///**
// * Add EQUIVALENT_TO filter (URL only, no UI)
// */
//add_filter('bs_product_filters', function ($filters) {
//	$filters['equivalent_to'] = [
//		'title' => __('Equivalent To', 'deepknead'),
//		'type' => 'select',
//		'lockup' => 'equivalent_to',
//		'key' => '_custom_product_meta__details__equivalent-to',
//		'atts' => [
//				'hideSearch' => true,
//				'postfix' => 'g',
//			],
//	];
//
//	return $filters;
//}, 10, 1);
