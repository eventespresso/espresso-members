<?php

//Espresso Members Settings
function espresso_add_members_to_admin_menu($submenu_page_sections, $espresso_manager) {
	global $espresso_premium;
	$submenu_page_sections['members'] = array(
			($espresso_premium),
			'events',
			__('Event Espresso - Member Settings', 'event_espresso'),
			__('Members', 'event_espresso'),
			apply_filters('filter_hook_espresso_management_capability', 'administrator', $espresso_manager['espresso_manager_members']),
			'members',
			'event_espresso_member_config_mnu'
	);
	return $submenu_page_sections;
}

add_filter('filter_hook_espresso_submenus_management_section', 'espresso_add_members_to_admin_menu', 30, 2);

add_action('admin_head', 'espresso_register_members_meta_boxes');

function espresso_register_members_meta_boxes() {
	global $espresso_premium;
	$screen = get_current_screen();
	$screen_id = $screen->id;
	switch ($screen_id) {
		case 'event-espresso_page_members':
			add_meta_box('espresso_news_post_box', __('New @ Event Espresso', 'event_espresso'), 'espresso_news_post_box', $screen_id, 'side');
			add_meta_box('espresso_links_post_box', __('Helpful Plugin Links', 'event_espresso'), 'espresso_links_post_box', $screen_id, 'side');
			if (!$espresso_premium)
				add_meta_box('espresso_sponsors_post_box', __('Sponsors', 'event_espresso'), 'espresso_sponsors_post_box', $screen_id, 'side');
			add_meta_box('espresso_members_admin_settings_metabox', __('Member Settings', 'event_espresso'), 'espresso_members_admin_settings_metabox', $screen_id, 'normal');
			add_action('admin_footer', 'espresso_admin_page_footer');
			break;
		case 'toplevel_page_events':
			add_meta_box('espresso_event_editor_members_box', __('Member Options', 'event_espresso'), 'espresso_event_editor_members_meta_box', 'toplevel_page_events', 'side', 'default');
			break;
	}
}

function espresso_event_editor_members_meta_box($event) {
	?>
	<div class="inside">
		<p><?php echo event_espresso_member_only($event->member_only); ?></p>
	</div>
	<?php
}

function espresso_members_admin_settings_metabox() {
	$member_options = get_option('events_member_settings');
	$login_page = empty($member_options['login_page']) ? '' : $member_options['login_page'];
	$register_page = empty($member_options['register_page']) ? '' : $member_options['register_page'];
	$member_only_all = empty($member_options['member_only_all']) ? FALSE : $member_options['member_only_all'];
	$autofilled_editable = empty($member_options['autofilled_editable']) ? FALSE : $member_options['autofilled_editable'];
	$values = array(
			array('id' => FALSE, 'text' => __('No', 'event_espresso')),
			array('id' => TRUE, 'text' => __('Yes', 'event_espresso')));
	?>
	<div class="padding">
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th> <label>
								<?php _e('Login page (if different from default Wordpress login page): ', 'event_espresso'); ?>
							</label>
						</th>
						<td><input type="text" name="login_page" size="25" <?php echo (isset($login_page) ? 'value="' . $login_page . '"' : "") ?> /></td>
					</tr>
					<?php if (!get_option('users_can_register')) { ?>
						<tr>
							<td colspan="2"><p class="updated" style="width:65%">
									<?php _e('New user registration is currently closed. If you would like to set a custom user regsistration page, you must enable "Anyone can register" in your Wordpress "<a href="options-general.php">General Settings</a>" page.', 'event_espresso') ?>
								</p></td>
						</tr>
					<?php } else {
						?>
						<tr>
							<th> <label>
									<?php _e('Member registration page (if different from default Wordpress register page): ', 'event_espresso'); ?>
								</label>
							</th>
							<td><input name="register_page" size="25" <?php echo (isset($register_page) ? 'value="' . $register_page . '"' : "") ?> /></td>
						</tr>
					<?php }
					?>
					<tr>
						<th> <label>
								<?php _e('Require login for all events? ', 'event_espresso'); ?>
							</label>
						</th>
						<td><?php
							echo select_input('member_only_all', $values, $member_only_all);
								?></td>
					</tr>
					<tr>
						<th> <label>
								<?php _e('Make autofilled fields editable? ', 'event_espresso'); ?>
							</label>
						</th>
						<td><?php
							echo select_input('autofilled_editable', $values, $autofilled_editable);
								?></td>
					</tr>
				</tbody>
			</table>
			<p>
				<input type="hidden" name="update_member_settings" value="update" />
				<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Settings', 'event_espresso'); ?>" id="save_member_settings" />
				<?php wp_nonce_field('espresso_form_check', 'add_member_settings') ?>
			</p>
		</form>
	</div>
	<!-- / .padding -->
	<?php
}

function event_espresso_member_config_mnu() {
	global $notices;
	if (!empty($_POST['update_member_settings']) && $_POST['update_member_settings'] == 'update' && check_admin_referer('espresso_form_check', 'add_member_settings')) {
		$member_options = get_option('events_member_settings');
		$member_options['login_page'] = $_POST['login_page'];
		$member_options['register_page'] = $_POST['register_page'];
		$member_options['member_only_all'] = $_POST['member_only_all'];
		$member_options['autofilled_editable'] = $_POST['autofilled_editable'];
		update_option('events_member_settings', $member_options);
		$notices['updates'][] = __('Member settings saved', 'event_espresso');
	}
	ob_start();
	do_meta_boxes('event-espresso_page_members', 'side', null);
	$sidebar_content = ob_get_clean();
	ob_start();
	do_meta_boxes('event-espresso_page_members', 'normal', null);
	$main_post_content = ob_get_clean();
	?>

	<div id="members-settings" class="wrap">
		<div id="icon-options-event" class="icon32"></div>
		<h2><?php echo _e('Manage Member Settings', 'event_espresso') ?></h2>
		<?php
		if (!espresso_choose_layout($main_post_content, $sidebar_content))
			return FALSE;
		?>
	</div>
	<!-- / .wrap -->
	<?php
}
