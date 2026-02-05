<?php
/**
 * Customize the shop page
 */

remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

add_action( 'woocommerce_before_shop_loop', 'dk_theme_shop_page_block_top_content', 1 );
/**
 * Outputs the custom top content block for the shop page if it is set.
 *
 * This function checks if the current page is the shop page and retrieves
 * the custom content associated with it through Advanced Custom Fields (ACF).
 * If custom content is defined, it displays the content inside a div element
 * with specific classes and applies the 'the_content' filter to it.
 *
 * @return void This function does not return a value but echoes the content directly if applicable.
 */
function dk_theme_shop_page_block_top_content() {
	if ( is_shop() ) {
		$shop_page_options = get_field( 'shop_page', 'option' );

		if ( $shop_page_options && !empty($shop_page_options['shop_top_content']) ) {
			echo '<div class="dk-shop-content woocommerce-shop-top-content">';
			echo apply_filters('the_content', $shop_page_options['shop_top_content']);
			echo '</div>';
		}
	}
}

add_action( 'woocommerce_after_shop_loop', 'dk_theme_shop_page_block_bottom_content', 99 );
/**
 * Outputs the bottom content for the WooCommerce shop page.
 * Fetches the content from the custom field 'shop_page' set in theme options, specifically the
 * 'shop_bottom_content' field, and displays it if it is not empty.
 *
 * @return void
 */
function dk_theme_shop_page_block_bottom_content() {
	if ( is_shop() ) {
		$shop_page_options = get_field( 'shop_page', 'option' );

		if ( $shop_page_options && !empty($shop_page_options['shop_bottom_content']) ) {
			echo '<div class="dk-shop-content woocommerce-shop-bottom-content">';
			echo apply_filters('the_content', $shop_page_options['shop_bottom_content']);
			echo '</div>';
		}
	}
}

add_action( 'woocommerce_before_shop_loop', 'dk_theme_product_category_block_top_content', 1 );
/**
 * Displays the top content block for a WooCommerce product category, if available.
 *
 * This function checks if the current page is a product category page. If so, it retrieves
 * and outputs the custom top content associated with the product category. The content is
 * processed through WordPress filters before being displayed.
 *
 * @return void
 */
function dk_theme_product_category_block_top_content() {
	if ( is_product_category() ) {
		$current_category = get_queried_object();
		$category_top_content = get_field( 'category_top_content', 'product_cat_' . $current_category->term_id );

		if ( $category_top_content && !empty($category_top_content) ) {
			echo '<div class="dk-shop-content woocommerce-category-top-content">';
			echo apply_filters('the_content', $category_top_content);
			echo '</div>';
		}
	}
}

add_action( 'woocommerce_after_shop_loop', 'dk_theme_product_category_block_bottom_content', 99 );
/**
 * Outputs the bottom content for the current product category on WooCommerce category pages.
 * Fetches the content from the custom field 'category_bottom_content' associated with the taxonomy term
 * and displays it if not empty.
 *
 * @return void
 */
function dk_theme_product_category_block_bottom_content() {
	if ( is_product_category() ) {
		$current_category = get_queried_object();
		$category_bottom_content = get_field( 'category_bottom_content', 'product_cat_' . $current_category->term_id );

		if ( $category_bottom_content && !empty($category_bottom_content) ) {
			echo '<div class="dk-shop-content woocommerce-category-bottom-content">';
			echo apply_filters('the_content', $category_bottom_content);
			echo '</div>';
		}
	}
}

/**
 * Convert % or mg by each category
 */
add_filter('bs_product_meta_field', 'dk_theme_product_meta_unit', 999, 3);
function dk_theme_product_meta_unit($field, $variation, $position)
{
	if ($field['name'] === 'thc' || $field['name'] === 'cbd') {
		$field['unit'] = dk_theme_convert_unit_percent_to_mg($field['unit'], $variation->get_id());
	}
	return $field;
}

add_filter('bs_product_filter_options', 'dk_theme_product_filter_meta_unit', 999, 1);
function dk_theme_product_filter_meta_unit($options)
{

	if ($options['name'] === 'thc' || $options['name'] === 'cbd') {
		if (is_product_category()) {
			$current_term = get_queried_object();
			$product_categories = [$current_term->slug];
			$options['postfix'] = dk_theme_has_matching_category($product_categories) ? "mg/unit" : "%";
		} else {
			$options['postfix'] = "% - mg/unit";
		}
	}
	return $options;
}

/**
 * [Change Text] "Discount only" on sidebar Filter
 */
add_filter('bs_discount_only_text', 'dk_theme_discount_only_text');
function dk_theme_discount_only_text($text) {
	return 'On sale';
}
