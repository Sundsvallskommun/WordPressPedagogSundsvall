<?php
/**
 * Tweaking the adminstrator role to manage some of the super admin tools.
 *
 */
class SK_Custom_Capabilities {

	private $caps = array( 'create_sites', 'manage_network', 'manage_network_users', 'manage_sites' );
	
	public function __construct() {

		add_action( 'admin_bar_menu', array( $this, 'add_link_to_network_admin' ), 30 );
		add_action( 'network_admin_menu', array( $this, 'remove_network_menu_items' ), 10 );
		add_action( 'admin_menu', array( $this, 'remove_admin_menu_items' ), 999 );

		add_action( 'show_user_profile', array( $this, 'add_user_meta_field' ), 10 );
		add_action( 'edit_user_profile', array( $this, 'add_user_meta_field' ), 10 );

		add_action( 'personal_options_update', array( $this, 'save_user_meta' ), 10 );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta' ), 10 );

	}


	/**
	 * Adding link to network admin in admin toolbar.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  object $wp_admin_bar
	 */
	public function add_capabilities( $user_id ) {

		if( ! $user_id > 0 )
			return false;

		$user = new WP_User( $user_id );
		$caps = $this->caps;

		if(! empty( $caps ) ){
			foreach( $caps as $cap ) {
				$user->add_cap( $cap );		
			}
		}
	}


	/**
	 * Removing added capabilities.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 */
	public function remove_capabilities( $user_id ) {

		if( ! $user_id > 0 )
			return false;

		$user = new WP_User( $user_id );
		$caps = $this->caps;

		if(! empty( $caps ) ){
			foreach( $caps as $cap ) {
				$user->remove_cap( $cap );		
			}
		}

	}


	/**
	 * Adding link to network admin in admin toolbar.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  object $wp_admin_bar
	 */
	public function add_link_to_network_admin( $wp_admin_bar ) {

		if( current_user_can( 'manage_sites' ) && !is_super_admin() ){

			$args = array(
				'id' => 'network-admin',
				'parent' => '',
				'title' => ':: Nätverksadmin ::',    
				'href' => network_admin_url()
			);
			$wp_admin_bar->add_node( $args );
		}
	}

	/**
	 * Remove some menu items in network admin for administrator.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @return none
	 */
	public function remove_network_menu_items(){

		if( current_user_can( 'manage_sites' ) && !is_super_admin() ){
			remove_menu_page( 'settings.php' );
			remove_submenu_page( 'index.php', 'upgrade.php');

		}
	}


	/**
	 * Remove some menu items in admin for administrator.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @return none
	 */
	public function remove_admin_menu_items(){

		if( ! current_user_can( 'manage_network_plugins' ) ){
			remove_submenu_page( 'options-general.php', 'sso_general.php' );
			remove_menu_page( 'edit.php?post_type=acf-field-group' );
		}
	}

	/**
	 * User meta field html output
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  object $user
	 */
	public function add_user_meta_field( $user ){
		if(! is_super_admin() )
			return false;

		if(! in_array( 'administrator', $user->roles ) )
			return false;

		$extended_for_network = get_user_meta( $user->ID, 'extended_for_network', true );
    ?>
        <h3><?php _e( 'Utökad behörighet - nätverksadmin', 'sk' ); ?></h3>
        <table class="form-table">
          <tr>
            <th><label for="facebook_profile"><?php _e('Utökad behörighet', 'sk' ); ?></label></th>
            <td><label><input type="checkbox" value="1" <?php checked( 1, !empty( $extended_for_network ) ? $extended_for_network : false, true ); ?> name="extended_for_network"><?php _e('Ge användaren utökad behörighet för att hantera webbplatser i nätverket', 'sk' ); ?></label>
            </td>
          </tr>
        </table>
    <?php
	}

	/**
	 * Save user meta for user and add/remove the user capabilities.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  int $user_id
	 * 
	 * @return none
	 */
	public function save_user_meta( $user_id ){
		if(! is_super_admin() )
			return false;

		$value = isset( $_POST['extended_for_network'][0] ) ? $_POST['extended_for_network'][0] : '';
		update_user_meta( $user_id, 'extended_for_network', $value );

		// remove or add capabilities from this user
		if( intval( $value ) === 1 )
			$this->add_capabilities( $user_id );
		else
			$this->remove_capabilities( $user_id );

	}



}

// Initialize object
if( class_exists( 'SK_Custom_Capabilities' ) ){
	new SK_Custom_Capabilities();
}