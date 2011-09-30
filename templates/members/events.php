<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_member_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>

						<?php do_action( 'bp_member_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'bp_before_member_body' ) ?>

				<h2>My Events</h2>
				
				<?php 
				
				global $bp;

		        $role = $wpdb->get_var("SELECT meta_value from wp_events_attendee_meta WHERE attendee_id ='$id' AND meta_key = 'EVENT_USER_ROLE'");
		        
				$events = $wpdb->get_results("SELECT am.meta_value, e.start_date, e.event_name, e.id FROM wp_events_attendee_meta am 
					JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = am.attendee_id
					JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
					WHERE am.meta_key = 'EVENT_USER_ROLE' AND am.meta_value = 'Organizer' AND u.user_id = '" . $bp->displayed_user->id . "'");
					
				if (sizeof($events) > 0) { ?>
				
					<h4>Organizer For:</h4>
				
					<ul style="margin-bottom: 20px;">
					
					<?
				
					foreach ($events as $event){
							$event_id = $event->event_id;
							$attendee_id = $event->attendee_id;
							$event_name = $event->event_name;
							$start_date = $event->start_date;
							$start_time = $event->start_time;
							$event_desc = $event->event_desc;
							$display_desc = $event->display_desc;
							$payment_status = $event->payment_status;
							$amount_pd = $event->amount_pd;
							$payment_date = $event->payment_date;
							$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
							$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';		
							
							?>
							
							<li><?php echo $event_link; ?> (<?php echo date('F jS, Y', strtotime($start_date)); ?>)</li>
							
							
							<?
							
					} ?>
					
					</ul>
								
				<? }
				
				
				$events = $wpdb->get_results("SELECT am.meta_value, e.start_date, e.event_name, e.id FROM wp_events_attendee_meta am 
					JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = am.attendee_id
					JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
					WHERE am.meta_key = 'EVENT_USER_ROLE' AND am.meta_value = 'Presenter' AND u.user_id = '" . $bp->displayed_user->id . "'");
					
				if (sizeof($events) > 0) { ?>
				
					<h4>Presenting At:</h4>
				
					<ul style="margin-bottom: 20px;">
					
					<?
				
					foreach ($events as $event){
							$event_id = $event->event_id;
							$attendee_id = $event->attendee_id;
							$event_name = $event->event_name;
							$start_date = $event->start_date;
							$start_time = $event->start_time;
							$event_desc = $event->event_desc;
							$display_desc = $event->display_desc;
							$payment_status = $event->payment_status;
							$amount_pd = $event->amount_pd;
							$payment_date = $event->payment_date;
							$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
							$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';		
							
							?>
							
							<li><?php echo $event_link; ?> (<?php echo date('F jS, Y', strtotime($start_date)); ?>)</li>
							
							
							<?
							
					} ?>
					
					</ul>
								
				<? }
				
				
				$events = $wpdb->get_results("SELECT am.meta_value, e.start_date, e.event_name, e.id FROM wp_events_attendee_meta am 
					JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = am.attendee_id
					JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
					WHERE am.meta_key = 'EVENT_USER_ROLE' AND am.meta_value = 'Volunteer' AND u.user_id = '" . $bp->displayed_user->id . "'");
					
				if (sizeof($events) > 0) { ?>
				
					<h4>Volunteering At:</h4>
				
					<ul style="margin-bottom: 20px;">
					
					<?
				
					foreach ($events as $event){
							$event_id = $event->event_id;
							$attendee_id = $event->attendee_id;
							$event_name = $event->event_name;
							$start_date = $event->start_date;
							$start_time = $event->start_time;
							$event_desc = $event->event_desc;
							$display_desc = $event->display_desc;
							$payment_status = $event->payment_status;
							$amount_pd = $event->amount_pd;
							$payment_date = $event->payment_date;
							$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
							$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';		
							
							?>
							
							<li><?php echo $event_link; ?> (<?php echo date('F jS, Y', strtotime($start_date)); ?>)</li>
							
							
							<?
							
					} ?>
					
					</ul>
								
				<? }
				
				
				
				
				$wpdb->get_results("SELECT id FROM ". EVENTS_MEMBER_REL_TABLE . " WHERE user_id = '" . $bp->displayed_user->id . "'");
				
				if ($wpdb->num_rows > 0) {
				
					$events = $wpdb->get_results("SELECT e.id event_id, e.event_name, e.start_date, e.event_desc, e.display_desc, a.id attendee_id, a.event_time start_time, a.payment_status, a.payment_date, a.amount_pd, u.user_id user_id 
				 	FROM " . EVENTS_ATTENDEE_TABLE . " a
					JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = a.id
					JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
					WHERE a.payment_status = 'completed' AND u.user_id = '" . $bp->displayed_user->id . "'
					GROUP BY e.id
					
					");
		
				
					//print_r ($events);		
					
					?>
					
					<h4>Scheduled To Attend:</h4>
					
					<ul style="margin-bottom: 20px;">
					
					<?
				
					foreach ($events as $event){
							$event_id = $event->event_id;
							$attendee_id = $event->attendee_id;
							$event_name = $event->event_name;
							$start_date = $event->start_date;
							$start_time = $event->start_time;
							$event_desc = $event->event_desc;
							$display_desc = $event->display_desc;
							$payment_status = $event->payment_status;
							$amount_pd = $event->amount_pd;
							$payment_date = $event->payment_date;
							$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
							$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';		
							
							?>
							
							<li><?php echo $event_link; ?> (<?php echo date('F jS, Y', strtotime($start_date)); ?>)</li>
							
							
							<?
							
					} ?>
					
					</ul>
					
					<?								
												
												
												
				}											
				
				?>
				
				<?php do_action( 'bp_after_member_body' ) ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_home_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>