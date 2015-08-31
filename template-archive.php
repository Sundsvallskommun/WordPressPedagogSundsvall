<?php 
/*
 * Template name: Arkiv
 */
 ?>
<?php get_header(); ?>

<div class="of-wrap">
  <div class="sk-main-padded of-inner of-clear">
		<?php
		/* Run the loop to output the posts.
		 * If you want to overload this in a child theme then include a file
		 * called loop-index.php and that will be used instead.
		 */
		 query_posts(array('post_type' => 'post') );
		 get_template_part( 'loop', 'index' );
		?>
  </div>
</div>
<?php get_footer(); ?>