<?php
/**
 * This file is included when BuddyPress is active, and after the plugins_loaded action.
 *
 * @author David Bisset <dbisset@dimensionmedia.com>
 * @package Event Espresso for BuddyPress
 * @subpackage members
 *
 */
 
/* Define a slug constant that will be used to view this components pages (http://example.org/SLUG) */
if ( !defined( 'EE_BP_SLUG' ) )
	define ( 'EE_BP_SLUG', 'events' );

/* Define other constants */
if ( !defined( 'EE_EVENT_AVATAR_ATTENDEE_HEIGHT' ) )
	define ( 'EE_EVENT_AVATAR_ATTENDEE_HEIGHT', '30' );
if ( !defined( 'EE_EVENT_AVATAR_ATTENDEE_WIDTH' ) )
	define ( 'EE_EVENT_AVATAR_ATTENDEE_WIDTH', '30' );

	
/*
 * register with hook 'wp_print_styles'
 */
add_action('wp_print_styles', 'add_event_espresso_stylesheet');

/*
 * Enqueue style-file, if it exists.
 */

function add_event_espresso_bp_stylesheet() {
    $myStyleUrl = WP_PLUGIN_URL . '/espresso-members/styles/style.css';
    $myStyleFile = WP_PLUGIN_DIR . '/espresso-members/styles/style.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('myStyleSheets', $myStyleUrl);
        wp_enqueue_style( 'myStyleSheets');
    }
}
	
/**
 * event_espresso_bp_setup_globals()
 *
 * Sets up global variables for your component.
 */
function event_espresso_bp_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->event_espresso_bp->id = 'example';

	$bp->event_espresso_bp->table_name = $wpdb->base_prefix . 'event_espresso_bp';
	$bp->event_espresso_bp->format_notification_function = 'event_espresso_bp_format_notifications';
	$bp->event_espresso_bp->slug = EE_BP_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->event_espresso_bp->slug] = $bp->event_espresso_bp->id;
}
/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_globals', 'event_espresso_bp_setup_globals' );
 */
add_action( 'wp', 'event_espresso_bp_setup_globals', 2 );
add_action( 'admin_menu', 'event_espresso_bp_setup_globals', 2 );

/**
 * Registers as a root component (example.com/cactivate/)
 *
 * @since 0.1
 */
function event_espresso_bp_setup_root_component() {
	bp_core_add_root_component( EE_BP_SLUG );
}
add_action( 'bp_setup_root_components', 'event_espresso_bp_setup_root_component' );

/**
 * Adds list item to the site's main navigation
 *
 * @global object $bp BuddyPress global settings
 * @since 0.1
 */
function bpca_add_to_site_nav() {
	global $bp;
?>
	<li<?php if ( bp_is_page( $bp->event_espresso_bp->slug ) ) : ?> class="selected"<?php endif; ?>>
		<a href="<?php echo site_url() ?>/<?php echo $bp->event_espresso_bp->slug ?>/"><?php _e( 'Events', 'event-espresso' ) ?></a>
	</li>
<?php
}
add_action( 'bp_nav_items', 'bpca_add_to_site_nav' );

function event_espresso_event_directory() {
    global $bp, $is_member_page;
// 	echo "<BR><BR><BR><BR><BR><BR>----".$bp->is_single_item."<BR>";
// 	print_r ($bp); exit;
	if ( $bp->current_component == $bp->event_espresso_bp->slug && !$bp->current_action && !$bp->current_item  ) {
                $bp->is_directory = true;

                do_action( 'event_espresso_setup_event_directory' );
                bp_core_load_template( apply_filters( 'event_espresso_template_directory', 'events/index' ) );
                return;
        } else if ($bp->current_action) {
                $bp->is_directory = false;        
                $bp->current_item = $bp->current_action;
                do_action( 'event_espresso_setup_event_detail_page' );        
                bp_core_load_template( apply_filters( 'event_espresso_template_detail_page', 'events/event' ) );
                return;
        }
}
add_action( 'wp', 'event_espresso_event_directory', 2 );

/**
 * event_espresso_bp_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 */
function event_espresso_bp_setup_nav() {
	global $bp;

	/* Add 'Example' to the main user profile navigation */
	bp_core_new_nav_item( array(
		'name' => __( 'Events', 'bp-event-espresso' ),
		'slug' => $bp->event_espresso_bp->slug,
		'position' => 80,
		'screen_function' => 'event_espresso_bp_screen_one',
		'default_subnav_slug' => 'screen-one'
	) );

	$example_link = $bp->loggedin_user->domain . $bp->event_espresso_bp->slug . '/';

	/* Create two sub nav items for this component */
	bp_core_new_subnav_item( array(
		'name' => __( 'Screen One', 'bp-event-espresso' ),
		'slug' => 'screen-one',
		'parent_slug' => $bp->event_espresso_bp->slug,
		'parent_url' => $example_link,
		'screen_function' => 'event_espresso_bp_screen_one',
		'position' => 10
	) );

	bp_core_new_subnav_item( array(
		'name' => __( 'Screen Two', 'bp-event-espresso' ),
		'slug' => 'screen-two',
		'parent_slug' => $bp->event_espresso_bp->slug,
		'parent_url' => $example_link,
		'screen_function' => 'event_espresso_bp_screen_two',
		'position' => 20,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );

	/* Add a nav item for this component under the settings nav item. See event_espresso_bp_screen_settings_menu() for more info */
	bp_core_new_subnav_item( array(
		'name' => __( 'My Events', 'bp-event-espresso' ),
		'slug' => 'my-events-management',
		'parent_slug' => $bp->settings->slug,
		'parent_url' => $bp->loggedin_user->domain . $bp->settings->slug . '/',
		'screen_function' => 'event_espresso_bp_screen_settings_menu',
		'position' => 40,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
}

/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'event_espresso_bp_setup_nav' );
 */
add_action( 'wp', 'event_espresso_bp_setup_nav', 2 );
add_action( 'admin_menu', 'event_espresso_bp_setup_nav', 2 );

/**
 * event_espresso_bp_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function event_espresso_bp_screen_one() when you want to load
 * a template file.
 */
function event_espresso_bp_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the example component pages.
	 */
	if ( $bp->current_component != $bp->event_espresso_bp->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'event_espresso_bp_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'event_espresso_bp_load_template_filter', 10, 2 );


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * event_espresso_bp_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "example/screen-one"
 */
function event_espresso_bp_screen_one() {
	global $bp;

	/**
	 * There are three global variables that you should know about and you will
	 * find yourself using often.
	 *
	 * $bp->current_component (string)
	 * This will tell you the current component the user is viewing.
	 *
	 * Example: If the user was on the page http://example.org/members/andy/groups/my-groups
	 *          $bp->current_component would equal 'groups'.
	 *
	 * $bp->current_action (string)
	 * This will tell you the current action the user is carrying out within a component.
	 *
	 * Example: If the user was on the page: http://example.org/members/andy/groups/leave/34
	 *          $bp->current_action would equal 'leave'.
	 *
	 * $bp->action_variables (array)
	 * This will tell you which action variables are set for a specific action
	 *
	 * Example: If the user was on the page: http://example.org/members/andy/groups/join/34
	 *          $bp->action_variables would equal array( '34' );
	 */

	/**
	 * On this screen, as a quick example, users can send you a "High Five", by clicking a link.
	 * When a user sends you a high five, you receive a new notification in your
	 * notifications menu, and you will also be notified via email.
	 */

	/**
	 * We need to run a check to see if the current user has clicked on the 'send high five' link.
	 * If they have, then let's send the five, and redirect back with a nice error/success message.
	 */
	if ( $bp->current_component == $bp->event_espresso_bp->slug && 'screen-one' == $bp->current_action && 'send-h5' == $bp->action_variables[0] ) {
		/* The logged in user has clicked on the 'send high five' link */
		if ( bp_is_my_profile() ) {
			/* Don't let users high five themselves */
			bp_core_add_message( __( 'No self-fives! :)', 'bp-event-espresso' ), 'error' );
		} else {
			if ( event_espresso_bp_send_highfive( $bp->displayed_user->id, $bp->loggedin_user->id ) )
				bp_core_add_message( __( 'High-five sent!', 'bp-event-espresso' ) );
			else
				bp_core_add_message( __( 'High-five could not be sent.', 'bp-event-espresso' ), 'error' );
		}

		bp_core_redirect( $bp->displayed_user->domain . $bp->event_espresso_bp->slug . '/screen-one' );
	}

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'event_espresso_bp_screen_one' );

	/****
	 * Displaying Content
	 */

	/****
	 * OPTION 1:
	 * You've got a few options for displaying content. Your first option is to bundle template files
	 * with your plugin that will be used to output content.
	 *
	 * In an earlier function event_espresso_bp_load_template_filter() we set up a filter on the core BP template
	 * loading function that will make it first look in the plugin directory for template files.
	 * If it doesn't find any matching templates it will look in the active theme directory.
	 *
	 * This example component comes bundled with a template for screen one, so we can load that
	 * template to display what we need. If you copied this template from the plugin into your theme
	 * then it would load that one instead. This allows users to override templates in their theme.
	 */

	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'event_espresso_bp_template_screen_one', 'members/events' ) );

	/****
	 * OPTION 2 (NOT USED FOR THIS SCREEN):
	 * If your component is simple, and you just want to insert some HTML into the user's active theme
	 * then you can use the bundle plugin template.
	 *
	 * There are two actions you need to hook into. One for the title, and one for the content.
	 * The functions you hook these into should simply output the content you want to display on the
	 * page.
	 *
	 * The follow lines are commented out because we are not using this method for this screen.
	 * You'd want to remove the OPTION 1 parts above and uncomment these lines if you want to use
	 * this option instead.
 	 */

//	add_action( 'bp_template_title', 'event_espresso_bp_screen_one_title' );
//	add_action( 'bp_template_content', 'event_espresso_bp_screen_one_content' );

//	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
	/***
	 * The second argument of each of the above add_action() calls is a function that will
	 * display the corresponding information. The functions are presented below:
	 */
	function event_espresso_bp_screen_one_title() {
		_e( 'Screen One', 'bp-event-espresso' );
	}

	function event_espresso_bp_screen_one_content() {
		global $bp;

		$high_fives = event_espresso_bp_get_highfives_for_user( $bp->displayed_user->id );

		/**
		 * For security reasons, we MUST use the wp_nonce_url() function on any actions.
		 * This will stop naughty people from tricking users into performing actions without their
		 * knowledge or intent.
		 */
		$send_link = wp_nonce_url( $bp->displayed_user->domain . $bp->current_component . '/screen-one/send-h5', 'event_espresso_bp_send_high_five' );
	?>
		<h4><?php _e( 'Welcome to Screen One', 'bp-event-espresso' ) ?></h4>
		<p><?php printf( __( 'Send %s a <a href="%s" title="Send high-five!">high-five!</a>', 'bp-event-espresso' ), $bp->displayed_user->fullname, $send_link ) ?></p>

		<?php if ( $high_fives ) : ?>
			<h4><?php _e( 'Received High Fives!', 'bp-event-espresso' ) ?></h4>

			<table id="high-fives">
				<?php foreach ( $high_fives as $user_id ) : ?>
				<tr>
					<td width="1%"><?php echo bp_core_fetch_avatar( array( 'item_id' => $user_id, 'width' => 25, 'height' => 25 ) ) ?></td>
					<td>&nbsp; <?php echo bp_core_get_userlink( $user_id ) ?></td>
	 			</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
	<?php
	}

/**
 * event_espresso_bp_screen_two()
 *
 * Sets up and displays the screen output for the sub nav item "example/screen-two"
 */
function event_espresso_bp_screen_two() {
	global $bp;

	/**
	 * On the output for this second screen, as an example, there are terms and conditions with an
	 * "Accept" link (directs to http://example.org/members/andy/example/screen-two/accept)
	 * and a "Reject" link (directs to http://example.org/members/andy/example/screen-two/reject)
	 */

	if ( $bp->current_component == $bp->event_espresso_bp->slug && 'screen-two' == $bp->current_action && 'accept' == $bp->action_variables[0] ) {
		if ( event_espresso_bp_accept_terms() ) {
			/* Add a success message, that will be displayed in the template on the next page load */
			bp_core_add_message( __( 'Terms were accepted!', 'bp-event-espresso' ) );
		} else {
			/* Add a failure message if there was a problem */
			bp_core_add_message( __( 'Terms could not be accepted.', 'bp-event-espresso' ), 'error' );
		}

		/**
		 * Now redirect back to the page without any actions set, so the user can't carry out actions multiple times
		 * just by refreshing the browser.
		 */
		bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component );
	}

	if ( $bp->current_component == $bp->event_espresso_bp->slug && 'screen-two' == $bp->current_action && 'reject' == $bp->action_variables[0] ) {
		if ( event_espresso_bp_reject_terms() ) {
			/* Add a success message, that will be displayed in the template on the next page load */
			bp_core_add_message( __( 'Terms were rejected!', 'bp-event-espresso' ) );
		} else {
			/* Add a failure message if there was a problem */
			bp_core_add_message( __( 'Terms could not be rejected.', 'bp-event-espresso' ), 'error' );
		}

		/**
		 * Now redirect back to the page without any actions set, so the user can't carry out actions multiple times
		 * just by refreshing the browser.
		 */
		bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component );
	}

	/**
	 * If the user has not Accepted or Rejected anything, then the code above will not run,
	 * we can continue and load the template.
	 */
	do_action( 'event_espresso_bp_screen_two' );

	add_action( 'bp_template_title', 'event_espresso_bp_screen_two_title' );
	add_action( 'bp_template_content', 'event_espresso_bp_screen_two_content' );

	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

	function event_espresso_bp_screen_two_title() {
		_e( 'Screen Two', 'bp-event-espresso' );
	}

	function event_espresso_bp_screen_two_content() {
		global $bp; ?>

		<h4><?php _e( 'Welcome to Screen Two', 'bp-event-espresso' ) ?></h4>

		<?php
			$accept_link = '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->event_espresso_bp->slug . '/screen-two/accept', 'event_espresso_bp_accept_terms' ) . '">' . __( 'Accept', 'bp-event-espresso' ) . '</a>';
			$reject_link = '<a href="' . wp_nonce_url( $bp->loggedin_user->domain . $bp->event_espresso_bp->slug . '/screen-two/reject', 'event_espresso_bp_reject_terms' ) . '">' . __( 'Reject', 'bp-event-espresso' ) . '</a>';
		?>

		<p><?php printf( __( 'You must %s or %s the terms of use policy.', 'bp-event-espresso' ), $accept_link, $reject_link ) ?></p>
	<?php
	}

function event_espresso_bp_screen_settings_menu() {
	global $bp, $current_user, $bp_settings_updated, $pass_error;

	if ( isset( $_POST['submit'] ) ) {
		/* Check the nonce */
		check_admin_referer('bp-event-espresso-admin');

		$bp_settings_updated = true;

		/**
		 * This is when the user has hit the save button on their settings.
		 * The best place to store these settings is in wp_usermeta.
		 */
		update_usermeta( $bp->loggedin_user->id, 'bp-event-espresso-option-one', attribute_escape( $_POST['bp-event-espresso-option-one'] ) );
	}

	add_action( 'bp_template_content_header', 'event_espresso_bp_screen_settings_menu_header' );
	add_action( 'bp_template_title', 'event_espresso_bp_screen_settings_menu_title' );
	add_action( 'bp_template_content', 'event_espresso_bp_screen_settings_menu_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

	function event_espresso_bp_screen_settings_menu_header() {
//		_e( 'Example Settings Header', 'bp-event-espresso' );
	}

	function event_espresso_bp_screen_settings_menu_title() {
//		_e( 'Example Settings', 'bp-event-espresso' );
	}

	function event_espresso_bp_screen_settings_menu_content() {
		global $bp, $bp_settings_updated; 
		global $espresso_premium; if ($espresso_premium != true) return;
		global $wpdb, $org_options;	
		//$wpdb->show_errors();
		require_once('user_vars.php');
		
		?>

	<div id="configure_organization_form" class="wrap meta-box-sortables ui-sortable">
  	<div id="event_reg_theme" class="wrap">
	<div id="icon-options-event" class="icon32"></div><h2><?php echo _e('My Events Management', 'event_espresso') ?></h2>
    <div id="poststuff" class="metabox-holder">
	<?php
		if($_POST['cancel_registration']){
			if (is_array($_POST['checkbox'])){
				while(list($key,$value)=each($_POST['checkbox'])):
					$del_id=$key;
					//Delete discount data
					$sql = "DELETE FROM " . EVENTS_ATTENDEE_TABLE . " WHERE id='$del_id'";
					$wpdb->query($sql);
					
					$sql = "DELETE FROM " . EVENTS_MEMBER_REL_TABLE . " WHERE attendee_id='$del_id'";
					$wpdb->query($sql);
				endwhile;	
			}
			?>
		<div id="message" class="updated fade"><p><strong><?php _e('Your event(s) have been successfully removed from your account.','event_espresso'); ?></strong></p></div>
	<?php
		}
	?>
	<form id="form1" name="form1" method="post" action="<?php echo $_SERVER["REQUEST_URI"]?>">
	
	<table id="table" class="widefat fixed" width="100%"> 
		<thead>
			<tr>
			  <th class="manage-column column-cb check-column" id="cb" scope="col" style="width:5%;"></th>
			  <th class="manage-column column-title" id="name" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Event Name','event_espresso'); ?></th>
			  <th class="manage-column column-author" id="start" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Start Date','event_espresso'); ?></th>
	          <th class="manage-column column-date" id="begins" scope="col" title="Click to Sort" style="width:20%;"><?php _e('Start Time','event_espresso'); ?></th>
	          <th class="manage-column column-date" id="status" scope="col" title="Click to Sort" style="width:10%;"><?php _e('Payment Status','event_espresso'); ?></th>
	          <th class="manage-column column-date" id="attendees" scope="col" title="Click to Sort" style="width:10%;"><?php _e('Cost','event_espresso'); ?></th>
	          <th class="manage-column column-author" id="actions" scope="col" title="Click to Sort" style="width:15%;"><?php _e('Date Paid','event_espresso'); ?></th>
			</tr>
	</thead>
	    <tbody>
<?php 
		$wpdb->get_results("SELECT id FROM ". EVENTS_MEMBER_REL_TABLE . " WHERE user_id = '" . $current_user->ID . "'");
		if ($wpdb->num_rows > 0) {
			$events = $wpdb->get_results("SELECT e.id event_id, e.event_name, e.start_date, e.event_desc, e.display_desc, a.id attendee_id, a.event_time start_time, a.payment_status, a.payment_date, a.amount_pd, u.user_id user_id 
											 	FROM " . EVENTS_ATTENDEE_TABLE . " a
												JOIN " . EVENTS_MEMBER_REL_TABLE . " u ON u.attendee_id = a.id
												JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = u.event_id
												WHERE u.user_id = '" . $current_user->ID . "'");
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
					if ($payment_status == ''){
						$payment_link = get_option('siteurl') . "/?page_id=" . $org_options['return_url'] . "&id=" . $attendee_id;
						$payment_status = '<a href="' . $payment_link . '">Pay Now</a>';
					}
					$event_url = home_url() . "/?page_id=" . $org_options['event_page_id']. "&regevent_action=register&event_id=". $event_id;
					$event_link = '<a class="row-title" href="' . $event_url . '">' . stripslashes_deep($event->event_name) . '</a>';

?>
	<tr>
	<td><input name="checkbox[<?php echo $attendee_id?>]" type="checkbox"  title="Cancel registration for <?php echo $event_name?>"></td>
			  <td class="post-title page-title column-title">
              <strong><?php echo $event_link?></strong> </td>
			  <td><?php echo event_date_display($start_date)?></td>
              <td><?php echo $start_time?></td>
              <td><?php echo $payment_status?></td>
              <td><?php echo $amount_pd?></td>
              <td><?php echo $payment_date?></td>
			  </tr>
              <div id="event_info_<?php echo $attendee_id ?>" style="display:none">
              <?php _e('<h2>Event Information</h2>','event_espresso'); ?>
				<ul>
                <li><h3 class="h3_event_title" id="h3_event_title-<?php echo $event_id;?>"><?php echo $event_name?></h3>
					<p class="p_start_date"><?php _e('Start Date:','event_espresso'); ?> <?php echo event_date_display($start_date)?></p></li>
				<li><p class="p_event_time"><?php _e('Start Time:','event_espresso'); ?> <?php echo $start_time?></p></li>
                <li><?php if ($display_desc == "Y"){ ?>
					<strong><?php _e('Description:','event_espresso'); ?></strong>
					<?php echo htmlspecialchars_decode($event_desc); ?>
					<?php }//End display description ?></li>
                
                </ul>
            </div>
	<?php } 
		}
		?>
          </tbody>
          </table>
		<input type="checkbox" name="sAll" onclick="selectAll(this)" /> <strong><?php _e('Check All','event_espresso'); ?></strong> 
    	<input name="cancel_registration" type="submit" class="button-secondary" id="cancel_registration" value="<?php _e('Cancel Registration','event_espresso'); ?>" style="margin-left:100px;" onclick="return confirmDelete();"> <!--<a style="margin-left:20px" class="button-primary"  onclick="window.location='profile.php#event_espresso_profile'">
    <?php _e('Your Profile','event_espresso'); ?>
    </a>-->
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
								 null
							]
	
		} );
		
	} );
	</script>


	<?php
	}



function event_espresso_add_activity_filters() { ?>
	<option value="register_event,created_event"><?php _e( 'Show All Event Activity', 'event_espresso' ) ?></option>
	<option value="created_event"><?php _e( 'Show Events Created', 'event_espresso' ) ?></option>
	<option value="register_event"><?php _e( 'Show Events Registered', 'event_espresso' ) ?></option>
<?php }

add_action( 'bp_member_activity_filter_options', 'event_espresso_add_activity_filters' );

function event_espresso_profile_header_events() {
	global $bp; 

	$link = $bp->displayed_user->domain . 'events';

	$html =  '<ul class="event-count-totals">';
	$user_events_volunteered = event_espresso_get_num_events($bp->displayed_user->id, 'Volunteer');
	if ($user_events_volunteered > 0) { 
		$html .= '<li>Events Volunteered: <span><a href="'.$link.'">'.$user_events_volunteered.'</a></span></li>';
	}	
	$user_events_presenting = event_espresso_get_num_events($bp->displayed_user->id, 'Presenter');
	if ($user_events_presenting > 0) { 
		$html .= '<li>Events Presented: <span>a href="'.$link.'">'.$user_events_presenting.'</a></span></li>';
	}	
	$user_events_organizer = event_espresso_get_num_events($bp->displayed_user->id, 'Organizer');
	if ($user_events_organizer > 0) { 
		$html .= '<li>Events Organized: <span>a href="'.$link.'">'.$user_events_organizer.'</a></span></li>';
	}	

	$html .= '</ul>';
	echo $html;

}

add_action ( 'bp_before_member_header_meta', 'event_espresso_profile_header_events' );

function event_espresso_get_total_event_count() {
	global $wpdb;	
	$sql = "SELECT COUNT(*) FROM " . EVENTS_DETAIL_TABLE ;
	$total = $wpdb->get_var( $wpdb->prepare( $sql ) );
	return $total;
}

function event_espresso_get_num_events($user_id, $user_role) {
	global $wpdb;
	
	if ($user_id > 0) {
		$sql = "SELECT ed.* FROM " . EVENTS_DETAIL_TABLE . " ed
													JOIN wp_events_member_rel mr ON ed.id = mr.event_id
													JOIN wp_users u ON u.ID = mr.user_id
													JOIN wp_events_attendee_meta am ON am.attendee_id = mr.attendee_id
													WHERE u.ID = " . $user_id . " AND am.meta_key = 'EVENT_USER_ROLE' ";
													
		if ($user_role != '') {
			$sql .= " AND am.meta_value = '".$user_role."'";
		}
 		$sql .= " GROUP BY ed.id";
		$total = $wpdb->get_var( $wpdb->prepare( $sql ) );

		return $total;
	}
}

function event_espresso_buddypress_add_event($event_id) {
	global $wpdb, $bp;

	if ($event_id > 0) {
	
		// get title of event to add into activity
		$event_name = $wpdb->get_var( $wpdb->prepare( "SELECT event_name FROM " . EVENTS_DETAIL_TABLE . " WHERE id = " .$event_id ) );
		
		$component = "event_espresso";
		$type = "created_event";
		
		$entry = array(
			'action' =>  sprintf( __( '%1$s created the event: \'%2$s\' ', 'event_espresso' ), bp_core_get_userlink( $bp->loggedin_user->id ), $event_name ),
			'component' => $component,
			'type' => $type,
			'primary_link' => bp_core_get_user_domain( $user_id ),
			'user_id' => $bp->loggedin_user->id,
			'item_id' => $event_id
		);
		
		return bp_activity_add( apply_filters( 'event_espresso_record_activity', $entry ) );
	
	}

}

function event_espresso_event_add_register_button() {
	global $events_template;
	
	echo event_espresso_get_add_register_button( $events_template->event->id );
}
add_action( 'bp_directory_events_actions', 'event_espresso_event_add_register_button' );

function event_espresso_get_add_register_button( $event_id = 0 ) {
	global $bp, $events_template, $org_options, $wpdb;

	if ( $event_id == 0 )
		return false;
		
 	$is_active = event_espresso_get_is_active($event_id);		
 	
    switch ($is_active['status']) {
        case 'EXPIRED': //only show the event description.
            $html = _e('<p class="expired_event">This event has passed.</p>', 'event_espresso');
            return $html;
            break;

        case 'REGISTRATION_CLOSED': //only show the event description.
            // if todays date is after $reg_end_date
            $html = _e('<p class="expired_event">We are sorry but registration for this event is now closed.</p>', 'event_espresso');
            return $html;
            break;

        case 'REGISTRATION_NOT_OPEN': //only show the event description.
            // if todays date is after $reg_end_date
            // if todays date is prior to $reg_start_date
            $html = _e('<p class="expired_event">We are sorry but this event is not yet open for registration.</p>', 'event_espresso');
            return $html;
            break;
    }
		
	$event = $wpdb->get_row("select * from ".EVENTS_DETAIL_TABLE." WHERE id = $event_id ");
	$externalURL = $event->externalURL;
	$registration_url = $externalURL != '' ? $externalURL : home_url() . '/?page_id='.$org_options['event_page_id'].'&regevent_action=register&event_id='. $event_id ;

	$button = array(
		'id'                => 'register',
		'component'         => 'events',
		'must_be_logged_in' => false,
		'block_self'        => false,
		'wrapper_class'     => 'register-button',
		'wrapper_id'        => 'register-button-' . $event_id,
		'link_class'        => 'requested',
		'link_href'         => $registration_url,
		'link_text'         => __( 'Register For Event', 'buddypress' ),
		'link_title'        => __( 'Register For Event', 'buddypress' )
	);

	// Filter and return the HTML button
	return bp_get_button( apply_filters( 'event_espresso_get_add_register_button', $button ) );
}






























/**
 * event_espresso_core_get_events()
 *
 * Return an array of event IDs based on the parameters passed.
 *
 */
function event_espresso_core_get_events( $args = '' ) {
	global $bp;

	$defaults = array(
		'type' => 'active', // active, newest, alphabetical, random
		'event_id' => 0, // Pass an event_id to limit things to a single event
		'search_terms' => false, // Limit to users that match these search terms
		'include' => false, // Pass comma separated list of event_ids to limit to only these events
		'per_page' => 20, // The number of results to return per page
		'page' => 1, // The page to return if limiting per page
		'populate_extras' => true, // Fetch additional event metadata crap
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	return apply_filters( 'event_espresso_core_get_events', Event_Espresso_Event::get_events( $type, $per_page, $page, $event_id, $include, $search_terms, $populate_extras ), &$params );	



}