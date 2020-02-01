<?php
namespace tw2113;

function override_zerospam_for_webmentions( $commentdata ) {
	if ( function_exists( 'zerospam_get_key' ) && '/wp-json/webmention/1.0/endpoint' === $_SERVER['REQUEST_URI'] && 'webmention' === $commentdata['comment_type'] ) {
		$_POST['zerospam_key'] = zerospam_get_key();
	}
	return $commentdata;
}
add_action( 'preprocess_comment', __NAMESPACE__ . '\override_zerospam_for_webmentions', 9 );