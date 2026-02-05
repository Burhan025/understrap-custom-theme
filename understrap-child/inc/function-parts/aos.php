<?php
function enqueue_aos_cdn_assets() {
    // AOS CSS
    wp_enqueue_style(
        'aos-css',
        'https://unpkg.com/aos@2.3.4/dist/aos.css',
        array(),
        '2.3.4'
    );

    // AOS JS
    wp_enqueue_script(
        'aos-js',
        'https://unpkg.com/aos@2.3.4/dist/aos.js',
        array(),
        '2.3.4',
        true // Load in footer
    );

    // Inline init script
    wp_add_inline_script( 'aos-js', 'AOS.init({ duration: 500, easing: "ease-out", once: true });' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_aos_cdn_assets' );
