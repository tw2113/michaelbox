<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package BlogSixteen
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main" data-test="test">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

			<?php endwhile; // End of the loop. ?>

            <hr/>
            <h2>Perhaps start with the latest from the blog:</h2>
            <?php

            $args = [
                'posts_per_page' => 1,
                'post_status'    => 'publish',
            ];

            $frontpost = new WP_Query( $args );

            while ( $frontpost->have_posts() ) {
                $frontpost->the_post();
				get_template_part('template-parts/content', 'frontpage');
			}
			wp_reset_postdata();
            ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
