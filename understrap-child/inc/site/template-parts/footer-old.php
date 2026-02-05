
<?php
/**
 * The footer for our theme
 *
 * @package Understrap Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//$footer_logo_this_site = get_stylesheet_directory_uri() . '/inc/site/images/footer-logo.png';
$footer_logo_fallback  = get_stylesheet_directory_uri() . '/assets/images/logo.png';
//$footer_logo_path = get_stylesheet_directory() . '/inc/site/images/footer-logo.png';

$footer_bg_desktop = $logo_footer = $logo_authorized ='';

$footer_background = get_field('footer_background', 'option');
$footer_images = get_field('footer_images', 'option');

$logo_footer      = !empty($footer_images['logo_footer']['url']) ? $footer_images['logo_footer']['url'] : '';
//$logo_authorized  = !empty($footer_images['logo_authorized']['url']) ? $footer_images['logo_authorized']['url'] : '';
$footer_bg_desktop = !empty($footer_background['footer-background-desktop']['url']) ? $footer_background['footer-background-desktop']['url'] : '';
$phone_number = get_field('phone_number', 'option');
$email_address = get_field('email_address', 'option');
$social_icons = get_field('social_icons', 'option');
$footer_right_content = get_field('footer_right_content', 'option');
$footer_html = get_field('footer_credits', 'option');
$in_stores_hours = get_field('in_stores_hours','option');
if ( $footer_html ) {
	$footer_html = str_replace(
		['{year}', '{site_name}', '{site_url}'],
		[date('Y'), get_bloginfo('name'), home_url()],
		$footer_html
	);
}
if ( $footer_right_content ) {
	$footer_right_content = str_replace(
		['{year}', '{site_name}', '{site_url}'],
		[date('Y'), get_bloginfo('name'), home_url()],
		$footer_right_content
	);
}
if ( $in_stores_hours) {
	$in_stores_hours = str_replace(
		['{year}', '{site_name}', '{site_url}'],
		[date('Y'), get_bloginfo('name'), home_url()],
		$in_stores_hours
	);
}

?>
<footer id="site-footer" role="contentinfo" class="text-white" style="<?php if ($footer_bg_desktop): ?>background-image: url('<?php echo esc_url($footer_bg_desktop); ?>');<?php endif; ?>">
	<div class="footer-navs-wrap">
		<div class="container">
			<div class="row text-white py-3 py-lg-5 gy-4 justify-content-evenly mt-0">
				<!-- Logo -->
				<div class="col-lg-3 col-xl-2 col-12 text-center text-start- wrapper-logo-footer">
					<div class="footer-logo ">
						<?php if( !empty($logo_footer) ): ?>
							<img src="<?php echo esc_url($logo_footer); ?>" alt="" class="logo-footer">
						<?php endif; ?>
					</div>
					<div class="contact d-none text-start d-lg-block">
						<h6 class="ft-title ft-title-no-line mb-3  text-start"><?php esc_html_e( 'Contact', 'understrap-child' ); ?></h6>
						<p class="footer-phone">
							<?php if ($phone_number): ?>
								<a href="tel:<?php echo preg_replace('/\D+/', '', $phone_number); ?>">
									<?php echo esc_html($phone_number); ?>
								</a>
							<?php endif; ?>
						</p>
						<p class="footer-mail footer-phone ">
							<?php if ($email_address): ?>
								<a href="mailto:<?php echo sanitize_email($email_address); ?>">
									<?php echo esc_html($email_address); ?>
								</a>
							<?php endif; ?>
						</p>
						<div class="footer-social">
							<div class="row justify-content-center">
								<?php
								if ( $social_icons ) :
									?>
									<div class="footer-nav footer-nav-social mb-3" aria-label="<?php esc_attr_e( 'Footer Social Menu', 'understrap-child' ); ?>">
										<ul class="list-inline mb-0  justify-content-start ">
											<?php foreach ( $social_icons as $item ) :
												$label = $item['label'] ?? '';
												$url   = $item['url'] ?? '';
												$icon  = $item['icon'] ?? '';
												?>
												<li class="list-inline-item mx-3">
													<a href="<?php echo esc_url($url); ?>" class="text-white fs-4" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($label); ?>">
														<i class="<?php echo esc_attr($icon); ?>"></i>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

				</div>

				<!-- Navigation Menus -->
				<div class="col-lg-9 col-xl-10 col-12">
					<div class="row  text-start">
						<!-- Home -->
						<div class=" home-shop-about col-6 col-md-4 row">
						<div class="home text-start col-12 col-md-6 mb-4 mb-md-0">
							<h6 class="ft-title mb-3  "><?php esc_html_e( 'Home', 'understrap-child' ); ?></h6>
							<?php
							wp_nav_menu( array(
								'theme_location' => 'primary-mobile',
								'container'      => false,
								'menu_class'     => 'list-unstyled footer-menu',
								'depth'          => 0,
							) );
							?>
						</div>

						<!-- Shop -->
						<div class="shop text-start col-12 col-md-6 mb-4 mb-md-0">
							<h6 class="ft-title mb-3 "><?php esc_html_e( 'Shop', 'understrap-child' ); ?></h6>
							<?php
							wp_nav_menu( array(
								'theme_location' => 'footer_products',
								'container'      => false,
								'menu_class'     => 'list-unstyled footer-menu',
								'depth'          => 0,
							) );
							?>
						</div>
						</div>

						<!-- Location -->
						<?php if( have_rows('locations', 'option') ): ?>
						<div class="locations col-6 col-lg-3 col-xl-3 col-md-4 mb-4 mb-md-0">
							<div class="col-12 contact d-block d-lg-none">
								<h6 class="ft-title ft-title-no-line mb-3  text-start"><?php esc_html_e( 'Contact', 'understrap-child' ); ?></h6>
								<p class="footer-phone">
									<?php if ($phone_number): ?>
										<a href="tel:<?php echo preg_replace('/\D+/', '', $phone_number); ?>">
											<?php echo esc_html($phone_number); ?>
										</a>
									<?php endif; ?>
								</p>
								<p class="footer-mail footer-phone ">
									<?php if ($email_address): ?>
										<a href="mailto:<?php echo sanitize_email($email_address); ?>">
											<?php echo esc_html($email_address); ?>
										</a>
									<?php endif; ?>
								</p>
								<div class="footer-social">
									<div class="row ">
										<?php
										if ( $social_icons ) :
											?>
											<div class="footer-nav footer-nav-social mb-3" aria-label="<?php esc_attr_e( 'Footer Social Menu', 'understrap-child' ); ?>">
												<ul class="list-inline mb-0  justify-content-center justify-content-lg-start ">
													<?php foreach ( $social_icons as $item ) :
														$label = $item['label'] ?? '';
														$url   = $item['url'] ?? '';
														$icon  = $item['icon'] ?? '';
														?>
														<li class="list-inline-item mx-3">
															<a href="<?php echo esc_url($url); ?>" class="text-white fs-4" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($label); ?>">
																<i class="<?php echo esc_attr($icon); ?>"></i>
															</a>
														</li>
													<?php endforeach; ?>
												</ul>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<h6 class="ft-title ft-title-no-line mb-3  text-start"><?php esc_html_e( 'Locations', 'understrap-child' ); ?></h6>
							<?php while( have_rows('locations', 'option') ): the_row();
								$name = get_sub_field('name');
								$address = get_sub_field('address');
								$map_url = get_sub_field('map_url');
								$license = get_sub_field('license');
								?>
								<div class="location-item mb-3">
									<h6 class="location-item__name mb-1"><?php echo esc_html($name); ?></h6>
									<?php if( $map_url ): ?>
										<a class="mb-1 fw-normal text-uppercase"  href="<?php echo esc_url($map_url); ?>" target="_blank" rel="noopener">
											<?php echo nl2br(esc_html($address)); ?>
										</a>
									<?php else: ?>
										<p class="mb-1 fw-normal text-uppercase" ><?php echo nl2br(esc_html($address)); ?></p>
									<?php endif; ?>
									<?php if( $license ): ?>
										<small class="">License #: <?php echo esc_html($license); ?></small>
									<?php endif; ?>
								</div>
							<?php endwhile; ?>
						</div>
						<?php endif; ?>
						<div class="stores_hours col-6 col-lg-3 col-md-4 mb-4 mb-md-0">
							<?php
							$path = __DIR__ . '/location-hours.php';

							if ( file_exists( $path ) ) {
								include $path;
							}
							?>
						</div>
						<?php if ( $footer_right_content ) : ?>
							<div class="image-content col-6 col-lg-3 col-xl-3  mb-4 mb-md-0  text-start wrapper-logo-auth">
								<div class="row ">
									<div class=" text-white">
										<?php echo wp_kses_post( $footer_right_content ); ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="divider bg-light"></div>
			</div>
		</div>
	</div>

	<!-- Footer Bottom -->
	<div class="footer-bottom-wrap text-center container py-4">
		<?php if ( $footer_html ) : ?>
			<div class="row justify-content-center">
				<div class="col-auto  text-white">
					<?php echo wp_kses_post( $footer_html ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
