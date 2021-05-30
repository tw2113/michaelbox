<?php

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
		'<span class="author vcard">' . get_the_author_meta( 'first_name' ) . '</span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}

function blogsixteen_entry_footer() {

    if ( ! is_search() ) {
        echo '<hr/>';
        ?>
            <div class="authorbio">
                <div class="avatar">
                    <?php
                        #echo get_avatar( get_the_author_meta( 'ID' ) );
                    ?>
                </div>
                <div class="biotext">
                <?php
                    echo get_avatar( get_the_author_meta( 'ID' ), 160 );
                    echo wpautop( get_the_author_meta('description') );
                ?>
                </div>
            </div>
        <?php
    }
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'blogsixteen' ) );
		if ( $categories_list && blogsixteen_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Categories: %1$s', 'blogsixteen' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'blogsixteen' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tags: %1$s', 'blogsixteen' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'blogsixteen' ), esc_html__( '1 Comment', 'blogsixteen' ), esc_html__( '% Comments', 'blogsixteen' ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
		/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'blogsixteen' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
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

function michaelbox_dashicons_front_end() {
	wp_enqueue_style( 'dashicons' );
}
add_action( 'wp_enqueue_scripts', 'michaelbox_dashicons_front_end' );

function michaelbox_webmention_header() {
    $obj = get_queried_object();

    if ( $obj instanceof WP_Post && 'open' === $obj->ping_status ) {
		echo '<h3>Webmentions</h3>';
    }
}
add_action( 'comment_form_after', 'michaelbox_webmention_header' );

function michaelbox_home_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'michaelbox_home_page_menu_args' );

function michaelbox_new_excerpt_more( $more ) {
    $title = strip_tags( get_the_title( get_the_ID() ) );
    $label = esc_attr( "Read more about {$title}" );
	return ' <a class="read-more" aria-label="' . $label . '" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Read More...', 'blogsixteen' ) . '</a>';
}

add_action('wp_head',function(){
	remove_filter( 'excerpt_more', 'blogsixteen_new_excerpt_more' );
	add_filter( 'excerpt_more', 'michaelbox_new_excerpt_more' );
});
