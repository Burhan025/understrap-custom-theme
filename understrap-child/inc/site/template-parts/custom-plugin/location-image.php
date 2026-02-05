<?php
/**
 * Location image (LEFT only)
 * Sync image when location selector (radio) changes
 */

declare(strict_types=1);

use BurhanAftab\Deepknead\Deepknead;

if (!defined('ABSPATH') || !class_exists(Deepknead::class)) {
	return;
}

/* =========================
 * 1. Get current location
 ========================= */
$provider        = Deepknead::instance()->locationDataProvider();
$currentLocation = $provider->currentLocation();
$currentSlug     = $currentLocation ? $currentLocation->slug() : '';

/* =========================
 * 2. Get ACF data (Options)
 * Repeater: location_images
 * - location_slug
 * - location_image_desktop
 * - location_image_mobile
 ========================= */
$rows = get_field('location_images', 'option');

if (!is_array($rows)) {
	return;
}

/* =========================
 * 3. Build image map
 * slug => { desktop, mobile }
 ========================= */
$imageMap        = [];
$currentDesktop  = '';
$currentMobile   = '';

foreach ($rows as $row) {
	if (empty($row['location_slug'])) {
		continue;
	}

	$slug = (string) $row['location_slug'];

	$desktop = is_array($row['location_image_desktop'] ?? null)
		? (string) $row['location_image_desktop']['url']
		: '';

	$mobile = is_array($row['location_image_mobile'] ?? null)
		? (string) $row['location_image_mobile']['url']
		: $desktop;

	if (!$desktop) {
		continue;
	}

	$imageMap[$slug] = [
		'desktop' => $desktop,
		'mobile'  => $mobile,
	];

	if ($slug === $currentSlug) {
		$currentDesktop = $desktop;
		$currentMobile  = $mobile;
	}
}

/* =========================
 * 4. Nothing to render
 ========================= */
if (!$currentDesktop) {
	return;
}
?>

<!-- ======================
     LOCATION IMAGE (LEFT)
====================== -->
<div class="bs-location-selector__left">
	<picture id="bs-location-left-image">
		<?php if ($currentMobile) : ?>
			<source
				media="(max-width: 767px)"
				srcset="<?php echo esc_url($currentMobile); ?>"
			/>
		<?php endif; ?>

		<img
			class="bs-location-selector__left-image"
			src="<?php echo esc_url($currentDesktop); ?>"
			alt="<?php esc_attr_e('Location image', 'deepknead'); ?>"
			loading="eager"
		/>
	</picture>
</div>

<!-- ======================
     Inject image map to JS
====================== -->
<script>
	window.DK_LOCATION_IMAGE_MAP = <?php echo wp_json_encode($imageMap); ?>;
</script>

<!-- ======================
     JS: Sync image with selector
====================== -->
<script>
	(function () {

		if (
			typeof window.DK_LOCATION_IMAGE_MAP === 'undefined' ||
			!window.DK_LOCATION_IMAGE_MAP
		) {
			return;
		}

		const imageMap = window.DK_LOCATION_IMAGE_MAP;

		const picture = document.getElementById('bs-location-left-image');
		if (!picture) return;

		const img = picture.querySelector('img');
		const source = picture.querySelector('source');

		if (!img) return;

		function isMobile() {
			return window.matchMedia('(max-width: 767px)').matches;
		}

		function updateImage(slug) {
			if (!slug || !imageMap[slug]) return;

			const data = imageMap[slug];
			const desktopUrl = data.desktop;
			const mobileUrl  = data.mobile || data.desktop;

			if (!desktopUrl) return;

			/**
			 * QUAN TRỌNG:
			 * - Desktop dùng <img src>
			 * - Mobile dùng <source srcset>
			 */
			if (source) {
				source.srcset = mobileUrl;
			}

			img.src = desktopUrl;

			/**
			 * Force browser re-evaluate <picture>
			 * (cực kỳ quan trọng trên mobile)
			 */
			img.src = img.src;
		}

		/* INIT */
		const checkedRadio = document.querySelector(
			'.bs-location-selector__item-radio:checked'
		);

		if (checkedRadio) {
			updateImage(
				checkedRadio.dataset.locationSlug || checkedRadio.value
			);
		}

		/* LISTEN */
		document.addEventListener('change', function (e) {
			const target = e.target;

			if (
				!target ||
				!target.classList.contains('bs-location-selector__item-radio')
			) {
				return;
			}

			updateImage(
				target.dataset.locationSlug || target.value
			);
		});

	})();
</script>

