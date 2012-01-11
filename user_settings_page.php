<?php

//============= Event Registration Members Subpage - Settings  =============== //
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
	
	$member_options = get_option('events_member_settings');
	$login_page = empty($member_options['login_page']) ? '' : $member_options['login_page'];
	$register_page = empty($member_options['register_page']) ? '' : $member_options['register_page'];
	$member_only_all = empty($member_options['member_only_all']) ? 'N' : $member_options['member_only_all'];
	$autofilled_editable = empty($member_options['autofilled_editable']) ? 'N' : $member_options['autofilled_editable'];
	?>

<div id="members-settings" class="wrap">
	<div id="icon-options-event" class="icon32"></div>
	<h2><?php echo _e('Manage Member Settings', 'event_espresso') ?></h2>
	<?php 
		if( did_action( 'action_hook_espresso_admin_notices') == false){
		do_action( 'action_hook_espresso_admin_notices'); 
		}?>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<?php event_espresso_display_right_column(); ?>
		<div id="post-body">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortables">
					<?php #### metabox #### ?>
					<div class="metabox-holder">
						<div class="postbox">
							<div title="Click to toggle" class="handlediv"><br />
							</div>
							<h3 class="hndle">
								<?php _e('Member Settings', 'event_espresso'); ?>
							</h3>
							<div class="inside">
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
												<?php
											if (!get_option('users_can_register')) {	?>
												<tr>
													<td colspan="2"><p class="updated" style="width:65%">
															<?php _e('New user registration is currently closed. If you would like to set a custom user regsistration page, you must enable "Anyone can register" in your Wordpress "<a href="options-general.php">General Settings</a>" page.', 'event_espresso') ?>
														</p></td>
												</tr>
												<?php  
											} else {?>
												<tr>
													<th> <label>
															<?php _e('Member registration page (if different from default Wordpress register page): ', 'event_espresso'); ?>
														</label>
													</th>
													<td><input name="register_page" size="25" <?php echo (isset($register_page) ? 'value="' . $register_page . '"' : "") ?> /></td>
												</tr>
												<?php 
											} ?>
												<tr>
													<th> <label>
															<?php _e('Require login for all events? ', 'event_espresso'); ?>
														</label>
													</th>
													<td><?php
													$values = array(
													array('id' => 'N', 'text' => __('No', 'event_espresso')),
													array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
													echo select_input('member_only_all', $values, $member_only_all);
												?></td>
												</tr>
												<tr>
													<th> <label>
															<?php _e('Make autofilled fields editable? ', 'event_espresso'); ?>
														</label>
													</th>
													<td><?php
													$values = array(
													array('id' => 'N', 'text' => __('No', 'event_espresso')),
													array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
													echo select_input('autofilled_editable', $values, $autofilled_editable);
												?></td>
												</tr>
											</tbody>
										</table>
										<p>
											<input type="hidden" name="update_member_settings" value="update" />
											<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Settings', 'event_espresso'); ?>" id="save_member_settings" />
											<?php wp_nonce_field( 'espresso_form_check', 'add_member_settings' ) ?>
										</p>
									</form>
								</div>
								<!-- / .padding --> 
							</div>
							<!-- / .inside --> 
						</div>
						<!-- /.postbox --> 
					</div>
					<!-- .metabox-holder -->
					<?php #### close metabox #### ?>
				</div>
				<!-- / #post-body-content --> 
			</div>
			<!-- / #post-body --> 
		</div>
		<!-- / #poststuff --> 
	</div>
</div>
<!-- / .wrap --> 
<script type="text/javascript" charset="utf-8">
		//<![CDATA[
		jQuery(document).ready(function() {
			postboxes.add_postbox_toggles('members');

		});
		//]]>
</script>
<?php
//============= End Event Registration Members Subpage - Settings  =============== //
}
