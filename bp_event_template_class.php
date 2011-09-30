<?php
/***
 * Members template loop that will allow you to loop all events or friends of a event
 * if you pass a user_id.
 */

class Event_Espresso_Events_Template {
	var $current_event = -1;
	var $event_count;
	var $events;
	var $event;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_event_count;

	function event_espresso_events_template( $type, $page_number, $per_page, $max, $event_id, $search_terms, $include, $populate_extras ) {
		global $bp;

		$this->pag_page = isset( $_REQUEST['upage'] ) ? intval( $_REQUEST['upage'] ) : $page_number;
		$this->pag_num  = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;
		$this->type     = $type;


		$this->events = event_espresso_core_get_events( array( 'type' => $this->type, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'event_id' => $event_id, 'include' => $include, 'search_terms' => $search_terms, 'populate_extras' => $populate_extras ) );					
		

				
		if ( !$max || $max >= (int)$this->events['total'] )
			$this->total_event_count = (int)$this->events['total'];
		else
			$this->total_event_count = (int)$max;

		$this->events = $this->events['events'];

		if ( $max ) {
			if ( $max >= count($this->events) ) {
				$this->event_count = count( $this->events );
			} else {
				$this->event_count = (int)$max;
			}
		} else {
			$this->event_count = count( $this->events );
		}

		if ( (int)$this->total_event_count && (int)$this->pag_num ) {
			$this->pag_links = paginate_links( array(
				'base'      => add_query_arg( 'upage', '%#%' ),
				'format'    => '',
				'total'     => ceil( (int)$this->total_event_count / (int)$this->pag_num ),
				'current'   => (int)$this->pag_page,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'mid_size'  => 1
			) );
		}
		
		

	}

	function has_events() {
		if ( $this->event_count )
			return true;

		return false;
	}

	function next_event() {
		$this->current_event++;
		$this->event = $this->events[$this->current_event];

		return $this->event;
	}

	function rewind_events() {
		$this->current_event = -1;
		if ( $this->event_count > 0 ) {
			$this->event = $this->events[0];
		}
	}

	function events() {
		if ( $this->current_event + 1 < $this->event_count ) {
			return true;
		} elseif ( $this->current_event + 1 == $this->event_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_events();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_event() {
		global $event, $bp;

		$this->in_the_loop = true;
		$this->event = $this->next_event();

		if ( 0 == $this->current_event ) // loop has just started
			do_action('loop_start');
	}
	
	

	
}


function bp_rewind_events() {
	global $events_template;

	return $events_template->rewind_events();
}

function bp_has_events( $args = '' ) {
	global $bp, $events_template;

	/***
	 * Set the defaults based on the current page. Any of these will be overridden
	 * if arguments are directly passed into the loop. Custom plugins should always
	 * pass their parameters directly to the loop.
	 */
	$type = 'active';
	$page = 1;
	$search_terms = false;

	// Pass a filter if ?s= is set.
	if ( isset( $_REQUEST['s'] ) && !empty( $_REQUEST['s'] ) )
		$search_terms = $_REQUEST['s'];

	// type: active ( default ) | random | newest | popular | online | alphabetical
	$defaults = array(
		'type' => $type,
		'page' => $page,
		'per_page' => 20,
		'max' => false,

		'include' => false, // Pass a user_id or comma separated list of user_ids to only show these users

		'event_id' => $event_id, // Pass a user_id to only show friends of this user
		'search_terms' => $search_terms, // Pass search_terms to filter users by their profile data

		'populate_extras' => true // Fetch usermeta? Friend count, last active etc.
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	if ( $max ) {
		if ( $per_page > $max )
			$per_page = $max;
	}

	$events_template = new Event_Espresso_Events_Template( $type, $page, $per_page, $max, $event_id, $search_terms, $include, (bool)$populate_extras );

	return apply_filters( 'bp_has_events', $events_template->has_events(), &$events_template );
}

function bp_the_event() {
	global $events_template;
	return $events_template->the_event();
}

function bp_events() {
	global $events_template;
	return $events_template->events();
}

function bp_events_pagination_count() {
	global $bp, $events_template;

	$start_num = intval( ( $events_template->pag_page - 1 ) * $events_template->pag_num ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $events_template->pag_num - 1 ) > $events_template->total_event_count ) ? $events_template->total_event_count : $start_num + ( $events_template->pag_num - 1 ) );
	$total = bp_core_number_format( $events_template->total_event_count );

	if ( 'active' == $events_template->type )
		echo sprintf( __( 'Viewing event %1$s to %2$s (of %3$s active events)', 'buddypress' ), $from_num, $to_num, $total );
	else if ( 'popular' == $events_template->type )
		echo sprintf( __( 'Viewing event %1$s to %2$s (of %3$s events with friends)', 'buddypress' ), $from_num, $to_num, $total );
	else if ( 'online' == $events_template->type )
		echo sprintf( __( 'Viewing event %1$s to %2$s (of %3$s events online)', 'buddypress' ), $from_num, $to_num, $total );
	else
		echo sprintf( __( 'Viewing event %1$s to %2$s (of %3$s events)', 'buddypress' ), $from_num, $to_num, $total );

	?><span class="ajax-loader"></span><?php
}




function bp_event_name() {
	echo apply_filters( 'bp_event_name', bp_get_event_name() );
}
	function bp_get_event_name() {
		global $events_template;
		return apply_filters( 'bp_get_event_name', $events_template->event->event_name );
	}
	add_filter( 'bp_get_event_name', 'wp_filter_kses' );
	add_filter( 'bp_get_event_name', 'stripslashes' );
	add_filter( 'bp_get_event_name', 'strip_tags' );


function bp_event_event_dates() {
	echo apply_filters( 'bp_event_dates', bp_get_event_dates() );
}
	function bp_get_event_dates() {
		global $events_template;

		$events_dates = date('F jS, Y', strtotime($events_template->event->start_date)) . ' - ' . date('F jS, Y', strtotime($events_template->event->end_date));

		return apply_filters( 'bp_get_event_dates', $events_dates );
	}

function bp_event_attendees_count() {
	echo apply_filters( 'bp_event_attendees_count', bp_get_event_attendees_count() );
}
	function bp_get_event_attendees_count() {
		global $events_template;

		return apply_filters( 'bp_get_event_attendees_count', $events_template->event->total_attendee_count );
	}

function bp_event_attendees() {
	echo apply_filters( 'bp_event_attendees', bp_get_event_attendees() );
}
	function bp_get_event_attendees() {
		global $events_template;

		if ($events_template->event->attendees > 0) {

			$attendees = explode(",",$events_template->event->attendees);
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

		return apply_filters( 'bp_get_event_attendees', $html );
	}


function bp_event_address() {
	echo apply_filters( 'bp_event_address', bp_get_event_address() );
}
	function bp_get_event_address() {
		global $events_template;
		
		$html = '';
		
		if ($events_template->event->address != '') {
			$html = '<span class="address">'.$events_template->event->address.'</span>';
		}
		
		if ($events_template->event->city != '' && $events_template->event->state != '') { 
			$html .= '<span class="city-state">'.$events_template->event->city.', '.$events_template->event->state.'</span>';
		}

		if ($events_template->event->zip != '') {
			$html .= '<span class="zip">'.$events_template->event->zip.'</span>';		
		}

		return apply_filters( 'bp_get_event_address', $html );
	}

function bp_event_venue() {
	echo apply_filters( 'bp_event_venue', bp_get_event_venue() );
}
	function bp_get_event_venue() {
		global $events_template;

		return apply_filters( 'bp_get_event_venue', $events_template->event->venue_title );
	}

function bp_event_permalink() {
	echo bp_get_event_permalink();
}
	function bp_get_event_permalink() {
		global $events_template;

		return apply_filters( 'bp_get_event_permalink', '/events/'.$events_template->event->id );
	}
	function bp_event_link() { echo bp_get_event_permalink(); }
	function bp_get_event_link() { return bp_get_event_permalink(); }















?>