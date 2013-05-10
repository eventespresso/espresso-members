<?php
if (!function_exists('event_espresso_my_events')) {
	function event_espresso_my_events(){
		global $espresso_premium; if ($espresso_premium != true) return;
		global $wpdb, $org_options;	
		global $ticketing_installed;
		$notifications['success'] = array(); 
		$notifications['error']	 = array(); 
		require_once('user_vars.php');
		if( ! is_user_logged_in() ){ 
			if( $login_page != '' ) {
				printf('<p>Please <a href="%s">log in</a> to view the events you are registered to attend.</p>', $login_page );
			}else{
				echo '<p>You must be logged in to view this page.</p>'; 
				return; 
			}
		}
		?>

<div id="configure_organization_form" class="wrap meta-box-sortables ui-sortable">
	<div id="event_reg_theme" class="wrap">
		<div id="icon-options-event" class="icon32"></div>
		<h2><?php echo _e('My Events Management', 'event_espresso') ?></h2>
		<div id="poststuff" class="metabox-holder">
			<?php
		if(isset($_POST['cancel_registration'])){
			if (is_array($_POST['checkbox'])){
				while(list($key,$value)=each($_POST['checkbox'])):
					
					$set_cols_and_values = array( 
						'payment_status' => 'Cancelled',
					);
					$set_format = array( '%s', );
					$where_cols_and_values = array( 'id'=> $key );
					$where_format = array( '%d' );
					// run the update
					$upd_success = $wpdb->update( EVENTS_ATTENDEE_TABLE, $set_cols_and_values, $where_cols_and_values, $set_format, $where_format );
					if ( $upd_success === FALSE ) {
						$notifications['error'][] = __('An error occured. The registration was not cancelled.', 'event_espresso');
					}else{
						$notifications['success'][] = __('You have cancelled your registration.', 'event_espresso'); 
					}
					
				endwhile;	
			}
			
		// display success messages
	if ( ! empty( $notifications['success'] )) { 
		$success_msg = implode( $notifications['success'], '<br />' );
	?>
			<div id="message" class="updated fade">
				<p> <strong><?php echo $success_msg; ?></strong> </p>
			</div>
			<?php
	 } 
	// display error messages
	if ( ! empty( $notifications['error'] )) {
		$error_msg = implode( $notifications['error'], '<br />' );
	?>
			<div id="message" class="error">
				<p> <strong><?php echo $error_msg; ?></strong> </p>
			</div>
			<?php } 
		}
	?>
			<form id="form1" name="form1" method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>">
				<div style="clear:both; margin-bottom:30px;"></div>
				<table id="table" class="widefat my_events_table" width="100%">
					<thead>
						<tr>
							<th class="manage-column column-cb check-column" id="cb" scope="col" style="width:5%;"></th>
							<th class="manage-column column-title" id="name" scope="col" title="Click to Sort" style="width:10%;"><span>
								<?php _e('Event Name','event_espresso'); ?>
								</span><span class="sorting-indicator"></span></th>
							<th class="manage-column column-title" id="event" scope="col" title="Click to Sort" style="width: 10%;"> <span>
								<?php _e('Type','event_espresso'); ?>
								</span> <span class="sorting-indicator"></span> </th>
							<th class="manage-column column-author" id="start" scope="col" title="Click to Sort" style="width:10%;"><span>
								<?php _e('Date','event_espresso'); ?>
								</span><span class="sorting-indicator"></span></th>
							<th class="manage-column column-date" id="begins" scope="col" title="Click to Sort" style="width:5%;"><span>
								<?php _e('Time','event_espresso'); ?>
								</span><span class="sorting-indicator"></span></th>
							<th class="manage-column column-date" id="status" scope="col" title="Click to Sort" style="width:5%;"><span>
								<?php _e('Status','event_espresso'); ?>
								</span><span class="sorting-indicator"></span></th>
							<th class="manage-column column-date" id="attendees" scope="col" title="Click to Sort" style="width:5%;"><span>
								<?php _e('Cost','event_espresso'); ?>
								</span><span class="sorting-indicator"></span></th>
							<th class="manage-column column-date" id="invoice" scope="col" style="width:5%;"><?php _e('Invoice','event_espresso'); ?></th>
							<?php echo $ticketing_installed == true?'<th class="manage-column column-author" id="ticket" scope="col" style="width:10%;">'.__('Ticket','event_espresso').'</th>':''; ?> </tr>
					</thead>
					<tbody>
						<?php 
			$wpdb->get_results("SELECT id FROM ". EVENTS_MEMBER_REL_TABLE . " WHERE user_id = '" . $current_user->ID . "'");
			if ($wpdb->num_rows > 0) {
				$events = $wpdb->get_results("SELECT e.id event_id, e.event_name, e.event_code, e.start_date, e.event_desc, e.display_desc, a.id attendee_id, a.event_time start_time, a.payment_status, a.payment_date, a.amount_pd, u.user_id user_id, a.registration_id, a.fname, a.lname, a.price_option, a.event_time
													FROM " . EVENTS_ATTENDEE_TABLE . " a
													JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = a.id
													JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
													WHERE u.user_id = '" . $current_user->ID . "'");
				foreach ($events as $event){
						$event_id = $event->event_id;
						$event_code = $event->event_code;
						$attendee_id = $event->attendee_id;
						$registration_id = $event->registration_id;
						$lname = $event->lname;
						$fname = $event->fname;
						$event_name = $event->event_name;
						$start_date = $event->start_date;
						$start_time = $event->start_time;
						$event_desc = $event->event_desc;
						$display_desc = $event->display_desc;
						$payment_status = $event->payment_status;
						$amount_pd = espresso_attendee_price(array('attendee_id'=>$attendee_id));
						$payment_date = $event->payment_date;
						$ticket_type = $event->price_option;
						if ($payment_status == ''){
							$payment_link = get_option('siteurl') . "/?page_id=" . $org_options['return_url'] . "&id=" . $attendee_id;
							$payment_status = '<a href="' . $payment_link . '">Pay Now</a>';
						}
						$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
						$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';
						//Build the payment link
						$payment_url = home_url() . "/?page_id=" . $org_options['return_url'] . "&amp;registration_id=" . $registration_id;
						//$payment_link = '<a href="' . $payment_url . '" title="'.__('View Your Payment Details').'">' . event_espresso_paid_status_icon( $payment_status ) . '</a>';
						
						//Deprecated ticketing system support
						//If the custom ticket is available, load the template file
						if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "/ticketing/template.php")){
							if (file_exists(EVENT_ESPRESSO_UPLOAD_DIR . "/ticketing/functions.php")){
								include_once(EVENT_ESPRESSO_UPLOAD_DIR . "/ticketing/functions.php");
								$qr_code = espresso_qr_code(array('attendee_id' => $attendee_id,'event_name' => stripslashes_deep($event_name), 'attendee_first' => $fname, 'attendee_last'=> $lname, 'registration_id'=> $registration_id, 'event_code'=> $event_code, 'ticket_type'=>$ticket_type, 'event_time'=>$event_time, 'amount_pd'=>$amount_pd));
							}
									//Build the ticket link
							$ticket_url = home_url() . "/?download_ticket=true&amp;id=" . $attendee_id . "&amp;registration_id=".$registration_id;
							$ticket_link = '<a href="' . $ticket_url . '">' . __('Download Ticket Now!') . '</a>';
						}
						
						//New ticketing system suport
						if (function_exists('espresso_ticket_launch')){
							$ticket_link = espresso_ticket_links($registration_id, $attendee_id);
						}
	
	?>
						<tr>
							<td><input name="checkbox[<?php echo $attendee_id?>]" type="checkbox"  title="Cancel registration for <?php echo $event_name?>"></td>
							<td class="post-title page-title column-title"><strong><?php echo $event_link?></strong></td>
							<td class="post-title page-title column-title"><?php echo $ticket_type ?></td>
							<td class="date column-date"><?php echo event_date_display($start_date)?></td>
							<td class="date column-date"><?php echo $start_time?></td>
							<td class="date column-date"><?php echo '<a target="_blank" href="' . $payment_url . '" title="'.__('View Your Payment Details').'">'; ?>
								<?php event_espresso_paid_status_icon( $payment_status ) . '</a>'; ?></td>
							<td class="date column-date"><?php echo $org_options[ 'currency_symbol' ] ?><?php echo $amount_pd?></td>
							<td class="date column-date"><a href="<?php echo home_url();?>/?download_invoice=true&amp;admin=true&amp;registration_id=<?php echo $registration_id ?>" target="_blank"  title="<?php _e('Download Invoice', 'event_espresso'); ?>"><img src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/icons/page_white_acrobat.png" width="16" height="16" alt="<?php _e('Download Invoice', 'event_espresso'); ?>" /></a></td>
							<?php echo $ticketing_installed == true?'<td class="post-title page-title column-title">'.$ticket_link.'</td>':''; ?> </tr>
						<?php } 
			}
			?>
					</tbody>
				</table>
				<div class="bottom_settings" style="clear:both; margin-bottom:30px;">
					<input type="checkbox" name="sAll" onclick="selectAll(this)" />
					<strong>
					<?php _e('Check All','event_espresso'); ?>
					</strong>
					<input name="cancel_registration" type="submit" class="button-secondary" id="cancel_registration" value="<?php _e('Cancel Registration','event_espresso'); ?>" onclick="return confirmDelete();">
					<a style="margin-left:20px" class="button-primary"  onclick="window.location='<?php echo admin_url(); ?>profile.php#event_espresso_profile'">
					<?php _e('Your Profile','event_espresso'); ?>
					</a> </div>
			</form>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {						
			
		/* show the table data */
		var mytable = $('#table').dataTable( {
				"bStateSave": true,
				"sPaginationType": "full_numbers",
	
				"oLanguage": {	"sSearch": "<strong><?php _e('Live Search Filter', 'event_espresso'); ?>:</strong>",
								"sZeroRecords": "<?php _e('No Records Found!','event_espresso'); ?>" },
				"aoColumns": [
								{ "bSortable": false },
								 null,
								 null,
								 null,
								 null,
								 null,
								 null,
								 { "bSortable": false },
								<?php echo $ticketing_installed == true?'{ "bSortable": false }':'';?>
							]
	
		} );
		
	} );
	</script>
<?php
	}

}

//Create a shortcode to place on a page
function event_espresso_my_events_fview(){
	echo event_espresso_my_events();
	wp_enqueue_style('my_events_table', EVNT_MBR_PLUGINFULLURL . 'styles/my_events_table.css'); //My events table css
	wp_enqueue_script('dataTables', EVENT_ESPRESSO_PLUGINFULLURL . 'scripts/jquery.dataTables.min.js', array('jquery')); //Events core table script
	wp_enqueue_script('dataTablesColVis', EVENT_ESPRESSO_PLUGINFULLURL . 'scripts/jquery.ColVis.min.js', array('jquery')); //Events core table column hide/show script
	wp_register_script('event_espresso_js', (EVENT_ESPRESSO_PLUGINFULLURL . "scripts/event_espresso.js"), false);
	wp_enqueue_script('event_espresso_js');
	return;
}
add_shortcode('ESPRESSO_MY_EVENTS', 'event_espresso_my_events_fview');
