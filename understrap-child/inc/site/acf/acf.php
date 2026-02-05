<?php
/**
 * ACF settings
 */

require_once __DIR__ . '/blocks/register-blocks.php';

add_filter('acf/settings/save_json', function($path) {
    // Change path to your theme folder
    return __DIR__ . '/json';
});

add_filter('acf/settings/load_json', function($paths) {
    // Remove the default path
    unset($paths[0]);
    // Add your custom path
    $paths[] = __DIR__ . '/json';
    return $paths;
});
