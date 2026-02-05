<?php
add_filter( 'woocommerce_thankyou_order_received_text', function ($text) {
	return 'Thank you for supporting your local retailer';
}, 99 );
