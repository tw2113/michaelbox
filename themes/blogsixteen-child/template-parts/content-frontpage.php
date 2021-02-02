<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package BlogSixteen
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
        <a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title( '<h3 class="entry-title">', '</h3>' ); ?></a>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->

