<?php
/**
 * Customize Your Post Type args after post type has been registered.
 *
 * @param array  $args      Array of arguments for registering a post type.
 * @param string $post_type Post type key.
 *
 * @return array
 */

add_filter( 'register_post_type_args', function ( $args, $post_type ) {
	if ( 'locations' === $post_type ) {
		$args['has_archive'] = false;
	}
	return $args;
}, 10, 2 );

add_filter( 'body_class', function ( $classes ) {
	if ( is_page() ) {
		global $post;
		$classes[] = 'page-' . $post->post_name;
	}

	return $classes;
} );
