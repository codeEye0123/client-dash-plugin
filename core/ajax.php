<?php

/**
 * Class ClientDash_AJAX
 *
 * Adds all AJAX functionality to Client Dash
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.5
 */
class ClientDash_AJAX {

	/**
	 * Constructs the class.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Cycle through each method and add its ajax action
		foreach ( get_class_methods( 'ClientDash_AJAX' ) as $method ) {

			// Skip construct method
			if ( $method == '__construct' ) {
				continue;
			}

			add_action( "wp_ajax_cd_$method", array( $this, $method ) );
		}
	}

	/**
	 * Resets all of the roles settings to default.
	 *
	 * @since Client Dash 1.5
	 */
	public function reset_roles() {

		global $ClientDash;

		update_option( 'cd_content_sections_roles', $ClientDash->option_defaults['content_sections_roles'] );

		echo 'Roles successfully reset!';

		die();
	}

	/**
	 * Resets ALL Client Dash settings by deleting them.
	 *
	 * @since Client Dash 1.6
	 */
	public function reset_all_settings() {

		global $ClientDash;

		// Cycle through all option defaults and delete them
		foreach ( $ClientDash->option_defaults as $name => $value ) {
			delete_option( "cd_$name" );
		}

		// Remove the modified nav menu
		wp_delete_nav_menu( 'cd_admin_menu' );

		echo 'Settings successfully reset!';

		die();
	}

	/**
	 * Replaces wp_ajax_add_menu_item() (wp-admin/includes/ajax-actions.php:~1056).
	 *
	 * @since Client Dash 1.6
	 */
	public function add_menu_item() {

		global $ClientDash;

		// Security
		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( - 1 );
		}

		// Save the new items
		$item_ids = ClientDash_Core_Page_Settings_Tab_Menus::save_menu_items( 0, $_POST['menu-item'] );

		// Setup the new items
		$menu_items = array();
		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj = wp_setup_nav_menu_item( $menu_obj );
				$menu_items[] = $menu_obj;
			}
		}

		// Include the custom CD walker class
		include_once( $ClientDash->path . '/core/tabs/settings/menus/walkerclass.php' );

		// Output the newly populated nav menu
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after' => '',
				'before' => '',
				'link_after' => '',
				'link_before' => '',
				'walker' => new Walker_Nav_Menu_Edit_CD(),
			);
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}

		wp_die();
	}

	/**
	 * Creates a nav menu item and it's sub-menus. Used for initial creation of nav menus.
	 *
	 * @since Client Dash 1.6
	 */
	public function populate_nav_menu() {

		// TODO Prevent duplicate separators from being added

		// Get our POST data from AJAX
		$menu_item          = $_POST['menu_item'];
		$menu_item_position = $_POST['menu_item_position'];
		$menu_ID            = $_POST['menu_ID'];
		$role               = $_POST['role'];

		// Skip links
		// TODO Figure out how to better deal with this
		if ( $menu_item['menu_title'] == 'Links' ) {
			die();
		}

		// Get the role object (for capabilities)
		$role = get_role( $role );

		// The WP Core "Appearance" sub-menu item "Customize" requires a capability that doesn't seem to exist...
		// the capability of "customize". So I need to add it.
		if ( $role->name == 'administrator' ) {
			$role->capabilities['customize'] = true;
		}

		// Deal with "Plugins" having an extra space
		$menu_item['menu_title'] = trim( $menu_item['menu_title'] );

		// Pass over if current role doesn't have the capabilities
		$no_parent = false;
		if ( array_key_exists( $menu_item['capability'], $role->capabilities ) ) {
			$args = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $menu_item );

			// Predefined menu position
			$args['position'] = $menu_item_position;

			$ID = ClientDash_Core_Page_Settings_Tab_Menus::update_menu_item( $menu_ID, 0, $args );
		} else {
			$no_parent = true;
		}

		// If there are submenus, cycle through them
		if ( isset( $menu_item['submenus'] ) && ! empty( $menu_item['submenus'] ) ) {

			foreach ( $menu_item['submenus'] as $position => $submenu_item ) {

				// Pass over if current role doesn't have the capabilities
				if ( ! array_key_exists( $submenu_item['capability'], $role->capabilities ) ) {
					continue;
				}

				$args = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $submenu_item, $menu_item );

				// Make it a child IF it has a parent, otherwise make it top-level
				if ( ! $no_parent ) {
					$args['parent-id'] = $ID;
					$args['cd-submenu-parent'] = $submenu_item['parent_slug'];
				}

				// Predefined menu position
				$args['position'] = $position;

				ClientDash_Core_Page_Settings_Tab_Menus::update_menu_item( $menu_ID, 0, $args );
			}
		}

		// We're done!
		die();
	}
}

new ClientDash_AJAX();