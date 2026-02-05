<?php

//add_filter('woocommerce_email_styles', function ($css) {
//	$css .= "
//		img {
//		max-width: 80px !important;
//		height: auto !important;
//		display: block !important;
//		}
//		";
//	return $css;
//});

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
// Chỉ áp dụng cho email
	if (did_action('woocommerce_email_header')) {
		$attr['alt'] = '';  // xoá alt
	}
	return $attr;
}, 10, 3);
/**
 * Remove A3 Lazy Load placeholders from WooCommerce email images
 */
/**
 * Add custom class to WooCommerce email product thumbnails
 */
add_filter('woocommerce_order_item_thumbnail', function ($image_html, $item) {

	// Chỉ áp dụng trong email WooCommerce
	if (did_action('woocommerce_email_header')) {

		// Chèn class wc-email-thumb vào thẻ <img>
		$image_html = str_replace(
			'<img',
			'<img class="wc-email-thumb"',
			$image_html
		);
	}

	return $image_html;

}, 10, 2);
add_filter('woocommerce_order_item_thumbnail', function ($image_html, $item) {

	if (did_action('woocommerce_email_header')) {

		// Chèn skip-lazy + data-skip-lazy
		$image_html = str_replace(
			'<img',
			'<img class="skip-lazy" data-skip-lazy',
			$image_html
		);
	}

	return $image_html;

}, 10, 2);
/**
 * Hide A3 Lazy Load placeholder image in WooCommerce emails
 */
add_filter('woocommerce_email_styles', function ($css) {

	$css .= "
        /* Ẩn placeholder của A3 Lazy Load trong email */
        img.lazy-hidden,
        img[src*='lazy_placeholder'] {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            mso-hide: all !important; /* Hide in Outlook */
        }
    ";

	return $css;
});
