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

function mbe_svg_favicon() {
	?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🥃</text></svg>">
	<?php
}
add_action( 'wp_head', 'mbe_svg_favicon' );
add_action( 'admin_head', 'mbe_svg_favicon' );
add_action( 'wp_head', function() {
	echo '<meta name="google-site-verification" content="nr59Sho8HZXYWDuPVFHUM9mEnBNDfNkQwhI-lzKA0ao" />';
}, 999 );

//Remove <p> tags from images
function mbe_filter_ptags_on_images($content){
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}
add_filter('the_content', 'mbe_filter_ptags_on_images');

// unset some of the default dashboard widgets that are never needed for clients
function mbe_remove_dashboard_widgets(){
  global$wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); //non-installed plugin information
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); //WordPress Blog
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); //Other WordPress News
}
add_action( 'wp_dashboard_setup', 'mbe_remove_dashboard_widgets' );

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
add_filter( 'admin_footer_text', 'mbe_edit_footer_admin' );

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
<?php
}
add_action('wp_footer', 'mbe_piwik');

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
add_action('admin_bar_menu', 'mbe_add_toolbar_items', 100);

add_action( 'plugins_loaded', function(){
	wp_oembed_add_provider('#https?://(?:api\.)?soundcloud\.com/.*#i', 'http://soundcloud.com/oembed', true);
});


function mbe_remove_some_wp_widgets(){
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Nav_Menu_Widget');
}
add_action( 'widgets_init', 'mbe_remove_some_wp_widgets', 1 );

function mbe_security_remove_emails($content) {
    $pattern = '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/i';
    $fix = preg_replace_callback( $pattern, 'mbe_security_remove_emails_logic', $content );

    return $fix;
}
add_filter( 'the_content', 'mbe_security_remove_emails', 20 );
add_filter( 'widget_text', 'mbe_security_remove_emails', 20 );

function mbe_security_remove_emails_logic( $result ) {
    return antispambot( $result[1] );
}

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
add_action('wp_head', 'mbe_opengraph_on_single');

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