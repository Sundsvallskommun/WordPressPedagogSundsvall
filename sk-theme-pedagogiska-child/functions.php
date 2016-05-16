<?php
/* ------------------------------------------
|  LOAD UTILITY CLASS, REMOVE FOR PRODUCTION |
 -------------------------------------------- */

require_once( locate_template( '/lib/util.php' ) );
/* ------------------------------------
|  LOAD AND INIT ALL SITE REQUIREMENTS |
 -------------------------------------- */

require_once locate_template( '/lib/class-sk-custom-capabilities.php' );

// Load parent themes template helpers
require_once( get_template_directory() . '/lib/helpers/advanced-template.php' );
require_once( get_template_directory() . '/lib/helpers/general-template.php' );

// Load child themes template helpers
require_once locate_template( '/lib/helpers/advanced-template.php' );
require_once locate_template( '/lib/helpers/general-template.php' );
require_once locate_template( '/lib/helpers/single-reportage.php' );

// Load parent init class
require_once( get_template_directory() . '/lib/class-sk-init.php' );
// Load child init class
require_once( get_stylesheet_directory() . '/lib/class-sk-init.php' );
$init = new SKChildTheme\SK_Init();

// Load parent posttypes class
require_once( get_template_directory() . '/lib/class-sk-post-types.php' );

// Load the global categories
require_once( get_stylesheet_directory() . '/lib/class-sk-global-categories.php' );
$global_categories = new SKChildTheme\SK_Global_Categories();

// Site info options field
require_once( locate_template( '/lib/class-sk-site-info.php' ) );
$sk_site_info = new SKChildTheme\Sk_Site_Info();

/* ------------------------------------
|  ACTIONS, FILTERS, FUNCTIONS         |
 -------------------------------------- */


add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
  wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/assets/css/style.css' );
}


/**
 * Add searchable sites to search form if relevanssi is activated.
 * 
 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
 * 
 * @return string
 */
function sk_is_multisite_search(){
  
  if( function_exists('relevanssi_search_multi') ){
    $args = array(
      'public'     => true,
      'archived'   => false,
      'mature'     => false,
      'spam'       => false,
      'deleted'    => false
    ); 

    $sites = wp_get_sites( $args );

    $search_in_sites = array();
    foreach ($sites as $site ) {
      $search_in_sites[] = $site['blog_id'];
    }

    echo '<input type="hidden" value="' . implode( ',', $search_in_sites ) . '" name="searchblogs">';
    
  }

  return false;

}


/**
 * Set only post type posts to be included in archive
 *
 * @since 1.0.0 
 * 
 * @param  array $query 
 *
 * @return 	null
 * 
 */
function archive_posts( $query ) {

  if ( is_admin() || ! $query->is_main_query() || isset( $query->query['post_type'] ) && $query->query['post_type'] == 'tribe_events' )
  	return;

  if ( $query->is_archive() ) {
 		$query->set('post_type', 'post');
  }
}
add_action( 'pre_get_posts', 'archive_posts', 1 );



/**
 * Filter for redirect a single.php call from main site
 *
 * @since 	1.0.0
 * 
 * @param  	string $template 
 * 
 * @return 	string $template
 * 
 */
function single_reportage( $template ){
	$post_type = get_post_type();

	// only for main site and post type post
	if( is_main_site() && $post_type == 'post' ){
		return get_stylesheet_directory() . "/single-reportage.php";
	}
	
	return $template;
} 
add_filter('single_template', 'single_reportage');


/**
 * [sk_get_site_color description]
 *
 * @since 1.0.0
 * 
 * @param  boolean $echo print or return
 * 
 * @return string
 */
function sk_get_site_color( $echo = false ){
	$main_theme = 'sk-theme-' . get_field( 'site_color', 'option' );
	if(!is_main_site( get_current_blog_id() )){
		switch_to_blog(1);
		$main_theme = 'sk-theme-' . get_field( 'site_color', 'option' );
		restore_current_blog();
	}

	if( $echo == true )
		echo $main_theme;
	else
		return $main_theme;
}

/**
 * Adding css class to body
 *
 * @since 1.0.0
 * 
 * @param  array $classes 	current classes
 * 
 * @return array $classes 	current + added classes
 * 
 */
function network_body_classes( $classes ) {

	$site_hierarchy = 'subsite';
	if( is_main_site( get_current_blog_id() ) )
		$site_hierarchy = 'mainsite';

	$id = get_current_blog_id();
	$slug = strtolower(str_replace(' ', '-', trim(get_bloginfo('name'))));
	
	$classes[] = $slug;
	$classes[] = $site_hierarchy;

	return $classes;
}
add_filter( 'body_class', 'network_body_classes' );


function get_latest_global_posts() {

	global $switched;

	$sites = wp_get_sites();
	$posts = array();

	// Check 
	if( count( $sites ) > 0 ) {

		foreach( $sites as $site ) {

			// No need to do this on main site. Already updated.
      if( $site['blog_id'] != $site['site_id'] ) {

      	switch_to_blog( $site['blog_id'] );
      	$term = get_term_by( 'slug', 'forskola', 'sitewidecats' );

      	$args = array(
      		'posts_per_page' => 5,
      		'tax_query' => array(
      			array(
      				'taxonomy' => 'sitewidecats',
      				'field' => 'term_id',
      				'terms' => $term->term_id
      			)
      		)
      	);

      	$posts[$site['blog_id']] = get_posts( $args );
      	restore_current_blog();

      }

		}

	}

	return $posts;

}

function the_breadcrumb(){
    // Settings
    $separator  = '&gt;';
    $id         = 'breadcrumbs';
    $class      = 'breadcrumbs';
    $home_title = 'Homepage';


global $post,$wp_query;
$category = get_the_category();

// Build the breadcrums
echo '<ul id="' . $id . '" class="' . $class . '">';

// Do not display on the homepage
if ( !is_front_page() ) {

// Home page
echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
echo '<li class="separator separator-home"> ' . $separator . ' </li>';

if ( is_single() ) {

// Single post (Only display the first category)
echo '<li class="item-cat item-cat-' . $category[0]->term_id . ' item-cat-' . $category[0]->category_nicename . '"><a class="bread-cat bread-cat-' . $category[0]->term_id . ' bread-cat-' . $category[0]->category_nicename . '" href="' . get_category_link($category[0]->term_id ) . '" title="' . $category[0]->cat_name . '">' . $category[0]->cat_name . '</a></li>';
echo '<li class="separator separator-' . $category[0]->term_id . '"> ' . $separator . ' </li>';
echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';

} else if ( is_category() ) {

// Category page
echo '<li class="item-current item-cat-' . $category[0]->term_id . ' item-cat-' . $category[0]->category_nicename . '"><strong class="bread-current bread-cat-' . $category[0]->term_id . ' bread-cat-' . $category[0]->category_nicename . '">' . $category[0]->cat_name . '</strong></li>';

} else if ( is_page() ) {

// Standard page
if( $post->post_parent ){

// If child page, get parents 
$anc = get_post_ancestors( $post->ID );

// Get parents in the right order
$anc = array_reverse($anc);

// Parent page loop
foreach ( $anc as $ancestor ) {
$parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
$parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
}

// Display parent pages
echo $parents;

// Current page
echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';

} else {

// Just display current page if not parents
echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';

}

} else if ( is_tag() ) {

// Tag page

// Get tag information
$term_id = get_query_var('tag_id');
$taxonomy = 'post_tag';
$args ='include=' . $term_id;
$terms = get_terms( $taxonomy, $args );

// Display the tag name
echo '<li class="item-current item-tag-' . $terms[0]->term_id . ' item-tag-' . $terms[0]->slug . '"><strong class="bread-current bread-tag-' . $terms[0]->term_id . ' bread-tag-' . $terms[0]->slug . '">' . $terms[0]->name . '</strong></li>';

} elseif ( is_day() ) {

// Day archive

// Year link
echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

// Month link
echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';

// Day display
echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';

} else if ( is_month() ) {

// Month Archive

// Year link
echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

// Month display
echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';

} else if ( is_year() ) {

// Display year archive
echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';

} else if ( is_author() ) {

// Auhor archive

// Get the author information
global $author;
$userdata = get_userdata( $author );

// Display author name
echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';

} else if ( get_query_var('paged') ) {

// Paginated archives
echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</strong></li>';

} else if ( is_search() ) {

// Search results page
echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';

} elseif ( is_404() ) {

// 404 page
echo '<li>' . 'Error 404' . '</li>';
}

}

echo '</ul>';

}

?>