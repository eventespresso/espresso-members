<?php

function espresso_event_editor_members_meta_box($event) {
	?>
	<div class="inside">
		<p><?php echo event_espresso_member_only($event->member_only); ?></p>
	</div>
	<?php
}

function espresso_register_members_event_editor_meta_boxes() {
	global $org_options;
	add_meta_box('espresso_event_editor_members_box', __('Member Options', 'event_espresso'), 'espresso_event_editor_members_meta_box', 'toplevel_page_events', 'side', 'default');
}

add_action('current_screen', 'espresso_register_members_event_editor_meta_boxes', 30);

		//Member Settings
function espresso_add_members_to_admin_menu($espresso_manager) {
			add_submenu_page('events', __('Event Espresso - Member Settings', 'event_espresso'), __('Members', 'event_espresso'), apply_filters( 'filter_hook_espresso_management_capability', 'administrator', $espresso_manager['espresso_manager_members']), 'members', 'event_espresso_member_config_mnu');
		}

		add_action('action_hook_espresso_add_new_submenu_to_group_settings', 'espresso_add_members_to_admin_menu', 20);