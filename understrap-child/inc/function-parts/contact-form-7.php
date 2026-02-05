<?php
add_filter( 'wpcf7_validate_text', 'honeypot_cf7_validation', 10, 2 );

function honeypot_cf7_validation( $result, $tag ) {

    if ( $tag->name === 'website' ) {
        $honeypot_field = isset( $_POST['website'] ) ? trim( $_POST['website'] ) : '';

        error_log( 'Honeypot value: ' . $honeypot_field );

        if ( ! empty( $honeypot_field ) ) {
            error_log( 'Spam detected — blocking submission.' );
            $result->invalidate( $tag, 'The form could not be submitted, please contact the store by phone.' );
        }
    }

    return $result;
}
