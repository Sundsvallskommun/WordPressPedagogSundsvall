<?php get_header(); ?>

<div class="of-wrap">
  <div class="sk-main-padded of-inner-padded-t of-clear">

    <div class="of-c-sm-4 of-c-md-3">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php // edit_post_link( __( 'Redigera den här sidan', 'sk' ), '<p>', '</p>' ); ?>
        <h1><?php the_title(); ?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
          <?php 
          $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'image-1000' );
          $alt = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ); 
          ?>
          <figure class="sk-featured-image-single">
            <img src="<?php echo $thumbnail[0]; ?>" alt="<?php echo $alt; ?>">
          </figure>
        <?php endif; ?>

        <div class="sk-entry-content">
          <?php 
          $answer = get_field( 'svar', $post->ID ); 
          $references = get_field( 'referenser', $post->ID );
          
           echo $answer;?>
           <div class="of-c-sm-10 of-c-xxl-10">
            <?php if ( ! empty( $references ) ) : ?>
                <?php _e('Relaterat:', 'sk'); ?>
                <?php foreach( $references as $reference ) : ?>
                  <a class="reference-link" href="<?php echo get_permalink( $reference->ID ); ?>"><?php echo $reference->post_title; ?></a>
                <?php endforeach; ?>
             
              <?php endif; ?>
              </div>
              
             <div class="single-faq-bottom"> 


              
                <div class="social-sharing">
               
                <p><?php _e('Dela frågan på:', 'sk'); ?></p>
                  <a class="of-icon" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(the_permalink()); ?>&t=<?php echo urlencode(the_title()); ?>" target="_blank"><i><svg viewBox="0 0 512 512"><use xlink:href="#facebook"></use></svg></i></a>

                  <a class="of-icon" href="http://twitter.com/share?text=<?php echo urlencode(the_title()); ?>&url=<?php echo urlencode(the_permalink()); ?>" target="_blank"> <i><svg viewBox="0 0 512 512"><use xlink:href="#twitter"></use></svg></i></a>

                  <!--<a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(the_permalink()); ?>"><i><svg viewBox="0 0 512 512"><use xlink:href="#linkedin"></use></svg></i>LinkedIn</a> -->
                </div> 
             
              
            </div>
          <?php // edit_post_link( __( 'Redigera den här sidan', 'sk' ), '<p>', '</p>' ); ?>
        </div>
      <?php endwhile; // end of the loop. ?>
    </div>
    
  </div>
</div>
<?php get_footer(); ?>