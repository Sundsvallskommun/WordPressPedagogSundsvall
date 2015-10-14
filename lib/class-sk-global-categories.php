<?php

namespace SKChildTheme;

/**
 * Custom post types.
 *
 * Register theme specific post types and taxonomies.
 *
 * @since 1.0.0
 *
 * @package sk-theme
 *
 */


class SK_Global_Categories {
	

  /**
   * Constructor function.
   * Initialize all actions and filters.
   *
   * @since  1.0.0
   * 
   */
  public function __construct() {

    $options = get_option( 'sk_use_global_categories', array() );
    $use_global_categories = isset( $options['use'] ) ? 'on' : 'off';

    // Only register if activated or is the main site
    if( get_current_blog_id() == 1 || $use_global_categories == 'on' ) {
      add_action('init', array( $this, 'register_taxonomy' ) );
    }
    
    
    // Only add these actions on the mainsite
    if( get_current_blog_id() == 1 ) {
      // Add actions related to global taxonomy
      add_action( 'create_term', array( $this, 'create_global_term' ), 1, 3 );
      add_action( 'edit_term', array( $this, 'edit_global_term' ), 1, 3 );
      add_action( 'delete_term', array( $this, 'delete_global_term' ), 10, 4 );
    }

    // Add a global categories setting to admin menu for admins
    if( is_admin() && current_user_can( 'manage_plugins' ) ) {
      add_action( 'admin_menu', array( $this, 'add_global_term_options' ) );
    }

    // Edit the admin menu
    add_action('admin_menu', array( $this, 'remove_menu_item' ), 999 );

    // Add action to listen to updating of the global categories option
    if( get_current_blog_id() != 1 ) {

      if( is_admin() ){
        if(isset($_POST['sk_hide_global_categories'])){
          $this->update_transient();
        }
      }

      //add_action( 'update_option_sk_use_global_categories', array( $this, 'update_transient' ), 10,  2 );  
      add_action( 'init', array( $this, 'sync_global_terms' ) );
    }

    // save some settings in option
    if( isset( $_POST['page_options'] ) && $_POST['page_options'] == 'sk_use_global_categories' ){
      $this->update_options();
    }

	}




  /*
   * -----------------------------------------------------------
   * GLOBAL CATEGORIES OPTIONS PAGE
   * -----------------------------------------------------------
   */
  

  public function update_options(){
    if( isset( $_POST['sk_hide_global_categories'] ) && !empty( $_POST['sk_hide_global_categories'] ) ){
      update_option( 'sk_hide_global_categories', absint( $_POST['sk_hide_global_categories'] ) );
    }else{
      delete_option( 'sk_hide_global_categories' );
    }

  }


  /**
   * Add a menu item to the settings section
   *
   * @since  1.0.0
   */
  public function add_global_term_options() {

    add_options_page( __( 'Inställningar för Globala kategorier', 'sk' ), __( 'Globala kategorier', 'sk' ), 'manage_options', 'functions', array( $this, 'global_term_options') );

  }


  /**
   * The output callback for the options page
   * 
   * @since 1.0.0
   * 
   */
  public function global_term_options() {

    $post_types = get_post_types();
    $options = get_option( 'sk_use_global_categories', array() );

    $use_global_categories = isset( $options['use'] ) ? 'on' : 'off';
    $checked_post_types = isset( $options['post_types'] ) ? $options['post_types'] : array();
    ?>

    <div class="wrap">
        <h2><?php _e( 'Inställningar för Globala kategorier', 'sk' ); ?></h2>
        <form method="post" action="options.php">
          <?php wp_nonce_field('update-options') ?>

          <table class="form-table">
            <tbody>
            <tr class="option-global-categories">
              <th><?php _e('Använd globala kategorier', 'sk'); ?></th>
              <td>
                <fieldset>
                  <legend class="screen-reader-text"><?php _e('Använd globala kategorier', 'sk'); ?></legend>
                  <label for="sk_use_global_categories">
                    <input type="checkbox" id="sk_use_global_categories" name="sk_use_global_categories[use]" <?php checked( $use_global_categories, 'on' ); ?> /> <?php _e('Aktivera', 'sk'); ?>
                  </label>
                  <p class="description"><?php _e('Aktivera om globala kategorier skall vara valbara på denna subsite'); ?></p>
                </fieldset>
              </td>
            </tr>
            <tr class="option-global-categories">
              <th><label for="hide_global_categories"><?php _e('Dölj en föräldrakategori och alla dess underkategorier från reportage', 'sk'); ?></label></th>

              <?php $terms = get_terms( 'sitewidecats', array( 'hide_empty' => false, 'parent' => 0 ) ); ?>
              <td>
                <fieldset>
                  <legend class="screen-reader-text"><?php _e('Dölj en föräldrakategori och alla dess underkategorier från reportage', 'sk'); ?></legend>
                  <select name="sk_hide_global_categories">
                    <option value=""><?php _e( 'Visa alla kategorier', 'sk' ); ?></option>
                  <?php foreach ($terms as $term) : ?>
                    <option value="<?php echo $term->term_id; ?>" <?php selected( $term->term_id, get_option('sk_hide_global_categories'), true ); ?>><?php echo $term->name; ?></option>
                  <?php endforeach; ?>
                  </select>
                  <p class="description"><?php _e( 'Välj en förälderkategori att dölja', 'sk' ); ?></p>
                </fieldset>
              </td>
            </tr>
            </tbody>
          </table>

          <h3><?php _e( 'Posttyper', 'sk' ); ?></h5>
          <table class="form-table">
            <tbody>

            <?php if( count( $post_types ) ) : ?>
              <?php foreach( $post_types as $post_type ) : ?>
                <?php 
                $checked = in_array( $post_type, array_keys( $checked_post_types ) ) ? ' checked ' : '';
                ?>
                <tr class="option-global-categories-post-types">
                  <th><?php echo $post_type; ?></th>
                  <td>
                    <fieldset>
                      <legend class="screen-reader-text"><?php echo $post_type; ?></legend>
                      <label for="">
                        <input type="checkbox" id="sk_global_categories_post_types" name="sk_use_global_categories[post_types][<?php echo $post_type; ?>]" <?php echo $checked; ?> /> <?php _e('Aktivera', 'sk'); ?>
                      </label>
                    </fieldset>
                  </td>
                </tr>

              <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
          </table>
          <p class="description"><?php _e('Kryssa i de posttyper som globala kategorier skall finnas tillgängliga på'); ?></p>

          <p><input type="submit" class="button button-primary" name="Submit" value="<?php _e('Spara'); ?>" /></p>
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="page_options" value="sk_use_global_categories" />
        </form>
      </div>
    <?php

  }





  /*
   * -----------------------------------------------------------
   * ACTIVATING AND DEACTIVATING GLOBAL CATEGORIES OPTION
   * -----------------------------------------------------------
   */


  /**
   * Set a transient to tell if we wish to sync
   * global categories
   *
   * @since  1.0.0
   * 
   * @param  string $old_value
   * @param  string $new_value
   * 
   */
  public function update_transient() {

    $use_global_categories = get_option( 'sk_use_global_categories' );
    $old_value = $use_global_categories['use'];

    if(isset( $_POST['sk_use_global_categories']['use'] ) && $_POST['sk_use_global_categories']['use'] == 'on' ){
      $new_value = 'on';  
    }else{
      $new_value = 'off';
    }

    // If we activate the global categories
    if( $new_value == 'on' ) {
      // Set a transient so we know that we wish to sync the global categories
      // later. This i because we cannot do this here. They do not exist yet.
      set_transient( 'sk_global_categories_on', true );
    }else{
      delete_transient( 'sk_global_categories_on' );
    }

  }


  /**
   * Get all sitewidecats terms from the main site
   * and sync them to the current sub site that is getting
   * global categories activated.
   *
   * @since  1.0.0
   */
  public function sync_global_terms() {

    // We wish to sync the global categories
    if( get_transient( 'sk_global_categories_on' ) ) {

      // Remove the transient
      //delete_transient( 'sk_global_categories_on' );

      // Get the terms from mainsite
      $terms = $this->get_all_global_terms();
      
      if( is_array( $terms ) ) {

        if( count( $terms ) > 0 ) {

          foreach( $terms as $term ) {

            $args = array(
              'slug' => $term->slug,
              'taxonomy' => $term->taxonomy,
              'description' => $term->description,
            );

            // This term has a parent term
            if( isset( $term->parent ) && $term->parent > 0 ) {

              $parent_term = $this->get_parent_term( $term->parent, $terms );

              if( $parent_term ) {

                $subsite_parent_term = get_term_by( 'slug', $parent_term->slug, $parent_term->taxonomy );

                if( !is_wp_error( $subsite_parent_term ) ) {

                  $args['parent'] = $subsite_parent_term->term_id;

                }

              }

            }

            // Remove actions to avoid inifinite loop
            remove_action( 'create_term', array( $this, 'create_global_term' ), 1, 3 );
            remove_action( 'edit_term', array( $this, 'edit_global_term' ), 1, 3 );

            // Add the term
            wp_insert_term( $term->name, $term->taxonomy, $args );

          }

        }

      }

    }

  }


  /**
   * Get the parent term from an array of terms
   * based on the parent terms id.
   *
   * @since  1.0.0
   * 
   * @param  integer $parent_id
   * @param  arrray $terms     array of term objects.
   * @return object|boolean            Will return either the term object or false
   */
  private function get_parent_term( $parent_id, $terms ) {

    if( count( $terms ) > 0 ) {

      foreach( $terms as $term ) {

        if( $parent_id == $term->term_id ) return $term;

      }

    }

    return false;

  }


  /**
   * Get all the terms from the sitewidecats taxonomy
   * from the mainsite.
   *
   * @since  1.0.0
   * 
   * @return array
   */
  private function get_all_global_terms() {
    global $switched;
    
    // Go to main site to get all the terms
    switch_to_blog( 1 );

      // Get all the terms, even the empty ones
      $terms = get_terms( 'sitewidecats', array( 'hide_empty' => false ) );

    restore_current_blog();
    // Going back to the sub site

    if( !is_array( $terms ) || count( $terms ) == 0 ) return false;

    return $terms;
  }


  /**
   * Removes the global categories link from the menu on all subsites but the main
   *
   * @since  1.0.0
   */
  public function remove_menu_item() {

    if( get_current_blog_id() != 1 ) {
      remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=sitewidecats' );
      remove_submenu_page( 'edit.php?post_type=page', 'edit-tags.php?taxonomy=sitewidecats&post_type=page' );
      remove_submenu_page( 'edit.php?post_type=tribe_events', 'edit-tags.php?taxonomy=sitewidecats&post_type=tribe_events' );
    }

  }


  /*
   * -----------------------------------------------------------
   * REGISTERING THE CUSTOM TAXONOMY FOR GLOBAL CATEGORIES
   * -----------------------------------------------------------
   */


  /**
   * Taxonomy sitewide
   *
   * @since 1.0.0
   * 
   */
  public function register_taxonomy(){
    
    global $switched;

    $options = get_option( 'sk_use_global_categories', array() );
    $attached_post_types = array();
    if( isset( $options['post_types'] ) && is_array( $options['post_types'] ) ) {
      $attached_post_types = array_keys( $options['post_types'] );  
    }

    // No need to do this if we do not have any post types to use with
    if( count( $attached_post_types ) > 0 ) {

      $labels = array(
        'name'              => __( 'Globala kategorier', 'sk' ),
        'singular_name'     => __( 'Global kategori', 'sk' ),
        'search_items'      => __( 'Sök global kategori', 'sk' ),
        'all_items'         => __( 'Alla globala kategorier', 'sk' ),
        'parent_item'       => __( 'Förälder, global kategori', 'sk' ),
        'parent_item_colon' => __( 'Kategoriförälder:', 'sk' ),
        'edit_item'         => __( 'Ändra global kategori', 'sk' ),
        'update_item'       => __( 'Uppdatera global kategori', 'sk' ),
        'add_new_item'      => __( 'Ny global kategori', 'sk' ),
        'new_item_name'     => __( 'Nytt kategorinamn', 'sk' ),
        'menu_name'         => __( 'Globala kategorier', 'sk' )
      );


      $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'query_var' => 'true',
        'rewrite' => array('slug' => 'sitewide-cats'),
        'show_admin_column' => 'true',
        'capabilities' => array(
          'manage_terms' => 'manage_options',
          'edit_terms' => 'manage_options',
          'delete_terms' => 'manage_options',
          'assign_terms' => 'edit_posts'
        )
      );

      // Loop all subsites and add the sitewide taxonomy
      register_taxonomy( 'sitewidecats', $attached_post_types, $args );

      $sites = wp_get_sites();

      if( count( $sites ) > 0 ) {

        foreach( $sites as $site ) {
          
          if( $site['blog_id'] != get_current_blog_id() ) {
            
            switch_to_blog( $site['blog_id'] );

            register_taxonomy( 'sitewidecats', $attached_post_types, $args );

            restore_current_blog();

          }

        }

      }

    }

  }


  

  /*
   * -----------------------------------------------------------
   * ACTION CALLBACKS FOR CREATING, UPDATING AND DELETING TERMS
   * -----------------------------------------------------------
   */


  /**
   * Create a new term in the global taxonomy on all subsites
   *
   * @since  1.0.0
   * 
   * @param  integer $term_id
   * @param  integer $tt_id
   * @param  string $taxonomy
   *
   */
  public function create_global_term( $term_id, $tt_id, $taxonomy ) {

    global $switched, $current_site;

    // Only allow doing this on the main site
    if( $current_site->id == 1 ) {

      // Only do this on global taxonomy
      if( isset( $taxonomy ) && $taxonomy == 'sitewidecats' ) {

        // The current term added on mainsite
        $term = get_term( $term_id, $taxonomy );

        // If this term has a parent, we need to load that too
        if( isset( $term->parent ) && $term->parent > 0 ) {
          $mainsite_parent_term = get_term( $term->parent, $taxonomy );
        }

        // Get all site in the network
        $sites = wp_get_sites();

        if( count( $sites ) > 0 ) {

          // Loop all blogs to create the term
          foreach( $sites as $site ) {
            
            // No need to do this on main site. Already updated.
            if( $site['blog_id'] != 1 ) {
              
              /* ------- Blog specific code ----------*/
              switch_to_blog( $site['blog_id'] );

                // Is there a parent term for the current term to create?
                if( isset( $mainsite_parent_term ) ) {

                  // Get the subsites parent term based on the term name
                  $subsite_parent_term = get_term_by( 'slug', $mainsite_parent_term->slug, $taxonomy );

                  remove_action( 'create_term', array( $this, 'create_global_term' ), 1, 3 );
                  remove_action( 'edit_term', array( $this, 'edit_global_term' ), 1, 3 );
                  $new_term_array = wp_insert_term( $term->name, $taxonomy, array( 'parent' => $subsite_parent_term->term_id ) );

                } else {

                  // Add the new term
                  wp_insert_term( $term->name, $taxonomy );

                }
              
              
              restore_current_blog();
              /* ------- End Blog specific code ----------*/

            }

          }

        }

      }

    }

  }


  /**
   * Edit a term in the global taxonomy
   *
   * @since  1.0.0
   * 
   * @param  integer $term_id
   * @param  integer $tt_id
   * @param  string $taxonomy
   */
  public function edit_global_term( $term_id, $tt_id, $taxonomy ) {

    global $switched, $current_site;

    // Only allow doing this on the main site
    if( $current_site->id == 1 ) {

      // Only do this on global taxonomy
      if( $taxonomy == 'sitewidecats' ) {

        // Get all blog sites
        $sites = wp_get_sites();

        if( count( $sites ) > 0 ) {

          $main_site_term_before_cache_clean = get_term( $term_id, $taxonomy );
          // Clean the cache to get the new term data
          clean_term_cache( $term_id, $taxonomy, true );
          $main_site_term_after_cache_clean = get_term( $term_id, $taxonomy );

          // If this term has a parent, we need to load that too
          if( isset( $main_site_term_after_cache_clean->parent ) && $main_site_term_after_cache_clean->parent > 0 ) {
            $mainsite_parent_term = get_term( $main_site_term_after_cache_clean->parent, $taxonomy );
          }

          // Loop the sites
          foreach( $sites as $site ) {

            // No need to do this on main site. Already updated.
            if( $site['blog_id'] != 1 ) {
              
              /* ------- Blog specific code ----------*/
              switch_to_blog( $site['blog_id'] );

              // Load the subsite term
              $term = get_term_by( 'slug', $main_site_term_before_cache_clean->slug, $taxonomy );

              // Check that this is an actual term object
              if( isset( $term ) && is_object( $term ) ) {
                
                // These are the updateable args
                $args = array(
                  'name' => $main_site_term_after_cache_clean->name,
                  'slug' => $main_site_term_after_cache_clean->slug,
                  'description' => $main_site_term_after_cache_clean->description,
                );

                if( isset( $mainsite_parent_term ) && !is_wp_error( $mainsite_parent_term ) ) {

                  // Get the subsites parent term based on the term name
                  $subsite_parent_term = get_term_by( 'slug', $mainsite_parent_term->slug, $taxonomy );

                  if( !is_wp_error( $subsite_parent_term ) ) {

                    $args['parent'] = $subsite_parent_term->term_id;

                  }

                } else {

                  // Orphan
                  $args['parent'] = 0;

                }

                // Remove the edit term action before updating the term to avoid infinite loop
                remove_action( 'create_term', array( $this, 'create_global_term' ), 1, 3 );
                remove_action( 'edit_term', array( $this, 'edit_global_term' ), 1, 3 );
                wp_update_term( $term->term_id, $taxonomy, $args );
              
              }

              restore_current_blog();
              /* ------- End Blog specific code ----------*/

            }

          }

        }

      }

    }

  }

  /**
   * Delete a term in the global taxonomy from all subsites.
   *
   * @since  1.0.0
   * 
   * @param  integer $term
   * @param  integer $tt_id
   * @param  string $taxonomy
   * @param  object $deleted_term
   */
  public function delete_global_term( $term, $tt_id, $taxonomy, $deleted_term ) {

    global $switched, $current_site;

    $args = array(
      'hide_empty' => 0
    );

    // Only allow doing this on the main site
    if( $current_site->id == 1 ) {

      // Only do this on global taxonomy
      if( $taxonomy == 'sitewidecats' ) {

        $global_slugs = array();
        $global_terms = get_terms( $taxonomy, $args ) ;
        foreach( $global_terms as $global_term ){
          $global_slugs[] = $global_term->slug;
        }
  
        // Get all blog sites
        $sites = wp_get_sites();

        if( count( $sites ) > 0 ) {

          foreach( $sites as $site ) {
            

            // No need to do this on main site. Already updated.
            if( $site['blog_id'] != 1 ) {
              
              /* ------- Blog specific code ----------*/
              switch_to_blog( $site['blog_id'] );
              
              $sub_terms = get_terms( $taxonomy, $args );
              $sub_slugs = array();
              foreach( $sub_terms as $sub_term ){
                $sub_slugs[] = $sub_term->slug;
              }

              $remove_terms = array_diff( $sub_slugs, $global_slugs );
              foreach ($remove_terms as $remove_term ) {
                $term = get_term_by( 'slug', $remove_term, $taxonomy );

                remove_action( 'delete_term', array( $this, 'delete_global_term' ), 10, 4 );
                wp_delete_term( $term->term_id, $taxonomy );
                add_action( 'delete_term', array( $this, 'delete_global_term' ), 10, 4 );
              }


              restore_current_blog();
              /* ------- End Blog specific code ----------*/

            }

          }

        }

      }

    }

  }

}