<?php
/**
 * BP_Core_User class can be used by any component. It will fetch useful
 * details for any user when provided with a user_id.
 *
 * Example:
 *    $user = new BP_Core_User( $user_id );
 *    $user_avatar = $user->avatar;
 *	  $user_email = $user->email;
 *    $user_status = $user->status;
 *    etc.
 *
 * @package BuddyPress Core
 */
class Event_Espresso_Event {
	var $id;
	var $event_details;
//	var $avatar;
//	var $avatar_thumb;
//	var $avatar_mini;
//	var $event_name;
//	var $event_code;
//	var $event_desc;
//	var $event_start_date;
//	var $event_end_date;

//	var $user_url;
//	var $user_link;

//	var $last_active;

	/* Extras */
//	var $total_friends;
//	var $total_blogs;
//	var $total_groups;

	function event_espresso_event( $event_id, $populate_extras = false ) {
		if ( $event_id ) {
			$this->id = $event_id;
			$this->populate( $event_id );

//			if ( $populate_extras )
//				$this->populate_extras();
		}
	}

	/**
	 * populate()
	 *
	 * Populate the instantiated class with data based on the Event ID provided.
	 *
	 * @package BuddyPress Core
 	 * @global $userdata WordPress user data for the current logged in user.
	 * @uses bp_core_get_userurl() Returns the URL with no HTML markup for a user based on their user id
	 * @uses bp_core_get_userlink() Returns a HTML formatted link for a user with the user's full name as the link text
	 * @uses bp_core_get_user_email() Returns the email address for the user based on user ID
	 * @uses get_user_meta() WordPress function returns the value of passed usermeta name from usermeta table
	 * @uses bp_core_fetch_avatar() Returns HTML formatted avatar for a user
	 * @uses bp_profile_last_updated_date() Returns the last updated date for a user.
	 */
	function populate( $event_id ) {
		global $wpdb;	

		$sql = array();

		$sql['select_main'] = "SELECT DISTINCT e.id as id, e.event_code, e.event_name, e.event_desc, e.start_date, e.end_date, e.registration_start, e.registration_end, e.address, e.city, e.state, e.zip, e.venue_title";

		$sql['from'] = "FROM " . EVENTS_DETAIL_TABLE . " e LEFT JOIN " . EVENTS_ATTENDEE_TABLE . " a ON a.event_id = e.id";

		$sql['where'] = 'WHERE e.id = ' . $event_id ;
		
		$events_sql = apply_filters( 'event_espresso_get_event_sql', join( ' ', (array)$sql ), $sql );
		$details = $wpdb->get_results( $events_sql );
		$this->event_details = $details[0];
		
		$attendee_count = $wpdb->get_var( "SELECT COUNT(*) as total_attendee_count FROM wp_events_member_rel WHERE event_id = ".$event_id." GROUP BY user_id" );
		$this->total_attendee_count = (int)$attendee_count;

		$attendees = $wpdb->get_var( "SELECT user_id FROM wp_events_member_rel WHERE event_id = ".$event_id." GROUP BY user_id" );
		$attendee_ids = $wpdb->escape( join( ',', (array)$attendees ) );
		$this->attendees = $attendees;


//		$this->avatar = bp_core_fetch_avatar( array( 'item_id' => $this->id, 'type' => 'full' ) );
//		$this->avatar_thumb = bp_core_fetch_avatar( array( 'item_id' => $this->id, 'type' => 'thumb' ) );
//		$this->avatar_mini = bp_core_fetch_avatar( array( 'item_id' => $this->id, 'type' => 'thumb', 'width' => 30, 'height' => 30 ) );

//		$this->last_active = bp_core_get_last_activity( get_user_meta( $this->id, 'last_activity', true ), __( 'active %s ago', 'buddypress' ) );
	}


	/* Static Functions */

	function get_events( $type, $limit = null, $page = 1, $user_id = false, $include = false, $search_terms = false, $populate_extras = true ) {
		global $wpdb, $bp;

		$sql = array();

		$sql['select_main'] = "SELECT DISTINCT e.id as id, e.event_code, e.event_name, e.event_desc, e.start_date, e.end_date, e.registration_start, e.registration_end, e.address, e.city, e.state, e.venue_title";

		$sql['from'] = "FROM " . EVENTS_DETAIL_TABLE . " e LEFT JOIN " . EVENTS_ATTENDEE_TABLE . " a ON a.event_id = e.id";

		$sql['where'] = 'WHERE 1=1 ' ;

		if ( 'active' == $type )
			$sql['where_active'] = "AND e.is_active = 'Y'";

		switch ( $type ) {
			case 'active': default:
				$sql[] = "ORDER BY e.submitted DESC";
				break;
		}

		if ( $limit && $page )
			$sql['pagination'] = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		/* Get paginated results */
		$paged_events_sql = apply_filters( 'bp_core_get_paged_users_sql', join( ' ', (array)$sql ), $sql );
		$paged_events     = $wpdb->get_results( $paged_events_sql );

		/* Re-jig the SQL so we can get the total user count */
		unset( $sql['select_main'] );

		if ( !empty( $sql['pagination'] ) )
			unset( $sql['pagination'] );

		array_unshift( $sql, "SELECT COUNT(DISTINCT e.id)" );

		/* Get total events results */
		$total_events_sql = apply_filters( 'bp_core_get_total_users_sql', join( ' ', (array)$sql ), $sql );
		$total_events     = $wpdb->get_var( $total_events_sql );

		/***
		 * Lets fetch some other useful data in a separate queries, this will be faster than querying the data for every user in a list.
		 * We can't add these to the main query above since only users who have this information will be returned (since the much of the data is in usermeta and won't support any type of directional join)
		 */
		if ( $populate_extras ) {
			foreach ( (array)$paged_events as $event )
				$event_ids[] = $event->id;

			$event_ids = $wpdb->escape( join( ',', (array)$event_ids ) );

			/* Add additional data to the returned results */
			$paged_events = Event_Espresso_Event::get_event_extras( &$paged_events, $event_ids, $type );
		}

		return array( 'events' => $paged_events, 'total' => $total_events );
	}


	function get_event_extras( $paged_events, $event_ids, $type = false ) {
		global $bp, $wpdb;

		if ( empty( $event_ids ) )
			return $paged_events;


		for ( $i = 0; $i < count( $paged_events ); $i++ ) {
			$attendee_count = $wpdb->get_var( "SELECT COUNT(*) as total_attendee_count FROM wp_events_member_rel WHERE event_id = ".$paged_events[$i]->id." GROUP BY user_id" );
			$paged_events[$i]->total_attendee_count = (int)$attendee_count;
		}

		for ( $i = 0; $i < count( $paged_events ); $i++ ) {
			$attendees = $wpdb->get_var( "SELECT user_id FROM wp_events_member_rel WHERE event_id = ".$paged_events[$i]->id." GROUP BY user_id" );
			$attendee_ids = $wpdb->escape( join( ',', (array)$attendees ) );
			$paged_events[$i]->attendees = $attendees;
		}


		return $paged_events;
	}
	

	function available_for_display() {
		return true;
	}


	

}


function event_espresso_event() {
	global $event_template;
}

function event_espresso_has_event( $args = '') {
	global $event_template;
	
	$defaults = array(
		'type' => $type,
		'event_id' => 0
	);
	
	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	if ($bp->current_action) {
		$event_id = $bp->current_action;
	}
	
	$event_template = new Event_Espresso_Event($event_id, false);
		
	return apply_filters( 'bp_has_event', $event_template->available_for_display(), &$event_template );
	
}

function event_espresso_single_event_name() {
	echo apply_filters( 'bp_event_name', event_espresso_get_single_event_name() );
}
	function event_espresso_get_single_event_name() {
		global $event_template;
		return apply_filters( 'event_espresso_get_single_event_name', $event_template->event_details->event_name );
	}
	add_filter( 'event_espresso_get_single_event_name', 'wp_filter_kses' );
	add_filter( 'event_espresso_get_single_event_name', 'stripslashes' );
	add_filter( 'event_espresso_get_single_event_name', 'strip_tags' );
	
function event_espresso_single_event_dates() {
	echo apply_filters( 'event_espresso_single_event_dates', event_espresso_get_single_event_dates() );
}
	function event_espresso_get_single_event_dates() {
		global $event_template;
		
		$events_dates = date('F jS, Y', strtotime($event_template->event_details->start_date)) . ' - ' . date('F jS, Y', strtotime($event_template->event_details->end_date));

		return apply_filters( 'event_espresso_get_single_event_dates', $events_dates );
	}	

function event_espresso_venue_title() {
	echo apply_filters( 'event_espresso_venue_title', event_espresso_get_venue_title() );
}
	function event_espresso_get_venue_title() {
		global $event_template;
		
		$info = $event_template->event_details->venue_title;

		return apply_filters( 'event_espresso_get_venue_title', $info );
	}	

function event_espresso_venue_address() {
	echo apply_filters( 'event_espresso_venue_address', event_espresso_get_venue_address() );
}
	function event_espresso_get_venue_address() {
		global $event_template;
		
		$info = $event_template->event_details->address;

		return apply_filters( 'event_espresso_get_venue_address', $info );
	}	
	
function event_espresso_venue_city() {
	echo apply_filters( 'event_espresso_venue_city', event_espresso_get_venue_city() );
}
	function event_espresso_get_venue_city() {
		global $event_template;
		
		$info = $event_template->event_details->city;

		return apply_filters( 'event_espresso_get_venue_city', $info );
	}	
	
function event_espresso_venue_state() {
	echo apply_filters( 'event_espresso_venue_state', event_espresso_get_venue_state() );
}
	function event_espresso_get_venue_state() {
		global $event_template;
		
		$info = $event_template->event_details->state;

		return apply_filters( 'event_espresso_get_venue_state', $info );
	}		

function event_espresso_venue_zip() {
	echo apply_filters( 'event_espresso_venue_state', event_espresso_get_venue_zip() );
}
	function event_espresso_get_venue_zip() {
		global $event_template;
						
		$info = $event_template->event_details->zip;

		return apply_filters( 'event_espresso_get_venue_zip', $info );
	}			
	
	
function event_espresso_event_attendees_count() {
	echo apply_filters( 'event_espresso_event_attendees_count', event_espresso_get_event_attendees_count() );
}
	function event_espresso_get_event_attendees_count() {
		global $event_template;
				
		if ($event_template->total_attendee_count > 0) {
			return apply_filters( 'event_espresso_get_event_attendees_count', $event_template->total_attendee_count );
		} else {
			return "0";
		}
	}
	
function event_espresso_event_show_register_button() {
	echo apply_filters( 'event_espresso_event_show_register_button', event_espresso_event_show_get_register_button() );
}
	function event_espresso_event_show_get_register_button() {
		global $event_template;
				
		$info = event_espresso_get_add_register_button( $event_template->id );

		return apply_filters( 'event_espresso_event_show_get_register_button', $info );
	}

function event_espresso_event_attendees() {
	echo apply_filters( 'event_espresso_event_attendees', event_espresso_get_event_attendees() );
}
	function event_espresso_get_event_attendees() {
		global $event_template;

		if ($event_template->total_attendee_count > 0) {
		
			$attendees = explode(",",$event_template->attendees);
			$html = '';
			foreach ($attendees as $attendee_id) {
				$type = '';
				$width = EE_EVENT_AVATAR_ATTENDEE_WIDTH;
				$height = EE_EVENT_AVATAR_ATTENDEE_HEIGHT;
				$html = '';
				$avatar = '<a href="'.bp_core_get_user_domain($attendee_id).'"><img src="'.bp_core_fetch_avatar( array( 'item_id' => $attendee_id, 'type' => $type, 'width' => $width, 'height' => $height, 'html' => $html ) ).'" /></a>';
				$html .= $avatar;
			}
		
		} else {
		
			$html = '';
			
		}

		return apply_filters( 'event_espresso_get_event_attendees', $html );

	}
	
	
	
?>