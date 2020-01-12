<?php

function dotcom_listener() {
	if ( '/dotcom/' !== $_SERVER['REQUEST_URI'] ) {
		return;
	}

	if ( empty( $_POST ) ) {
		return;
	}

	$args = [
		'post_type' => 'music_video',
		'post_status' => 'publish',
		'post_content' => wp_kses_post( $_POST['post_content'] ),
		'post_title' => sanitize_text_field( $_POST['post_title'] ),
	];

	$id = wp_insert_post( $args );

	update_post_meta( $id, 'post_dates', [ 'date' => sanitize_text_field( $_POST['post_date'] ) ] );
	update_post_meta( $id, 'name', sanitize_text_field( $_POST['post_name'] ) );
	update_post_meta( $id, 'permalink', sanitize_text_field( $_POST['post_url'] ) );
}
add_action( 'init', 'dotcom_listener' );


function dotcom_import() {
	if ( empty( $_GET ) ) {
		return;
	}

	if ( ! isset( $_GET['do-import'] )|| 'true' !== $_GET['do-import'] ) {
		return;
	}

	set_time_limit( 0 );

	$posts = wp_remote_retrieve_body( wp_remote_get( 'https://public-api.wordpress.com/rest/v1.1/sites/thepirateasylum.wordpress.com/posts/?category=music&status=publish&number=100' ) );

	$data = json_decode( $posts );

	$args = [
		'post_type' => 'music_video',
		'post_status' => 'publish',
	];
	foreach ( $data->posts as $post ) {
		$args['post_content'] = $post->content;
		$args['post_title'] = sanitize_text_field( $post->title );

		$id = wp_insert_post( $args );

		update_post_meta( $id, 'post_dates', [ 'date' => sanitize_text_field( $post->date ) ] );
		update_post_meta( $id, 'name', sanitize_text_field( $post->name ) );
		update_post_meta( $id, 'permalink', sanitize_text_field( $post->URL ) );
	}
}
add_action( 'admin_init', 'dotcom_import' );

function blogsixteen_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'blogsixteen' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'blogsixteen' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}

function michaelbox_header_anchor_links() {
?>

<script>
	jQuery(document).ready(function($){
        let url = window.location.href.split('#')[0];
        $('.entry-content')
		.find('h2[id],h3[id],h4[id]')
		.each(function(i,entry){
			let id = $(this).attr('id');
			$(this).append(
			    '<a class="anchorlink" href="'+url+'#'+id+'"><span class="dashicons dashicons-admin-links"></span></a>'
			);
		});
    });
</script>
<?php
}
add_action( 'wp_footer', 'michaelbox_header_anchor_links' );