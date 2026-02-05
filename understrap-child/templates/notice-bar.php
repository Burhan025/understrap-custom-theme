<?php
/**
 * Template part for displaying the notice bar
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$notice_text = $args['notice_text'] ?? '';
?>
<div id="notice-bar" class="notice-bar">
	<div class="container">
		<div class="notice-content">
			<span class="notice-text"><?php echo wp_kses( $notice_text, array( 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></span>
			<button type="button" class="notice-dismiss" aria-label="<?php esc_attr_e( 'Dismiss notice', 'understrap' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
				</svg>
			</button>
		</div>
	</div>
</div>