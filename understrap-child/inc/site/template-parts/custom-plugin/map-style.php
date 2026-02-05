<?php

/**
 * Location selector modal template
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

use BurhanAftab\Deepknead\Deepknead;
use BurhanAftab\Deepknead\Locations\Location;
use BurhanAftab\Deepknead\Hooks;

if (empty($args) || empty($args['locations'])) {
	return;
}

$locations = $args['locations'];
$title = $args['title'] ?? '';
$description = $args['description'] ?? '';
$siteTitle = $args['siteTitle'] ?? '';

/**
 * @var Location[] $availableLocations
 */
$availableLocations = $args['availableLocations'] ?? [];

$maps = array_map(
	static fn (Location $location) => $location->position(),
	$availableLocations
);

$current_location_slug = $_COOKIE['cart_location'] ?? '';
$disableSubmitBtn = count($availableLocations) === 1 || $current_location_slug ? '' : 'disabled';
$active = $current_location_slug ? '' : 'active';

?>
<div id="locations-modal" class="bs-modal bs-location-selector bs-location-selector--map-style <?php echo esc_attr($active); ?>">
	<div class="bs-modal__wrapper">
		<div class="bs-modal__inner">
			<div class="bs-location-selector__content">
				<div class="bs-location-selector__header mb-3">
					<div class="bs-location-selector__logo mx-auto mb-3">

					</div>

					<?php $select_store_text = apply_filters(Hooks::LOCATION_SELECT_STORE_TEXT, __('Select a store', 'deepknead')); ?>
					<h2 class="bs-location-selector__title "><?php esc_html_e($select_store_text); ?></h2>
				</div>

				<div class="bs-location-selector__items">
					<?php
					foreach ($availableLocations as $location) :
						$autoSelected = count($availableLocations) === 1 || $location->slug() === $current_location_slug ? 'checked' : '';
						Deepknead::instance()->templateLoader()->loadTemplate('locations/list/map-style-item', [
							'location' => $location,
							'autoSelected' => $autoSelected
						]);
					endforeach;
					?>
				</div>

				<div class="bs-location-selector__footer">
					<button id="btn-bs-continue-select-location" type="button" class="bs-location-link bs-location-selector__button dk-bg--primary dk-color--white dk-border--primary" <?php echo esc_attr($disableSubmitBtn); ?>>
						<?php
						$cta_text = apply_filters(Hooks::LOCATION_MAP_CTA_TEXT, __('CONTINUE', 'deepknead'));
						esc_html_e($cta_text);
						?>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14" fill="none">
							<path d="M13 0L11.59 1.41L16.17 6H0V8H16.17L11.58 12.59L13 14L20 7L13 0Z" fill="white"/>
						</svg>
					</button>
				</div>

			</div>
			<?php
			$template = locate_template(
				'inc/site/template-parts/custom-plugin/location-image.php',
				false,
				false
			);

			if ($template) {
				load_template($template, false);
			}
			?>
		</div>
	</div>
</div>
