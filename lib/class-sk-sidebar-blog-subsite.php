<?php

namespace SKChildTheme;

/**
 * Handles the sidebar content for blogs (subsites)
 *
 * @since 1.0.0
 *
 * @package sk-theme
 */
echo "tesat";
class SK_Post_Sidebar_Blog_Subsite {
	
  /**
   * Constructor
   */
  public function __construct() {

	}

	public function get_blog_info_html(){
		$blog_image = $this->get_blog_image('url');
		$blog_desc = $this->get_blog_desc();
		if( empty( $blog_image ) && empty( $blog_desc ) )
			return false;
		?>
		<div class="sk-blog-info sk-sidebar-panel">
		<?php if(! empty( $blog_image )) : ?>
			<div class="sk-blog-image"><img src="<?php echo $this->get_blog_image('url'); ?>"></div>
		<?php endif; ?>
			<div class="sk-blog-desc"><?php echo $blog_desc; ?></div>		
		</div>
		
		<?php
	}

	public function get_blog_image( $attr ){

		switch ( $attr ) {
			case 'url':
				$key = 0;
			break;
			case 'width':
				$key = 1;
			break;
			case 'height':
				$key = 2;
			break;

			default:
				$key = 0;
				break;			
			
		}

		$image_id = get_field( 'sk-blog-image', 'options');
		$image = wp_get_attachment_image_src( $image_id, 'full' );

		return $image[$key];
	}

	public function get_blog_desc(){
		$desc = get_field( 'sk-blog-desc', 'options');
		return $desc;
	}	

}

// Initializing class
if( class_exists( 'SKChildTheme\\SK_Post_Sidebar_Blog_Subsite' ) ){
  $sk_sidebar_blog_subsite = new SK_Post_Sidebar_Blog_Subsite();
}

?>