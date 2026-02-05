<?php
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * Modifies the product tabs displayed on the product page.
 *
 * This function removes the 'additional_information' tab from the list
 * of product tabs.
 *
 * @param array $tabs An associative array of the current product tabs.
 *
 * @return array The modified array of product tabs.
 */
add_filter( 'woocommerce_product_tabs', 'dk_theme_product_tabs', 100 );
function dk_theme_product_tabs( $tabs ) {
	unset( $tabs['additional_information'] );

	if ( isset( $tabs['bsdk_specifications'] ) && isset( $tabs['bsdk_specifications']['callback'] ) && is_callable( $tabs['bsdk_specifications']['callback'] ) ) {
		ob_start();
		call_user_func( $tabs['bsdk_specifications']['callback'] );
		$content = ob_get_clean();
		$plain_text = trim( wp_strip_all_tags( $content ) );
		if ( empty( trim( $plain_text ) ) ) {
			unset( $tabs[ 'bsdk_specifications' ] );
		}
	}


	return $tabs;
}

add_filter( 'woocommerce_product_description_heading', function ($heading) {
	return '';
} );

add_filter( 'woocommerce_gallery_thumbnail_size', function ( $size ) {
	return array( 400, 400 );
} );

add_filter( 'loop_shop_columns', function ( $columns ) {
	if( is_shop() || is_product_taxonomy() ) {
		return 4;
	}
	return 5;
} );

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 5;
	$args['columns'] = 5;

	return $args;
} );

add_filter( 'woocommerce_product_review_comment_form_args', 'dk_theme_woocommerce_product_review_comment_form_args', 10, 1 );
function dk_theme_woocommerce_product_review_comment_form_args( $comment_form ) {
	$comment_form['format'] = 'xhtml';

	return $comment_form;
}

add_filter( 'woocommerce_product_get_image_id', 'dk_theme_filter_alter_product_image_id', 20, 1 );
function dk_theme_filter_alter_product_image_id( $image_id ) {
	if ( $image_id == "" ) {
		$placeholder_image_id = get_option( 'woocommerce_placeholder_image', 0 );
		if ( is_numeric( $placeholder_image_id ) ) {
			$image_id = (int) $placeholder_image_id;
		}
	}

	return $image_id;
}
