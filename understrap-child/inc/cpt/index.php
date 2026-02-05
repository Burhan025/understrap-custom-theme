<?php

add_action( 'init', function() {
    $cpt_files = [
        __DIR__ . '/types/cpt-location.php',
    ];

    foreach ( $cpt_files as $file ) {
        $cpt = require $file;
        register_post_type(
            $cpt['slug'],
            $cpt['args']
        );
    }
});
