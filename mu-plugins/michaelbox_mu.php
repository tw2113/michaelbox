<?php
/*
Plugin Name: Michaelbox Utilities.
Plugin URI: http://michaelbox.net
Author: Michael Beckwith
Version: 2.113
*/

add_action('init', function(){
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
});


/*add_action( 'wp_head', function(){
	echo '<link rel="shortcut icon" href="'.get_bloginfo('url').'/favicon.ico" />';
	echo '<link rel="apple-touch-icon" href="'.get_bloginfo('url').'/apple-touch-icon.png" />';
});*/
/* adds the favicon/appleicon to the wp_head() call*/

add_action( 'wp_head', function() {
	echo '<meta name="google-site-verification" content="nr59Sho8HZXYWDuPVFHUM9mEnBNDfNkQwhI-lzKA0ao" />';
}, 999 );

add_filter('the_content', 'mbe_filter_ptags_on_images');
//Remove <p> tags from images
function mbe_filter_ptags_on_images($content){
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// automatically set links around inserted images to no link
$image_set = get_option( 'image_default_link_type' );
if (!$image_set == 'none') {
	update_option('image_default_link_type', 'none');
}

add_action('wp_dashboard_setup', 'mbe_remove_dashboard_widgets');
// unset some of the default dashboard widgets that are never needed for clients
function mbe_remove_dashboard_widgets(){
  global$wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); //non-installed plugin information
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); //WordPress Blog
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); //Other WordPress News
}

add_filter('admin_footer_text', 'mbe_edit_footer_admin');
//Customize Admin footer text
function mbe_edit_footer_admin () {
    $quotes = [
		'You look very nice today.',
		'If you are not questioning my sanity, I am not trying hard enough.',
		'Doctor: And where do you live, Simon? Mary Hobbes: I live in the weak and the wounded... Doc.',
		'If drinking coffee/beer and pissing sarcasm was a career option, I\'d be close to CEO',
		'Just ordered a cup of black coffee from Starbucks. Everyone stared.',
		'How do I know if something is inappropriate? If it makes makes me laugh for 5 minutes or longer',
		'If you gave me a car made of diamonds and blowjobs all day I still wouldn\'t drink Coors Light.',
		'Welcome to the inner workings of my mind. So dark and foul I can\'t disguise, Can\'t disguise'
	];
    $count = count($quotes) - 1;
	echo $quotes[ rand( 0, $count) ];
}

add_action('wp_footer', 'mbe_piwik');
function mbe_piwik() {
	if ( is_user_logged_in() ) return; ?>

	<!-- Piwik -->
	<script type="text/javascript">
		var _paq = _paq || [];
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);
		(function() {
			var u=(("https:" == document.location.protocol) ? "https" : "http") + "://trexthepirate.com/traffic/";
			_paq.push(['setTrackerUrl', u+'piwik.php']);
			_paq.push(['setSiteId', 1]);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
			g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		})();
	</script>
	<noscript><p><img src="http://trexthepirate.com/traffic/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik Code -->
<?php }

add_action('admin_bar_menu', 'mbe_add_toolbar_items', 100);
function mbe_add_toolbar_items($admin_bar){
	if ( is_admin() ) {
		return;
	}

	$admin_bar->add_menu( [
		'id' => 'plugins',
		'parent' => 'site-name',
		'title' => 'Plugins',
		'href' => admin_url('plugins.php'),
		'meta' => [
			'title' => __('Plugins Admin'),
			'class' => 'plugins_class'
		],
	]);
}

add_action( 'plugins_loaded', function(){
	wp_oembed_add_provider('#https?://(?:api\.)?soundcloud\.com/.*#i', 'http://soundcloud.com/oembed', true);
});

add_action('widgets_init', 'mbe_remove_some_wp_widgets', 1);
function mbe_remove_some_wp_widgets(){
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Nav_Menu_Widget');
}

add_filter( 'the_content', 'mbe_security_remove_emails', 20 );
add_filter( 'widget_text', 'mbe_security_remove_emails', 20 );
function mbe_security_remove_emails($content) {
    $pattern = '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/i';
    $fix = preg_replace_callback($pattern, 'mbe_security_remove_emails_logic', $content);

    return $fix;
}
function mbe_security_remove_emails_logic($result) {
    return antispambot($result[1]);
}

add_action( 'wp_head', 'mbe_link_share' );
function mbe_link_share() {
	global $post;

	if ( is_single() && false === get_post_format( $post->ID ) ) { ?>
	<style>
		.share a.sharefb, .share a.sharetw {
			background: url("<?php bloginfo('url'); ?>/wp-content/sharebtns.png") no-repeat scroll 0 0 transparent;
		    display: block;
		    float: left;
		    height: 23px;
		    margin-right: 15px;
		    text-indent: -999em;
		    width: 60px;
		}
		.share a.sharetw {
			background-position: -68px 0;
		}
		.affiliate {
			border-top: solid rgb(166, 166, 158) 3px;
			clear: both;
			float: left;
			font-size: 13px;
			margin-top: 20px;
			padding-top: 5px;
		}
	</style>
	<?php }
	if ( is_page( 366 ) ) { ?>
	<style>
		blockquote {
			padding: 15px;
		}
		blockquote:nth-child(even) {
			background-color: #ddd;
		}
	</style>
	<?php
	}
}

add_filter('the_content', 'mbe_social_and_affiliate_on_single', 99);
function mbe_social_and_affiliate_on_single( $content ) {
	global $post;
	setup_postdata( $post );

	if ( 'music_video' === get_post_type( $post->ID ) ) {
		return $content;
	}

	if ( is_single() && false === get_post_format( $post->ID ) ) {
		$social = '
		<div class="share">
		<a href="http://twitter.com/home?status=Reading now on MB.net: ' . get_the_title() . ' - ' . get_permalink() . '" class="sharetw">Twitter</a>
		<a target="blank" href="http://www.facebook.com/sharer.php?u=' . get_permalink() . '&t=' . get_the_title() . '" class="sharefb">Facebook</a>';

		if ( function_exists( 'bccl_get_license_text_hyperlink' ) ) {
			$social .= bccl_get_license_text_hyperlink();
		}
		$social .= '</div>';
		$affiliate = '
		<div class="affiliate">
		Is your current hosting dragging you down and you want to find a better place to host your WordPress site? You should head over to WPEngine and sign up. Use my <a href="http://www.shareasale.com/r.cfm?b=398784&u=668008&m=41388&urllink=&afftrack=" title="You are awesome if you use this link!">affiliate link</a> to tell them I sent you.
  		</div>';
  		$content = $content.$social.$affiliate;
	}
	return $content;
}

add_action('wp_head', 'mbe_opengraph_on_single');
function mbe_opengraph_on_single() {
	if ( is_single() ) {
        global $post;
        setup_postdata( $post );
echo
'<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@tw2113">
<meta name="twitter:creator" content="@tw2113">
<meta name="twitter:url" content="' . get_permalink() . '">
<meta name="twitter:title" content="' . esc_html( get_the_title() ) . '">
<meta name="twitter:description" content="' . wp_trim_words( get_the_content(), 30 ) . '">';

echo
'<meta property="og:type" content="article" />
<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />
<meta property="og:url" content="' . get_permalink() . '" />';

        if ( has_post_thumbnail() ) {
        $imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
echo '<meta property="og:image" content="' . $imgsrc[0] . '" />';
        }
    }
}

function mbe_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'mbe_mime_types' );

function kaspersky_dequeue_jquery_migrate( &$scripts){
	if(!is_admin() && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) )){
		$scripts->remove( 'jquery');
		$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
	}
}
add_action( 'wp_default_scripts', 'kaspersky_dequeue_jquery_migrate' );

function mbe_atom_links() {
    $tmpl = '<link rel="%s" type="%s" title="%s" href="%s" />';

    printf(
        $tmpl,
        esc_attr( 'alternate' ),
        esc_attr( 'application/atom+xml' ),
        esc_attr( get_bloginfo( 'name' ) . '&raquo; Atom Feed link'  ),
		get_bloginfo( 'atom_url' )
    );
}
add_action( 'wp_head', 'mbe_atom_links' );

function mbe_add_atom_mime_support( $mimes ) {
	$mimes = array_merge(
		$mimes,
		array(
			'atom' => 'application/atom+xml',
		)
	);

	return $mimes;
}
add_filter( 'mime_types', 'mbe_add_atom_mime_support' );