<?php
/**
 * Template part
 *
 * @package understrap-child
 */

$background_url  = get_stylesheet_directory_uri() . '/assets/images/hero-bg.jpg';
$page_title      = '';
$bredcrumb_title = '';

if ( is_singular() ) {
	// Get featured image if available.
	$thumbnail_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$background_url = ! empty( $thumbnail_url ) ? $thumbnail_url : $background_url;

	// Set titles.
	$page_title      = get_the_title();
	$parent          = get_post_parent( get_the_ID() );
	$bredcrumb_title = $parent ? get_the_title( $parent ) : $page_title;
} else {
	$archive_title   = get_the_archive_title();
	$page_title      = $archive_title;
	$bredcrumb_title = $archive_title;
}

// Override titles if provided in args.
$page_title      = $args['title'] ?? $page_title;
$bredcrumb_title = $args['breadcrumb_title'] ?? $bredcrumb_title;
?>
<section class="section-hero" style="background-image: url(<?php echo esc_url( $background_url ); ?>);">
	<div class="section-hero-content">
		<div class="breadcrumbs">
			<?php
			if ( is_woocommerce() ) {
				woocommerce_breadcrumb();
			} else {
				?>
				<a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'understrap-child' ); ?></a>
				<span>></span>
				<span>
					<?php echo esc_html( $bredcrumb_title ); ?>
				</span>
				<?php
			}
			?>
		</div>
		<div class="page-title">
			<h1><?php echo esc_html( $page_title ); ?></h1>
		</div>
	</div>
</section>
