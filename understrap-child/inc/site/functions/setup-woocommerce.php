<?php
//add_action('after_setup_theme', function () {
////		remove_action('wp_body_open', 'woocommerce_demo_store', 10);
////		add_action('wp_footer', 'woocommerce_demo_store', 10);
//
//	add_filter('woocommerce_demo_store', function () {
//		if (!function_exists('is_store_notice_showing') || !is_store_notice_showing()) {
//			return;
//		}
//
//		$notice = get_option('woocommerce_demo_store_notice');
//
//		if (empty($notice)) {
//			return;
//		}
//
//		$notice_id = md5($notice);
//
//		echo '<p role="complementary" aria-label="' . esc_attr__('Store notice', 'woocommerce') . '" class="woocommerce-store-notice demo_store" data-notice-id="' . esc_attr($notice_id) . '" style="display:none;">' . wp_kses_post($notice) . ' <a role="button" href="#" class="woocommerce-store-notice__dismiss-link">' . esc_html__('Dismiss', 'woocommerce') . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
//	});
//
//}, 99);
//add_action('init', function () {
//	remove_action('wp_footer', 'woocommerce_demo_store');
//});
//function mytheme_custom_store_notice() {
//	// Chỉ hiển thị khi admin bật Store Notice trong WooCommerce
//	if ( 'yes' !== get_option( 'woocommerce_demo_store', 'no' ) ) {
//		return;
//	}
//
//	// Lấy nội dung từ option của WooCommerce
//	$notice = get_option( 'woocommerce_demo_store_notice' );
//
//	if ( empty( $notice ) ) {
//		$notice = __( 'This is a demo store for testing purposes — no orders shall be fulfilled.', 'understrap-child' );
//	}
//	?>
	<!--	<div class="my-store-notice" id="my-store-notice">-->
	<!--		<div class="my-store-notice__inner">-->
	<!--            <span class="my-store-notice__text">-->
	<!--                --><?php //echo wp_kses_post( $notice ); ?>
	<!--            </span>-->
	<!---->
	<!--			<button type="button"-->
	<!--					class="my-store-notice__close "-->
	<!--					aria-label="--><?php //esc_attr_e( 'Đóng thông báo', 'understrap-child' ); ?><!--">-->
	<!--				&times;-->
	<!--			</button>-->
	<!--		</div>-->
	<!--	</div>-->
	<!---->
	<!--	<script>-->
	<!--		(function () {-->
	<!--			var storageKey = 'my_store_notice_dismissed';-->
	<!---->
	<!--			// Nếu user đã tắt trước đó thì ẩn luôn-->
	<!--			if (window.localStorage && localStorage.getItem(storageKey)) {-->
	<!--				var el = document.getElementById('my-store-notice');-->
	<!--				if (el) el.style.display = 'none';-->
	<!--				return;-->
	<!--			}-->
	<!---->
	<!--			var btn = document.querySelector('.my-store-notice__close');-->
	<!--			if (!btn) return;-->
	<!---->
	<!--			btn.addEventListener('click', function () {-->
	<!--				var wrapper = document.getElementById('my-store-notice');-->
	<!--				if (wrapper) wrapper.style.display = 'none';-->
	<!--				if (window.localStorage) {-->
	<!--					localStorage.setItem(storageKey, '1');-->
	<!--				}-->
	<!--			});-->
	<!--		})();-->
	<!--	</script>-->
	<!--	--><?php
//}
//add_action( 'wp_footer', 'mytheme_custom_store_notice', 5 );
