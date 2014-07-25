<?php

/**
 * Class ClientDash_Page_Settings_Tab_General
 *
 * Adds the core content block for Settings -> General.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_General extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		$this->add_content_block(
			'Core Settings General',
			'settings',
			'General',
			array( $this, 'block_output' )
		);
	}

	/**
	 * The content for the content block.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {
		// Get options
		$active_widgets          = get_option( 'cd_active_widgets', null );
		$cd_remove_which_widgets = get_option( 'cd_remove_which_widgets' );
		$cd_hide_page_account    = get_option( 'cd_hide_page_account' );
		$cd_hide_page_reports    = get_option( 'cd_hide_page_reports' );
		$cd_hide_page_help       = get_option( 'cd_hide_page_help' );
		?>

		<h3>Widget Settings</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_remove_which_widgets">Widgets to not Remove</label>
				</th>
				<td>
					<?php
					if ( ! empty( $active_widgets ) ) {
						foreach ( $active_widgets as $widget => $values ) {
							echo '<input type="checkbox" name="cd_remove_which_widgets[' . $widget . ']" id="cd_remove_which_widgets' . $widget . '" value="' . $widget . '" ' . ( isset( $cd_remove_which_widgets[ $widget ] ) ? 'checked' : '' ) . '/><label for="cd_remove_which_widgets' . $widget . '">' . $values['title'] . '</label><br/>';
						}
					} else {
						cd_error( 'Please visit the <a href="/wp-admin/index.php">dashboard</a> once for "Widgets to not Remove" settings to appear.' );
					}
					?>
				</td>
			</tr>
		</table>

		<h3>Page Settings</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_remove_which_widgets">Hide these default Client Dash pages</label>
				</th>
				<td>
					<input type="hidden" name="cd_hide_page_account" value="0"/>
					<input type="checkbox" name="cd_hide_page_account" id="cd_hide_page_account"
					       value="1" <?php checked( '1', $cd_hide_page_account ); ?> />
					<label for="cd_hide_page_account">Account</label><br/>

					<input type="hidden" name="cd_hide_page_reports" value="0"/>
					<input type="checkbox" name="cd_hide_page_reports" id="cd_hide_page_reports"
					       value="1" <?php checked( '1', $cd_hide_page_reports ); ?> />
					<label for="cd_hide_page_account">Reports</label><br/>

					<input type="hidden" name="cd_hide_page_help" value="0"/>
					<input type="checkbox" name="cd_hide_page_help" id="cd_hide_page_help"
					       value="1" <?php checked( '1', $cd_hide_page_help ); ?> />
					<label for="cd_hide_page_help">Help</label><br/>
				</td>
			</tr>
		</table>
	<?php
	}
}