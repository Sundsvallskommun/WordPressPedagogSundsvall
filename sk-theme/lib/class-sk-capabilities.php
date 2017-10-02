<?php

/**
 * Modifying capabilities
 *
 * @since   1.0.0
 *
 * @package sundsvall_se
 */
class SK_Capabilities {
	function __construct() {

		add_action( 'init', array( $this, 'run_once' ), 10 );

		add_action( 'after_switch_theme', array( $this, 'extend_editor_gf_capabilities' ) );

		add_action( 'switch_theme', array( $this, 'reset_editor_gf_capabilities' ) );

		add_action( 'after_switch_theme', array( $this, 'extend_author_gf_capabilities' ) );

		add_action( 'switch_theme', array( $this, 'reset_author_gf_capabilities' ) );

		add_action( 'after_switch_theme', array( $this, 'extend_contributor_gf_capabilities' ) );

		add_action( 'switch_theme', array( $this, 'reset_contributor_gf_capabilities' ) );
	}


	/**
	 * A run once script so we dont need to switch theme to add custom caps.
	 *
	 * @author Daniel Pihlström
	 *
	 * @return null
	 */
	public function run_once() {

		// To run once again, delete current option key and create a new one.
		// delete_option('sk_capabilities_{date}');

		if ( ! get_option( 'sk_capabilities_20161012' ) ) {

			// add caps
			$this->extend_editor_gf_capabilities();
			$this->extend_author_gf_capabilities();
			$this->extend_contributor_gf_capabilities();

			// remove cap for editor.
			$role = get_role( 'editor' );
			$role->remove_cap( 'gform_full_access' );

			update_option( 'sk_capabilities_20161012', '1' );
		}

	}


	/**
	 * Extend editor capabilities for gravityforms on theme activation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function extend_editor_gf_capabilities() {
		$capabilities = array(
			'gravityforms_edit_forms',
			'gravityforms_delete_forms',
			'gravityforms_create_form',
			'gravityforms_view_entries',
			'gravityforms_edit_entries',
			'gravityforms_delete_entries',
			'gravityforms_view_settings',
			'gravityforms_edit_settings',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes',
			'gravityforms_view_updates',
			'gravityforms_view_addons',
			'gravityforms_preview_forms'
		);

		$role = get_role( 'editor' );

		foreach ( $capabilities as $cap ) {
			$role->add_cap( $cap );
		}
	}

	/**
	 * Reset editor capabilities for gravityforms on theme deactivation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function reset_editor_gf_capabilities() {
		$capabilities = array(
			'gravityforms_edit_forms',
			'gravityforms_delete_forms',
			'gravityforms_create_form',
			'gravityforms_view_entries',
			'gravityforms_edit_entries',
			'gravityforms_delete_entries',
			'gravityforms_view_settings',
			'gravityforms_edit_settings',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes',
			'gravityforms_view_updates',
			'gravityforms_view_addons',
			'gravityforms_preview_forms'
		);

		$role = get_role( 'editor' );

		foreach ( $capabilities as $cap ) {
			$role->remove_cap( $cap );
		}
	}

	/**
	 * Extend author capabilities for gravityforms on theme activation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function extend_author_gf_capabilities() {
		$capabilities = array(
			'gravityforms_edit_forms',
			'gravityforms_delete_forms',
			'gravityforms_create_form',
			'gravityforms_view_entries',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes',
			'gravityforms_preview_forms'
		);

		$role = get_role( 'author' );

		foreach ( $capabilities as $cap ) {
			$role->add_cap( $cap );
		}
	}

	/**
	 * Reset author capabilities for gravityforms on theme deactivation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function reset_author_gf_capabilities() {
		$capabilities = array(
			'gravityforms_edit_forms',
			'gravityforms_delete_forms',
			'gravityforms_create_form',
			'gravityforms_view_entries',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes',
			'gravityforms_preview_forms'
		);

		$role = get_role( 'author' );

		foreach ( $capabilities as $cap ) {
			$role->remove_cap( $cap );
		}
	}

	/**
	 * Extend contributor capabilities for gravityforms on theme activation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function extend_contributor_gf_capabilities() {
		$capabilities = array(
			'gravityforms_view_entries',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes'
		);

		$role = get_role( 'contributor' );

		foreach ( $capabilities as $cap ) {
			$role->add_cap( $cap );
		}
	}

	/**
	 * Reset contributor capabilities for gravityforms on theme deactivation.
	 *
	 * @author Therese Persson
	 *
	 * @return null
	 */
	public function reset_contributor_gf_capabilities() {
		$capabilities = array(
			'gravityforms_view_entries',
			'gravityforms_export_entries',
			'gravityforms_view_entry_notes',
			'gravityforms_edit_entry_notes'
		);

		$role = get_role( 'contributor' );

		foreach ( $capabilities as $cap ) {
			$role->remove_cap( $cap );
		}
	}
}