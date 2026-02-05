<?php
/**
 * Displays a single Location instance.

 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<section class=" location-hero d-flex flex-column align-items-center text-center py-3 w-100">
			<div class="container my-2">
				<div class="breadcrumbs my-2">
					<a href="<?php echo esc_url(home_url()); ?>"><?php esc_html_e('Home', 'understrap-child'); ?></a>
					<span>></span>
					<a href="/locations/"><?php esc_html_e('Locations', 'understrap-child'); ?></a>
					<span>></span>
					<span><?php the_title(); ?></span>
				</div>
				<h2 class="mb-3"><?php the_field('cpt_location__headline'); ?></h2>
				<p class="mb-3"><?php the_field('cpt_location__intro_text'); ?></p>
				<?php if ($url = get_field('cpt_location__shop_btn_target')): ?>
					<a class="btn btn-secondary" href="<?php echo esc_url($url); ?>">Shop Now</a>
				<?php endif; ?>
			</div>
		</section>
		<div class="wrapper" id="index-wrapper ">
	<div class="container-fluid" id="content" tabindex="-1">
		<div class="row">
			<div class="col-md content-area px-0" id="primary">
				<main>


				<section class="container location-meta d-md-flex flex-md-row">
					<div class="col-md-6 store-map position-relative mb-5 me-md-5" style="min-height: 500px;">
						<?php if ( $map_iframe = get_field('cpt_location__google_maps') ): ?>
						<?php
							echo str_replace(
							'<iframe',
							'<iframe class="position-absolute top-0 start-0 w-100 h-100 border-0"',
							$map_iframe
							);
						?>
						<?php endif; ?>
					</div>

					<div class="col-md-6 store-info mb-4">
						<h3 class="mb-2">Store Information</h3>

						<section class="store-info ">
					<h4 class="mb-3">Service Options</h4>

					<?php
					$services = get_field('cpt_location__service_options');

					if ( $services ):
					$icon_map = [
						'In-store Shopping' => 'cart.svg',
						'Trackable'         => 'search-icon.svg',
						'In-store Pickup'   => 'store-pickup.svg',
					];
					?>
						<ul class="row list-unstyled mb-0">
						<?php foreach ( $services as $service ):
						$icon_filename = $icon_map[ $service ] ?? 'default.svg';
						$icon_url      = get_stylesheet_directory_uri() . "/inc/site/acf/images/{$icon_filename}";
						?>
						<li class="col-12 col-md-6 d-flex align-items-center mb-3">
							<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $service ); ?> icon" width="24" height="24" class="me-2" />
							<span class="fw-normal"><?php echo esc_html( $service ); ?></span>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					</section>

						<section class="find-us mb-3">
					<h4 class="mb-3">Where To Find Us?</h4>

					<?php if ( have_rows('cpt_location__addresses') ): ?>
						<?php while ( have_rows('cpt_location__addresses') ): the_row();
						$street      = get_sub_field('street');
						$city        = get_sub_field('city');
						$province    = get_sub_field('province');
						$postal_code = get_sub_field('postal_code');
						$country     = get_sub_field('country');

						$address_line = sprintf(
							'%s, %s, %s %s %s',
							$street,
							$city,
							$province,
							$postal_code,
							$country
						);
						?>
						<div class="align-items-center mb-3">
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/map-pin.png' ); ?>" alt="Location icon" width="48" height="48" class="mb-2" /><br>
							<address class="mb-0"><?php echo esc_html( $address_line ); ?></address>
						</div>
						<?php endwhile; ?>
					<?php endif; ?>
					</section>

						<section class="lets-talk p-3 px-4 rounded mb-4">
					<h4 class="mb-3">Need To Talk?</h4>

					<?php
						$phone = get_field('cpt_location__phone');
						$email = get_field('cpt_location__email');
					?>
					<ul class="list-unstyled mb-0">
						<li class="d-flex align-items-center mb-2">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/call.svg' ); ?>" alt="Phone icon" width="24" height="24" class="me-2" />
						<span>
							Call us:
							<a href="tel:<?php echo esc_attr( preg_replace('/\D+/', '', $phone) ); ?>">
							<?php echo esc_html( $phone ); ?>
							</a>
						</span>
						</li>
						<li class="d-flex align-items-center">
							<img
								src="<?php echo esc_url(get_stylesheet_directory_uri() . '/inc/site/acf/images/icon-mail-light.svg'); ?>"
								alt="Email icon" width="24" height="24" class="me-2"/>
						<span>
							Shoot them to:
							<a href="mailto:<?php echo esc_attr( $email ); ?>">
							<?php echo esc_html( $email ); ?>
							</a>
						</span>
						</li>
					</ul>
					</section>

						<section class="operation-hours  p-3 px-4 rounded mb-4">
					<h4 class="mb-3">Hours of Operation</h4>

					<?php if ( have_rows('cpt_location__hours') ): ?>
						<ul class="list-unstyled mb-0">
						<?php while ( have_rows('cpt_location__hours') ): the_row();
							$day   = get_sub_field('day');
							$hours = get_sub_field('hours');
						?>
							<li class="row align-items-center py-1">
							<div class="col-6 d-flex align-items-center">
								<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/clock.svg' ); ?>" alt="Clock icon" width="24" height="24" class="me-2" />
								<span><?php echo esc_html( $day ); ?></span>
							</div>
							<span class="col-6 text-start"><?php echo esc_html( $hours ); ?></span>
							</li>
						<?php endwhile; ?>
						</ul>
					<?php endif; ?>
					</section>

					<?php if ($url = get_field('cpt_location__shop_btn_target')): ?>
					<div class="d-grid mb-4">
						<a href="<?php echo esc_url( $url ); ?>"class="btn btn-secondary">VIEW DISPENSARY INFO</a>
					</div>
					<?php endif; ?>

					</div>
				</section>

				<section class="location-content">
					<?php the_content(); ?>
				</section>

				</main>
			</div>
		</div>
	</div>
</div>
<?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();
