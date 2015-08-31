<?php 
/*
 * Template name: Bloggar
 */

	$args = array(
		'network_id'	=> $wpdb->siteid,
		'offset'			=> 1
		);
	$sites = wp_get_sites( $args );

	foreach ( $sites as $key => $s ){
		switch_to_blog( $s['blog_id'] );
		$sites[$key]['name'] = get_bloginfo('name');
		$sites[$key]['description'] = get_bloginfo('description');

		$sites[$key]['image'] = wp_get_attachment_image_src(get_field( 'sk-blog-image', 'options' ), 'thumbnail' );
		restore_current_blog();
	}
 ?>
<?php get_header(); ?>

<div class="of-wrap">
  <div class="sk-main-padded of-inner of-clear">

		<?php if( !empty( $sites ) ) : ?>
		<div class="sk-main sk-latest-posts-block list-blogs">  		  	
			<ul class="sk-grid-list">  
				<?php foreach ( $sites as $site ) : ?>
					<li>
						<a href="<?php switch_to_blog($site['blog_id']); ?><?php echo get_bloginfo('url' ); ?><?php restore_current_blog(); ?>" class="of-dark-link">
							
							<?php if(!empty( $site['image'] ) ) : ?>
								<figure>
									<img width="<?php echo $site['image'][1]; ?>" height="<?php echo $site['image'][2]; ?>" alt="<?php echo $site['description'];  ?>" class="attachment-thumbnail wp-post-image" src="<?php echo $site['image'][0]; ?>">
								</figure>
							<?php endif; ?>

								<article class="sk-narrow">
									<header>
										<h5><?php echo $site['name']; ?></h5>
									</header>
									<p><?php echo $site['description']; ?></p>
								</article>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<?php
		/* Run the loop to output the posts.
		 * If you want to overload this in a child theme then include a file
		 * called loop-index.php and that will be used instead.
		 */
		 //query_posts(array('post_type' => 'post') );
		 //get_template_part( 'loop', 'index' );
		?>
  </div>
</div>
<?php get_footer(); ?>