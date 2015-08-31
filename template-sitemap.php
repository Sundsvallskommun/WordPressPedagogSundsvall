<?php 
/*
 * Template name: Webbkarta
 */
 ?>
<?php get_header(); ?>

<div class="of-wrap">
  <div class="sk-main-padded of-inner of-clear">
      <?php while ( have_posts() ) : the_post(); ?>
      <?php edit_post_link( __( 'Redigera den här sidan', 'sk' ), '<p>', '</p>' ); ?>
    	<h1><?php the_title(); ?></h1>
		<div class="sk-sitemap">
			<ul>
				<?php $sk_sitemap->sitemap(); ?>
			</ul>
		</div>
	<?php endwhile; ?>
  </div>
</div>
<?php get_footer(); ?>