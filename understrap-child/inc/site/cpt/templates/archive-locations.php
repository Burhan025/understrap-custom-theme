<?php
/**
 * Archive template for the Locations CPT
 */

get_header();
get_template_part( 'templates/hero', null, array( 'title' => 'Locations', 'breadcrumb_title' => 'Locations' ) );
?>
<div class="container py-5">

  <?php if ( have_posts() ): ?>
    <div class="row g-4">
      <?php while ( have_posts() ): the_post();
        // ACF fields
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
        $dispensary_url = get_field('cpt_location__dispensary_info_target') ?: get_permalink();
      ?>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card h-100 border-0 rounded-3 p-4 bg-light">
            <?php if ( $thumb ): ?>
              <div class="overflow-hidden rounded-3 mb-3" style="height:260px;">
                <img src="<?php echo esc_url( $thumb['url'] ); ?>" alt="<?php echo esc_attr( $headline ); ?>" style="width:100%; height:100%; object-fit:cover;" />
              </div>
            <?php endif; ?>

            <div class="card-body d-flex flex-column p-0">
              <h4 class="card-title mb-2">
                <?php echo esc_html( $headline ); ?>
              </h4>

              <?php if ( $address_line ): ?>
                <p class="mb-2 d-flex align-items-center">
                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/location-pin.svg' ); ?>" width="20" class="me-2" alt="" />
                  <span><?php echo esc_html( $address_line ); ?></span>
                </p>
              <?php endif; ?>

              <?php if ( $phone ): ?>
                <p class="mb-2 d-flex align-items-center">
                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/phone-icon.svg' ); ?>" width="20" class="me-2" alt="" />
                  <span><?php echo esc_html( $phone ); ?></span>
                </p>
              <?php endif; ?>

              <?php if ( $email ): ?>
                <p class="mb-3 d-flex align-items-center">
                  <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/inc/site/acf/images/email-icon.svg' ); ?>" width="20" class="me-2" alt="" />
                  <span><?php echo esc_html( $email ); ?></span>
                </p>
              <?php endif; ?>

              <a href="<?php echo esc_url( $dispensary_url ); ?>" class="btn btn-primary w-100 mt-auto text-uppercase">Location Info</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="mt-5">
      <?php
        the_posts_pagination([
          'mid_size'  => 1,
          'prev_text' => '&laquo;',
          'next_text' => '&raquo;',
        ]);
      ?>
    </div>
  <?php else: ?>
    <p class="text-center">No locations found.</p>

  <?php endif; ?>
</div>

<?php get_footer(); ?>
