<?php

/**
 * @todo  Add some kind of switch to determine what kind of data
 * to output in latest_subsite_posts_box. Perhaps some do not want
 * excerpts fex.
 */


namespace SKChildTheme;


/**
 * Represents the campaign block in the advanced template.
 *
 * Prepares the data for use and returns it.
 *
 * @since 1.0.0
 * 
 * @return object
 */
function get_campaign_block() {
  $data = new \stdClass;

  $data->title = get_sub_field( 'campaign_title' );
  $data->categories = get_sub_field( 'campaign_category' );
  $data->limit = 3;

  $args = array(
    'numberposts' => empty( $data->limit ) ? 5 : $data->limit
  ); 

  if ( ! empty( $data->categories ) ) {
    $args['cat'] = implode( ',', $data->categories );
  }

  $data->posts = get_posts( $args );

  foreach( $data->posts as $key => $post ) {
    setup_postdata( $post );

    $excerpt_length = has_post_thumbnail( $post->ID ) ? 65 : 100;
    $data->posts[ $key ]->excerpt = get_excerpt_max_charlength( $excerpt_length );
  }

  return $data; 
}

/**
 * Represents the campaign block in the advanced template.
 *
 * @since 1.0.0
 * 
 * @return null
 */
function the_campaign_block() {
  $campaign_posts = get_campaign_block();
   
        
  

  if ( empty( $campaign_posts->posts ) ) : ?>
    <?php return; ?>
  <?php endif; ?>
  
  <div class="sk-main sk-campaign-block">
    <ul class="sk-campaign-content-list">  
    <?php if ( ! empty( $campaign_posts->title ) ) : ?>
      <header>
        <h2><?php echo $campaign_posts->title; ?></h2>
      </header>
    <?php endif; ?>
  <li class="sk-campaign-slider">
  
  <div class="owl-carousel 
        <?php if(count(get_field('campaign_content')) == 1) : 
          echo 'single'; 
        else : 
          echo 'multiple'; 
      endif; ?>">

    <?php if( have_rows('campaign_content') ): ?>

   
      <?php while( have_rows('campaign_content') ): the_row(); 

        // vars
        $campaign_image = get_sub_field( 'campaign_image' ); 
        
        ?>

        <div class="item">
          
            <img src="<?php echo $campaign_image['url']; ?>" alt="<?php echo $campaign_image['alt'] ?>" />
        
          <?php if ( get_sub_field( 'campaign_image_url' ) ) : ?>
            <a href="<?php the_sub_field( 'campaign_image_url' ); ?>">
          <?php endif; ?>

            <div class="wrap">
                <div class="text"><?php the_sub_field( 'campaign_image_text' ); ?></div>
            </div>
        <?php if ( get_sub_field( 'campaign_image_url' ) ) : ?>
          </a>
       <?php endif; ?>
       
        </div>
          



      <?php endwhile; ?>

      

    <?php endif; ?>




  </div>
  </li>

  <li class="sk-campaign-list">
         <?php if ( ! empty( $campaign_posts->title ) ) : ?>
      <header>
        <h2><?php echo $campaign_posts->title; ?></h2>
      </header>
     <?php endif; ?>
    <ul class="sk-campaign-grid-list">
      
      <?php foreach ( $campaign_posts->posts as $post ) : ?>
        <li>
         
            <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
              <figure>
                <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
              </figure>
            <?php endif; ?>
            
            <article<?php if ( has_post_thumbnail( $post->ID ) ) : ?> class="sk-narrow"<?php endif; ?>>
              <header> 

                <a class="of-dark-link" href="<?php echo get_permalink( $post->ID ); ?>">
                <h5><?php echo $post->post_title; ?></h5>
                </a>
              </header>
              
              <?php if ( ! empty( $post->excerpt ) ) : ?>
                <p><?php echo $post->excerpt; ?>...</p>
              <?php endif; ?>

              <ul class="of-meta-line">
                <li><a href="<?php echo get_permalink( $post->ID ); ?>">
                  Läs mer och titta här
                   </a>
                </li>
              </ul>
            </article>
          
        </li>
      <?php endforeach; ?>      
    </ul>
  </li>
  </ul>
  </div>
  <?php
}


/**
 * Gets the latests subsite posts from fiven global categories
 *
 * @since  1.0.0
 * 
 * @param  object $box
 * @return array
 */
function get_latest_subsite_posts_box( $box ) {

  global $post, $switched;
  $post_tmp = $post;

  $options = get_option( 'sk_use_global_categories', array() );
  $attached_post_types = isset( $options['post_types'] ) && count( $options['post_types'] ) > 0 ? array_keys( $options['post_types'] ) : array(); 

  // Create a new stdclass to be used as wrapper
  $data = new \stdClass;
  $data->title = get_field( 'latest_subsite_posts_title', $box->post->ID );
  $data->categories = array();
  $data->posts = array();
  $categories = get_field( 'latest_subsite_posts_categories', $box->post->ID );
  
  // Loop and setup categories to fetch.
  // Must use slug since categories may
  // have differnt ids on subsites.
  if( count( $categories ) > 0 ) {

    foreach( $categories as $category ) {
      $term = get_term( $category, 'sitewidecats' );
      if( isset( $term ) && is_object( $term ) ) $data->categories []= $term->slug;
    }

  }

  // Set the nr of posts to show
  $data->limit = get_field( 'latest_subsite_posts_max_count', $box->post->ID );

  if( empty( $attached_post_types ) ) return $data;

  // Setup query args
  $args = array(
    'numberposts' => empty( $data->limit ) ? 5 : $data->limit,
    'post_type' => $attached_post_types,
    'tax_query' => array(
      array(
        'taxonomy' => 'sitewidecats',
        'terms' => $data->categories,
        'field' => 'slug'
      )
    )
    
  );

  // Get all subsites and loop them
  $sites = wp_get_sites();
  $total_posts = array();

  if( count( $sites ) > 0 ) {

    foreach( $sites as $site ) {

      switch_to_blog( $site['blog_id'] );

      $options = get_option('sk_use_global_categories', array());
      $use_global_categories = isset( $options['use'] ) && $options['use'] == 'on' ? 'on' : 'off';

      // No need to proceed if the subsite do not use global categories
      if( $use_global_categories == 'on' ) {
        
        $posts = get_posts( $args );

        // Loop posts and set certain values
        foreach( $posts as $key => $post ) {
          
          setup_postdata( $post );

          $excerpt_length = has_post_thumbnail( $post->ID ) ? 65 : 100;
          $posts[ $key ]->excerpt = get_excerpt_max_charlength( $excerpt_length );
          $posts[ $key ]->link = get_permalink( $post->ID );
          $posts[ $key ]->blog_id = $site['blog_id'];

          $data->posts []= $posts[ $key ];

        }

      }

      restore_current_blog();

    }

  }

  if( ! empty( $data->posts )){
    usort( $data->posts, 'SKChildTheme\sort_posts_by_date' );
    $data->posts = array_slice( $data->posts, 0, $data->limit );
  }

   wp_reset_postdata();

  return $data;

}



function sort_posts_by_date( $a, $b ) {
  if( $a->post_date == $b->post_date ) {
    return 0;
  }
  return( $a->post_date > $b->post_date ) ? -1 : +1;
}


/**
 * The HTML output for the latest subsite posts box
 *
 * @since  1.0.0
 * 
 * @param  object $box
 */
function the_latest_subsite_posts_box( $box ) {
  $latest_posts = get_latest_subsite_posts_box( $box );

  $link = get_field( 'latest_subsite_posts_title_link', $box->post->ID );
  $img_data = get_field( 'latest_subsite_posts_image', $box->post->ID );
  $image = wp_get_attachment_image_src( $img_data['ID'], 'full' );
  //\util::debug( $image );
  
  if ( empty( $latest_posts->posts ) ) : ?>
    <?php return; ?>
  <?php endif; ?>
  <section>
  <div class="box-image">
  <?php if(!empty($image)) : ?>
    <img src="<?php echo $image[0] ?>" />
  <?php endif; ?>
  </div>
    <header>
      <h5>
        <span>
          <?php if( empty( $link ) ) : ?>
            <?php echo $latest_posts->title; ?>
          <?php else : ?>
            <a href="<?php echo $link; ?>" title="<?php echo $link; ?>"><?php echo $latest_posts->title; ?></a>
          <?php endif; ?>
        </span>
      </h5>
    </header>

    <div class="box-list">
      <ul class="of-grid-list of-widget">
        <?php 
          foreach ( $latest_posts->posts as $post ) : 

            switch ( get_post_type( $post ) ) {
              case 'tribe_events':
                $label = __('Kalenderhändelse', 'sk');
                $icon = 'glyphicon glyphicon-calendar';
                break;
              
              case 'post':
                $label = __('Blogginlägg', 'sk');
                $icon = 'glyphicon glyphicon-user';
                if( is_main_site( $post->blog_id ) ){
                  $label = __('Reportage', 'sk');
                  $icon = 'glyphicon glyphicon-pencil';
                }
                break;
              
              default:
                $label = '';
                break;
            }
        ?>
          <li>
            <a class="of-dark-link" href="<?php echo $post->link; ?>"><div class="icon-circle-sm"><span title="<?php echo $label; ?>" class="<?php echo $icon; ?>"></span></div>
              <?php echo $post->post_title; ?>
            </a>
          </li>
        <?php endforeach; ?>      
        </ul>
    </div>
  </section>
  <?php
}


/**
 * Shows the calendar box
 *
 * @since 1.0.0 
 * 
 * @param  string $box
 * 
 * @return null
 */
function the_calendar_box( $box = '' ) {

  require_once( get_stylesheet_directory() . '/lib/class-sk-calendar.php' );

  $calendar = new \Class_SK_Calendar();
  echo $calendar->show();

}


/**
 * Represents the boxes block in the advanced template.
 *
 * @since 1.0.0
 * 
 * @return null
 */
function the_boxes_block( $field_name = 'boxes_content', $acf_function = 'get_sub_field', $sidebar = false ) {

  global $post;
  global $flexible_index;
  $boxes = get_boxes_block( $field_name, $acf_function );
  $title = get_sub_field( 'boxes_title' );
  $class = $sidebar !== false ? 'sk-boxes-sidebar' : 'sk-boxes';
  $width = get_sub_field( 'boxes_width');

  ?>
  <?php if ( ! empty( $boxes ) ) : ?>

    <div class="sk-main">
      <?php if ( ! empty( $title ) ) : ?>
        <header>
          <h2><?php echo $title; ?></h2>
        </header>
      <?php endif; ?>

      <ul class="<?php echo $class; ?>">

        <?php foreach( $boxes as $box ) : ?>

            <li class="sk-box-type-<?php echo $box->type->slug; ?> sk-box-width-<?php echo $width;?>">
              <?php if( $box->type->slug == 'lanklista' ) : ?>
                <?php the_links_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'bild' ) : ?>
                <?php the_image_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'text' ) : ?>
                <?php the_text_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'facebook-flode' ) : ?>
                <?php the_facebook_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'senaste-inlaggen' ) : ?>
                <?php the_latest_posts_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'dokument' ) : ?>
                <?php the_documents_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'kontaktkort' ) : ?>
                <?php the_contact_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'bild-och-lanklista' ) : ?>
                <?php the_image_and_textlist_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'senaste-inlaggen-subsajter' ) : ?>
                <?php the_latest_subsite_posts_box( $box ); ?>
              <?php elseif ( $box->type->slug == 'kalender' ) : ?>
                <?php the_calendar_box( $box ); ?>
              <?php endif; ?>
            </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif;
}


/**
 * Represents the image and textlist box in the boxes block.
 *
 * @since 1.0.0
 *
 * @param object $box Box object which contains the box post and box type term.
 * 
 * @return null
 */
function the_image_and_textlist_box( $box ) {
  global $post;
  global $flexible_index;
  
  $title = get_field( 'image_and_textlist_title', $box->post->ID );
  $image = get_field( 'image_and_textlist_image', $box->post->ID );
  
  
  ?>
  <ul class="sk-image-link-list">
    <li class="sk-image-link-image">
      <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt'] ?>" />
    </li>
    <li class="sk-image-link-linklist">
  <?php if ( ! empty( $title ) ) : ?>
    <header>
      <h4><?php echo $title; ?></h4>
    </header>
  <?php endif; ?>
 
   <ul class="sk-links-list">
    <?php while( the_flexible_field( 'image_and_textlist_links', $box->post->ID ) ) : ?>
      <?php if ( get_row_layout() == 'image_and_textlist_internal_link' ) : ?>
         <li>
          
            <a href="<?php the_sub_field( 'link' ); ?>">
              <?php the_sub_field( 'text' ); ?>
            </a>
        </li>
      <?php elseif ( get_row_layout() == 'image_and_textlist_external_link' ) : ?>
        <li>
         
            <a href="<?php the_sub_field( 'link' ); ?>"<?php if ( get_sub_field( 'new_window' ) ) : ?> target="_blank"<?php endif; ?>>
              <?php the_sub_field( 'text' ); ?>
              <i class="of-icon"><?php icon( 'external' ); ?></i>
            </a>
        </li>
      <?php endif; ?>
    <?php endwhile; ?>
  </ul>
</li>
</ul>
  <?php
}

function custom_breadcrumbs() {
 
  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = ''; // delimiter between crumbs &raquo;
  $home = 'Hem'; // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<li><span class="current-breadcrumb">'; // tag before the current crumb
  $after = '</span></li>'; // tag after the current crumb
 
  global $post;
  $homeLink = get_bloginfo('url');
 
  if (is_home() || is_front_page()) {
 
    if ($showOnHome == 1) echo '<div class="of-wrap breadcrumbs"><ul class="of-breadcrumbs"><li><a href="' . $homeLink . '">' . $home . '</a></li></ul>';
 
  } else {
 
    echo '<div class="of-wrap breadcrumbs"><ul class="of-breadcrumbs"><li><a href="' . $homeLink . '">' . $home . '</a></li> ' . $delimiter . ' ';
 
    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
      echo $before . __('Kategori', 'sk').' "' . single_cat_title('', false) . '"' . $after;
      //echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
 
    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;
 
    } elseif ( is_day() ) {
      echo ' <li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
      echo ' <li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a></li> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo ' <li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;
 
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo ' <li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li>';
        if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');

        if( strstr($cats, '>Reportage<') ){
          $cats = str_replace('/category/reportage', '', $cats);
          echo '<li>' . trim( $cats ) . '</li>';

        } elseif ($showCurrent == 0) {
          $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
          echo '<li>'.__('Kategori', 'sk'). ' "'. trim( $cats ) . '"</li>';
        }
        //echo '<li>' . trim( $cats ) . '</li>';
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo ' <li><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></li>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = ' <li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
      }
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
 
    } elseif ( is_tag() ) {
      echo $before . __('Etiketter', 'sk') . ' "' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Sida') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '</ul></div>';
 
  }
} // end custom_breadcrumbs()

