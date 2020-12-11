<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BlogSixteen
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="footer-content">
			<div class="site-info">
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'blogsixteen' ) ); ?>"><?php printf( esc_html__( '// Proudly powered by %s', 'blogsixteen' ), 'WordPress' ); ?></a>
				<span class="sep"> | </span>
				<?php printf( esc_html__( 'Theme: %1$s', 'blogsixteen' ), 'blogsixteen', ''); ?>
				<p>// Like how well the site runs? Use my <a href="https://m.do.co/c/4641c57e4ba2">affiliate link</a> to get your own DigitalOcean droplet.</p>
			</div><!-- .site-info -->
		</div>

	</footer><!-- #colophon -->

	</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
