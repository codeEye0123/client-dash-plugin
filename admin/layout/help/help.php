<?php
// Add the submenu page
function cd_help_menu() {
	add_submenu_page('index.php', 'Helpful Information', 'Help', 'edit_others_posts', 'help', 'cd_help_page');
}
add_action( 'admin_menu', 'cd_help_menu' );

// Construct menu tabulation and get the content
function cd_help_page() {
	if ( !current_user_can( 'edit_others_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap cd cd-help">

		<h2 class="wp-menu-image">Help</h2>
		<?php settings_errors();
		// Get the tab query parameter. If none set, stick with first tab
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'faq';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=help&tab=faq" class="nav-tab <?php echo $active_tab == 'faq' ? 'nav-tab-active' : ''; ?>">FAQs</a>
			<a href="?page=help&tab=tutorials" class="nav-tab <?php echo $active_tab == 'tutorials' ? 'nav-tab-active' : ''; ?>">Tutorials</a>
			<a href="?page=help&tab=tickets" class="nav-tab <?php echo $active_tab == 'tickets' ? 'nav-tab-active' : ''; ?>">Tickets</a>
			<a href="?page=help&tab=forum" class="nav-tab <?php echo $active_tab == 'forum' ? 'nav-tab-active' : ''; ?>">Forum</a>
			<a href="?page=help&tab=info" class="nav-tab <?php echo $active_tab == 'info' ? 'nav-tab-active' : ''; ?>">Site Info</a>
			<?php do_action( 'cd_help_tabs' ); ?>
		</h2>
		<?php cd_get_tab($active_tab, 'help');	?>
	</div><!--.wrap-->
	<?php
}
?>