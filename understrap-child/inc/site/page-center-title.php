<?php
/**
 * Template Name: Page Center Title
 * Template Post Type: page
 */

get_header();
?>
	<div class="page-content-wrapper">
		<div class="page-header page-header__simple text-center ">
			<div class="breadcrumbs">
				<?php
				if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
					woocommerce_breadcrumb();
				} else {
					?>
					<a href="<?php echo esc_url( home_url() ); ?>">
						<?php esc_html_e( 'Home' ); ?>
					</a>
					<span>></span>
					<span><?php the_title(); ?></span>
					<?php
				}
				?>
			</div>
			<div class="page-title">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
		<div class="container py-5 my-3">
			<!-- Title -->


			<div class="row">
				<div class="col-lg-12">
					<!-- Page Content -->
					<div class="page-content">
						<?php
						while ( have_posts() ) :
							the_post();
							the_content();
						endwhile;
						?>
					</div>

				</div>
			</div>
		</div>
	</div>


<?php
get_footer();
