<?php
// Luôn hiển thị ảnh sản phẩm trong email order + set size cố định
add_filter('woocommerce_email_order_items_args', function ($args) {
	$args['show_image'] = true;
	$args['image_size'] = array(80, 80); // kích thước thumbnail trong email
	return $args;
}, 10, 1);

// Tùy biến HTML ảnh trong email để không bị alt text dài phá layout
add_filter('woocommerce_order_item_thumbnail', function ($image_html, $item) {

	$product = $item->get_product();
	if (!$product) {
		return $image_html;
	}

	$image_id = $product->get_image_id();
	if (!$image_id) {
		return ''; // không có hình thì bỏ trống
	}

	$src = wp_get_attachment_image_url($image_id, 'thumbnail');
	if (!$src) {
		return $image_html;
	}

	// alt="" để nếu email client chặn ảnh thì không show text dài
	return sprintf(
		'<img src="%s" alt="" width="80" height="80" style="display:block;width:80px;height:auto;max-width:80px;margin-right:10px;" />',
		esc_url($src)
	);
}, 10, 2);

add_filter('woocommerce_order_item_name', 'my_short_email_product_name', 10, 3);
function my_short_email_product_name($name, $item, $is_visible)
{

	// Chỉ áp dụng trong email (không áp dụng ở frontend / My Account)
	if (!did_action('woocommerce_email_header')) {
		return $name;
	}

	$max = 70; // giới hạn ký tự bạn muốn

	if (function_exists('mb_strlen')) {
		if (mb_strlen($name) > $max) {
			$name = mb_substr($name, 0, $max) . '…';
		}
	} else {
		if (strlen($name) > $max) {
			$name = substr($name, 0, $max) . '…';
		}
	}

	return $name;
}

