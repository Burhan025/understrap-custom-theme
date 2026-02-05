<?php
/**
 * Template Name: Contact Us
 * Description: Shows all Locations + contact form + map
 */

get_header();

if ( have_posts() ) :
  while ( have_posts() ) : the_post();
    get_template_part( 'templates/hero', null, array( 'breadcrumb_title' => 'Contact Us' ) );
    ?>

    <div class="wrapper" id="index-wrapper">
      <div class="container" id="content" tabindex="-1">
        <div class="row">
          <div class="col-md content-area" id="primary">
            <main>
              <!-- Locations Grid -->
              <section class="location-box">
                <div class="container py-4 pt-5">

                  <div class="row g-4">
                    <?php
                    $args = [
                      'post_type'      => 'locations',
                      'posts_per_page' => -1,
                      'orderby'        => 'title',
                      'order'          => 'ASC',
                    ];
                    $loop = new WP_Query( $args );
                    if ( $loop->have_posts() ) :
                      while ( $loop->have_posts() ) : $loop->the_post();
                        $thumb          = get_field('cpt_location__storefront_thumbnail');
                        $headline       = get_the_title();
                        $addresses      = get_field('cpt_location__addresses') ?: [];
                        $first_address  = $addresses[0] ?? null;
                        $address_line   = $first_address
                          ? sprintf(
                              '%s, %s, %s %s',
                              $first_address['street'],
                              $first_address['city'],
                              $first_address['province'],
                              $first_address['postal_code']
                            )
                          : '';
                        $phone          = get_field('cpt_location__phone');
                        $email          = get_field('cpt_location__email');
                    ?>
                        <div class="col-12 col-md-6 col-lg-3">
                          <div class="card h-100 border-0 p-4">
                            <?php if ( $thumb ) : ?>
                              <div class="card-img overflow-hidden mb-3" style="height:260px;">
                                <img src="<?php echo esc_url( $thumb['url'] ); ?>" alt="<?php echo esc_attr( $headline ); ?>" style="width:100%; height:100%; object-fit:cover;" />
                              </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column p-0">
                            	<h3 class="card-title mb-2 fw-bold">
									<?php echo esc_html( $headline ); ?>
								</h3>

                              <?php if ( $address_line ) : ?>
                                <p class="mb-2 d-flex align-items-center">
                                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/location-pin.svg' ); ?>" width="20" class="me-2" alt="Location pin" />
                                  <span><?php echo esc_html( $address_line ); ?></span>
                                </p>
                              <?php endif; ?>

                              <?php if ( $phone ) : ?>
                                <p class="mb-2 d-flex align-items-center">
                                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/phone-icon.svg' ); ?>" width="20" class="me-2" alt="Phone icon" />
                                  <span><?php echo esc_html( $phone ); ?></span>
                                </p>
                              <?php endif; ?>

                              <?php if ( $email ) : ?>
                                <p class="mb-0 d-flex align-items-center">
                                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/email-icon.svg' ); ?>" width="20" class="me-2" alt="Email icon" />
                                  <span><?php echo esc_html( $email ); ?></span>
                                </p>
                              <?php endif; ?>

                            </div>
                          </div>
                        </div>
                    <?php
                      endwhile;
                      wp_reset_postdata();
                    else :
                    ?>
                      <p class="text-center">No locations available.</p>
                    <?php endif; ?>
                  </div>
                </div>
              </section>

              <!-- Contact Form Section -->
              <?php
                $form_title     = get_field('page_contact__form_title');
                $form_shortcode = get_field('page_contact__form_shortcode');
              ?>
			<section class="contact-form py-4">
				<?php if ( $form_title ) : ?>
					<h2 class="text-center mb-5 text-uppercase fw-bold"><?php echo esc_html( $form_title ); ?></h2>
				<?php endif; ?>
            	<div class="container">
              	<div class="main-form p-4 text-white">
                <?php if ( $form_shortcode ) : ?>
                  <div class="row justify-content-center">
                    <div>
                      <?php
                        echo do_shortcode( $form_shortcode );
                      ?>
                    </div>
                  </div>
                <?php else : ?>
                  <p class="text-center">[Form will appear here]</p>
                <?php endif; ?>
              </div>
			  </div>
			</section>

              <!-- Map Placeholder Section -->
			<section class="contact-map py-4 mt-3">
              <?php $map_title = get_field('page_contact__map_title'); ?>
              <div class="mb-3">
                <?php if ( $map_title ) : ?>
                  <h2 class="text-center mb-5 text-uppercase fw-bold"><?php echo esc_html( $map_title ); ?></h2>
                <?php endif; ?>
				<div class="container">
                <div class="ratio ratio-16x9 bg-light">
	                <?php echo do_shortcode( '[bs_locations_map]' ); ?>
                </div>
              </div>
			  </div>
			  </section>
            </main>
          </div>
        </div>
      </div>
    </div>

  <?php
  endwhile;
endif;

get_footer();
