<?php
/**
 * Enqueue scripts and styles
 *
 * @package BurhanAftab\UnderstrapChild
 */

namespace BurhanAftab\UnderstrapChild;

/**
 * Enqueue scripts and styles
 */
class Enqueue {
	/**
	 * Initialize the enqueue class
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_third_party_scripts' ) );
	}

	/**
	 * Dequeue the parent theme's styles and scripts
	 */
	public function dequeue_parent_scripts() {
		// wp_dequeue_style( 'understrap-styles' );
		wp_deregister_style( 'understrap-styles' );

		// wp_dequeue_script( 'understrap-scripts' );
		wp_deregister_script( 'understrap-scripts' );
	}

	/**
	 * Enqueue the child theme's styles and scripts
	 */
	public function enqueue_scripts() {
		// Get the theme data.
		$theme_version = wp_get_theme()->get( 'Version' );

		// Append timestamp to bust cache: mmddhhmmss
		$timestamp     = gmdate( 'mdHis' );
		$version       = $theme_version . '-' . $timestamp;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'understrap-child-styles', get_stylesheet_directory_uri() . "/inc/site/assets/css/child-theme{$suffix}.css", array(), $version );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'understrap-child-scripts', get_stylesheet_directory_uri() . "/inc/site/assets/js/child-theme{$suffix}.js", array(), $version, true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue third party scripts
	 */
	public function enqueue_third_party_scripts() {
		wp_enqueue_style( 'understrap-child-glider-styles', get_stylesheet_directory_uri() . '/assets/css/glider.min.css', array(), '1.7.9' );
		wp_enqueue_style('font-awesome-6','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',	array(),'6.5.2');
		wp_enqueue_style('google-fonts-poppins','https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap', array(), null );
	}
}
