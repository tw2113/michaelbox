<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package BlogSixteen
 */

get_header();?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

        <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php
			if ( function_exists( 'bcn_display' ) ) {
				bcn_display();
			}?>
        </div>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'single' ); ?>

			<?php
			the_post_navigation(
				[
					'prev_text' => '<-- %title',
					'next_text' => '%title -->',
				]
			);
			?>

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>

		<?php endwhile; // End of the loop. ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
