<?php get_header();

  $classes[] = 'has-sidebar-menu';
  $classes[] = 'has-sidebar-boxes';
?>
<div class="of-wrap<?php echo ! empty( $classes ) ? ' ' . implode( ' ', $classes ) : ''; ?>">
  <div class="sk-main-padded of-inner of-clear">

  <div class="of-c-sm-fixed-4 of-c-md-fixed-1 of-c-lg-fixed-2 of-c-lg-fixed-2 sk-sidebar of-hide-to-lg sof-push-8">
    <div class="sk-article-panel-wrapper">
      <?php SKChildTheme\get_article_panels(); ?>
    </div>
  </div>

  <div class="of-c-lg-flexible-10 of-c-xl-flexible-10 of-omega">
    <div class="of-c-sm-4 of-inner-padded-r of-c-md-4 of-c-lg-12 of-c-xl-flexible-10 of-omega sof-pull-2">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php edit_post_link( __( 'Redigera den här sidan', 'sk' ), '<p>', '</p>' ); ?>
        <h1><?php the_title(); ?></h1>
        <div class="of-inner-padded-b-half">
          <ul class="of-meta-line">
            <li><?php the_time('j F Y H:i'); ?></li>
            <li>Publicerat av: <?php the_author(); ?></li>
          </ul>
        </div>
        <?php if ( has_post_thumbnail() ) : ?>
          <?php 
          $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
          $alt = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ); 
          ?>
          <figure class="sk-featured-image-single">
            <img src="<?php echo $thumbnail[0]; ?>" alt="<?php echo $alt; ?>">
          </figure>
        <?php endif; ?>

        <div class="sk-entry-content">
          <?php the_content(); ?>
          <?php SKChildTheme\get_article_categories(); ?>
          <?php edit_post_link( __( 'Redigera den här sidan', 'sk' ), '<p>', '</p>' ); ?>
        </div>
      <?php endwhile; // end of the loop. ?>
      <?php comments_template( '', true );  ?>
      </div>

        <div class="of-c-sm-4 of-c-md-4 of-c-lg-12 of-c-xl-fixed-2 sk-sidebar of-hide-from-lg">
          <?php SKChildTheme\get_article_panels(); ?>
        </div>

        <div class="of-c-sm-4 of-c-md-4 of-c-lg-12 of-c-xl-fixed-2 of-omega sk-sidebar">
          <?php get_sidebar( 'reportage' ); ?>
        </div>

  </div>



  </div>
</div>
<?php get_footer(); ?>