<?php
/**
 * The template for displaying all single posts
 *
 * @package understrap-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$page_title      = get_the_title();
?>
<div class="wrapper blog-detail-wrapper py-3 py-lg-5" id="single-wrapper">
	<div class="container" id="content" tabindex="-1">

		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class('blog-detail-inner'); ?> id="post-<?php the_ID(); ?>">
				<div class="page-header page-header__simple text-center mb-5">
					<div class="breadcrumbs">
						<?php
						if (function_exists('is_woocommerce') && is_woocommerce()) {
							woocommerce_breadcrumb();
						} else {
							?>
							<a href="<?php echo esc_url(home_url()); ?>">
								<?php esc_html_e('Home'); ?>
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

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="entry-image mb-lg-5 text-center">
						<?php the_post_thumbnail( 'full', ['class' => 'img-fluid rounded w-100'] ); ?>
					</div>
				<?php endif; ?>

				<div class="entry-content mb-4">
					<?php the_content(); ?>
				</div>

				<?php
				/**
				 * Display related blog posts on single post page
				 */


				$args = array(
					'post__not_in'        => array( get_the_ID() ),    // exclude current post
					'posts_per_page'      => 4,                     // number of related posts to show
					'ignore_sticky_posts' => 1,                     // ignore sticky posts
				);
				// Get category/tag IDs of the current post
				$tags = wp_get_post_tags( get_the_ID() );
				if ( $tags ) {
					$tag_ids = wp_list_pluck( $tags, 'term_id' );
					$args['tag__in'] = $tag_ids;
				} else {
					$categories = wp_get_post_categories( get_the_ID() );
					if($categories) {
						$args['category__in'] = $categories;
					}
				}

				$related_query = new WP_Query( $args );

				if ( $related_query->have_posts() ) :
					?>
					<div class="related-posts py-3 py-lg-5">
						<h3 class="related-title h2 text-center mb-5"><?php esc_html_e( 'Related Posts', 'dk-theme' ); ?></h3>
						<div class="row blog-row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
							<?php
							while ( $related_query->have_posts() ) :
								$related_query->the_post();
								?>
								<div class="col post-col">
									<article <?php post_class('h-100 card card-blog-item'); ?> id="post-<?php the_ID(); ?>">
										<div class="card-header">
											<a href="<?php the_permalink(); ?>" class="card-img-top ratio ratio-16x9 d-block overflow-hidden">
												<?php if (has_post_thumbnail()) : ?>
													<?php the_post_thumbnail('medium_large', ['class' => 'object-fit-cover w-100 h-100']); ?>
												<?php endif; ?>
											</a>
										</div>
										<div class="card-body d-flex flex-column">
											<time class="text-muted small mb-2" datetime="<?php echo get_the_date('c'); ?>">
												<?php echo get_the_date(); ?>
											</time>
											<h2 class="card-title h4"><a class="text-decoration-none text-body" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
											<p class="card-text flex-grow-1"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
											<a href="<?php the_permalink(); ?>" class="card-btn text-decoration-none mt-2">
												<?php echo __('Read More', 'dk-theme'); ?> <i class="mx-3 fa-solid fa-angle-right"></i>
											</a>
										</div>
									</article>
								</div>
							<?php
							endwhile;
							?>
						</div>
					</div>
				<?php
				endif;

				wp_reset_postdata();
				?>

			</article>

		<?php endwhile; ?>

	</div>
</div>

<?php get_footer(); ?>
