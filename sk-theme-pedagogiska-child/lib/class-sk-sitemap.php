<?php
/**
 * SK Sitemap
 *
 *
 * @since   1.0.0
 *
 * @package sk-theme
 */

// Initialize
$sk_sitemap = new SK_Sitemap();


class SK_Sitemap {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	public function sitemap() {
		$args  = array(
			'sort_order'  => 'asc',
			'sort_column' => 'post_title',
		);
		$posts = get_pages( $args );

		foreach ( $posts as $key => $post ) {
			$new_posts[ $key ]                 = $post;
			$new_posts[ $key ]->post_permalink = get_permalink( $post->ID );
		}

		$posts = $new_posts;

		foreach ( $posts as $post ) : ?>
			<?php
			$child_posts = array();
			if ( get_page_template_slug( $post->ID ) == 'template-bloggar.php' ) {
				$child_posts = $this->get_sitemap_subsites();
			} elseif ( get_page_template_slug( $post->ID ) == 'template-archive.php' ) {
				$child_posts = $this->get_sitemap_posts( 'post' );
			}

			?>
			<?php if ( $post->post_parent == 0 ): ?>
				<li><a href="<?php echo $post->post_permalink; ?>"><?php echo $post->post_title; ?></a></li>
			<?php else: ?>
				<li class="child"><a href="<?php echo $post->post_permalink; ?>"><?php echo $post->post_title; ?></a>
				</li>
			<?php endif; ?>


			<?php if ( ! empty( $child_posts ) ): ?>

				<?php foreach ( $child_posts as $child_post ) : ?>
					<li class="child"><a
							href="<?php echo $child_post->post_permalink; ?>"><?php echo $child_post->post_title; ?></a>
					</li>
				<?php endforeach; ?>

			<?php endif; ?>


		<?php endforeach;
	}


	public function get_sitemap_posts( $post_type = '' ) {
		if ( empty( $post_type ) ) {
			return false;
		}

		$args = array(
			'post_type' => $post_type,
		);

		$posts = get_posts( $args );

		foreach ( $posts as $key => $post ) {
			$new_posts[ $key ]                 = $post;
			$new_posts[ $key ]->post_permalink = get_permalink( $post->ID );
		}

		if ( empty( $posts ) ) {
			return false;
		}

		return $posts;

	}


	public function get_sitemap_subsites() {

		$args = array(
			'offset'  => 1,
			'public'  => 1,
			'deleted' => 0
		);

		$sites = get_sites( $args );

		if ( ! empty( $sites ) ) {
			foreach ( $sites as $key => $site ) {
				$site_info                            = get_blog_details( $site->blog_id );
				$sites_object[ $key ]                 = new stdClass();
				$sites_object[ $key ]->post_title     = $site_info->blogname;
				$sites_object[ $key ]->post_permalink = get_bloginfo( 'url' ) . $site->path;
			}
		}

		return $sites_object;

	}


}

?>