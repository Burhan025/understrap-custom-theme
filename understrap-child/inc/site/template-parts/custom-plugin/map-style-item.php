<?php
/**
 * Single location selector item for map-style.
 *
 * NOTE: This template overrides the 'map-style-item' template part from DeepKnead Plugin.
 */
declare(strict_types=1);

defined('ABSPATH') || exit;

use BurhanAftab\Deepknead\Locations\Location;

/**
 * @var Location $location
 * @var string $locationSlug
 */
$location             = $args['location'];
$autoSelected         = $args['autoSelected'];

$locationSlug         = $location->slug();
?>
<div class="bs-location-selector__item">

    <label class="bs-location-selector__item-detail" for="<?php echo esc_attr($locationSlug); ?>">
        <div class="bs-location-selector__item-title dk-color--primary">
			<input class="bs-location-selector__item-radio" type="radio"
				   name="location_store"
				   id="<?php echo esc_attr($locationSlug); ?>"
				   value="<?php echo esc_attr($locationSlug); ?>"
				   data-location-slug="<?php echo esc_attr($locationSlug); ?>"
				<?php echo $autoSelected; ?>>
            <?php echo esc_html($location->label()); ?>
        </div>
		<?php if ( $location->fullAddress() ) : ?>
			<p class="bs-location-selector__item-address mb-0">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8.00004 1.33301C5.42004 1.33301 3.33337 3.41967 3.33337 5.99967C3.33337 9.49967 8.00004 14.6663 8.00004 14.6663C8.00004 14.6663 12.6667 9.49967 12.6667 5.99967C12.6667 3.41967 10.58 1.33301 8.00004 1.33301ZM8.00004 7.66634C7.08004 7.66634 6.33337 6.91967 6.33337 5.99967C6.33337 5.07967 7.08004 4.33301 8.00004 4.33301C8.92004 4.33301 9.66671 5.07967 9.66671 5.99967C9.66671 6.91967 8.92004 7.66634 8.00004 7.66634Z" fill="#2C2C2C"/></svg>
				<?php echo esc_html($location->fullAddress()); ?>
			</p>
		<?php endif; ?>
        <?php if ( $location->phone() ) : ?>
            <div class="bs-location-selector__item-phone">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.6538 9.24141C13.2002 9.19025 12.7524 9.09748 12.3158 8.96426C12.0507 8.88254 11.7683 8.87449 11.4989 8.94099C11.2295 9.00749 10.9833 9.14603 10.7867 9.34176L9.95523 10.1723C8.3571 9.29537 7.04223 7.98083 6.16493 6.38292L6.99639 5.55242C7.19196 5.35567 7.33031 5.10942 7.39665 4.84006C7.46298 4.5707 7.45478 4.28836 7.37294 4.0233C7.23983 3.58672 7.14706 3.13885 7.09578 2.68532C7.05368 2.31278 6.87568 1.96885 6.59585 1.71934C6.31602 1.46984 5.954 1.33229 5.57909 1.33301H3.52912C3.12357 1.33301 2.73463 1.49411 2.44787 1.78088C2.1611 2.06764 2 2.45658 2 2.86213C2 2.90896 2 2.9577 2.00669 3.00262C2.1401 4.4688 2.5326 5.89967 3.16595 7.22871C3.78279 8.52195 4.61677 9.69986 5.63165 10.7113C6.64347 11.7244 7.82136 12.5568 9.11422 13.1722C10.4409 13.8044 11.8691 14.1966 13.3327 14.3305C13.5445 14.3503 13.7581 14.3257 13.9598 14.2583C14.1616 14.1908 14.3471 14.082 14.5044 13.9388C14.6617 13.7957 14.7874 13.6212 14.8735 13.4267C14.9595 13.2321 15.004 13.0218 15.0042 12.809V10.7591C15.0052 10.3841 14.8679 10.022 14.6185 9.74199C14.3692 9.46198 14.0254 9.28375 13.6528 9.24141" fill="#2C2C2C"/></svg>
                <?php echo esc_html($location->phone()); ?>
            </div>
        <?php endif; ?>
    </label>
</div>
