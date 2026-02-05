<?php
// Basic ACF Fallbacks
if ( ! function_exists( 'get_field' ) ) {
	function get_field( $selector = '', $post_id = false, $format_value = true ) {
		return null;
	}
}
if ( ! function_exists( 'the_field' ) ) {
	function the_field( $selector = '', $post_id = false ) {
		echo get_field( $selector, $post_id );
	}
}
if ( ! function_exists( 'have_rows' ) ) {
	function have_rows( $selector = '', $post_id = false ) {
		return false;
	}
}
if ( ! function_exists( 'the_row' ) ) {
	function the_row() {
		return false;
	}
}
if ( ! function_exists( 'get_sub_field' ) ) {
	function get_sub_field( $selector = '', $format_value = true ) {
		return null;
	}
}
if ( ! function_exists( 'the_sub_field' ) ) {
	function the_sub_field( $selector = '' ) {
		echo get_sub_field( $selector );
	}
}
if ( ! function_exists( 'get_fields' ) ) {
	function get_fields( $post_id = false ) {
		return [];
	}
}
if ( ! function_exists( 'get_field_object' ) ) {
	function get_field_object( $selector = '', $post_id = false, $format_value = true, $load_value = true ) {
		return null;
	}
}
if ( ! function_exists( 'get_sub_field_object' ) ) {
	function get_sub_field_object( $selector = '', $format_value = true, $load_value = true ) {
		return null;
	}
}
if ( ! function_exists( 'update_field' ) ) {
	function update_field( $selector = '', $value = null, $post_id = false ) {
		return false;
	}
}
if ( ! function_exists( 'delete_field' ) ) {
	function delete_field( $selector = '', $post_id = false ) {
		return false;
	}
}

// ACF PRO Fallbacks
if ( ! function_exists( 'acf_add_options_page' ) ) {
	function acf_add_options_page( $args = [] ) {
		return false;
	}
}
if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	function acf_add_local_field_group( $group = [] ) {
		return false;
	}
}
if ( ! function_exists( 'acf_register_block_type' ) ) {
	function acf_register_block_type( $block = [] ) {
		return false;
	}
}
if ( ! function_exists( 'acf_get_field' ) ) {
	function acf_get_field( $selector = '' ) {
		return false;
	}
}
if ( ! function_exists( 'acf_get_fields' ) ) {
	function acf_get_fields( $parent = '' ) {
		return [];
	}
}
if ( ! function_exists( 'acf_get_field_groups' ) ) {
	function acf_get_field_groups( $args = [] ) {
		return [];
	}
}
