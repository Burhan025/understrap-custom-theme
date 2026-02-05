<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package Understrap
 */

defined('ABSPATH') || exit;

get_header();

if (is_home() && !is_front_page()) :
	$blog_page_id = get_option('page_for_posts');
	$blog_title = $blog_page_id ? get_the_title($blog_page_id) : __('What’s Happening at Jerry’s Cannabis', 'dk-theme');

elseif (is_category()) :
	$blog_title = single_cat_title('', false);

elseif (is_tag()) :
	$blog_title = sprintf(__('Tag %s', 'dk-theme'), single_tag_title('', false));

elseif (is_search()) :
	$blog_title = sprintf(__('Search Results for: %s', 'dk-theme'), get_search_query());

elseif (is_author()) :
	the_post();
	$blog_title = sprintf(
		__('Author %s', 'dk-theme'),
		'<span class="vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" title="' . esc_attr(get_the_author()) . '" rel="me">' . get_the_author() . '</a></span>'
	);
	rewind_posts();

elseif (is_day()) :
	$blog_title = sprintf(__('Daily %s', 'dk-theme'), get_the_date());

elseif (is_month()) :
	$blog_title = sprintf(__('Monthly %s', 'dk-theme'), get_the_date('F Y'));

elseif (is_year()) :
	$blog_title = sprintf(__('Yearly %s', 'dk-theme'), get_the_date('Y'));

elseif (is_tax('post_format', 'post-format-aside')) :
	$blog_title = __('Asides', 'dk-theme');

elseif (is_tax('post_format', 'post-format-image')) :
	$blog_title = __('Images', 'dk-theme');

elseif (is_tax('post_format', 'post-format-video')) :
	$blog_title = __('Videos', 'dk-theme');

elseif (is_tax('post_format', 'post-format-quote')) :
	$blog_title = __('Quotes', 'dk-theme');

elseif (is_tax('post_format', 'post-format-link')) :
	$blog_title = __('Links', 'dk-theme');

else :
	$blog_title = __('Archive', 'dk-theme');

endif;
?>
	<div class=" blog-wrapper pb-3 pb-lg-5" id="index-wrapper">
		<div class="page-header page-header__simple text-center p-5 py-lg-5 mb-3 mb-lg-3">
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
					<span><?php echo $blog_title; ?></span>
					<?php
				}
				?>
			</div>
			<div class="page-title">
				<h1><?php echo $blog_title; ?></h1>
			</div>
		</div>
		<div class="container" id="content" tabindex="-1">
			<?php if (have_posts()) : ?>
				<?php if (is_category('cannabis-education')) : ?>
					<div class="row blog-row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
						<?php while (have_posts()) : the_post(); ?>
							<?php get_template_part('inc/site/template-parts/education/card'); ?>
						<?php endwhile; ?>
					</div>

				<?php else : ?>
				<div class="row blog-row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
					<?php while (have_posts()) : the_post();; ?>
						<div class="col post-col">
							<article <?php post_class('h-100 card card-blog-item'); ?> id="post-<?php the_ID(); ?>">
								<div class="card-header">
									<a href="<?php the_permalink(); ?>"
									   class="card-img-top ratio ratio-16x9 d-block overflow-hidden">
										<?php if (has_post_thumbnail()) : ?>
											<?php the_post_thumbnail('medium_large', ['class' => 'object-fit-cover w-100 h-100']); ?>
										<?php endif; ?>
									</a>
								</div>
								<div class="card-body d-flex flex-column">
									<time class="text-muted small mb-2" datetime="<?php echo get_the_date('c'); ?>">
										<?php echo get_the_date(); ?>
									</time>
									<h2 class="card-title h4"><a class="text-decoration-none text-body"
																 href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h2>
									<p class="card-text flex-grow-1"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
									<a href="<?php the_permalink(); ?>" class="card-btn text-decoration-none mt-2">
										<?php echo __('Read More', 'dk-theme'); ?> <i
											class="mx-3 fa-solid fa-angle-right"></i>
									</a>
								</div>
							</article>
						</div>
					<?php endwhile; ?>
				</div>
				<?php endif; ?>
				<div class="blog-pagination-wrapper mt-5">
					<?php
					if (function_exists('bs_pagination')) {
						bs_pagination();
					} else {
						echo paginate_links([
							'type' => 'list',
						]);
					}
					?>
				</div>

			<?php else : ?>
				<?php get_template_part('loop-templates/content', 'none'); ?>
			<?php endif; ?>
		</div>
	</div>
<?php
get_footer();
