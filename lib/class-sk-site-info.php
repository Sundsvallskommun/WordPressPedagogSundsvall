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
 */


class SK_Site_Info {
	
  public function __construct() {
    add_action('admin_footer', array( $this, 'add_option_field' ) );

    global $pagenow;
    if( $pagenow == 'site-info.php' ) {
      //util::debug( $_POST );
    }

	}

  /**
   * Register custom post types
   *
   * @since 1.0.0
   *
   * @return null
   */
	public function add_option_field() {
    
    global $pagenow;
    if( 'site-info.php' == $pagenow ) {
        ?><table><tr id="sk-site-info">
            <th scope="row">My own option</th>
            <td><input name="test" type="text"/></td>
        </tr></table>
        <script>jQuery(function($){
            $('.form-table tbody').append($('#sk-site-info'));
        });</script><?php
    }


	}

}