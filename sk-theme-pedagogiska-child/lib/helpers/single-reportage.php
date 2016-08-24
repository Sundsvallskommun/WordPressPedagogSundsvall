<?php
//@TODO add file description

namespace SKChildTheme;

function get_article_panels() {
	global $post;
	$terms = wp_get_post_terms( $post->ID, 'sitewidecats', array( "fields" => "all" ) );

	$term_names = array();

	$sk_panel_terms = array();
	foreach ( $terms as $term ) {
		$sk_panel_terms['term_names'][] = mb_strtolower( str_replace( 'å', 'a', str_replace( 'ä', 'a', str_replace( 'ö', 'o', $term->name ) ) ) );

		if ( $term->parent == 0 ) {
			$sk_panel_terms['parents'][] = $term;
		} else {
			$sk_panel_terms['children'][] = $term;
		}
	}

	?>
	<h5><?php _e( 'Relaterade inlägg', 'sk' ); ?></h5>
	<?php

	get_article_panel_by_global_category( $parent_category = array( 'verksamhet' ), $panel_title = __( 'Verksamhet', 'sk' ), $offset = '-1' );
	get_article_panel_by_global_category( $parent_category = array(
		'pedagogik',
		'amne'
	), $panel_title = __( 'Ämne & pedagogik', 'sk' ), $offset = '-1' );
	get_article_panel_by_global_category( $parent_category = array(), $panel_title = __( 'Bloggar', 'sk' ), $offset = 1, $only_subsites = '1' );
	get_calendar_panel_by_global_category( $parent_category = array(), $panel_title = __( 'Kalender', 'sk' ), $offset = 1, $only_subsites = '1' );

}

function html_output( $posts, $term_name = false, $panel_title = '', $limit = 5 ) {
	?>
	<?php if ( ! empty( $posts ) ) : ?>
		<div class="sk-article-panel">
			<div class="sk-article-panel-title"><?php echo $panel_title; ?></div>
			<div class="sk-article-panel-list">
				<ul>
					<?php $i = 0; ?>
					<?php foreach ( $posts as $post ) : if ( $i < $limit ) : ?>
						<li>
							<a href="<?php echo ! empty( $post->permalink ) ? $post->permalink : get_permalink( $post->ID ); ?>"
							   title=""><?php echo $post->post_title; ?></a>
							<?php //get_article_categories_for_panel( $post->ID, $post->site_id ); ?></li>
						<?php $i ++; endif; endforeach; ?>
				</ul>
			</div><!-- .sk-article-panel-list -->
		</div><!-- .sk-article-panel -->
	<?php endif; ?>
	<?php
}


function get_article_categories_for_panel( $post_id, $site_id ) {
	switch_to_blog( $site_id );
	$terms = wp_get_post_terms( $post_id, 'sitewidecats', array( "fields" => "all" ) );
	restore_current_blog();
	// check if theres some categories to hide
	$hide_category = get_option( 'sk_hide_global_categories' );
	if ( ! empty( $hide_category ) ) {
		foreach ( $terms as $key => $term ) {
			// remove terms from array
			if ( $term->term_id == $hide_category || $term->parent == $hide_category ) {
				unset( $terms[ $key ] );
			}
		}
	}

	if ( ! empty( $terms ) ) :
		?>
		<span style="font-size: 10px; display:block; margin-bottom: 5px;">
			<?php foreach ( $terms as $term ) : ?>
				<?php echo $term->name; ?>
			<?php endforeach; ?>
			</span>
	<?php endif; ?>

	<?php


}

function get_article_panel_by_global_category( $categories = array(), $panel_title, $offset = '0', $only_subsites = false ) {
	global $post, $wpdb;
	$taxonomy = 'sitewidecats';

	// get post terms in taxonomy sitewidecats
	$term = term_exists( 'Uncategorized', 'category' );
	if ( $term !== 0 && $term !== null ) {
		echo "'Uncategorized' category exists!";
	}

	// get the term object for parent term
	$notice       = '';
	$parent_terms = array();
	foreach ( $categories as $category ) {
		$is_term = term_exists( $category, $taxonomy );
		if ( $is_term !== 0 && $is_term !== null ) {
			$term           = get_term_by( 'slug', $category, $taxonomy );
			$parent_terms[] = $term;
		} else {

			printf( __( '<div style="font-size: 11px; font-style:italic; margin-bottom: 10px;">Felmeddelande: Kategorislug %s existerar inte</div>' ), $category );

			return false;

		}


	}
	// retrieve terms in post within parent term

	$term_children = array();
	if ( ! empty( $parent_terms ) ) {
		foreach ( $parent_terms as $parent_term ) {

			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					//\util::debug( $term, $parent_term );
					if ( $term->parent == $parent_term->term_id ) { //EDIT - Put the right ID here.
						$term_children[] = $term->slug;
					}
				}
			}

		}
	}


	$categories = $term_children;

	$hide_category     = get_option( 'sk_hide_global_categories' );
	$current_permalink = get_permalink( $post->ID );
	if ( $only_subsites == '1' ) {
		$terms = wp_get_post_terms( $post->ID, 'sitewidecats', array( 'fields' => 'all' ) );
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $key => $term ) {
				// remove terms from array
				if ( $term->term_id == $hide_category || $term->parent == $hide_category ) {
					unset( $terms[ $key ] );
				}
			}

			foreach ( $terms as $term ) {
				$categories[] = $term->slug;
			}

		}
	}


// show only main site

	$args = array(
		'network_id' => $wpdb->siteid,
		'offset'     => 1,
	);

	if ( $offset == '-1' ) {
		$args = array(
			'network_id' => $wpdb->siteid,
			'offset'     => 0,
			'limit'      => 1
		);
	}

	$sites = get_sites( $args );

	$args = array( 'post_type' => 'post' );
	if ( ! empty( $categories ) ) {
		$args = array(
			'post_type' => 'post',
			'tax_query' => array(
				array(
					'taxonomy' => 'sitewidecats',
					//'field'    => 'name',
					'field'    => 'slug',
					'terms'    => $categories,
				),
			),
		);
	}


	// get posts from all subsites
	$mixed_posts = array();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		$wp_query = new \WP_Query( $args );
		if ( ! empty( $wp_query->posts[0] ) ) {
			foreach ( $wp_query->posts as $key => $value ) {
				$wp_query->posts[ $key ]->site_id = $site->blog_id;
			}
			$mixed_posts[] = $wp_query->posts;
		}
		restore_current_blog();
	}

	// loop through and save mixed posts as single posts in array
	$subsite_posts = array();
	foreach ( $mixed_posts as $key => $post ) {

		foreach ( $post as $single_post ) {

			switch_to_blog( $single_post->site_id );
			$single_post->permalink = get_permalink( $single_post->ID );
			if ( $single_post->permalink != $current_permalink ) {
				$subsite_posts[] = $single_post;
			}

			restore_current_blog();


		}
	}

	wp_reset_query();
	html_output( $subsite_posts, $terms = false, $panel_title );

}


function get_calendar_panel_by_global_category( $categories = array(), $panel_title ) {
	global $post, $wpdb;
	$hide_category = get_option( 'sk_hide_global_categories' );

	$terms = wp_get_post_terms( $post->ID, 'sitewidecats', array( 'fields' => 'all' ) );
	if ( ! empty( $terms ) ) {
		foreach ( $terms as $key => $term ) {
			// remove terms from array
			if ( $term->term_id == $hide_category || $term->parent == $hide_category ) {
				unset( $terms[ $key ] );
			}
		}

		foreach ( $terms as $term ) {
			$categories[] = $term->slug;
		}

	}


	$posts = array();
	if ( ! empty( $categories ) ) {
		$args = array(
			'post_type'  => 'tribe_events',
			'tax_query'  => array(
				array(
					'taxonomy' => 'sitewidecats',
					'field'    => 'slug',
					'terms'    => $categories,
				),
			),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_EventEndDate',
					'value'   => current_time( 'mysql' ),
					'type'    => 'date',
					'compare' => '>'
				)
			)
		);

		$posts = get_posts( $args );
	}


	wp_reset_query();
	html_output( $posts, $terms = false, $panel_title );


}


/**
 * Get categories for an article
 *
 *
 * @return false
 */
function get_article_categories() {
	global $post;
	$terms = wp_get_post_terms( $post->ID, 'sitewidecats', array( "fields" => "all" ) );

	// check if theres some categories to hide
	$hide_category = get_option( 'sk_hide_global_categories' );
	if ( ! empty( $hide_category ) ) {
		foreach ( $terms as $key => $term ) {
			// remove terms from array
			if ( $term->term_id == $hide_category || $term->parent == $hide_category ) {
				unset( $terms[ $key ] );
			}
		}
	}

	if ( ! empty( $terms ) ) :

		$terms_title = __( 'Kategorier: ', 'sk' );
		if ( count( $terms ) == 1 ) {
			$terms_title = __( 'Kategori: ', 'sk' );
		}
		?>
		<div class="sk-article-terms">
			<div class="sk-article-terms-title of-left"><?php echo $terms_title; ?></div>
			<div class="sk-article-terms-list">
				<?php foreach ( $terms as $term ) : ?>
					<a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a>
				<?php endforeach; ?>
			</div><!-- .sk-article-terms-list -->
		</div><!-- .sk-article-terms -->

	<?php endif; ?>

	<?php


}

?>