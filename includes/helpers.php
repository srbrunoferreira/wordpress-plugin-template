<?php

/**
 * This is a simpel WordPress plugin template.
 * Author URI: https://github.com/srbrunoferreira
 * Requires at least: 5.8
 * Tested up to: 5.8
 *
 * @package WordPress
 * @author Bruno Ferreira
 * @since 1.0.0
 */

/**
 * @param $length The length of the string to be returned.
 * @return string
 */
function wpptGenerateRandomString($length = 5)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function crunchify_print_scripts_styles()
{
    $result = [];
    $result['scripts'] = [];
    $result['styles'] = [];

    // Print all loaded Scripts
    global $wp_scripts;
    foreach ($wp_scripts->queue as $script) :
        $result['scripts'][] =  $wp_scripts->registered[$script]->src . ";";
    endforeach;

    // Print all loaded Styles (CSS)
    global $wp_styles;
    foreach ($wp_styles->queue as $style) :
        $result['styles'][] =  $wp_styles->registered[$style]->src . ";";
    endforeach;

    echo '<pre>';
    print_r($result);
    echo '</pre><hr>';
}
