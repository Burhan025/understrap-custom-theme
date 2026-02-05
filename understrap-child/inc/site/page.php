<?php 

/**
 * Content for defalut 'Page.php' and 'templates/default.php' in Understrap Child
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main>
	<?php
	while ( have_posts() ) {
		the_post();
		if ( get_field( 'show_header_hero' ) ) {
			get_template_part( 'templates/hero' );
		}

		the_content();
	}
	?>
</main>

<?php
get_footer();