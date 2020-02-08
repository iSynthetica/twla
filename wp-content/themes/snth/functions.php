<?php
/**
 * Synthetica functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Synthetica
 */

/** -----------------------------------------
 *    01. Define constants
 *  ----------------------------------------- */
define('SNTH_VERSION', '1.0.0');

define('SNTH_PATH', get_stylesheet_directory());
define('SNTH_PARENT_PATH', get_template_directory());
define('SNTH_INC', SNTH_PATH . '/includes');

define('SNTH_URI', get_stylesheet_directory_uri());
define('SNTH_PARENT_URI', get_template_directory_uri());
define('SNTH_ASSETS', SNTH_URI . '/assets');
define('SNTH_CSS', SNTH_ASSETS . '/css');
define('SNTH_JS', SNTH_ASSETS . '/js');
define('SNTH_IMG', SNTH_ASSETS . '/img');
define('SNTH_VENDORS', SNTH_ASSETS . '/vendors');

if (file_exists(SNTH_INC . '/class-snth-core.php')) {
    include_once (SNTH_INC . '/class-snth-core.php');
}

if (class_exists('Woocommerce') && file_exists(SNTH_INC . '/class-snth-woo.php')) {
    include_once (SNTH_INC . '/class-snth-woo.php');
}

if (class_exists('Woocommerce') && file_exists(SNTH_INC . '/class-snth-woo.php')) {
    include_once (SNTH_INC . '/generate_posts.php');
}

//add_action( 'shutdown', function(){
//    print '<pre>';
//    print_r( _get_all_image_sizes() );
//    print '</pre>';
//});
//
//
//function _get_all_image_sizes() {
//    global $_wp_additional_image_sizes;
//
//    $default_image_sizes = array( 'thumbnail', 'medium', 'large' );
//
//    foreach ( $default_image_sizes as $size ) {
//        $image_sizes[ $size ][ 'width' ] = intval( get_option( "{$size}_size_w" ) );
//        $image_sizes[ $size ][ 'height' ] = intval( get_option( "{$size}_size_h" ) );
//        $image_sizes[ $size ][ 'crop' ] = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
//    }
//
//    if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
//        $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
//    }
//
//    return $image_sizes;
//}

