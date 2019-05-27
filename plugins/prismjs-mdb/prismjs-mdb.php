<?php
/*
Plugin Name: PrismJS Syntax
Plugin URI: http://michaelbox.net
Description: Add PrismJS support for code highlighting
Version: 1.0
Author: Michael Beckwith
Author URI: http://michaelbox.net
License: GPLv2
*/

function mdb_enqueue_scripts() {
	if ( mdb_has_shortcode( 'code' ) ) {
		wp_enqueue_script( 'prismjs', plugins_url( 'prism.js', __FILE__ ) );
		wp_enqueue_style( 'prismcss', plugins_url( 'prism.css', __FILE__ ) );
	}
}
add_action( 'wp_enqueue_scripts', 'mdb_enqueue_scripts' );

/**
 * Check posts to see if shortcode has been used
 *
 * @since 1.0
 */
function mdb_has_shortcode( $shortcode = '' ) {
    global $wp_query;
    foreach( $wp_query->posts as $post ) {
        if ( ! empty( $shortcode ) && ( false !== stripos( $post->post_content, '[' . $shortcode ) ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Functionality to set up highlighter shortcode correctly.
 *
 * This function is attached to the 'the_content' filter hook.
 *
 * @since 1.0
 */
function mdb_pre_process_shortcode( $content ) {
    global $shortcode_tags;

    $orig_shortcode_tags = $shortcode_tags;
    $shortcode_tags = array();

    // New shortcodes
    add_shortcode( 'code', 'mdb_syntax_highlighter' );

    $content = do_shortcode( $content );
    $shortcode_tags = $orig_shortcode_tags;

    return $content;
}
add_filter( 'the_content', 'mdb_pre_process_shortcode', 7 );

/**
 * Code shortcode function
 *
 * This function is attached to the 'code' shortcode hook.
 *
 * @since 1.0
 */
function mdb_syntax_highlighter( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'type' => 'php',
        'title' => '',
        'linenums' => '',
    ), $atts ) );

    $title = ( $title ) ? ' rel="' . $title . '"' : '';
    $linenums = ( $linenums ) ? ' data-linenums="' . $linenums . '"' : '';
    $find_array = array( '[', ']' );
    $replace_array = array( '[', ']' );
    return '<pre><code class="language-' . $type . '">' . preg_replace_callback( '|(.*)|isU', 'mdb_pre_entities', trim( str_replace( $find_array, $replace_array, $content ) ) ) . '</code></pre>';
}

/**
 * Helper function for 'mdb_syntax_highlighter'. Not perfect
 *
 * @since 1.0
 */
function mdb_pre_entities( $matches ) {
    return str_replace( $matches[1], htmlentities( $matches[1]), $matches[0] );
}