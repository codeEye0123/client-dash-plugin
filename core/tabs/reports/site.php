<?php

/**
 * Class ClientDash_Page_Reports_Tab_Site
 *
 * Adds the core content section for Reports -> Site.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Reports_Tab_Site extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		$this->add_content_section( array(
			'name' => 'Basic Information',
			'page' => 'Reports',
			'tab' => 'Site',
			'callback' => array( $this, 'block_output' )
		));
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {
		// Get the site information
		$args = array( 'public' => true );
		$cd_count_comments = wp_count_comments();
		$cd_count_users    = count_users();
		$cd_post_types	   = get_post_types( $args, 'objects' )
		?>

		<table class="form-table">
			<?php foreach ($cd_post_types as $type) {
				$nice_name = $type->labels->name;
				$name = $type->name;
				$count = wp_count_posts( $name );
				$link = get_admin_url(). 'edit.php?post_type=' .$name;

				if ($name != 'attachment') {
					?>
					<tr valign="top">
						<th scope="row">
							<a href="<?php echo $link; ?>">
								<?php echo $nice_name; ?>
							</a>
						</th>
						<td>
							<ul>
								<li><a href="<?php echo $link. '&post_status=publish'; ?>"><?php echo $count->publish; ?> published</a></li>
								<li><a href="<?php echo $link. '&post_status=pending'; ?>"><?php echo $count->pending; ?> pending</a></li>
								<li><a href="<?php echo $link. '&post_status=draft'; ?>"><?php echo $count->draft; ?> drafts</a></li>
							</ul>
						</td>
					</tr>
				<?php }
			} ?>
			<tr valign="top">
				<th scope="row">
					<a href="<?php echo get_admin_url(); ?>edit-comments.php">
						Comments
					</a>
				</th>
				<td>
					<ul>
						<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=approved'; ?>"><?php echo $cd_count_comments->approved; ?> approved</a></li>
						<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=moderated'; ?>"><?php echo $cd_count_comments->moderated; ?> pending</a></li>
						<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=spam'; ?>"><?php echo $cd_count_comments->spam; ?> spam</a></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<a href="<?php echo get_admin_url(); ?>upload.php">
						Media
					</a>
				</th>
				<?php
				$upload_dir  = wp_upload_dir();
				$dir_info    = $this->get_dir_size( $upload_dir['basedir'] );
				$attachments = wp_count_posts( 'attachment' );
				?>
				<td><?php echo $attachments->inherit; ?> total media items<br/>
					<?php echo $this->format_dir_size( $dir_info['size'] ); ?> total media library size
					(<?php echo $dir_info['count']; ?> files)
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<a href="<?php echo get_admin_url(); ?>users.php">
						Users
					</a>
				</th>
				<td><?php echo $cd_count_users['total_users']; ?> total registered users<br/>
					<?php foreach ( $cd_count_users['avail_roles'] as $role => $count ) {
						echo $count . ' ' . $role . '<br/>';
					} ?>
				</td>
			</tr>
		</table>
	<?php
	}
}