<?php
/**
 * Class for slider with calendar puff
 *
 * @since 1.0.0
 *
 * @package sk-theme
 */

// Initialize
$sk_block_slider_special = new SK_Block_Slider_Special();


class SK_Block_Slider_Special {
	
	/**
	 * Constructor
	 *
	 * @since 1.0.0 
	 */
  public function __construct() {
  	//add_action('init', array($this, 'acf_fields'));
  }

	/**
	 * Represents the special slider block for Pedagogiska in the advanced template.
	 *
	 * @since 1.0.0
	 * 
	 * @return null
	 */
	public function the_block() {
		global $post;
	?>
		<div class="block-slider-special sk-main of-inner-padded-b of-clear">
	  	<div class="block-main of-c-md-4 of-c-lg-5 of-c-xl-6">
	    <?php if ( ! empty( $campaign_posts->title ) ) : ?>
	      <header>
	        <h2><?php echo $campaign_posts->title; ?></h2>
	      </header>
	    <?php endif; ?>

		  <div class="owl-carousel <?php echo count( get_sub_field('slider_special_content') ) == 1 ? 'single' : 'multiple' ?>">
		    <?php if( have_rows('slider_special_content') ): ?>
		      <?php while( have_rows('slider_special_content') ): the_row(); 
		        $image = wp_get_attachment_image_src( get_sub_field( 'slider_image' ), 'full' ); 
		        ?>
		        <div class="item">
		        	<img src="<?php echo $image[0]; ?>" alt="<?php echo $image[3] ?>" />
		          <?php if ( get_sub_field( 'slider_image_url' ) ) : ?>
		            <a href="<?php the_sub_field( 'slider_image_url' ); ?>">
		          <?php endif; ?>
		            <div class="wrap">
		                <div class="text"><?php the_sub_field( 'slider_image_text' ); ?></div>
		            </div>
		        <?php if ( get_sub_field( 'slider_image_url' ) ) : ?>
		          </a>
		       <?php endif; ?>
		        </div>
		      <?php endwhile; ?>
		    <?php endif; ?>
		   </div><!-- .owl-carousel -->

	  	</div><!-- .block-main -->

		  <div class="block-sidebar of-c-md-4 of-c-lg-3 of-c-xl-2 of-omega">
				<?php SKChildTheme\the_calendar_box(); ?>
		  </div><!-- .sidebar-block -->
	  </div><!-- .sk-main-padded -->
	  <?php
	}

	/**
	 * Class for implementing ACF Fields
	 * CURRENTLY NOT IN USE
	 * 
	 */
	public function acf_fields(){

	}

}

?>