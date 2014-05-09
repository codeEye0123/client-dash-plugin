<?php
/*
Plugin Name: Client Dash
Description: Addressing the Real Big needs for a client interface.
Version: 1.0
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Dashboard widgets
require_once('widgets/widgets.php');

// Create admin pages
require_once('admin/admin.php');

// Enhance the toolbar
require_once('toolbar/toolbar.php');

// Store admin color scheme for later
global $admin_colors; // only needed if colors must be available in classes
add_action('admin_init', function() {
  global $_wp_admin_css_colors;
  global $admin_colors; // only needed if colors must be available in classes
  $admin_colors = $_wp_admin_css_colors;
});
?>