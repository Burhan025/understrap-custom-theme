<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="index-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<?php
			// Do the left sidebar check and open div#primary.
			get_template_part( 'global-templates/left-sidebar-check' );
			?>

			<main class="site-main bs-posts" id="main">

				<?php
				if ( have_posts() ) {
					// Start the Loop.
					while ( have_posts() ) {
						the_post();
						?>
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

							<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

							<header class="entry-header">
								<span class="posted-on">
									<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
										<?php echo esc_html( get_the_date() ); ?>
									</time>
								</span>

								<?php
								the_title(
									sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
									'</a></h2>'
								);
								?>

							</header><!-- .entry-header -->


							<div class="entry-content">
								<?php the_excerpt(); ?>
							</div><!-- .entry-content -->
							<a href="<?php the_permalink(); ?>" class="bs-read-more">
								<span><?php _e( 'Read More', 'understrap-child' ); ?></span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M9.70504 6L8.29504 7.41L12.875 12L8.29504 16.59L9.70504 18L15.705 12L9.70504 6Z" fill="#0D0D0B"/>
								</svg>
							</a>

						</article><!-- #post-<?php the_ID(); ?> -->
						<?php
					}
				} else {
					get_template_part( 'loop-templates/content', 'none' );
				}
				?>

			</main>

			<?php
			// Display the pagination component.
			understrap_pagination();

			// Do the right sidebar check and close div#primary.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
