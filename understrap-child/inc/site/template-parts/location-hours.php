<?php
use BurhanAftab\Deepknead\Deepknead;

if (!class_exists(Deepknead::class)) return;

$provider = Deepknead::instance()->locationDataProvider();
$location = $provider->currentLocation();
if (!$location) return;

$slug = $location->slug();
//$location_name = $location->name();
$rows = get_field('location_hours', 'option');

if (!$rows) return;

$match = null;
foreach ($rows as $row) {
	if (!empty($row['location_slug']) && $row['location_slug'] === $slug) {
		$match = $row;
		break;
	}
}

if (!$match) return;

function render_hours_block($label, $items) {
	if (!$items) {
		return;
	}
	?>
	<div class="location-hours__block mb-3 mb-lg-5">
		<h6 class="ft-title ft-title-no-line mb-2 text-start">
			<?php echo esc_html($label); ?>
		</h6>
		<?php foreach ($items as $item) : ?>
			<div class="location-hours__row d-flex justify-content-between mb-2">
				<span class="days"><?php echo esc_html($item['days']); ?></span>
				<span class="hours"><?php echo esc_html($item['hours']); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}


render_hours_block('In-Store Hours', $match['instore_hours'] ?? []);
render_hours_block('Delivery Hours', $match['delivery_hours'] ?? []);
