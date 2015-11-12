<?php
	/**
	 *
	 * @package  
	 * @todo  Remove Util class to separate file
	 * 
	 */

	namespace SKChildTheme;

	// include acf fields for blog settings on subsites so we dont need to add them manually to every single subsite
	if( !is_main_site() ) 
		require_once( locate_template( '/lib/includes/acf-fields-blogsettings-subsite.php' ) );

	require_once( locate_template( '/lib/class-sk-block-slider-special.php' ) );
	require_once( locate_template( '/lib/class-sk-sitemap.php' ) );
	

	/**
	 * General child theme settings.
	 *
	 * Controls settings like image sizes, what files that can be uploaded, etc.
	 *
	 * @since 1.0.0
	 *
	 * @package sk-theme-vuxenutbildning
	 */

	class SK_Init {
		
		public function __construct() {
			add_action( 'sk_default_box_types', array( $this, 'default_box_types' ) );
			add_filter( 'sk_default_option_sub_pages', array( $this, 'sub_option_pages' ) ); 
			add_action( 'init', array( $this, 'unregister_post_type' ),100 );
		}

		/**
		 * Remove post types from blog sites
		 * 
		 * @return none 
		 */
		public function unregister_post_type() {
			global $wp_post_types;

			if(is_main_site())
				return false;

			$remove_post_types = array( 'faq', 'boxes' );

			foreach ($remove_post_types as $pt  ) {
				if ( isset( $wp_post_types[ $pt ] ) ) {
	      	unset( $wp_post_types[ $pt ] );
				}
    	}
    
		}

		/**
		 * [sub_option_pages description]
		 * @param  [type] $option_sub_pages [description]
		 * @return [type]                   [description]
		 */
		public function sub_option_pages( $option_sub_pages ){

				if( is_main_site() ){

					$option_sub_pages[] = array(
						'page_title' 	=> 'Blogginställningar',
						'menu_title'	=> 'Blogginställningar',
						//'capability'	=> 'manage_options',
						'parent_slug'	=> 'general-settings'
					);

					return $option_sub_pages;
				}

				// remove option pages for subsites
				foreach( $option_sub_pages as $key => $option_page ){

					switch ( $option_page['page_title'] ) {
						case 'Tema':
							unset( $option_sub_pages[$key] );
						break;

						case 'Sidhuvud':
							unset( $option_sub_pages[$key]);
						break;

						case 'Sidfot':
							unset( $option_sub_pages[$key]);
						break;				
					}       	


				}
				
				

				//add new option page for subsite
				$option_sub_pages = array(
					array(
						'page_title' 	=> 'Blogginställningar',
						'menu_title'	=> 'Blogginställningar',
						//'capability'	=> 'manage_options',
						'parent_slug'	=> 'general-settings',
					)
				);
				return $option_sub_pages;
		}


		/**
		 * Filter default box types
		 * 
		 * @since 1.0.0
		 * 
		 * @param array $default_box_types
		 * @return array
		 */
		public function default_box_types( $default_box_types ) {

			$default_box_types []= 'Bild och Länklista';
			$default_box_types []= 'Senaste inläggen subsajter';
			$default_box_types []= 'Kalender';


			return $default_box_types;

		}

	}