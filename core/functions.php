<?php

// TODO Document

/**
 * Class ClientDash_Functions
 *
 * The main, extensible class for all other classes within Client Dash.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.5
 */
abstract class ClientDash_Functions {

	/**
	 * Outputs the default Client Dash toggle switch (on|off).
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $name The name of the input.
	 * @param string|int $value The value for the input to output.
	 * @param string|int $current_val The current value of whatever the input has output.
	 * @param bool $horizontal Optional. Whether the switch is vertical or horizontal.
	 * @param bool $echo Optional. Whether to echo the HTML or just return it.
	 * @param bool $invert Optional. Whether to invert the relationship between the value and on|off.
	 * @param bool|array $atts Optional. Additional attributes for the item.
	 *
	 * @return string HTML of toggle switch.
	 */
	public function toggle_switch( $name, $value, $current_val, $horizontal = false, $echo = true, $invert = false, $atts = false ) {

		// TODO Use this function across the entire plugin

		// Setup orientation
		if ( $horizontal ) {
			$horizontal = 'horizontal';
		}

		// If the current value matches the input value, it's not disabled
		// (if $invert is true, reverse the output)
		$disabled = $invert ? false : true;
		if ( $value == $current_val ) {
			$disabled = $invert ? true : false;
		}

		// The HTML output
		$html = '<span class="cd-toggle-switch ' . ( $disabled ? 'off' : 'on' ) . " $horizontal\"";
		if ( $atts ) {
			foreach ( $atts as $att => $att_val ) {
				$html .= " $att='$att_val' ";
			}
		}
		$html .= '>';
		$html .= "<input type='hidden' id='$name' name='$name' value='$value' " . ( $disabled && ! $invert || $invert && ! $disabled ? 'disabled' : '') . '/>';
		$html .= '</span>';

		// Echo by default, return if told so
		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}
	/**
	 * Outputs a helpful tip that can be closed.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param string $content The content of the tip.
	 * @param string $position The position of the tip. (left or right)
	 * @param string $classes A string containing all the classes to add to the tip.
	 *
	 * @return string The tip.
	 */
	public function pointer( $content, $position = 'left', $classes = null ) {

		return "<span class='$classes' data-cd-pointer-position='$position'>$content<span class='cd-tip-close'>X</span></span>";
	}

	/**
	 * Outputs the page title with Client Dash standards.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $page The page we're on. Default 'account'.
	 */
	public function the_page_title( $page = 'account' ) {

		global $ClientDash;

		// Get the current dashicon
		$dashicon = get_option( 'cd_dashicon_' . $page, $ClientDash->option_defaults[ 'dashicon_' . $page ] );

		// If Webmaster, get name
		if ( $page == 'webmaster' ) {
			$page = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );
		}

		echo '<h2 class="cd-title"><span class="dashicons ' . $dashicon . ' cd-icon"></span><span class="cd-title-text">' . ucwords( $page ) . '</span></h2>';
	}

	/**
	 * The main function for building the CD pages.
	 *
	 * @since Client Dash 1.0
	 */
	public function create_tab_page() {

		global $ClientDash;

		// Get the page for building url
		$current_page = str_replace( 'cd_', '', $_GET['page'] );

		// If a tab is open, get it
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		} else {
			$active_tab = null;
		}

		// If a section is open, get it
		if ( isset( $_GET['section'] ) ) {
			$active_section = $_GET['section'];
		} else {
			$active_section = null;
		}

		// If no content on this page, show error and bail
		if ( empty( $ClientDash->content_sections[ $current_page ] ) ) {
			$this->error_nag( 'This page has no content' );

			return;
		}
		// Cycle through all tabs and output the menu
		echo '<h2 class="nav-tab-wrapper">';
		$i = 0;
		foreach ( $ClientDash->content_sections[ $current_page ] as $tab_ID => $props ) {
			$i ++;

			// If active tab, set class
			if ( $active_tab == $tab_ID || ! $active_tab && $i == 1 ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}

			// Output the tab menu item
			echo '<a href="?page=cd_' . $current_page . '&tab=' . $tab_ID . '" class="nav-tab ' . $active . '">' . $props['name'] . '</a>';
		}
		echo '</h2>';

		// If no active tab was set, take the first one
		if ( ! $active_tab ) {
			reset( $ClientDash->content_sections[ $current_page ] );
			$active_tab = key( $ClientDash->content_sections[ $current_page ] );
		}

		// Cycle through all sections and output the menu
		// Skip if total is only 1
		$total = count( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
		if ( $total > 1 ) {
			echo '<ul class="subsubsub cd-sections-menu">';

			$i = 0;
			foreach ( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] as $section_ID => $props ) {
				$i ++;

				echo '<li>';

				// If active section, set class
				if ( $active_section == $section_ID || ! $active_section && $i == 1 ) {
					$active = 'current';
				} else {
					$active = '';
				}

				// Add on a pipe if not the last item
				$output_text = $props['name'] . ( $i < $total ? ' |' : '' );

				// Output the section menu item
				echo "<a href=\"?page=cd_$current_page&tab=$active_tab&section=$section_ID\" class=\"$active\">$output_text</a>";

				echo '</li>';
			}
			echo '</ul>';
		}

		// If no active section was set, take the first one
		if ( ! $active_section ) {
			reset( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
			$active_section = key( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
		}

		// Get our current section
		$section_output = $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'][ $active_section ];

		// This calls the dynamic class and dynamic callback function
		echo '<div class="cd-content-section">';

		// Only call the tab if it exists
		if ( method_exists( $section_output['callback'][0], $section_output['callback'][1] ) ) {
			call_user_func( array( $section_output['callback'][0], $section_output['callback'][1] ) );
		} else {

			// Let the user know the tab doesn't exist
			$this->error_nag( 'This tab doesn\'t seem to exist! Sorry about that.' );

			// Also need to remove the submit button if on settings pages
			if ( $_GET['page'] == 'cd_settings' ) {
				add_filter( 'cd_submit', '__return_false' );
			}
		}

		echo '</div>';
	}

	/**
	 * Creates content sections.
	 *
	 * This function creates a content section for Client Dash. It can be set to
	 * go into a specific tab in a specific tab.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param array $content_section All of the arguments for the function.
	 */
	public function add_content_section( $content_section ) {

		global $ClientDash;

		if ( ! isset( $content_section['priority'] ) ) {
			$content_section['priority'] = 10;
		}

		// Generate the content section ID
		$ID = $this->translate_name_to_id( $content_section['name'] );

		// Fix up the tab name (to allow spaces and such)
		$tab_ID = $this->translate_name_to_id( $content_section['tab'] );

		// Fix up the page name (to allow spaces and such)
		$page = $this->translate_name_to_id( $content_section['page'] );

		// Add to the array
		$ClientDash->content_sections[ $page ][ $tab_ID ]['name'] = $content_section['tab'];

		$ClientDash->content_sections[ $page ][ $tab_ID ]['content-sections'][ $ID ] = array(
			'name'     => $content_section['name'],
			'callback' => $content_section['callback'],
			'priority' => $content_section['priority']
		);

		// Also add for the unmodified version
		$ClientDash->content_sections_unmodified[ $page ][ $tab_ID ]['name'] = $content_section['tab'];

		$ClientDash->content_sections_unmodified[ $page ][ $tab_ID ]['content-sections'][ $ID ] = array(
			'name'     => $content_section['name'],
			'callback' => $content_section['callback'],
			'priority' => $content_section['priority']
		);
	}

	/**
	 * Adds a widget to be available under Settings -> Widgets.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param array $widget The new widget to add.
	 */
	public function add_widget( $widget ) {

		global $ClientDash;

		$widgets = array(
			'ID'            => 'cd_' . $widget['ID'],
			'title'         => $widget['title'],
			'callback'      => $widget['callback'],
			'edit_callback' => $widget['edit_callback'],
			'description'   => $widget['description'],
			'cd_core'       => true,
			'cd_page'       => $widget['cd_page']
		);

		// Make sure something is there!
		if ( empty( $ClientDash->widgets ) ) {
			$ClientDash->widgets = array();
		}
		array_push( $ClientDash->widgets, $widgets );
	}

	/**
	 * Displays widgets for Settings -> Widgets.
	 *
	 * Loops through all widgets supplied and outputs them accordingly. This is
	 * used for the Settings -> Widgets page for both the "Available Dashboard Widgets"
	 * and "Dashboard" sections.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param array $widgets All widgets to display.
	 * @param bool $disabled Whether or not the inputs should be disabled.
	 * @param bool $draggable Whether or not these should be draggable.
	 */
	public function widget_loop( $widgets, $disabled = false, $draggable = false ) {

		$i = - 1;
		foreach ( $widgets as $key => $widget ) {
			$i ++;

			if ( ! isset( $widget['ID'] ) ) {
				if ( isset( $widget['cd_core'] ) ) {
					$widget['ID'] = $this->translate_name_to_id( $widget['title'] );
				} else {
					$widget['ID'] = $key;
				}
			}
			?>
			<li class="cd-dash-widget<?php echo $draggable ? ' ui-draggable' : '';
			echo isset( $widget['deactivated'] ) ? ' deactivated' : ''; ?>">
				<h4 class="cd-dash-widget-title">
					<?php echo $widget['title']; ?>
					<span class="cd-up-down"></span>
				</h4>

				<div class="cd-dash-widget-settings">
					<?php
					if ( isset( $widget['edit_callback'] ) && $widget['edit_callback'] ) {
						call_user_func( $widget['edit_callback'][0] . '::' . $widget['edit_callback'][1] );
					} else {
						echo 'No settings';
					}
					?>

					<div class="cd-dash-widget-footer">
						<a href="#" class="cd-dash-widget-delete"
						   onclick="cdWidgets.remove(this); return false;">
							Delete
						</a>
					</div>
				</div>

				<p class="cd-dash-widget-description">
					<?php echo isset( $widget['description'] ) ? $widget['description'] : ''; ?>
				</p>

				<?php
				foreach ( $widget as $name => $value ) {
					$disabled = $disabled ? 'disabled' : '';

					// This is account for the callback value, which is an array
					if ( is_array( $value ) ) {

						// Sometimes an object gets saved instead of the class name, this
						// accounts for that possibility
						if ( is_object( $value[0] ) ) {
							echo "<input type='hidden' name='cd_widgets[$i][$name][0]' value='" . get_class( $value[0] ) . "' $disabled />";
							echo "<input type='hidden' name='cd_widgets[$i][is_object]' value='1' $disabled/>";
						} else {
							// If the plugin is deactivated, we will get an incomplete class error
							if ( ! is_object( $value[0] ) && gettype( $value[0] ) == 'object' ) {
								continue;
							}

							echo "<input type='hidden' name='cd_widgets[$i][$name][0]' value='$value[0]' $disabled />";
						}

						echo "<input type='hidden' name='cd_widgets[$i][$name][1]' value='$value[1]' $disabled />";
					} else {
						echo "<input type='hidden' name='cd_widgets[$i][$name]' value='$value' $disabled />";
					}
				}
				?>
			</li>
		<?php
		}
	}

	/**
	 * Resets all Client Dash settings.
	 *
	 * @since Client Dash 1.5.4
	 *
	 * @param bool $force Whether or not to overrite existing values.
	 */
	public function reset_settings( $force = false ) {

		global $ClientDash;

		foreach ( $ClientDash->option_defaults as $name => $value ) {
			$existing_option = get_option( "cd_$name" );

			if ( $force ) {
				update_option( "cd_$name", $value );
			} elseif ( empty( $existing_option ) ) {
				update_option( "cd_$name", $value );
			}
		}
	}

	/**
	 * Strips out spaces and dashes and replaces them with underscores. Also
	 * translates to lowercase.
	 *
	 * @since Client Dash 1.3
	 *
	 * @param string $name The name to be translated.
	 *
	 * @return string Translated ID.
	 */
	public function translate_name_to_id( $name ) {

		return strtolower( str_replace( array( ' ', '-' ), '_', $name ) );
	}

	/**
	 * Replaces underscores with spaces and capitalizes words.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The ID to be translated.
	 *
	 * @return string Translated name.
	 */
	public function translate_id_to_name( $ID ) {

		return ucwords( str_replace( '_', ' ', $ID ) );
	}

	/**
	 * Gets the current color scheme.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param $which_color
	 *
	 * @return array Current color scheme
	 */
	public function get_color_scheme( $which_color = null ) {

		global $ClientDash;

		$current_color = get_user_option( 'admin_color' );
		$colors        = $ClientDash->admin_colors[ $current_color ];

		$output = array(
			'primary'      => $colors->colors[1],
			'primary-dark' => $colors->colors[0],
			'secondary'    => $colors->colors[2],
			'tertiary'     => $colors->colors[3]
		);

		if ( ! $which_color ) {
			return $output;
		} elseif ( $which_color == 'primary' ) {
			return $output['primary'];
		} elseif ( $which_color == 'primary-dark' ) {
			return $output['primary-dark'];
		} elseif ( $which_color == 'secondary' ) {
			return $output['secondary'];
		} elseif ( $which_color == 'tertiary' ) {
			return $output['tertiary'];
		} else {
			return false;
		}
	}

	/**
	 * Gets the size of a directory on the server.
	 *
	 * @since ClientDash 1.1
	 *
	 * @param $path
	 *
	 * @return mixed
	 */
	public function get_dir_size( $path ) {

		$totalsize  = 0;
		$totalcount = 0;
		$dircount   = 0;
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				$nextpath = $path . '/' . $file;
				if ( $file != '.' && $file != '..' && ! is_link( $nextpath ) ) {
					if ( is_dir( $nextpath ) ) {
						$dircount ++;
						$result = $this->get_dir_size( $nextpath );
						$totalsize += $result['size'];
						$totalcount += $result['count'];
						$dircount += $result['dircount'];
					} elseif ( is_file( $nextpath ) ) {
						$totalsize += filesize( $nextpath );
						$totalcount ++;
					}
				}
			}
		}
		closedir( $handle );
		$total['size']     = $totalsize;
		$total['count']    = $totalcount;
		$total['dircount'] = $dircount;

		return $total;
	}

	/**
	 * Correctly formats the bytes size into a more readable size.
	 *
	 * @since Client Dash 1.1
	 *
	 * @param int $size Size in bytes
	 *
	 * @return string
	 */
	public function format_dir_size( $size ) {

		if ( $size < 1024 ) {
			return $size . " bytes";
		} else if ( $size < ( 1024 * 1024 ) ) {
			$size = round( $size / 1024, 1 );

			return $size . " KB";
		} else if ( $size < ( 1024 * 1024 * 1024 ) ) {
			$size = round( $size / ( 1024 * 1024 ), 1 );

			return $size . " MB";
		} else {
			$size = round( $size / ( 1024 * 1024 * 1024 ), 1 );

			return $size . " GB";
		}
	}

	/**
	 * This allows us to use array_key_exists recursively.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param string $needle The array key to search for.
	 * @param array $haystack The array to search in.
	 *
	 * @return bool The result.
	 */
	public function array_key_exists_r( $needle, $haystack ) {

		$result = array_key_exists( $needle, $haystack );
		if ( $result ) {
			return $result;
		}
		foreach ( $haystack as $v ) {
			if ( is_array( $v ) ) {
				$result = $this->array_key_exists_r( $needle, $v );
			}
			if ( $result ) {
				return $result;
			}
		}

		return $result;
	}

	/**
	 * Get's the current user's role.
	 *
	 * @since Client Dash 1.4
	 *
	 * @return mixed The role.
	 */
	public function get_user_role() {

		global $current_user;
		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );
		return $user_role;
	}

	/**
	 * Displays a WordPress error nag.
	 *
	 * The nag will only show if the current user has the capabilities that
	 * are defined by the second parameter. This defaults to allowing
	 * everybody to see the message.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param string $message The message to show.
	 * @param string $caps Optional. A WordPress recognized capability.
	 * @param bool $echo Optional. Whether to echo or return the error.
	 *
	 * @return string The error.
	 * is 'read'.
	 */
	public function error_nag( $message, $caps = 'read', $echo = true ) {

		if ( current_user_can( $caps ) ) {
			if ( $echo ) {
				echo "<div class='error inline cd-message'><p>$message</p></div>";
			} else {
				return "<div class='error inline cd-message'><p>$message</p></div>";
			}
		}
	}

	/**
	 * Displays a WordPress update nag.
	 *
	 * The nag will only show if the current user has the capabilities that
	 * are defined by the second parameter. This defaults to allowing
	 * everybody to see the message.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param string $message The message to show.
	 * @param string $caps . Optional. A WordPress recognized capability. Default
	 * is 'read'.
	 */
	public function update_nag( $message, $caps = 'read' ) {

		if ( current_user_can( $caps ) ) {
			echo "<div class='updated inline cd-message'><p>$message</p></div>";
		}
	}

	/**
	 * Returns the settings url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public function get_settings_url( $tab = null, $section = null ) {

		return get_admin_url() . 'options-general.php?page=cd_settings' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the account url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public function get_account_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_account' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the help url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public function get_help_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_help' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the reports url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public function get_reports_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_reports' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the webmaster url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public function get_webmaster_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_webmaster' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns 1.
	 *
	 * Built for use in turning the dashboard columns into one.
	 *
	 * @since Client Dash 1.5
	 */
	public function return_1() {

		return 1;
	}

	/**
	 * Used in widgets_init.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param mixed $matches Matches from a preg_replace_callback.
	 *
	 * @return string Upped count.
	 */
	public function replace_count( $matches ) {

		$n = intval( $matches[2] ) + 1;

		return $matches[1] . $n;
	}

	/**
	 * Allows an array of needles instead of just one.
	 *
	 * @since Client Dash 1.6
	 *
	 * @author Binyamin (stackoverflow)
	 *
	 * @param $haystack
	 * @param array $needles
	 * @param int $offset
	 *
	 * @return bool|mixed
	 */
	public static function strposa( $haystack, $needles = array(), $offset = 0 ) {
		$chr = array();
		foreach ( $needles as $needle ) {
			$res = strpos( $haystack, $needle, $offset );
			if ( $res !== false ) {
				$chr[ $needle ] = $res;
			}
		}
		if ( empty( $chr ) ) {
			return false;
		}

		return min( $chr );
	}
}