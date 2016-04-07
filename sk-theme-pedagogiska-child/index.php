<?php get_header(); ?>

<div class="of-wrap">
  <!--<div class="sk-main-padded of-inner-padded-t of-clear">-->
  <div class="sk-main of-inner of-clear">
  	<div class="of-c-sm-4 of-c-md-3">
			<?php
			/* Run the loop to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-index.php and that will be used instead.
			 */
			 get_template_part( 'loop', 'index' );
			?>
		</div>
		<?php get_sidebar( 'blog-subsite' ); ?>
  </div>
</div>
<?php get_footer(); ?>
